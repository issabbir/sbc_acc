<?php


namespace App\Http\Controllers\Gl;

use App\Contracts\Ap\ApLookupContract;
use App\Contracts\Gl\GlContract;
use App\Contracts\LookupContract;
use App\Entities\Gl\GlCoaParams;
use App\Entities\Gl\GlTransDetail;
use App\Entities\Gl\GlTransDocs;
use App\Entities\Gl\GlTransMaster;
use App\Enums\ApprovalStatus;
use App\Enums\ApprovalStatusView;
use App\Enums\BudgetMonitoring\BmnFunctionType;
use App\Enums\Common\DebitCredit;
use App\Enums\Common\LGlInteFun;
use App\Enums\Common\LGlInteModules;
use App\Enums\Common\LTransAmtType;
use App\Enums\Gl\FunctionTypes;
use App\Http\Controllers\Controller;
use App\Enums\ProActionType;
use App\Enums\YesNoFlag;
use App\Helpers\HelperClass;
use App\Managers\Ar\ArLookupManager;
use App\Managers\Common\CommonManager;
use App\Managers\FlashMessageManager;
use App\Managers\Gl\GlManager;
use App\Managers\LookupManager;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    use HasPermission;

    protected $commonManager;
    protected $attachment;

    /** @var LookupManager */
    private $lookupManager;

    /** @var GlManager */
    private $glManager;

    private $glCoaParam;
    private $apLookupManager;
    private $arLookupManager;

    public function __construct(LookupContract $lookupManager, GlContract $glManager, CommonManager $commonManager, ApLookupContract $apLookupManager,
                                ArLookupManager $arLookupManager)
    {
        $this->lookupManager = $lookupManager;
        $this->glManager = $glManager;
        $this->commonManager = $commonManager;
        $this->attachment = new GlTransDocs();
        $this->glCoaParam = new GlCoaParams();
        $this->apLookupManager = $apLookupManager;
        $this->arLookupManager = $arLookupManager;

    }

    public function index()
    {
        $moduleId = LGlInteModules::FIN_ACC_GENE_LEDGER;
        $fiscalYear = $this->lookupManager->getACurrentFinancialYear();

        $fiscalYears = $this->lookupManager->getCurrentFinancialYear();
        $department = $this->lookupManager->getDeptCostCenter();

        return view('gl.transaction.index', [
            'dptList' => $this->lookupManager->getDeptCostCenter(),
            'lBillSecList' => $this->lookupManager->findLBillSec(),
            'postPeriodList' => $this->lookupManager->findPostingPeriod($fiscalYear->fiscal_year_id),
            'cashTranFunTypeList' => $this->lookupManager->getIntegrationFunList($moduleId),
            'fiscalYear' => $fiscalYears,
            'department' => $department,
            $billSecs = $this->lookupManager->getBillSections(BmnFunctionType::BUDGET_BOOKING)

        ]);
    }

    public function searchTransactionsMst(Request $request)
    {

        /** All Parameter Filter **/
          $params = [
            'p_trans_period_id' => $request->post('period', null),
            'p_bill_section' => $request->post('bill_sec_id', null),
            'p_bill_register' => $request->post('bill_reg_id', null),
            'p_workflow_approval_status' => $request->post('status', null),
        ];

        /** Execute MS SQL Function With Params **/
        $sql = "select * from  sbcacc.glGetTransactionMasterView (:p_trans_period_id,:p_bill_section,:p_bill_register,:p_workflow_approval_status)";

        $queryResult = DB::select($sql, $params);

        return datatables()->of($queryResult)

            ->editColumn('document_date',function ($d){
                return HelperClass::dateConvert($d->document_date);
            })
            ->editColumn('debit_sum',function ($d){
                return HelperClass::getCommaSeparatedValue($d->debit_sum);
            })
            ->editColumn('credit_sum',function ($d){
                return HelperClass::getCommaSeparatedValue($d->credit_sum);
            })
            ->editColumn('status', function ($query) {
                if ($query->approval_status == ApprovalStatusView::PENDING) {
                    return '<span class="badge badge-primary badge-pill">' . ApprovalStatusView::PENDING . '</span>';
                } else if ($query->approval_status == ApprovalStatusView::FORWARDED) {
                    return '<span class="badge badge-warning badge-pill">' . ApprovalStatusView::FORWARDED . '</span>';
                } else if ($query->approval_status == ApprovalStatusView::APPROVED) {
                    return '<span class="badge badge-success badge-pill">' . ApprovalStatusView::APPROVED . '</span>';
                } else {
                    return '<span class="badge badge-danger badge-pill">' . ApprovalStatusView::REJECTED . '</span>';
                }
            })
            ->addColumn('action', function ($query) {
            /*$editRoute = '|<a href="' . route("transaction.edit", ['id' => $query->trans_master_id]) . '"><i class="bx bx-edit"></i></a>';*/
                return '<a href="#" class="trans-mst"  id="' . $query->trans_master_id . '"><i class="bx bx-show"></i></a>';
            })

            ->rawColumns(['status', 'action'])
            ->addIndexColumn()
            ->make(true);


    }

    public function searchTransactionsDtl(Request $request)
    {
        $terms = $request->post();
        $queryResult = [];

        if (empty($terms['trans_mst_id'])) {
            $queryResult = [];
        } else {
            $sql = "select * from sbcacc.getTransactionDetailView (:p_trans_master_id) ";
            $queryResult = DB::select($sql, ['p_trans_master_id' => $terms['trans_mst_id']]);
            //dd($queryResult);
        }

        return datatables()->of($queryResult)
            ->editColumn('party_account_id',function ($q){
                return $q->party_id;
            })
            ->editColumn('party_account_name',function ($q){
                return $q->party_name;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = $this->attachment->where('trans_doc_file_id', '=', $attachmentId)->first();
        $content = base64_decode($attachment->trans_doc_file_content);

        return response()->make($content, 200, [
            'Content-Type' => $attachment->trans_doc_file_type,
            'Content-Disposition' => 'attachment;filename="' . $attachment->trans_doc_file_name . '"'
        ]);
    }

    public function edit($id)
    {
        $user_id = auth()->id();
        $glTransMstInfo = DB::selectOne("select sbcacc.fas_gl_trans.get_transaction_edit_view (:p_trans_master_id) from dual", ['p_trans_master_id' => $id]);
        $glTransMstInfo->trans_master_id = $id;
        $fiscalYear = $this->lookupManager->getCurrentFinancialYear();
        //$postingDate = $this->lookupManager->findPostingPeriod($fiscalYear->fiscal_year_id);
        $department = $this->lookupManager->getDeptCostCenter();
        switch ($glTransMstInfo->function_parent_id) {
            case LGlInteFun::CASH_REC_VOUCHER:
                $funcType = $this->lookupManager->getIntegrationFunType(LGlInteFun::CASH_REC_VOUCHER, LGlInteModules::FIN_ACC_GENE_LEDGER);
                break;
            case LGlInteFun::CASH_PAY_VOUCHER:
                $funcType = $this->lookupManager->getIntegrationFunType(LGlInteFun::CASH_PAY_VOUCHER, LGlInteModules::FIN_ACC_GENE_LEDGER);
                break;
            case LGlInteFun::CASH_TRANS_VOUCHER:
                $funcType = $this->lookupManager->getIntegrationFunType(LGlInteFun::CASH_TRANS_VOUCHER, LGlInteModules::FIN_ACC_GENE_LEDGER);
                break;
            default:
                $funcType = $this->lookupManager->getIntegrationFunType(LGlInteFun::JOURNAL_VOUCHER, LGlInteModules::FIN_ACC_GENE_LEDGER);
                break;
        }
        $billSecs = $this->lookupManager->getBillSections($glTransMstInfo->function_id);

        $lastGlTranMst = $this->glManager->findLastGlTranMst(LGlInteFun::CASH_REC_VOUCHER, $user_id);
        $coaParams = $this->glCoaParam->get();

        $customerCategory = $this->arLookupManager->getCustomerCategory();
        $vendorCategory = $this->apLookupManager->getVendorCategory();
        $vendorType = $this->apLookupManager->getVendorTypes();

        return view('gl.common_edit_reference', compact('glTransMstInfo','billSecs', 'coaParams', 'vendorType', 'customerCategory', 'vendorCategory', 'fiscalYear', 'department', 'funcType', 'lastGlTranMst'));

    }

    public function transactionUpdate(Request $request)
    {
        $status_code = sprintf("%4000d", "");
        $status_message = sprintf("%4000s", "");

        $params = [
            'p_trans_master_id' => $request->post('transMasterId'),
            'p_trans_period_id' => $request->post('transPeriod'),
            'p_trans_date' => HelperClass::dateFormatForDB($request->post('transDate')),
            'p_document_date' => HelperClass::dateFormatForDB($request->post('documentDate')),
            'p_document_no' => $request->post('documentNumber'),
            'p_document_ref' => $request->post('documentRef'),
            'p_department_id' => $request->post('department'),
            'p_bill_reg_id' => $request->post('billRegister'),
            'p_bill_sec_id' => $request->post('billSection'),
            'p_narration' => $request->post('documentNarration'),
            'p_user_id' => Auth()->id(),
            'o_status_code' => &$status_code,
            'o_status_message' => &$status_message
        ];

        DB::beginTransaction();
        try {
            DB::executeProcedure('sbcacc.fas_gl_trans$trans_gl_ref_update', $params);
            DB::commit();
        } catch (\Exception $e) {
            return ['response_code' => 99, 'response_message' => $e->getMessage()];
            DB::rollBack();
        }
        return ['response_code' => $status_code, 'response_message' => $status_message];
    }
}
