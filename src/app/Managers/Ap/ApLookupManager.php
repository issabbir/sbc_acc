<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 09/12/2021
 * Time: 2:27 PM
 */

namespace App\Managers\Ap;


use App\Contracts\Ap\ApLookupContract;
use App\Entities\Ap\FasApVendors;
use App\Entities\Ar\LArCustomerCategory;
use App\Entities\Ar\LArTransactionType;
use App\Entities\Common\LApInvoiceStatus;
use App\Entities\Common\LApInvoiceType;
use App\Entities\Common\LApVendorCategory;
use App\Entities\Common\LApVendorType;
use App\Entities\Common\LGlIntegrationFunctions;
use App\Enums\ApprovalStatus;
use App\Enums\Common\LGlInteFun;
use App\Enums\YesNoFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApLookupManager implements ApLookupContract
{
    public function getBankAcc()
    {
        return DB::select("select sbcacc.fas_cm_config.get_bank_account as bank_acc from dual");
    }

    public function getApBankAcc()
    {
        return  DB::select("select * from sbcacc.apGetBankAccount()");
    }

    public function getVendorTypes()
    {
        return LApVendorType::where('active_yn', '=', 'Y')->get();
    }

    public function getVendorCategory()
    {
        return LApVendorCategory::where('active_yn', 'Y')->get();
    }


    public function getVendorCategoryOnType($id)
    {
        return LApVendorCategory::where('active_yn', 'Y')->where('vendor_type_id', $id)->get();
    }

    public function findInvoiceType()
    {
        return LApInvoiceType::where('active_yn', '=', 'Y')->get();
    }

    public function getVendors($searchQ = null)
    {
        return FasApVendors::where('inactive_yn', YesNoFlag::NO)->where('workflow_approval_status', ApprovalStatus::APPROVED)->get(); // Add Where Condition- Pavel-15-02-22
    }

    public function getInvoiceStatus()
    {
        return LApInvoiceStatus::all();
    }

    public function getBudgetBookingHeadList($calendar, $department, $nameCode)
    {
        return DB::select("select sbcacc.fas_ap_trans.get_budget_booking_head_list(:p_fiscal_year_id, :p_cost_center_dept_id, :p_head_name_code) from dual",
            ["p_fiscal_year_id" => $calendar, "p_cost_center_dept_id" => $department, "p_head_name_code" => $nameCode]);
    }

    public function getBudgetBookingTransList($calendar, $department)
    {
        return DB::select('select * from sbcacc.apGetBudgetBookingTransList(:p_fiscal_year_id, :p_cost_center_dept_id)',["p_fiscal_year_id" => $calendar,"p_cost_center_dept_id" => $department]);
        //return DB::select("select sbcacc.fas_ap_trans.get_budget_booking_trans_list(:p_fiscal_year_id, :p_cost_center_dept_id) from dual",["p_fiscal_year_id" => $calendar, "p_cost_center_dept_id" => $department]);
    }
    public function getBudgetHeadDetailInfo($calendar,$department,$budget_id)
    {
        //dd($calendar,$department,$budget_id);
        return DB::selectOne("select * from sbcacc.apGetBudgetBookingHeadInfo(:p_fiscal_year_id, :p_cost_center_dept_id, :p_budget_head_id)",
            ["p_fiscal_year_id" => $calendar,"p_cost_center_dept_id" => $department,"p_budget_head_id" => $budget_id]);
    }

    public function getPartySubLedger($functionId = null, $intBillPayYn = null)
    {   //dd(DB::select('select * from sbcacc.apGetSubsidiaryLedger(:p_function_id, :p_internal_bill_pmt_yn)',["p_function_id" => $functionId,"p_internal_bill_pmt_yn" => $intBillPayYn]));
        return DB::select('select * from sbcacc.apGetSubsidiaryLedger(:p_function_id, :p_internal_bill_pmt_yn)',["p_function_id" => $functionId,"p_internal_bill_pmt_yn" => $intBillPayYn ?? 'DEFAULT']);

        //return DB::select('select sbcacc.fas_ap_config$get_gl_subsidiary_ledger(:p_function_id, :p_internal_bill_pmt_yn)',["p_function_id" => $functionId,"p_internal_bill_pmt_yn" => $intBillPayYn]);
    }

}
