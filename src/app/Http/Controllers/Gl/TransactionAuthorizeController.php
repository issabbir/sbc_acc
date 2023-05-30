<?php


namespace App\Http\Controllers\Gl;

use App\Contracts\Gl\GlContract;
use App\Contracts\LookupContract;
use App\Entities\Gl\GlTransDetail;
use App\Entities\Gl\GlTransDocs;
use App\Entities\Gl\GlTransMaster;
use App\Entities\Security\SecUserRoles;
use App\Entities\WorkFlowMapping;
use App\Entities\WorkFlowTemplate;
use App\Enums\Ap\Role;
use App\Enums\ApprovalStatus;
use App\Enums\ApprovalStatusView;
use App\Enums\Common\DebitCredit;
use App\Enums\Common\LGlInteFun;
use App\Enums\Common\LGlInteModules;
use App\Enums\Common\LTransAmtType;
use App\Enums\WkReferenceColumn;
use App\Http\Controllers\Controller;
use App\Enums\ProActionType;
use App\Enums\YesNoFlag;
use App\Helpers\HelperClass;
use App\Managers\Common\CommonManager;
use App\Managers\FlashMessageManager;
use App\Managers\Gl\GlManager;
use App\Managers\LookupManager;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TransactionAuthorizeController extends Controller
{
    use HasPermission;

    protected  $commonManager;
    protected $attachment;

    /** @var LookupManager */
    private $lookupManager;

    /** @var GlManager */
    private $glManager;


    public function __construct(LookupContract $lookupManager, GlContract $glManager, CommonManager $commonManager)
    {
        $this->lookupManager = $lookupManager;
        $this->glManager = $glManager;

        $this->commonManager = $commonManager;
        $this->attachment = new GlTransDocs();
    }

    public function index($filter = null)
    {
        $moduleId = LGlInteModules::FIN_ACC_GENE_LEDGER;
        //$fiscalYear = $this->lookupManager->getACurrentFinancialYear(); //Block-Pavel: 14-07-22
        $fiscalYear = $this->lookupManager->getCurrentFinancialYear(); //Add-Pavel: 14-07-22

        $filterData = isset($filter) ? explode('#',Crypt::decryptString($filter)) : $filter;

        return view('gl.transaction-authorize.index', [
            'dptList' => $this->lookupManager->getLCostCenter(),
            'lBillSecList' => $this->lookupManager->findLBillSec(),
            //'postPeriodList' => $this->lookupManager->findPostingPeriod($fiscalYear->fiscal_year_id),
            'fiscalYear' => $fiscalYear,
            'cashTranFunTypeList' => $this->lookupManager->getIntegrationFunListOnAuth(),
        ],compact('filterData'));
    }

    public function searchTransactionsAuthorizeMst(Request $request)
    {
        $terms = $request->post();
        $queryResult = [];
        $user_id = auth()->id();

        /** All Parameter Filter **/
        $params = [
            'p_user_id' =>  $user_id,
            'p_workflow_approval_status' => $terms['trans_mst_approval_status'] ? $terms['trans_mst_approval_status'] : null,
            /*'p_function_id' =>   $terms['fun_type_id'] ?   $terms['fun_type_id'] : null,*/
            //'p_fiscalYear' =>   $terms['fiscalYear'] ?   $terms['fiscalYear'] : null,
            'p_trans_period_id' =>   $terms['period'] ?   $terms['period'] : null,
            'p_bill_sec_id' =>   $terms['bill_sec_id'] ?   $terms['bill_sec_id'] : null,
            'p_bill_reg_id' =>   $terms['bill_reg_id'] ?   $terms['bill_reg_id'] : null,
            /*'p_department_id' =>   $terms['dpt_id'] ?   $terms['dpt_id'] : null,
            'p_trans_date' =>   $terms['posting_date_field'] ?   HelperClass::dateFormatForDB($terms['posting_date_field']) : null,
            'p_trans_batch_id' =>   $terms['posting_batch_id'] ?   $terms['posting_batch_id'] : null,*/

        ];

        $filteredData = Crypt::encryptString($terms['fiscalYear'] .'#'.$terms['period'] .'#'. $terms['bill_sec_id'] .'#'. $terms['bill_reg_id'] .'#'. $terms['trans_mst_approval_status']);

        /** Execute Oracle Function With Params **/
        //$sql ="select sbcacc.fas_gl_trans.get_transaction_authorize_list (:p_user_id,:p_workflow_approval_status,:p_function_id,:p_trans_period_id,:p_bill_sec_id,:p_bill_reg_id,:p_department_id,:p_trans_date,:p_trans_batch_id) from dual";
        $sql ="select * from sbcacc.glGetTransactionAuthorizeList (:p_trans_period_id,:p_bill_sec_id,:p_bill_reg_id,:p_workflow_approval_status,:p_user_id)";
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

            ->editColumn('status', function($query) {
                if($query->approval_status == ApprovalStatusView::PENDING){
                    return '<span class="badge badge-primary badge-pill">'.ApprovalStatusView::PENDING.'</span>';
                } else if ($query->approval_status == ApprovalStatusView::FORWARDED) {
                    return '<span class="badge badge-warning badge-pill">'.ApprovalStatusView::FORWARDED.'</span>';
                } else if ($query->approval_status == ApprovalStatusView::APPROVED) {
                    return '<span class="badge badge-success badge-pill">'.ApprovalStatusView::APPROVED.'</span>';
                } else {
                    return '<span class="badge badge-danger badge-pill">'.ApprovalStatusView::REJECTED.'</span>';
                }
            })
            ->addColumn('action', function ($query) use ($filteredData) {
                $dataString = $query->workflow_mapping_id."##".$query->workflow_master_id."##".$query->workflow_reference_table."##".$query->workflow_reference_status."##".$filteredData;
                return '<a data-transaction-data="'.$dataString.'" class="trans-mst btn btn-primary btn-sm cursor-pointer" style="color:white"  id="'.$query->trans_master_id.'">Select</a>';
            })
            ->rawColumns(['status','action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function searchTransactionsAuthorizeDtl(Request $request)
    {
        $terms = $request->post();
        $queryResult = [];
        ///dd($terms['trans_mst_id']);

        if(empty($terms['trans_mst_id'])) {
            $queryResult = [];
        } else {
            //$queryResult = GlTransDetail::with(['gl_coa'])->where('trans_master_id', $terms['trans_mst_id'])->get();
            $sql ="select * from sbcacc.getTransactionDetailView (:p_trans_master_id)";
            $queryResult = DB::select($sql,['p_trans_master_id' => $terms['trans_mst_id']] );

        }


        return datatables()->of($queryResult)

            ->addIndexColumn()
            ->make(true);
    }

    public function approveRejectCancel(Request $request, $wkMapId=null) {

        $filter = $request->get('filter');

        if ($request->get('ref_status') == ApprovalStatus::CANCEL)
        {
            try {
                DB::beginTransaction();
                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");

                $params = [
                    'p_trans_master_id' => $request->get('trans_mst_id'),
                    'p_user_id' => auth()->id(),
                    'o_status_code' => &$status_code,
                    'o_status_message' => &$status_message,
                ];

                DB::executeProcedure('sbcacc.FAS_GL_TRANS$trans_gl_cancel', $params);

                if ($params['o_status_code'] != 1) {
                    DB::rollBack();
                    return redirect()->back()->with(['filter'=>$filter,'error' => $status_message]);

                }else{
                    DB::commit();
                    return redirect()->route('transaction-authorize.index',['filter'=>$filter])->with('success', $status_message);
                }
            }catch (\Exception $e){
                DB::rollBack();
                return redirect()->back()->with(['filter'=>$filter,'error' => $e->getMessage()]);

            }
        }

        $response = $this->transaction_authorize_api_approved_rejected($request, $wkMapId);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message)->withInput();
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('transaction-authorize.index',['filter'=>$filter]);
    }

    private function transaction_authorize_api_approved_rejected($request, $wkMapId)
    {
        $wkMstId = $request->get('wk_mst_id');
        $refTable = $request->get('ref_tbl');
        if ($request->get('ref_status') == ApprovalStatus::APPROVED)
        {
            $refStatus = ApprovalStatus::APPROVED;
        }elseif ($request->get('ref_status') == ApprovalStatus::CANCEL)
        {
            $refStatus = ApprovalStatus::CANCEL;
        }else{
            $refStatus = ApprovalStatus::REJECT;
        }
          $remarks = $request->get('rem') == 'true' ? 'N/A' : $request->get('rem');

        DB::beginTransaction();
        try {
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'I_WORKFLOW_MAPPING_ID' => $wkMapId,
                'I_WORKFLOW_MASTER_ID' => $wkMstId,
                'I_REFERENCE_TABLE' => $refTable,
                'I_REFERENCE_KEY' => WkReferenceColumn::TRANS_MASTER_ID,
                'I_REFERENCE_STATUS' => $refStatus,
                'I_REFERENCE_COMMENT' => $remarks,
                'I_USER_ID' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('sbcacc.WORKFLOW_APPROVAL_ENTRY', $params);
            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        DB::commit();
        return $params;
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = $this->attachment->where('trans_doc_file_id','=',$attachmentId)->first();
        $content =  base64_decode($attachment->trans_doc_file_content);

        return response()->make($content, 200, [
            'Content-Type' => $attachment->trans_doc_file_type,
            'Content-Disposition' => 'attachment;filename="'.$attachment->trans_doc_file_name.'"'
        ]);
    }



}
