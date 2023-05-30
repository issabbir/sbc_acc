<?php


namespace App\Http\Controllers\Gl;

use App\Contracts\LookupContract;
use App\Entities\Budget\BudgetHeadLines;
use App\Entities\Gl\GlCoa;
use App\Entities\Gl\GlCoaOfficeMapAcc;
use App\Entities\Gl\GlTransDetail;
use App\Entities\Pmis\Office;
use App\Enums\Offices;
use App\Enums\ProActionType;
use App\Enums\YesNoFlag;
use App\Helpers\HelperClass;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use App\Managers\LookupManager;
use App\Traits\Security\HasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoaController extends Controller
{
    use HasPermission;

    /** @var LookupManager */
    private $lookupManager;
    private $office;

    public function __construct(LookupContract $lookupManager)
    {
        $this->lookupManager = $lookupManager;
        $this->office = new Office();
    }

    public function index()
    {
        //$gl_chart_list = DB::select("select * from SBCACC.getCoaTreeChart()");
        $gl_chart_list = DB::select("select * from SBCACC.glGetCoaTreeChart()");
        $offices = $this->office->where('active_yn','Y')->get();
        return view('gl.chart-of-account.index', [
            'gl_chart_list' => $gl_chart_list,
            'offices'   => $offices
        ]);
    }

    public function coaSetup()
    {
        $gl_chart_list = DB::select("select * from SBCACC.glGetCoaTreeChart() order by gl_acc_id");

        return view('gl.chart-of-account.setup', [
            'accTypeList' => $this->lookupManager->findGlCoaParams(),
            'lCurList' => $this->lookupManager->findLCurrency(),
            'date' => $this->lookupManager->findCurDate(),
            'dptCostCenterList' => $this->lookupManager->getDeptCostCenter(),
            'gl_chart_list' => $gl_chart_list
        ]);
    }

    public function edit(Request $request, $id)
    {
        $coaInfo = GlCoa::with(['acc_type', 'l_curr', 'budget_head', 'coa_parent_info'])->where('gl_acc_id', $id)->first();

        return view('gl.chart-of-account.setup', [
            'accTypeList' => $this->lookupManager->findGlCoaParams(),
            'lCurList' => $this->lookupManager->findLCurrency(),
            'date' => $this->lookupManager->findCurDate(),
            'coaInfo' => $coaInfo,
            'dptCostCenterList' => $this->lookupManager->getDeptCostCenter(),
        ]);
    }

    public function view(Request $request, $id)
    {
        $coaInfo = GlCoa::with(['budget_head', 'coa_parent_info','cost_center_dep'])->where('gl_acc_id', $id)->first();
        return view('gl.chart-of-account.view', [
            'accTypeList' => $this->lookupManager->findGlCoaParams(),
            'lCurList' => $this->lookupManager->findLCurrency(),
            'date' => $this->lookupManager->findCurDate(),
            'coaInfo' => $coaInfo
        ]);
    }

    public function store(Request $request)
    {
        $response = $this->coa_entry_api_ins($request);

        $message = $response['o_status_message'];

        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', $message)->withInput();
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('coa.coa-setup-index');
    }

    public function update(Request $request, $id)
    {
        $response = $this->coa_entry_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message)->withInput();
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('coa.coa-setup-index');
    }

    private function coa_entry_api_ins(Request $request)
    {
        DB::beginTransaction();
        $postData = $request->post();
        $opening_date = isset($postData['opening_date']) ? HelperClass::dateFormatForDB($postData['opening_date']) : '';
        $acc_posting = isset($postData['acc_posting']) && ($postData['acc_posting'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO;
        $budget_head_control = isset($postData['budget_head_control']) && ($postData['budget_head_control'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO;
        $budget_head_line_id = isset($postData['budget_head_id']) ? $postData['budget_head_id'] : '';
        $allow_dept_cost_center_yn = isset($postData['allow_dept_cost_center_cot']) && ($postData['allow_dept_cost_center_cot'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO;
        $dept_cost_center_id = isset($postData['dept_cost_center_id']) ? $postData['dept_cost_center_id'] : '';

        try {
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'p_action_type' => ProActionType::INSERT,
                'p_gl_acc_id' => NULL,
                'p_gl_acc_name' => $postData['acc_name'],
                'p_gl_acc_code' => isset ($postData['acc_code_legacy']) ? $postData['acc_code_legacy'] : NULL,
                'p_gl_type_id' => $postData['acc_type'],
                'p_gl_parent_id' => $postData['parent_acc_code'],
                'p_currency_code' => $postData['currency'],
                'p_postable_yn' => $acc_posting,
                'p_cost_center_dept_control_yn' => $allow_dept_cost_center_yn,
                'p_cost_center_dept_id' => $dept_cost_center_id,
                'p_budget_control_yn' => $budget_head_control,
                'p_budget_head_line_id' => $budget_head_line_id,
                'p_opening_date' => $opening_date,
                'p_inactive_yn' => YesNoFlag::NO,
                'p_inactive_date' => NULL,
                'p_office_id'  => Offices::HEAD_OFFICE, //head office
                'p_user_id' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('sbcacc.GL_CREATE_UPDATE_COA', $params);
            if ($status_code != 1){
                DB::rollBack();
            }else{
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

    private function coa_entry_api_upd($request, $id)
    {
        $postData = $request->post();
        $opening_date = isset($postData['opening_date']) ? HelperClass::dateFormatForDB($postData['opening_date']) : '';
        $acc_posting = isset($postData['acc_posting']) && ($postData['acc_posting'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO;
        $budget_head_control = isset($postData['budget_head_control']) && ($postData['budget_head_control'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO;
        $budget_head_line_id = isset($postData['budget_head_id']) ? $postData['budget_head_id'] : '';
        $allow_dept_cost_center_yn = isset($postData['allow_dept_cost_center_cot']) && ($postData['allow_dept_cost_center_cot'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO;
        $dept_cost_center_id = isset($postData['dept_cost_center_id']) ? $postData['dept_cost_center_id'] : '';
        $acc_inactive_date = isset($postData['acc_inactive_date']) ? HelperClass::dateFormatForDB($postData['acc_inactive_date']) : '';
        $acc_inactive = isset($postData['acc_inactive']) && ($postData['acc_inactive'] == YesNoFlag::YES) ? YesNoFlag::YES : YesNoFlag::NO;
        DB::beginTransaction();
        try {
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'p_action_type' => ProActionType::UPDATE,
                'p_gl_acc_id' => $id,
                'p_gl_acc_name' => $postData['acc_name'],
                'p_gl_acc_code' => isset ($postData['acc_code_legacy']) ? $postData['acc_code_legacy'] : NULL,
                'p_gl_type_id' => $postData['acc_type'],
                'p_gl_parent_id' => $postData['parent_acc_code'],
                'p_currency_code' => $postData['currency'],
                'p_postable_yn' => $acc_posting,
                'p_cost_center_dept_control_yn' => $allow_dept_cost_center_yn,
                'p_cost_center_dept_id' => $dept_cost_center_id,
                'p_budget_control_yn' => $budget_head_control,
                'p_budget_head_line_id' => $budget_head_line_id,
                'p_opening_date' => $opening_date,
                'p_inactive_yn' => $acc_inactive,
                'p_inactive_date' => $acc_inactive_date,
                'p_office_id'  => Offices::HEAD_OFFICE, //head office
                'p_user_id' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message
            ];

            DB::executeProcedure('sbcacc.GL_CREATE_UPDATE_COA', $params);
            if ($status_code != 1){
                DB::rollBack();
            }else{
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }
        return $params;
    }

    public function accTypeWiseCoa(Request $request)
    {
        $terms = $request->post();
        $queryResult = [];

        if (empty($terms['acc_type_id'])) {
            $queryResult = [];
        } else {
           $queryResult = DB::select('select * from sbcacc.glGetParentAccounts(:p_gl_type_id)', ['p_gl_type_id' => $terms['acc_type_id']]);
        }

        $html = view('gl.chart-of-account.coa_tree',['gl_chart_list'=>$queryResult])->render();
        return response()->json($html);
    }

    public function budgetHeadWiseList(Request $request)
    {

        $queryResult = DB::select('select sbcacc.fas_gl_config.get_budget_gl_head_list from dual');


        return datatables()->of($queryResult)
            ->addColumn('select', function ($query) {
                if ($query->postable_yn == YesNoFlag::NO) {
                    return 'N/A';
                } else {
                    return '<button class="btn btn-primary btn-sm budget-heads-data"  id="' . $query->budget_head_id . '">Select</button>';
                }
            })

            ->rawColumns(['select'])
            ->addIndexColumn()
            ->make(true);
    }


    public function searchAccNamesCodes(Request $request)
    {
        $terms = $request->post();
        $searchTerm = ($terms['acc_name_code']);
        $office_id = $request->post('office_id');
        $queryResult = [];

        if (empty($terms['acc_name_code'])) {
            $queryResult = [];
        } else {
            $queryResult = GlCoa::with(['acc_type'])
                ->where(function ($query) use ($searchTerm) {
                    $query->where(DB::raw('LOWER(gl_acc_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                        ->orWhere('gl_acc_code', 'like', '%' . trim($searchTerm) . '%');
                })
                //->where('office_id',$office_id)
                ->orderBy('gl_acc_id', 'asc')
                ->get();
        }

        return datatables()->of($queryResult)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('coa.coa-setup-view', [$query->gl_acc_id]) . '"><i class="bx bx-show cursor-pointer"></i></a>|<a href="' . route('coa.coa-setup-edit', [$query->gl_acc_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })

            ->addIndexColumn()
            ->make(true);
    }

}
