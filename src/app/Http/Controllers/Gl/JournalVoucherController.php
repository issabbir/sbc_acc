<?php
/**
 *Created by PhpStorm
 *Created at ২৫/৫/২১ ৩:৪২ PM
 */

namespace App\Http\Controllers\Gl;


use App\Contracts\Ap\ApLookupContract;
use App\Contracts\Gl\GlContract;
use App\Contracts\LookupContract;
use App\Entities\Gl\GlCoa;
use App\Entities\Gl\GlCoaOfficeMapAcc;
use App\Entities\Gl\GlCoaParams;
use App\Enums\Ap\ApFunType;
use App\Enums\Ar\ArFunType;
use App\Enums\BudgetMonitoring\BmnFunctionType;
use App\Enums\Common\DebitCredit;
use App\Enums\Common\GlSubsidiaryParams;
use App\Enums\Common\LGlInteFun;
use App\Enums\Common\LGlInteModules;
use App\Enums\Common\LTransAmtType;
use App\Enums\Gl\FunctionTypes;
use App\Enums\ProActionType;
use App\Enums\WkReferenceColumn;
use App\Enums\WkReferenceTable;
use App\Enums\WorkFlowMaster;
use App\Enums\YesNoFlag;
use App\Helpers\HelperClass;
use App\Http\Controllers\Controller;
use App\Managers\Ar\ArLookupManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalVoucherController extends Controller
{
    private $lookupManager;
    private $glManager;
    private $glCoa;
    private $glCoaParam;
    private $apLookupManager;
    private $arLookupManager;


    public function __construct(LookupContract $lookupManager, GlContract $glManager, ApLookupContract $apLookupManager,
                                ArLookupManager $arLookupManager)
    {
        $this->lookupManager = $lookupManager;
        $this->glManager = $glManager;
        //$this->glCoa = new GlCoa();
        $this->glCoa = new GlCoaOfficeMapAcc();
        $this->glCoaParam = new GlCoaParams();
        $this->apLookupManager = $apLookupManager;
        $this->arLookupManager = $arLookupManager;
    }

    public function index()
    {
        $user_id = auth()->id();
        $fiscalYear = $this->lookupManager->getCurrentFinancialYear();
        //$postingDate = $this->lookupManager->findPostingPeriod($fiscalYear->fiscal_year_id);
        //$department = $this->lookupManager->getDeptCostCenter();
        $costCenter = $this->lookupManager->getLCostCenter();
        $funcType = $this->lookupManager->getIntegrationFunType(LGlInteFun::JOURNAL_VOUCHER, LGlInteModules::FIN_ACC_GENE_LEDGER);
        $billSecs = $this->lookupManager->getBillSections(BmnFunctionType::BUDGET_BOOKING);
        $lastGlTranMst = $this->glManager->findLastGlTranMst(LGlInteFun::JOURNAL_VOUCHER, $user_id);
        $accountType = $this->glCoaParam->get();

        $arSubsidiaryType = $this->arLookupManager->getArPartySubLedger(ArFunType::AR_INVOICE_BILL_ENTRY);

        $apSubsidiaryType = $this->apLookupManager->getPartySubLedger(ApFunType::AP_INVOICE_BILL_ENTRY);

        $customerCategory = $this->arLookupManager->getCustomerCategory();
        $vendorCategory = $this->apLookupManager->getVendorCategory();
        $vendorType = $this->apLookupManager->getVendorTypes();
        $isRequired = [
            'document_required' => (DB::selectOne('select SBCACC.getLiveDeployPolicyFlag ()  as get_live_deploy_policy_flag')->get_live_deploy_policy_flag == 1 ? 'readonly' : 'required')
        ];
        return view('gl.journal-voucher.index', compact('isRequired', 'apSubsidiaryType', 'customerCategory', 'vendorCategory', 'vendorType', 'arSubsidiaryType', 'accountType', 'fiscalYear', 'costCenter', 'funcType', 'billSecs', 'lastGlTranMst'));
    }

    public function creditBankAccDatatable(Request $request)
    {
        $glType = $request->post('glType');
        $accNameCode = $request->post('accNameCode');

        $creditBankAccounts = $this->glCoa->where(
            [
                /**
                 * Search COA (Problem: Account Type must be Optional, Search result to be ordered by Account ID) REF# email
                 * Made glType optional.
                 * Logic added:04-04-2022**/
                ['gl_type_id', '=', DB::raw("NVL('" . $glType . "',gl_type_id)")],
                ['postable_yn', '=', YesNoFlag::YES],
                ['active_yn', '=', YesNoFlag::YES], //Add Condition- Pavel-14-02-22
            ]
        )->where(function ($query) use ($accNameCode) {
            $query->where(DB::raw('upper(sbcacc.gl_coa_office_map_acc.gl_acc_name)'), 'like', strtoupper('%' . trim($accNameCode) . '%'))
                ->orWhere(DB::raw('CAST(sbcacc.gl_coa_office_map_acc.gl_acc_id AS VARCHAR(MAX))'), '=', trim($accNameCode));
//            $query->where(DB::raw('upper(fas_gl_coa.gl_acc_name)'), 'like', strtoupper('%' . trim($accNameCode) . '%'))
//                ->orWhere(DB::raw('to_char(fas_gl_coa.gl_acc_id)'), '=', trim($accNameCode))
//                //->orWhere('fas_gl_coa.gl_acc_code', '=', trim($accNameCode) );
//                ->orWhere(DB::raw('to_char(fas_gl_coa.old_coa_code)'), '=', trim($accNameCode))     //Add two condition Part :pavel-14-03-2022
//                ->orWhere(DB::raw('to_char(fas_gl_coa.old_sub_code)'), '=', trim($accNameCode));
        })->orderBy('gl_acc_id', 'asc')->get();

        return datatables()->of($creditBankAccounts)
            ->addIndexColumn()
            ->editColumn('gl_acc_code', function ($data) {
                return $data->gl_acc_code;
            })
            ->editColumn('gl_acc_id', function ($data) {
                return $data->gl_acc_id;
            })
            ->editColumn('gl_acc_name', function ($data) {
                return $data->gl_acc_name;
            })
            ->editColumn('action', function ($data) {
                return "<button class='btn btn-sm btn-primary' onclick='c_getAccountDetail($data->gl_acc_id)' >Select</button>";
            })
            ->make(true);
    }

    public function debitBankAccDatatable(Request $request)
    {
        $glType = $request->post('glType');
        $accNameCode = $request->post('accNameCode');

        $debitBankAccounts = $this->glCoa->where(
            [
                /**
                 * Search COA (Problem: Account Type must be Optional, Search result to be ordered by Account ID) REF# email
                 * Made glType optional.
                 * Logic added:04-04-2022**/
                ['gl_type_id', '=', DB::raw("NVL('" . $glType . "',gl_type_id)")],
                ['postable_yn', '=', YesNoFlag::YES],
                ['active_yn', '=', YesNoFlag::NO],   //Add Condition- Pavel-14-02-22
            ]
        )->where(function ($query) use ($accNameCode) {
            $query->where(DB::raw('upper(sbcacc.gl_coa_office_map_acc.gl_acc_name)'), 'like', strtoupper('%' . trim($accNameCode) . '%'))
                ->orWhere(DB::raw('CAST(sbcacc.gl_coa_office_map_acc.gl_acc_id AS VARCHAR(MAX))'), '=', trim($accNameCode));

//            $query->where(DB::raw('upper(fas_gl_coa.gl_acc_name)'), 'like', strtoupper('%' . trim($accNameCode) . '%'))
//                ->orWhere(DB::raw('to_char(fas_gl_coa.gl_acc_id)'), '=', trim($accNameCode))
//                //->orWhere('fas_gl_coa.gl_acc_code', '=', trim($accNameCode) );
//                ->orWhere(DB::raw('to_char(fas_gl_coa.old_coa_code)'), '=', trim($accNameCode))     //Add two condition Part :pavel-14-03-2022
//                ->orWhere(DB::raw('to_char(fas_gl_coa.old_sub_code)'), '=', trim($accNameCode));
        })->orderBy('gl_acc_id', 'asc')->get();

        return datatables()->of($debitBankAccounts)
            ->addIndexColumn()
            ->editColumn('gl_acc_code', function ($data) {
                return $data->gl_acc_code;
            })
            ->editColumn('gl_acc_id', function ($data) {
                return $data->gl_acc_id;
            })
            ->editColumn('gl_acc_name', function ($data) {
                return $data->gl_acc_name;
            })
            ->editColumn('action', function ($data) {
                return "<button class='btn btn-sm btn-primary' onclick='d_getAccountDetail($data->gl_acc_id)' >Select</button>";
            })
            ->make(true);
    }

    public function store(Request $request, $id = null)
    {

        DB::beginTransaction();

        try {
            $trans_mst_id = null;
            $o_trans_batch_id = null;
            $o_document_no = sprintf("%4000s", "");
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'p_action_type' => ProActionType::INSERT,
                'p_trans_master_id' => &$trans_mst_id,
                'p_module_id' => LGlInteModules::FIN_ACC_GENE_LEDGER,
                'p_function_id' => $request->post('function_type'),
                'p_cost_center_id' => $request->post('cost_center'),
                'p_department_id' => $request->post('department') ? $request->post('department') : NULL,
                'p_bill_sec_id' => $request->post('bill_section'),
                'p_bill_reg_id' => $request->post('bill_register'),
                'p_trans_period_id' => $request->post('period'),
                'p_trans_date' => HelperClass::dateFormatForDB($request->post('posting_date')),
                'p_document_date' => HelperClass::dateFormatForDB($request->post('document_date')),
                'p_document_no' => $request->post('document_number'),
                'p_document_ref' => $request->post('document_reference'),
                'p_narration' => $request->post('narration'),
                'p_user_id' => auth()->id(),
                'p_system_generated_yn' => 'N',
                'o_trans_batch_id' => &$o_trans_batch_id,
                'o_document_no' => &$o_document_no,
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message
            ];

            DB::executeProcedure('SBCACC.GL_TRANS_MASTER_MAKE', $params);
            if ($params['o_status_code'] == "99") {
                DB::rollBack();
                return response()->json(["response_code" => $status_code, "response_msg" => $status_message]);
            } else {
//                $o_trans_mst_id = $params['p_trans_master_id']['value'];
                $o_trans_mst_id = $params['p_trans_master_id'];

                if ($o_trans_mst_id) {
                    foreach ($request->post("line") as $key => $line) {
                        if ($line['action_type'] == "A") {
                            ${"debit_status_code" . $key} = sprintf("%4000s", "");
                            ${"debit_status_message".$key} = sprintf("%4000s", "");

                            ${"dtlDebitParams".$key} = [
                                'p_action_type' => ProActionType::INSERT,
                                'p_trans_detail_id' => NULL,
                                'p_trans_master_id' => $o_trans_mst_id,
                                'p_gl_acc_id' => $line['account_code'],
                                'p_dr_cr' => $line['dr_cr'],
                                'p_currency_code' => $line['currency'],
                                'p_exchange_rate' => $line['exchange_rate'],
                                'p_amount_type_id' => LTransAmtType::GENERAL_OTHERS,
                                'p_amount_ccy' => $line['amount_ccy'],
                                'p_amount_lcy' => ($line['dr_cr'] == DebitCredit::DEBIT) ? $line['debit_amount'] : $line['credit_amount'],
                                'p_without_cheque_yn' => NULL,
                                'p_cheque_no' => NULL,
                                'p_cheque_date' => NULL,
                                'p_without_challan_yn' => NULL,
                                'p_challan_type_id' => NULL,
                                'p_challan_no' => NULL,
                                'p_challan_date' => NULL,
                                'p_gl_subsidery_id' => isset($line['module_id']) ? $line['party_sub_ledger'] : NULL,
                                'p_customer_id' => isset($line['module_id']) ? (($line['module_id'] == LGlInteModules::ACCOUNT_RECEIVABLE) ? $line['party_id'] : NULL) : NULL,
                                'p_vendor_id' => isset($line['module_id']) ? (($line['module_id'] == LGlInteModules::ACC_PAY_VENDOR) ? $line['party_id'] : NULL) : NULL,
                                'p_emp_id' => NULL,
                                'p_cost_center_id' => $request->post('cost_center'),
                                'p_user_id' => auth()->id(),
                                'o_status_code' => &${"debit_status_code" . $key},
                                'o_status_message' => &${"debit_status_message" . $key}
                            ];

                            DB::executeProcedure('sbcacc.GL_TRANS_DETAIL_MAKE', ${"dtlDebitParams".$key});  //dd(${"dtlDebitParams".$key});
                            $code = ${"debit_status_code" . $key};
                            $msg = ${"debit_status_message" . $key};

                            if ($code == "99") {
                                DB::rollBack();
                                return response()->json(["response_code" => $code, "response_msg" => $msg]);
                            }
                        }
                    }

                    if (!is_null($request->file('attachment'))) {
                        $files = $request->file('attachment');
                        $descriptions = $request->post('attachment');
                        foreach ($files as $key => $file) {
                            if ($file['file'] && $descriptions[$key]['actionType'] == "I") {

                                $byteCode = base64_encode(file_get_contents($file['file']->getRealPath()));
                                $fileExt = $file['file']->extension();
                                $fileName = $file['file']->getClientOriginalName();

                                $fileId = "";
                                ${"file_status_code" . $key} = sprintf("%4000s", "");
                                ${"file_status_message" . $key} = sprintf("%4000s", "");

                                ${"file_params" . $key} = [
                                    'p_action_type' => 'I',
                                    'p_trans_master_id' => $o_trans_mst_id,
                                    'p_trans_doc_file_id' => &$fileId,
                                    'p_trans_doc_file_name' => $fileName,
                                    'p_trans_doc_file_name_bng' => "",
                                    'p_trans_doc_file_desc' => $descriptions[$key]["description"],
                                    'p_trans_doc_file_type' => $fileExt,

                                    "p_trans_doc_file_content" => [
                                        'value' => $byteCode,
                                        'type' => null,
                                    ],
                                    'p_user_id' => auth()->id(),
                                    'o_status_code' => &${"file_status_code" . $key},
                                    'o_status_message' => &${"file_status_message" . $key}
                                ];

                                DB::executeProcedure('sbcacc.GL_TRANS_DOCS_ATTACH', ${"file_params" . $key});

                                if (${"file_status_code" . $key} == "99") {
                                    DB::rollBack();
                                    return response()->json(["response_code" => ${"file_status_code" . $key}, "response_msg" => ${"file_status_message" . $key}]);
                                }
                            }
                        }
                        DB::commit();

                    }
                    if ($o_trans_mst_id) {
                        $wk_mapping_status_code = sprintf("%4000s", "");
                        $wk_mapping_status_message = sprintf("%4000s", "");

                        $wkMappingParams = [
                            'P_WORKFLOW_MASTER_ID' => WorkFlowMaster::GL_JOURNAL_VOUCHER_APPROVAL,
                            'P_REFERENCE_TABLE' => WkReferenceTable::FAS_GL_TRANS_MASTER,
                            'P_REFERANCE_KEY' => WkReferenceColumn::TRANS_MASTER_ID,
                            'P_REFERANCE_ID' => $o_trans_mst_id,
                            'P_TRANS_PERIOD_ID' => $request->post('period'),
                            'P_INSERT_BY' => auth()->id(),
                            'o_status_code' => &$wk_mapping_status_code,
                            'o_status_message' => &$wk_mapping_status_message,
                        ];


                        DB::executeProcedure('sbcacc.WORKFLOW_MAPPING_ENTRY', $wkMappingParams);
                        if ($wkMappingParams['o_status_code'] != 1) {
                            DB::rollBack();
                            return $wkMappingParams;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["response_code" => "99", "response_msg" => $e->getMessage()]);
        }
        DB::commit();
        return response()->json(["response_code" => $status_code, "response_msg" => $status_message, "o_batch" => $o_trans_batch_id, "o_document_no" => $o_document_no, "period" => $params['p_trans_period_id']]);
    }

    public function bankAccDatatable(Request $request)
    {
        $glType = $request->post('glType');
        $accNameCode = $request->post('accNameCode');
        $costCenter = $request->post('costCenter');

        $creditBankAccounts = $this->glCoa->where(
            [
                ['gl_type_id', '=', DB::raw("ISNULL('" . $glType . "',gl_type_id)")],
                ['postable_yn', '=', YesNoFlag::YES],
                ['active_yn', '=', YesNoFlag::YES],
            ]
        )   ->where('cost_center_id', $costCenter)
            ->where(function ($query) use ($accNameCode) {
                $query->where(DB::raw('upper(sbcacc.gl_coa_office_map_acc.gl_acc_name)'), 'like', strtoupper('%' . trim($accNameCode) . '%'))
                    ->orWhere(DB::raw('CAST(sbcacc.gl_coa_office_map_acc.gl_acc_id AS VARCHAR(MAX))'), '=', trim($accNameCode));
            })
            ->orderBy('gl_acc_id', 'asc')
            ->get();

        return datatables()->of($creditBankAccounts)
            ->addIndexColumn()
            ->editColumn('gl_acc_code', function ($data) {
                return $data->gl_acc_code;
            })
            ->editColumn('gl_acc_id', function ($data) {
                return $data->gl_acc_id;
            })
            ->editColumn('gl_acc_name', function ($data) {
                return $data->gl_acc_name;
            })
            ->editColumn('action', function ($data) {
                return "<button class='btn btn-sm btn-primary' onclick='getAccountDetail($data->gl_acc_id)' >Select</button>";
            })
            ->make(true);
    }

}
