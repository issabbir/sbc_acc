<?php


namespace App\Http\Controllers\Gl;


use App\Contracts\Common\CommonContract;
use App\Entities\Gl\CalendarDetail;
use App\Entities\Gl\CalendarMaster;
use App\Entities\Gl\CalendarProperties;
use App\Entities\Gl\LFiscalPeriod;
use App\Entities\Gl\LPeriodType;
use App\Enums\Gl\CalendarStatus;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Barryvdh\Debugbar\Facade as DebugBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    protected $lperiod;
    protected $lfiscal;
    protected $flashMessage;
    protected $calendarMaster;
    protected $calendarProperty;
    protected $calendarDetail;

    public function __construct()
    {
        $this->lperiod = new LPeriodType();
        $this->lfiscal = new LFiscalPeriod();
        $this->flashMessage = new FlashMessageManager();
        $this->calendarMaster = new CalendarMaster();
        $this->calendarProperty = new CalendarProperties();
        $this->calendarDetail = new CalendarDetail();
    }

    public function index()
    {
        /*$gl_chart_list = DB::select("select * from SBCACC.getCoaTreeChart() order by gl_acc_id");
        dd($gl_chart_list);*/
        return view('gl.calendar.index');
    }

    public function setup()
    {
        $data['periodType'] = $this->lperiod->get();
        $data['fiscalPeriod'] = $this->lfiscal->get();
        $data['defaultProperty'] = $this->calendarProperty->where('properties_id', '=', '1')->first();
        //DebugBar::addMessage($data);
        return view('gl.calendar.setup', compact('data'));
    }

    public function store(Request $request, $id = null)
    {
        $request->validate([
            'yearStart' => 'required|',
            //'yearEnd' => 'required|gt:yearStart'
        ]);

        $calenderId = isset($id) ? $id : null;
        $actionType = isset($id) ? 'U' : 'I';
        $status_msg = sprintf("%4000s", "");
        $status_code = sprintf("%4000d", "");



        try {
            $params = [
                "p_action_type" => $actionType,
                "p_calendar_id" => &$calenderId,
                "p_fiscal_period_id" => (int)$request->post('fiscalYearPeriod'),
                "p_fiscal_beg_year" => (int)$request->post('yearStart'),
                "p_fiscal_end_year" => (int)$request->post('yearEndData'),
                "p_posting_period_code" => $request->post('postingCalendarPeriod'),
                "p_calendar_status" => 'I',
                "p_user_id" => Auth::user()->user_id,
                "o_status_code" => &$status_code,
                "o_status_message" => &$status_msg,
            ];//dd($params);

            DB::executeProcedure('sbcacc.FAS_GL_CONFIG_CREATE_FISCAL_CALENDAR', $params);
            //dd($params);
        } catch (\Exception $e) {
            //echo '<pre>'; print_r($params); echo '</pre>'; die();
            //dd($e.'--'.$params);
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
        $response = $this->flashMessage->getMessage($params);
        if ($status_code == '99') {
            return redirect()->back()->with($response['class'], $response['message'])->withInput();
        } else {
            return redirect('general-ledger/calendar')->with($response['class'], $response['message']);
        }
    }

    public function defaultSetup()
    {
        $data['periodType'] = $this->lperiod->get();
        $data['fiscalPeriod'] = $this->lfiscal->get();
        $data['defaultProperty'] = $this->calendarProperty->where('properties_id', '=', '1')->first();
        return view('gl.calendar.default_setup', compact('data'));
    }

    public function defaultStore(Request $request, $id = null)
    {
        $propertiesId = isset($id) ? $id : '';
        $actionType = isset($id) ? 'U' : 'I';
        $status_msg = sprintf("%4000s", "");
        $status_code = sprintf("%4000d", "");

        $params = [
            /*"p_action_type" => $actionType,
            "p_properties_id" => [
                'value' => &$propertiesId,
                'type' => \PDO::PARAM_INPUT_OUTPUT
            ],*/
            "p_fiscal_period_id" => $request->post('fiscalYearPeriod'),
            "p_posting_period_code" => $request->post('postingCalendarPeriod'),
            /*"p_posting_period_display_name" => '',*/
            "p_max_fiscal_year_open_allow" => $request->post('maxYear'),
            "p_max_posting_prd_open_allow" => $request->post('maxPeriod'),
            "p_user_id" => Auth::user()->user_id,
            "o_status_code" => &$status_code,
            "o_status_message" => &$status_msg,
        ];
        try {//dd($params);
            DB::executeProcedure('sbcacc.FAS_GL_CONFIG_CHANGE_CALENDAR_PROPERTIES', $params);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        $response = $this->flashMessage->getMessage($params);
        return redirect()->back()->with($response['class'], $response['message'])->withInput();
    }

    public function detailView(Request $request, $id)
    {
        $calMst = CalendarMaster::with(['fiscal_period'])->where('calendar_id', $id)->first();
        //$calDtl = CalendarDetail::where('calendar_id', $id)->get();

        $calDtl = CalendarDetail::addSelect(['previous_month_status' => function ($query) {
            $query->select('posting_period_status')
                ->from('sbcacc.fas_calendar_detail as cd')
                ->whereRaw('cd.posting_period_beg_date = DATEADD(MONTH,-1, sbcacc.fas_calendar_detail.posting_period_beg_date)');
        }])->where('calendar_id', $id)->orderBy('calendar_detail_id', 'asc')->get();

        //dd($calMst);

        return view('gl.calendar.detail',
            [
                'calMst' => $calMst,
                'calDtl' => $calDtl,
            ]);
    }

    public function statusList($detailId)
    {
        try {
            //$data = DB::select("select * from SBCACC.apGetInvoiceEntryList(:p_trans_period_id,:p_bill_sec_id,:p_bill_reg_id,:p_workflow_approval_status)",["p_trans_period_id"=>$period,"p_bill_sec_id"=>$billSection,"p_bill_reg_id"=>$billReg,"p_workflow_approval_status"=>$approvalStatus]);
            $list = DB::select("select * from SBCACC.GET_NEXT_PERIOD_STATUS (:p_calendar_detail_id)", ['p_calendar_detail_id' => $detailId]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'data' => null, 'msg' => $e->getMessage()]);
        }
        if (!is_null($list)) {
            return response()->json(['status' => 200, 'data' => $list, 'msg' => 'Data found']);
        } else {
            return response()->json(['status' => 204, 'data' => null, 'msg' => 'Data not found']);
        }
    }

    public function detailStore(Request $request)
    {
        $cal_id = $request->get('cal_id');
        $response = $this->gl_cal_dtl_api_upd($request);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', $message)->withInput();
        }

        $calId = isset($cal_id) ? $cal_id : 0;

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);
        return redirect()->route('calendar.detail-view', $calId);
    }

    private function gl_cal_dtl_api_upd(Request $request)
    {
        $postData = $request->post();
        $params = [];
        DB::beginTransaction();

        try {
            $status_code = sprintf("%4000d", "");
            $status_message = sprintf("%4000s", "");
            $params = [
                'p_calendar_detail_id' => $postData['selected_cal_dtl_id'],
                'p_posting_period_status' => $postData['pos_period_status'],
                'p_user_id' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message
            ];
            DB::executeProcedure('sbcacc.CHANGE_POSTING_PERIOD_STATUS', $params);//dd($params);

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

    public function calendarList()
    {
        $calenders = $this->calendarMaster->with('fiscal_period', 'period_type')
                    ->where('deleted_yn', '=', 'N')
                    ->orderBy('fiscal_beg_year', 'desc')
                    ->get();
        //dd($calenders->all());
        return datatables()->of($calenders)
            ->addIndexColumn()
            ->editColumn('fiscal_period', function ($data) {
                return $data->fiscal_period->fiscal_period_nm;
            })
            ->editColumn('fiscal_year', function ($data) {
                if($data->fiscal_period_id == 2){
                    return $data->fiscal_beg_year;
                }else{
                    return $data->fiscal_beg_year.'-'.$data->fiscal_end_year;
                }

                //return $data->fiscal_year;
            })
            ->editColumn('posting_period', function ($data) {
                return $data->period_type->period_type_name;
            })
            ->editColumn('status', function ($data) {
                switch ($data->calendar_status) {
                    case CalendarStatus::INACTIVE:
                        return "Inactive";
                    case CalendarStatus::OPENED:
                        return "Opened";
                    case CalendarStatus::CLOSED:
                        return "Closed";
                    case CalendarStatus::OPENED_SPECIAL:
                        return "Open";
                }
            })
            ->editColumn('action', function ($data) {
                //return '<a href="'.route('calendar.detail-view',['id'=>$data->calendar_id]).'"><i class="bx bx-show"></i> </a>|<a href="#"><i class="bx bx-wrench"></i></a>';
                return '<a style="text-decoration:underline" href="' . route('calendar.detail-view', ['id' => $data->calendar_id]) . '">settings</a>';
            })
            ->make(true);
    }

}
