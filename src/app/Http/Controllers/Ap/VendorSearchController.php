<?php
/**
 *Created by PhpStorm
 *Created at ১৫/৯/২১ ১:১২ PM
 */

namespace App\Http\Controllers\Ap;


use App\Contracts\Ap\ApLookupContract;
use App\Entities\Ap\FasApVendors;
use App\Entities\Gl\GlCoaParams;
use App\Enums\ApprovalStatus;
use App\Enums\ApprovalStatusView;
use App\Http\Controllers\Controller;
use App\Managers\Ap\ApLookupManager;
use App\Managers\FlashMessageManager;
use App\Managers\LookupManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorSearchController extends Controller
{
    protected $glCoaParam;
    protected $lookupManager;
    protected $flashMessageManager;

    /** @var ApLookupManager */
    private $apLookupManager;

    public function __construct(LookupManager $lookupManager, ApLookupContract $apLookupManager, FlashMessageManager $flashMessageManager)
    {
        $this->lookupManager = $lookupManager;
        $this->glCoaParam = new GlCoaParams();
        $this->flashMessageManager = $flashMessageManager;
        $this->apLookupManager = $apLookupManager;
    }

    public function index()
    {
        $data['vendorType'] = $this->apLookupManager->getVendorTypes();
        $data['vendorCategory'] = $this->apLookupManager->getVendorCategory();

        return view('ap.vendor-search.index', compact('data'));
    }


    public function dataList(Request $request)
    {
        $reqData = $request->all();

        $vendorType = isset($reqData['vendorType'])?$reqData['vendorType'] : null;
        $vendorCategory = isset($reqData['vendorCategory'])?$reqData['vendorCategory'] : null;
        $vendorName = isset($reqData['vendorName'])? strtoupper($reqData['vendorName']) : null;
        $vendorShortName = isset($reqData['vendorShortName'])?$reqData['vendorShortName'] : null;

        $data = FasApVendors::where('vendor_type_id', '=', DB::raw("ISNULL('" . $vendorType . "',vendor_type_id)"))
           /* ->where('inactive_yn','N')
            ->where('vendor_category_id', '=', DB::raw("ISNULL('" . $vendorCategory . "',vendor_category_id)"))
            ->where(DB::raw("upper(vendor_name)"), 'like', '%'. DB::raw("ISNULL('" . $vendorName . "',vendor_name)") .'%')  //Add two Where Condition- Pavel-14-03-22
            ->where(function ($query) use ($vendorShortName) {
                $query->where(DB::raw('upper(fas_ap_vendors.vendor_short_name)'), 'like', strtoupper('%' . trim($vendorShortName) . '%') )
                    ->orWhere( 'vendor_short_name', '=', trim($vendorShortName) )
                    ->orWhere('vendor_short_name', '=', DB::raw("ISNULL('" . $vendorShortName . "',vendor_name)"));
            })->with(['vendor_type', 'vendor_category'])*/
            ->orderBy('vendor_id','asc')
            ->get();
        //dd($data);
        return datatables()->of($data)
            ->editColumn('name', function ($data) {
                return $data->vendor_name;
            })
            ->editColumn('short_name', function ($data) {
                return $data->vendor_short_name;
            })
            ->editColumn('category', function ($data) {
                return $data->vendor_category->vendor_category_name;
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
                return "<a style='text-decoration:underline' class='' href='" . route('vendor-profile.edit', ['id' => $data->vendor_id]) . "' ><i class='bx bx-edit-alt'></i></a> || " . "<a style='text-decoration:underline' href='".route('vendor-profile.edit', ['id' =>$data->vendor_id,'view'=>true ])."' data-target='' ><i class='bx bx-show-alt'></i></button>";
            })
            ->rawColumns(['status','action'])
            ->make(true);
    }
}
