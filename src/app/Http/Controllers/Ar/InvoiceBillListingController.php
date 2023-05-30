<?php
/**
 *Created by PhpStorm
 *Created at ২২/৯/২১ ১০:৪৩ AM
 */

namespace App\Http\Controllers\Ar;


use App\Contracts\Ar\ArContract;
use App\Entities\Ap\FasApInvoice;
use App\Entities\Ar\FasArInvoiceDocs;
use App\Entities\Common\LCurrency;
use App\Entities\Gl\GlCoaParams;
use App\Enums\Ap\HoldFlag;
use App\Enums\Ar\ArFunType;
use App\Enums\Common\LGlInteModules;
use App\Helpers\HelperClass;
use App\Http\Controllers\Controller;
use App\Managers\Ap\ApLookupManager;
use App\Managers\Ar\ArLookupManager;
use App\Managers\Ar\ArManager;
use App\Managers\FlashMessageManager;
use App\Managers\LookupManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class InvoiceBillListingController extends Controller
{
    protected $glCoaParam;
    protected $lookupManager;
    protected $flashMessageManager;

    /** @var ApLookupManager */
    private $arLookupManager;

    /** @var ArManager */
    private $arManager;

    protected $invoice;
    private $currency;
    private $attachment;

    public function __construct(LookupManager $lookupManager, ArLookupManager $arLookupManager, FlashMessageManager $flashMessageManager, ArContract $arManager)
    {
        $this->lookupManager = $lookupManager;
        $this->glCoaParam = new GlCoaParams();
        $this->flashMessageManager = $flashMessageManager;
        $this->arLookupManager = $arLookupManager;
        $this->arManager = $arManager;
        $this->invoice = new FasApInvoice();
        $this->currency = new LCurrency();
        $this->attachment = new FasArInvoiceDocs();
    }

    public function index($filter = null)
    {
        $fiscalYear = $this->lookupManager->getCurrentFinancialYear();
        $data['department'] = $this->lookupManager->getDeptCostCenter();
        $data['billSecs'] = $this->lookupManager->getBillSections(ArFunType::AR_INVOICE_BILL_ENTRY);
        $data['subsidiary_type'] = $this->lookupManager->findArPartySubLedger(LGlInteModules::ACCOUNT_RECEIVABLE);
        $data['customerCategory'] = $this->arLookupManager->getCustomerCategory();
        $data['customers'] = $this->arLookupManager->getCustomers();
        $data['invoiceStatus'] = $this->arLookupManager->getInvoiceStatus();
        $filterData = isset($filter) ? explode('#',Crypt::decryptString($filter)) : $filter;
        return view('ar.invoice-bill-listing.index', compact('data','fiscalYear','filterData'));
    }

    public function update(Request $request)
    {
        $oldFlag = $request->post('oldFlag');
        $reason = $request->post('holdUnHoldReason');
        $invoice = $request->post('invoiceId');

        if ($oldFlag == HoldFlag::HOLD) {
            $newFlag = HoldFlag::UN_HOLD;
        } else {
            $newFlag = HoldFlag::HOLD;
        }
        $status_code = sprintf("%4000s", "");
        $status_message = sprintf("%4000s", "");

        $param = [
            "p_action_type" => 'U',
            "p_invoice_id" => $invoice,
            "p_payment_hold_flag" => $newFlag,
            "p_payment_hold_reason" => $reason,
            //"p_trans_date" => '',
            "p_user_id" => auth()->id(),
            "o_status_code" => &$status_code,
            "o_status_message" => &$status_message
        ];

        DB::beginTransaction();
        try {
            DB::executeProcedure('sbcacc.fas_ap_trans$trans_ap_invoice_hold_unlhold', $param);
            DB::commit();
            return response()->json(["response_code" => $status_code, "response_msg" => $status_message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["response_code" => $status_code, "response_msg" => $e->getMessage()]);
        }
    }

    public function dataList(Request $request)
    {
        $period = $request->post('period');
        $billSection = $request->post('bill_section');
        $billReg = $request->post('bill_reg_id');
        $approvalStatus = $request->post('approval_status');

        $data = DB::select("select * from sbcacc.arGetInvoiceEntryList(:p_trans_period_id, :p_bill_sec_id, :p_bill_reg_id, :p_workflow_approval_status)",
            [ "p_trans_period_id" => $period, "p_bill_sec_id" => $billSection, "p_bill_reg_id" => $billReg,"p_workflow_approval_status" => $approvalStatus
                /*"p_gl_subsidiary_id" => $partySubLedger,"p_department_id" => $department,"p_bill_sec_id" => $billSection,"p_bill_reg_id" => $billReg,*/
            ]);
        $filteredData = Crypt::encryptString($request->post('fiscalYear') .'#'.$request->post('period') .'#'. $request->post('bill_section') .'#'. $request->post('bill_reg_id') .'#'. $request->post('approval_status'));

        return datatables()->of($data)
            ->addIndexColumn()
            ->editColumn('document_no', function ($data) {
                return $data->document_no;
            })
            ->editColumn('document_date', function ($data) {
                return HelperClass::dateConvert($data->document_date);
            })->editColumn('invoice_amount', function ($data) {
                return HelperClass::getCommaSeparatedValue($data->invoice_amount);
            })->editColumn('vat_amount', function ($data) {
                return HelperClass::getCommaSeparatedValue($data->vat_amount);
            })->editColumn('receivable_amount', function ($data) {
                return HelperClass::getCommaSeparatedValue($data->receivable_amount);
            })
            ->editColumn('document_reference', function ($data) {
                return $data->document_ref;
            })
            ->editColumn('approval_status', function ($data) {
                return $data->approval_status;
            })
            ->editColumn('action', function ($data) use ($filteredData) {
                return "<a style='text-decoration:underline' href='" . route('ar-invoice-bill-listing.view', ['id' => $data->invoice_id,'filter'=>$filteredData]) . "' class='' data-target='' ><i class='bx bx-show'></i></a>";
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function view($id, $filter=null)
    {
        $user_id = auth()->id();
        $inserted_data = DB::selectOne("select * from sbcacc.arGetInvoiceView(:p_invoice_id)", ["p_invoice_id" => $id]);
        if (isset ($inserted_data)){
            $inserted_data->invoice_line = DB::select("select * from sbcacc.arGetInvoiceTransView(:p_invoice_id)", ["p_invoice_id" => $id]);
            $inserted_data->invoice_file = DB::select("select * from sbcacc.arGetInvoiceDocsView(:p_invoice_id)", ["p_invoice_id" => $id]);
        }


        $fiscalYear = $this->lookupManager->getCurrentFinancialYear();
        $department = $this->lookupManager->getLCostCenter();
        $billSecs = $this->lookupManager->getBillSections(ArFunType::AR_INVOICE_BILL_ENTRY);

        $vendorCategory = $this->arLookupManager->getCustomerCategory();
        $data['subsidiary_type'] = $this->lookupManager->findArPartySubLedger(LGlInteModules::ACCOUNT_RECEIVABLE);
        $data['invoice_type'] = $this->arLookupManager->getTransactionType();
        $data['currency'] = $this->currency->get();
        $coaParams = $this->lookupManager->getSpecifiedGlCoaParams([\App\Enums\Common\GlCoaParams::ASSET, \App\Enums\Common\GlCoaParams::EXPENSE]);
        $receiptTerms = $this->lookupManager->getArPaymentTerms();
        $receiptMethods = $this->lookupManager->getArPaymentMethods();
        $transactionType = $this->arLookupManager->getTransactionType();
        return view('ar.invoice-bill-listing.view', compact('transactionType','fiscalYear', 'receiptTerms', 'receiptMethods', 'department', 'billSecs', 'data', 'coaParams', 'vendorCategory', 'inserted_data','filter'));
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

    public function updateInvoiceEntry(Request $request)
    {
        $status_code = sprintf("%4000d","");
        $status_message = sprintf("%4000s","");

        $params = [
            'p_invoice_id' => $request->post('invoiceId'),
            'p_trans_period_id' => $request->post('period'),
            'p_trans_date' => HelperClass::dateFormatForDB($request->post('postingDate')),
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
            DB::executeProcedure('sbcacc.fas_ar_trans$trans_ar_invoice_ref_update',$params);
            DB::commit();
        }catch (\Exception $e){
            return ['response_code'=>99, 'response_message'=>$e->getMessage()];
            DB::rollBack();
        }
        return ['response_code'=>$status_code, 'response_message'=>$status_message];
    }
}
