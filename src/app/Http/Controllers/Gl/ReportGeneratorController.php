<?php
/**
 *Created by PhpStorm
 *Created at ৩/৬/২১ ১২:১৭ PM
 */

namespace App\Http\Controllers\Gl;


use App\Entities\Common\LGlIntegrationFunctions;
use App\Entities\Gl\CalendarDetail;
use App\Entities\Gl\LPeriodClosingEvent;
use App\Entities\Pmis\Office;
use App\Entities\Security\Report;
use App\Enums\Common\LGlInteModules;
use App\Enums\ModuleInfo;
use App\Http\Controllers\Controller;
use App\Managers\LookupManager;
use App\Traits\Security\HasPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportGeneratorController extends Controller
{
    use HasPermission;

    protected $lookupManager;
    protected $calenderDetail;
    protected $functions;
    protected $closingEvent;

    public function __construct(LookupManager $lookupManager)
    {
        $this->lookupManager = $lookupManager;
        $this->calenderDetail = new CalendarDetail();
        $this->functions = new LGlIntegrationFunctions();
        $this->closingEvent = new LPeriodClosingEvent();
    }

    public function index()
    {
        $module = ModuleInfo::GL_MODULE_ID;
        $reportObj = new Report();

        if (auth()->user()->hasGrantAll()) {
            $reports = $reportObj->where(['module_id'=>$module, 'active_yn'=>'Y'])->orderBy('report_name', 'ASC')->get();
        } else {
            $roles = auth()->user()->getRoles();
            $reports = array();
            foreach ($roles as $role) {
                if (count($role->reports)) {
                    //$rpts = $role->reports->where(['module_id'=>$module, 'active_yn'=>'Y']);
                    $rpts = $role->reports->where('module_id',$module)->where('active_yn','Y');
                    foreach ($rpts as $report) {
                        $reports[$report->report_id] = $report;
                    }
                }
            }
            //Sorted the list according to name.
            $array_column = array_column($reports, 'report_name');
            array_multisort($array_column, SORT_ASC, $reports);
        }

        return view('gl.reportgenerator.index', compact('reports'));
    }

    public function reportParams($id)
    {
        $report = Report::find($id);
        //$modules = $this->lookupManager->getGLModuleList();
        $currentFiscalYear = $this->lookupManager->getACurrentFinancialYear();

        $postingPeriod = $this->lookupManager->findPostingPeriod(isset($currentFiscalYear) ? $currentFiscalYear->fiscal_year_id : null );

        /*$oldPostingPeriods = $this->calenderDetail->select('posting_period_display_name', 'calendar_detail_id')->where('posting_period_beg_date', '<', function ($q) {
            $q->select('posting_period_beg_date')->from('fas_calendar_detail')->where('posting_period_status', '=', 'O');
        })->orderBy('posting_period_beg_date', 'DESC')->get();*/
        /*$billSecs = $this->lookupManager->findLBillSec();
        $funcType = $this->functions->where("module_id", "=", LGlInteModules::FIN_ACC_GENE_LEDGER)->where("function_parent_id", "!=", null)->orderBy("function_id", "ASC")->get();
        $department = $this->lookupManager->getDeptCostCenter();
        $closingEvents = $this->closingEvent->get();*/
        /*$users = DB::select("select distinct su.user_name, su.user_id, emp.emp_name
                                        from sbc_dev.workflow_template wt
                                        join app_security.sec_role secr on wt.step_role_key = secr.role_key
                                        join app_security.sec_user_roles secur on secur.role_id = secr.role_id
                                        join app_security.sec_users su on su.user_id = secur.user_id
                                        join pmis.employee emp on emp.emp_id = su.emp_id
                                        order by emp.emp_name asc");*/
        $fiscalYear = DB::select("SELECT * FROM sbcacc.rptCtrlGetFinancialYears()");
        //$offices = Office::where('active_yn','Y')->orderBy('office_name')->get();
        $offices = Office::where('active_yn','Y')->orderBy( 'order_no', 'asc')->get();
        $costCenters = $this->lookupManager->getLCostCenter();

        //Write your dependency elements query here
        return view('gl.reportgenerator.report-params', compact('fiscalYear','costCenters', 'report', 'postingPeriod','offices'))->render();
    }
}
