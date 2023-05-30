<?php
/**
 *Created by PhpStorm
 *Created at ১৫/৯/২১ ১:১২ PM
 */

namespace App\Http\Controllers\Ar;


use App\Contracts\Ap\ApLookupContract;
use App\Entities\Ap\FasApVendors;
use App\Entities\Ar\FasArCustomers;
use App\Entities\Gl\GlCoaParams;
use App\Enums\ApprovalStatus;
use App\Enums\ApprovalStatusView;
use App\Http\Controllers\Controller;
use App\Managers\Ap\ApLookupManager;
use App\Managers\Ar\ArLookupManager;
use App\Managers\FlashMessageManager;
use App\Managers\LookupManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerSearchController extends Controller
{
    protected $glCoaParam;
    protected $lookupManager;
    protected $flashMessageManager;
    private $arLookupManager;

    /** @var ApLookupManager */
    private $apLookupManager;

    public function __construct(ArLookupManager $arLookupManager, LookupManager $lookupManager, ApLookupContract $apLookupManager, FlashMessageManager $flashMessageManager)
    {
        $this->lookupManager = $lookupManager;
        $this->glCoaParam = new GlCoaParams();
        $this->flashMessageManager = $flashMessageManager;
        $this->apLookupManager = $apLookupManager;
        $this->arLookupManager = $arLookupManager;
    }

    public function index()
    {
        $data['customerCategory'] = $this->arLookupManager->findCustomerCategory();

        return view('ar.customer-search.index', compact('data'));
    }


    public function dataList(Request $request)
    {
        $customerCategory = $request->post('customerCategory');
        $customerName = $request->post('customerName');
        $customerShortName = $request->post('customerShortName');

        $sql = FasArCustomers::where('customer_category_id', '=', DB::raw("ISNULL('" . $customerCategory . "',customer_category_id)"));

        if (isset($customerName)) {
            $sql->where(DB::raw('upper(customer_name)'), 'LIKE', '%' . strtoupper($customerName) . '%');
        }

        if (isset($customerShortName)) {
            $sql->where(DB::raw('upper(customer_short_name)'), 'LIKE', '%' . strtoupper($customerShortName) . '%');
        }

        $data = $sql->with(['customer_category'])
            ->where('inactive_yn','N')
            ->orderBy('customer_id','asc')
            ->get();

        return datatables()->of($data)
            ->editColumn('name', function ($data) {
                return $data->customer_name;
            })
            ->editColumn('short_name', function ($data) {
                return $data->customer_short_name;
            })
            ->editColumn('category', function ($data) {
                return $data->customer_category->customer_category_name;
            })
            ->editColumn('address', function ($data) {
                return $data->address_line1.' '. $data->address_line2;
            })
            ->editColumn('status', function ($data) {
                if ($data->workflow_approval_status == ApprovalStatus::PENDING) {
                    return '<span class="badge badge-primary badge-pill">' . ApprovalStatusView::PENDING . '</span>';
                } else if ($data->workflow_approval_status == ApprovalStatus::APPROVED) {
                    return '<span class="badge badge-success badge-pill">' . ApprovalStatusView::APPROVED . '</span>';
                } else {
                    return '<span class="badge badge-danger badge-pill">' . ApprovalStatusView::REJECTED . '</span>';
                }
            })
            ->editColumn('action', function ($data) {
                return "<a style='text-decoration:underline' class='' href='" . route('customer-profile.edit', ['id' => $data->customer_id]) . "' ><i class='bx bx-edit-alt'></i></a>" . "||<a style='text-decoration:underline' href='" . route('customer-profile.edit', ['id' => $data->customer_id, 'view' => true]) . "'  data-target='' ><i class='bx bx-show-alt'></i></button>";
            })
            ->rawColumns(['status','action'])
            ->make(true);
    }
}
