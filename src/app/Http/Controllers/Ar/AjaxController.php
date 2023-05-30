<?php
/**
 *Created by PhpStorm
 *Created at ৯/৯/২১ ৩:২৯ PM
 */

namespace App\Http\Controllers\Ar;


use App\Contracts\Ap\ApLookupContract;
use App\Entities\Ap\FasApInvoiceParams;
use App\Entities\Ap\FasApVendors;
use App\Entities\Ar\FasArCustomers;
use App\Entities\Ar\FasArInvoiceParams;
use App\Entities\Ar\VWAgencyInfo;
use App\Entities\Cims\PurchaseRcvMst;
use App\Entities\Cm\CmBankDistrict;
use App\Entities\Cm\CmBankInfo;
use App\Entities\Common\LBillRegister;
use App\Entities\Gl\GlCoa;
use App\Entities\Gl\GlCoaOfficeMapAcc;
use App\Entities\Gl\GlCoaParams;
use App\Entities\Security\User;
use App\Entities\WorkFlowMapping;
use App\Enums\ApprovalStatus;
use App\Enums\WorkFlowMaster;
use App\Enums\YesNoFlag;
use App\Helpers\HelperClass;
use App\Http\Controllers\Controller;
use App\Managers\Ap\ApLookupManager;
use App\Managers\FlashMessageManager;
use App\Managers\LookupManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class AjaxController extends Controller
{
    protected $lookupManager;
    protected $purchaseRcvMst;
    protected $apLookUpManager;
    protected $glCoa;

    public function __construct(LookupManager $lookupManager, FlashMessageManager $flashMessageManager, ApLookupManager $apLookUpManager)
    {
        $this->lookupManager = $lookupManager;
        $this->purchaseRcvMst = new PurchaseRcvMst();
        $this->apLookUpManager = $apLookUpManager;
        //$this->glCoa = new GlCoa();
        $this->glCoa = new GlCoaOfficeMapAcc();
    }

    /*public function contraLedgers(Request $request)
    {
        $id = $request->post('selectedLedger');
        $preSetContra = $request->post('preSelectedContra');

        $coaParams = $this->lookupManager->getContraPartySubLedger('3', $id);
        $option = '<option value="">Select Party Sub Ledger</option>';

        foreach ($coaParams as $param) {
            $option .= "<option value='$param->gl_subsidiary_id'";
            if ($param->gl_subsidiary_id == $preSetContra) {
                $option .= 'Selected';
            }
            $option .= ">" . $param->gl_subsidiary_name . "</option>";
        }

        return response()->json($option);
    }*/

    public function getCustomerDetails(Request $request)
    {
        $vendorId = $request->post('customerId');
        $customerCategory = $request->post('customerCategory');

        $vendor = FasArCustomers::select('customer_id', 'customer_name', 'customer_category_id')
            ->where(
                [
                    ['customer_id', '=', $vendorId],  //Add Where Condition- Pavel-14-02-22
                    ['inactive_yn', '=', YesNoFlag::NO],
                    ['workflow_approval_status', '=', ApprovalStatus::APPROVED],
                ]
            )//->where('customer_category_id', '=', DB::raw("ISNULL('" . $customerCategory . "',customer_category_id)"))
            ->with('customer_category')->first();//dd($vendor);
        return response()->json($vendor);
    }

    public function getTransactionTypesOnSubsidiary(Request $request)
    {
        $subsidiaryId = $request->get('subsidiaryId');
        $transactionTypes = FasArInvoiceParams::where('gl_subsidiary_id', '=', $subsidiaryId)->with('transaction_type')->get();
        $html = "<option value=''>&lt;Select&gt;</option>";
        foreach ($transactionTypes as $type) {
            $html .= '<option value="'. $type->transaction_type->transaction_type_id . '">'. $type->transaction_type->transaction_type_name . '</option>';
        }
        /*if ($transactionTypes->transaction_type->count() > 0) {
            foreach ($transactionTypes->transaction_type as $type) {
                $html .= "<option value='" . $type->transaction_type_id . "' >" . $type->transaction_type_name . "</option>";
            }
        }*/

        return response()->json($html);
    }

    public function poList(Request $request)
    {
        $vendorId = $request->post('vendor');
        $orderNo = $request->post('purchaseOrder');
        $orderDate = HelperClass::dateFormatForDB($request->post('purchaseDate'));
        $invoiceNo = $request->post('invoiceNo');
        $invoiceDate = HelperClass::dateFormatForDB($request->post('invoiceDate'));
        $data = [];
        $response = DB::select("select * from sbcacc.apGetGoodsReceivedInfo(:p_vendor_id,:p_purchase_order_no,:p_purchase_order_date,:p_invoice_no,:p_invoice_date)",
            ["p_vendor_id" => $vendorId,
                "p_purchase_order_no" => $orderNo,
                "p_purchase_order_date" => HelperClass::dateFormatForDB($orderDate),
                "p_invoice_no" => $invoiceNo,
                "p_invoice_date" => HelperClass::dateFormatForDB($invoiceDate)]);
        $data = isset($response) ? $response : [];


        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('po_date', function ($data) {
                return HelperClass::dateConvert($data->po_date);
            })
            ->editColumn('invoice_date', function ($data) {
                return HelperClass::dateConvert($data->invoice_date);
            })
            ->editColumn('action', function ($data) {
                return "<button class='poSelect btn btn-sm btn-primary'  data-po='" . $data->purchase_rcv_mst_id . "#" . $data->po_number . "#" . HelperClass::dateConvert($data->po_date) . "#" . $data->invoice_no . "#" . HelperClass::dateConvert($data->invoice_date) . "#" . $data->invoice_amount . "'  >Select</button>";
            })
            ->make(true);
    }

    public function coaListOnSearch(Request $request)
    {//dd($request->all());
        $glType = $request->post('glType');
        //$search = $request->post('searchText');
        $costCenter = $request->post('costCenter'); //Add Part :pavel-31-01-22
        $accNameCode = $request->post('searchText');

        /* $sql = GlCoa::select("*")->where('gl_type_id', '=', $glType);
         if (isset($search)) {
             $sql->where(function ($q) use ($search) {
                 $q->Where(DB::raw('upper(gl_acc_name)'), 'like', '%' . strtoupper($search) . '%')
                     ->orWhere('gl_acc_id', 'like', '%' . $search . '%');
             });
         }
         $glCoaInfo = $sql->where('postable_yn', '=', 'Y')->get();*/

        $glCoaInfo = $this->glCoa->with(['cost_center'])->where(
            [
                ['gl_type_id', '=', $glType],
                ['postable_yn', '=', YesNoFlag::YES],
                ['active_yn', '=', YesNoFlag::YES],   //Add Condition- Pavel-14-02-22
            ]
        )//->where('office_id','=',111)
        ->where(function ($query) use ($costCenter) {       //Add costCenterDpt Part :pavel-31-01-22
            $query->where('cost_center_id','=', $costCenter)
                ->orWhereNull('cost_center_id');
        })->where(function ($query) use ($accNameCode) {
            $query->where(DB::raw('upper(sbcacc.gl_coa_office_map_acc.gl_acc_name)'), 'like', strtoupper('%' . trim($accNameCode) . '%'))
                ->orWhere(DB::raw('CAST(sbcacc.gl_coa_office_map_acc.gl_acc_id AS VARCHAR(MAX))'), '=', trim($accNameCode));
                //->orWhere('fas_gl_coa.gl_acc_code', '=', trim($accNameCode))
                //->orWhere(DB::raw('CAST(sbcacc.gl_coa_office_map_acc.old_coa_code AS VARCHAR(MAX))'), '=', trim($accNameCode))     //Add two condition Part :pavel-14-03-2022
                //->orWhere(DB::raw('CAST(sbcacc.gl_coa_office_map_acc.old_sub_code AS VARCHAR(MAX))'), '=', trim($accNameCode));
        })
            ->orderBy('gl_acc_id','asc')->get();


        return datatables()->of($glCoaInfo)
            ->addIndexColumn()
            //Add costCenterDpt Part :pavel-31-01-22
            ->editColumn('dept_name', function ($data) {
                //$dept = (isset($data->cost_center_dep) ? $data->cost_center_dep->cost_center_dept_name : 'No Data Found');
                $dept = (isset($data->cost_center_dep) ? $data->cost_center_dep->cost_center_dept_name : '');
                return $dept;
            })
            ->editColumn('action', function ($data) {
                return "<button class=' btn btn-sm btn-primary'  onclick='getAccountDetail($data->gl_acc_id)'  >Select</button>";
            })
            ->make(true);
    }

    public function getAccountDetailsOnGlType(Request $request)
    {
        $accountId = $request->post('accId');
        $allowedGlType = $request->post('allowedGlType');

        $account = GlCoaOfficeMapAcc::where('gl_acc_id', '=', $accountId)->whereIn('gl_type_id', $allowedGlType)->first();
        if (isset($account)) {
            $bankAccountInfo = DB::selectOne("select * from sbcacc.glGetAccountInfo (:p_gl_acc_id, default )", ['p_gl_acc_id' => $accountId]);
        } else {
            $bankAccountInfo = [];
        }
        //dd($bankAccountInfo);
        return response()->json($bankAccountInfo);
    }

    public function sectionByRegisterList(Request $request, $sectionId)
    {
        $searchTerm = $request->get('term');
        return $this->lookupManager->getBillRegistersOnSection($sectionId,$searchTerm);
    }

    public function customerList(Request $request)
    {
        $customerCategory = $request->post('customerCategory');
        $customerName = $request->post('customerName');
        $customerShortName = $request->post('customerShortName');
        $forApAdditionalAccount = $request->post('forAdditionalAcc',true);   //For Account Payable Bill entry
//$cusCatagory = isset($customerCategory)?$customerCategory: 1;

        //dd($customerCategory);
        $sql = FasArCustomers::where('customer_category_id', '=', DB::raw("ISNULL('" . (int)$customerCategory . "',customer_category_id)"))->where('workflow_approval_status', '=', ApprovalStatus::APPROVED)->where('inactive_yn', '=', YesNoFlag::NO)/*->get()*/;

        if (isset($customerName)) {
            $sql->where(DB::raw('upper(customer_name)'), 'LIKE', '%' . strtoupper($customerName) . '%');
        }

        if (isset($customerShortName)) {
            $sql->where(DB::raw('upper(customer_short_name)'), 'LIKE', '%' . strtoupper($customerShortName) . '%');
        }

        $data = $sql->with(['customer_category'])
            ->orderBy('customer_id','asc')
            ->get();

        return datatables()->of($data)
            ->addIndexColumn()
            ->editColumn('name', function ($data) {
                return $data->customer_name;
            })
            ->editColumn('short_name', function ($data) {
                return $data->customer_short_name;
            })
            /*->editColumn('category', function ($data) {
                return $data->customer_category->customer_category_name;
            })*/
            ->editColumn('address', function ($data) {
                return $data->address_line1.' '. $data->address_line2;
            })
            ->editColumn('action', function ($data) use($forApAdditionalAccount) {
                return "<button class='customerSelect btn btn-sm btn-primary' data-foradd='".$forApAdditionalAccount."'  data-customer='" . $data->customer_id . "'  >Select</button>";
            })
            ->make(true);
    }

    public function getCustomerWithOutstandingBalance(Request $request)
    {
        $customerId = $request->post('customerId');
        $customer = DB::selectOne("select * from sbcacc.arGetCustomerAccountInfo(:p_customer_id)", ['p_customer_id' => $customerId]);
        $ledgerInfo = DB::select("select * from sbcacc.arGetCustomerLedgerInfo(:p_customer_id)", ['p_customer_id' => $customerId]);
        $tbody = "";

        foreach ($ledgerInfo as $info)
        {
            $tbody .= "<tr><td>" . $info->party_account_id . "</td><td>" . $info->gl_subsidiary_name . "</td><td class='text-right-align'>".HelperClass::getCommaSeparatedValue($info->opening_balance)." ".$info->opening_balance_type."</td><td class='text-right-align'>".HelperClass::getCommaSeparatedValue("$info->debit_amount")."</td><td class='text-right-align'>".HelperClass::getCommaSeparatedValue("$info->credit_amount")."</td><td class='text-right-align'>" . HelperClass::getCommaSeparatedValue($info->closing_balance) . " " . $info->closing_balance_type . "</td><td class='text-right-align'>" . HelperClass::getCommaSeparatedValue($info->authorized_balance) . " " . $info->authorized_balance_type . "</td></tr>";
        }
        return response()->json(["customer"=>$customer,"ledger"=>$tbody]);
    }

    public function invoiceReferenceList(Request $request)
    {
        $postData = $request->post();
        $customerId = isset($postData['customer_id']) ? $postData['customer_id'] : '';
        //$glSubsidiaryId = isset($postData['party_sub_ledger']) ? $postData['party_sub_ledger'] : '';
        //dd($request);
        $invRefList = [];

        if ($customerId) {
            //$invRefList = DB::select("select sbcacc.fas_ar_trans.get_customer_invoice_referance (:p_gl_subsidiary_id, :p_customer_id) from dual", ['p_gl_subsidiary_id' => $glSubsidiaryId, 'p_customer_id' => $customerId]);
            $invRefList = DB::select("select * from sbcacc.arGetReceiptRefMk (:p_customer_id)", ['p_customer_id' => $customerId]);
        }

        $html = view('ar.ajax.invoice_reference_list')->with('invRefList', $invRefList)->render();

        $jsonArray = [
            'html' => $html
        ];

        return response()->json($jsonArray);
    }

    public function shippingAgentDetail(Request $request)
    {

        $agentId = $request->get('agent_id');
        $detail = VWAgencyInfo::where('agency_id', '=', $agentId)->first();
        return response()->json($detail);
    }

    /************************** -Report methods- ******************************************/
    public function reportGlAccounts(Request $request)
    {
        return DB::select('select sbcacc.fas_report.get_gl_account_name(:p_search_value) from dual', ['p_search_value' => $request->get('term')]);
    }

    public function reportGlFiscalYears(Request $request)
    {
        return DB::select('select sbcacc.fas_report.get_financial_years() from dual');
    }

    public function reportGlPostingPeriods(Request $request)
    {
        $fromPeriods = DB::select('select sbcacc.fas_report.get_posting_periods(:p_fiscal_year_id) from dual', ['p_fiscal_year_id' => $request->get("fiscal_year_id")]);
        $fromOptions = '';
        foreach ($fromPeriods as $period) {
            $selected = "";
            /*if ($period->posting_period_status == 'O') {
                $selected = "selected";
            }*/
            $fromOptions .= '<option ' . $selected . ' data-mindate="' . HelperClass::dateConvert($period->posting_period_beg_date) . '"
                                        data-maxdate="' . HelperClass::dateConvert($period->posting_period_end_date) . '"
                                         value="' . $period->posting_period_id . '">' . $period->posting_period_name . ' </option>';
        }

        $toPeriods = DB::select('select sbcacc.fas_report.get_posting_periods(:p_fiscal_year_id) from dual', ['p_fiscal_year_id' => $request->get("fiscal_year_id")]);
        $toOptions = '';
        foreach ($toPeriods as $period) {
            $toOptions .= '<option ' . $selected . ' data-mindate="' . HelperClass::dateConvert($period->posting_period_beg_date) . '"
                                        data-maxdate="' . HelperClass::dateConvert($period->posting_period_end_date) . '"
                                         value="' . $period->posting_period_id . '">' . $period->posting_period_name . ' </option>';
        }

        return response()->json(['fromOptions' => $fromOptions, 'toOptions' => $toOptions]);
    }

    public function reportArCustomers(Request $request, $subsidiaryId)
    {
        $search = $request->get('term', null);

        $customers = DB::select('select sbcacc.fas_report.get_ar_customers(:p_gl_subsidiary_id,:p_search_value) from dual', ['p_gl_subsidiary_id' => $subsidiaryId, 'p_search_value' => $search]);

        return response()->json($customers);
    }

    public function getRegisterDetail($id)
    {
        return LBillRegister::where('bill_reg_id','=',$id)->first();
    }
}
