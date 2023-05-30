<?php
/**
 *Created by PhpStorm
 *Created at ২২/৯/২১ ১০:৪৩ AM
 */

namespace App\Http\Controllers\Ar;


use App\Contracts\LookupContract;
use App\Entities\Ap\FasApInvoice;
use App\Entities\Ap\FasApInvoiceDoc;
use App\Entities\Common\LCurrency;
use App\Entities\Gl\GlCoaParams;
use App\Enums\ApprovalStatus;
use App\Enums\Ar\ArFunType;
use App\Enums\WorkFlowRoleKey;
use App\Helpers\HelperClass;
use App\Entities\Security\User;
use App\Entities\WorkFlowMapping;
use App\Enums\ApprovalStatusView;
use App\Enums\Common\LGlInteModules;
use App\Enums\WkReferenceColumn;
use App\Enums\WkReferenceTable;
use App\Enums\WorkFlowMaster;
use App\Http\Controllers\Controller;
use App\Managers\Ar\ArLookupManager;
use App\Managers\Common\CommonManager;
use App\Managers\FlashMessageManager;
use App\Managers\LookupManager;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class InvoiceBillAuthorizeController extends Controller
{
    use HasPermission;

    protected $commonManager;

    /** @var LookupManager */
    private $lookupManager;

    /** @var ArLookupManager */
    private $arLookupManager;

    protected $glCoaParam;
    //protected $lookupManager;
    protected $flashMessageManager;
    protected $invoice;
    private $currency;
    private $attachment;

    public function __construct(LookupContract $lookupManager, ArLookupManager $arLookupManager, CommonManager $commonManager, FlashMessageManager $flashMessageManager)
    {
        $this->lookupManager = $lookupManager;
        $this->arLookupManager = $arLookupManager;
        $this->commonManager = $commonManager;
        $this->flashMessageManager = $flashMessageManager;

        $this->glCoaParam = new GlCoaParams();
        $this->invoice = new FasApInvoice();
        $this->currency = new LCurrency();
        $this->attachment = new FasApInvoiceDoc();
    }

    public function index($filter = null)
    {
        $fiscalYear = $this->lookupManager->getCurrentFinancialYear();
        $data['billSecs'] = $this->lookupManager->getBillSections(ArFunType::AR_INVOICE_BILL_ENTRY);
        $data['invoiceStatus'] = $this->arLookupManager->getInvoiceStatus();
        $filterData = isset($filter) ? explode('#', Crypt::decryptString($filter)) : $filter;

        return view('ar.invoice-bill-authorize.index', compact('data', 'fiscalYear', 'filterData'));
    }

    public function searchInvoiceBillAuthorize(Request $request)
    {
        $period = $request->post('period');
        $billSection = $request->post('bill_section');
        $billReg = $request->post('bill_reg_id');
        $approvalStatus = $request->post('approval_status');
        $user_id = auth()->id();

        $filteredData = Crypt::encryptString($request->post('fiscalYear') . '#' . $request->post('period') . '#' . $request->post('bill_section') . '#' . $request->post('bill_reg_id') . '#' . $request->post('approval_status'));

        $data = DB::select("select * from sbcacc.arGetInvoiceAuthList (
            :p_fiscal_year_id,
            :p_trans_period_id,
            :p_bill_sec_id,
            :p_bill_reg_id,
            :p_workflow_approval_status,
            :p_user_id)",
            [
                "p_fiscal_year_id"=>$request->post('fiscalYear'),
                "p_trans_period_id" => $period,
                "p_bill_sec_id" => $billSection,
                "p_bill_reg_id" => $billReg,
                "p_workflow_approval_status" => $approvalStatus,
                "p_user_id" => $user_id
            ]);


        return datatables()->of($data)
            ->addIndexColumn()
            ->editColumn('document_no', function ($data) {
                return $data->document_no;
            })
            ->editColumn('document_date', function ($data) {
                return HelperClass::dateConvert($data->document_date);
            })
            ->editColumn('invoice_amount', function ($data) {
                return HelperClass::getCommaSeparatedValue($data->invoice_amount);
            })
            ->editColumn('vat_amount', function ($data) {
                return HelperClass::getCommaSeparatedValue($data->vat_amount);
            })
            ->editColumn('receivable_amount', function ($data) {
                return HelperClass::getCommaSeparatedValue($data->receivable_amount);
            })
            ->editColumn('document_reference', function ($data) {
                return $data->document_ref;
            })
            ->editColumn('invoice_status', function ($query) {
                if ($query->approval_status == ApprovalStatusView::PENDING) {
                    return '<span class="badge badge-primary badge-pill">' . ApprovalStatusView::PENDING . '</span>';
                } else if ($query->approval_status == ApprovalStatusView::APPROVED) {
                    return '<span class="badge badge-success badge-pill">' . ApprovalStatusView::APPROVED . '</span>';
                } else if ($query->approval_status == ApprovalStatusView::REJECTED) {
                    return '<span class="badge badge-danger badge-pill">' . ApprovalStatusView::REJECTED . '</span>';
                } else {
                    return '<span class="badge badge-warning badge-pill">' . ApprovalStatusView::FORWARDED . '</span>';
                }
            })
            ->editColumn('action', function ($data) use ($filteredData) {
                return "<a style='text-decoration:underline' href='" . route('ar-invoice-bill-authorize.approval-view', [$data->invoice_id, 'wk_map_id' => $data->workflow_mapping_id, 'user_id' => $data->login_user_id, 'wk_ref_status' => $data->workflow_reference_status, 'filter' => $filteredData]) . "' class='' data-target='' ><i class='bx bx-show'></i></a>";

            })
            ->rawColumns(['action', 'invoice_status'])
            ->make(true);
    }

    public function approvalView(Request $request, $id, $filter = null)
    {
        $wkMapId = $request->get('wk_map_id');
        $userId = $request->get('user_id');
        $wkRefStatus = $request->get('wk_ref_status');

        $empInfo = User::with(['employee'])->where('user_id', $userId)->first();

        $wkMapInfo = WorkFlowMapping::where('workflow_mapping_id', $wkMapId)->first();

        /*$inserted_data = DB::selectOne("select sbcacc.fas_ar_trans.get_ar_invoice_view(:p_invoice_id) from dual", ["p_invoice_id" => $id]);
        $inserted_data->invoice_line = DB::select("select sbcacc.fas_ar_trans.get_ar_invoice_trans_view(:p_invoice_id) from dual", ["p_invoice_id" => $id]);
        $inserted_data->invoice_file = DB::select("select sbcacc.fas_ar_trans.get_ar_invoice_docs_view(:p_invoice_id) from dual", ["p_invoice_id" => $id]);*/

        $inserted_data = DB::selectOne("select * from sbcacc.arGetInvoiceView(:p_invoice_id)", ["p_invoice_id" => $id]);
        $inserted_data->invoice_line = DB::select("select * from sbcacc.arGetInvoiceTransView(:p_invoice_id) ", ["p_invoice_id" => $id]);
        $inserted_data->invoice_file = DB::select("select * from sbcacc.arGetInvoiceDocsView(:p_invoice_id) ", ["p_invoice_id" => $id]);

        $fiscalYear = $this->lookupManager->getCurrentFinancialYear();
        $department = $this->lookupManager->getLCostCenter();
        $billSecs = $this->lookupManager->getBillSections(ArFunType::AR_INVOICE_BILL_ENTRY);

        $customerCategory = $this->arLookupManager->getCustomerCategory();
        $data['subsidiary_type'] = $this->lookupManager->findPartySubLedger(LGlInteModules::ACCOUNT_RECEIVABLE);
        $data['invoice_type'] = $this->arLookupManager->getTransactionType();
        $data['currency'] = $this->currency->get();
        $coaParams = $this->lookupManager->getSpecifiedGlCoaParams([\App\Enums\Common\GlCoaParams::ASSET, \App\Enums\Common\GlCoaParams::EXPENSE]);
        $receiptTerms = $this->lookupManager->getArPaymentTerms();
        $receiptMethods = $this->lookupManager->getArPaymentMethods();
        $transactionType = $this->arLookupManager->getTransactionType();

        $cancelPermission = 'hidden';
        if ($wkMapInfo->reference_status == ApprovalStatus::APPROVED) {
            $response = HelperClass::checkRoleStatus(WorkFlowRoleKey::CAN_CANCEL_AR_ENTRY_VOUCHER);
            $cancelPermission = isset($response) ? '' : 'hidden';
        }

        return view('ar.invoice-bill-authorize.view', compact('cancelPermission', 'transactionType', 'receiptTerms', 'fiscalYear', 'receiptMethods', 'department', 'billSecs', 'data', 'coaParams', 'customerCategory', 'inserted_data', 'wkMapId', 'wkRefStatus', 'empInfo', 'wkMapInfo', 'filter'));
    }

    public function download($id)
    {
        $attachment = $this->attachment->where('doc_file_id', '=', $id)->first();
        $content = base64_decode($attachment->doc_file_content);

        return response()->make($content, 200, [
            'Content-Type' => $attachment->doc_file_type,
            'Content-Disposition' => 'attachment;filename="' . $attachment->doc_file_name . '"'
        ]);
    }

    public function approveRejectCancel(Request $request, $wkMapId = null, $filter = null)
    {
        /*** BLock this section cancel voucher ***/
        /*if ($request->get('approve_reject_value') == ApprovalStatus::CANCEL) {
            try {
                DB::beginTransaction();
                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");

                $params = [
                    'p_invoice_id' => $request->get('invoice_id'),
                    'p_user_id' => auth()->id(),
                    'o_status_code' => &$status_code,
                    'o_status_message' => &$status_message,
                ];

                DB::executeProcedure('sbcacc.fas_ar_trans$trans_ar_invoice_cancel', $params);
                if ($params['o_status_code'] != 1) {
                    DB::rollBack();
                    return redirect()->back()->with(['filter' => $filter, 'error' => $status_message]);
                } else {
                    DB::commit();
                    return redirect()->route('ar-invoice-bill-authorize.index', ['filter' => $filter])->with('success', $status_message);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with(['filter' => $filter, 'error' => $e->getMessage()]);

            }
        }*/

        $response = $this->inv_bill_api_approved_rejected($request, $wkMapId);

        /*$message = $response['o_status_message'];*/
        $flashMessageContent = $this->flashMessageManager->getMessage($response);

        if ($response['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }
        return redirect()->route('ar-invoice-bill-authorize.index', ['filter' => $filter])->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function inv_bill_api_approved_rejected($request, $wkMapId)
    {
        $postData = $request->post();

        DB::beginTransaction();
        try {
            $status_code = sprintf("%4000d", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'i_workflow_mapping_id' => $wkMapId,
                'i_workflow_master_id' => WorkFlowMaster::AR_INVOICE_BILL_ENTRY_APPROVAL,
                'i_reference_table' => WkReferenceTable::FAS_AR_INVOICE,
                'i_reference_key' => WkReferenceColumn::AR_INVOICE_ID,
                'i_reference_status' => $postData['approve_reject_value'],
                'i_reference_comment' => $postData['comment_on_decline'],
                'i_user_id' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('sbcacc.WORKFLOW_APPROVAL_ENTRY_DUMMY', $params);
            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        DB::commit();
        return $params;
    }
}
