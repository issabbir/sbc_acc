<form id="cas_receive_form" action="#" method="post" enctype="multipart/form-data">
    @csrf
    <h5 style="text-decoration: underline">Receipt Voucher</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                <label for="function_type" class="required col-md-4 col-form-label">Function Type</label>

                <select required name="function_type" class="form-control form-control-sm col-md-6" id="function_type">
                    <option value="">Select a type</option>
                    @foreach($funcType as $type)
                        <option
                            {{  old('function_type') ==  $type->function_id ? "selected" : "" }} value="{{$type->function_id}}">{{ $type->function_name}}</option>
                    @endforeach
                </select>

                {{--<div class="form-group col-md-4">
                    <div class="row">
                        <label for="department" class="col-md-5 col-form-label">Department</label>
                        <div class="col-md-7">
                            <select name="department" class="form-control form-control-sm select2" id="department">
                                <option value="">Select a department</option>
                                @foreach($department as $dpt)
                                    <option
                                        {{  old('department') ==  $dpt->department_id ? "selected" : "" }} value="{{$dpt->department_id}}">{{ $dpt->department_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>--}}
            </div>
            <div class="form-group row">
                <label for="period" class="required col-md-4 col-form-label">Posting Period</label>

                <select required name="period" class="form-control form-control-sm col-md-4" id="period">
                    {{--<option value="">Select a period</option>--}}
                    @foreach($postingDate as $post)
                        <option
                            {{  ((old('period') ==  $post->posting_period_id) || ($post->posting_period_status == 'O')) ? "selected" : "" }}
                            data-mindate="{{ \App\Helpers\HelperClass::dateConvert($post->posting_period_beg_date)}}"
                            data-maxdate="{{ \App\Helpers\HelperClass::dateConvert($post->posting_period_end_date)}}"
                            data-currentdate="{{ \App\Helpers\HelperClass::dateConvert($post->current_posting_date)}}"
                            data-postingname="{{ $post->posting_period_name}}"
                            value="{{$post->posting_period_id}}">{{ $post->posting_period_name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group row">
                <label for="posting_date_field" class="required col-md-4 col-form-label ">Posting Date</label>
                <div class="input-group date posting_date col-md-4 pl-0 pr-0"
                     id="posting_date"
                     data-target-input="nearest">
                    <input required type="text" autocomplete="off" onkeydown="return false"
                           name="posting_date"
                           id="posting_date_field"
                           class="form-control form-control-sm datetimepicker-input"
                           data-target="#posting_date"
                           data-toggle="datetimepicker"
                           value="{{ old('posting_date', isset($data['insertedData']->posting_date) ?  $data['insertedData']->posting_date : '') }}"
                           data-predefined-date="{{ old('posting_date', isset($data['insertedData']->posting_date) ?  $data['insertedData']->posting_date : '') }}"
                           placeholder="DD-MM-YYYY">
                    <div class="input-group-append posting_date" data-target="#posting_date"
                         data-toggle="datetimepicker">
                        <div class="input-group-text">
                            <i class="bx bxs-calendar font-size-small"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="document_date_field" class="col-md-4 col-form-label">Document Date</label>
                <div class="input-group date document_date col-md-4 pl-0 pr-0"
                     id="document_date"
                     data-target-input="nearest">
                    <input type="text" autocomplete="off" onkeydown="return false"
                           name="document_date"
                           id="document_date_field"
                           class="form-control form-control-sm datetimepicker-input"
                           data-target="#document_date"
                           data-toggle="datetimepicker"
                           value="{{ old('document_date', isset($data['insertedData']->document_date) ?  $data['insertedData']->document_date : '') }}"
                           data-predefined-date="{{ old('document_date', isset($data['insertedData']->document_date) ?  $data['insertedData']->document_date : '') }}"
                           placeholder="DD-MM-YYYY">
                    <div class="input-group-append document_date" data-target="#document_date"
                         data-toggle="datetimepicker">
                        <div class="input-group-text">
                            <i class="bx bxs-calendar font-size-small"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="document_number" class=" col-md-4 col-form-label">Document Number</label>
                <input maxlength="50" type="text" class="form-control form-control-sm col-md-6" name="document_number" id="document_number" oninput="this.value = this.value.toUpperCase()"
                       value="">
            </div>
            <div class="form-group row">
                <label for="document_reference" class="col-md-4 col-form-label">Document Reference</label>
                <input maxlength="200" type="text" class="form-control form-control-sm col-md-6" id="document_reference" name="document_reference"
                       value="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row d-flex justify-content-end">
                <label for="department" class="col-form-label col-md-4 required">Dept/Cost Center</label>
                <div class="col-md-5">
                    <select required name="department" class="form-control form-control-sm select2" id="department">
                        <option value="">Select a department</option>
                        @foreach($department as $dpt)
                            <option
                                {{  old('department') ==  $dpt->cost_center_dept_id ? "selected" : "" }} value="{{$dpt->cost_center_dept_id}}">{{ $dpt->cost_center_dept_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row d-flex justify-content-end">
                <label for="bill_section" class="required col-md-4 col-form-label">Bill Section</label>
                <div class="col-md-5">
                    <select required name="bill_section" class="form-control form-control-sm select2" id="bill_section">
                        {{--<option value="">Select a bill</option>
                        @foreach($billSecs as $value)
                            <option value="{{$value->bill_sec_id}}">{{ $value->bill_sec_name}}
                            </option>
                        @endforeach--}}
                    </select>
                </div>
            </div>
            <div class="form-group row d-flex justify-content-end">
                <label for="bill_register" class="required col-md-4 col-form-label">Bill Register</label>
                <div class="col-md-5">
                    <select required name="bill_register" class="form-control form-control-sm select2" id="bill_register">
                    </select>
                </div>

            </div>
        </div>
    </div>
    <div class="form-group row pr-1">
        <label for="narration" class="required col-md-2 col-form-label">Narration</label>
        <textarea maxlength="500" required name="narration" class="required form-control form-control-sm col-md-10 "
                  id="narration"></textarea>
    </div>

    <div class="row mt-1">
        <fieldset class="border pl-1 pr-1 col-md-12">
            <legend class="w-auto" style="font-size: 12px; font-weight: bold">DEBIT ACCOUNT: TO BANK/CASH
            </legend>
            <div class="form-group row">
                <label class="required col-md-2 col-form-label" for="d_bank_account">Bank Account</label>
                <div class="col-md-6 pl-0 pr-0">
                    <select required class="form-control form-control-sm" id="d_bank_account" name="d_bank_account">
                    </select>
                </div>
                <label class="col-md-2 col-form-label" for="d_account_balance">Account Balance</label>
                <input name="d_account_balance" class="form-control form-control-sm text-right-align col-md-2" value=""
                       id="d_account_balance" readonly tabindex="-1">
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="d_currency">Currency</label>
                <div class="col-md-6">
                    <div class="form-group row">
                        <input class="form-control form-control-sm col-md-2" id="d_currency" name="d_currency" type="text"
                               readonly tabindex="-1"/>
                        <span class="col-md"></span>
                        <label class="required col-md-3 col-form-label" for="d_amount_ccy">Amount CCY</label>
                        <input required class="form-control form-control-sm col-md-4 text-right-align" id="d_amount_ccy" maxlength="17" oninput="maxLengthValid(this)"
                               name="d_amount_ccy" min="0"  step="0.01" type="number">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <label class="col-md-6 col-form-label" for="d_authorized_balance">Authorized Balance</label>
                        <input name="d_authorized_balance" class="form-control form-control-sm text-right-align col-md-6" value=""
                               id="d_authorized_balance" readonly tabindex="-1">
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="d_exchange_rate">Exchange Rate</label>
                <div class="col-md-6">
                    <div class="form-group row">
                        <input class="form-control form-control-sm col-md-2" id="d_exchange_rate" name="d_exchange_rate" type="text"
                               readonly tabindex="-1">
                        <span class="col-md"></span>
                        <label class="col-md-3 col-form-label" for="d_amount_lcy">Amount LCY</label>
                        <input class="form-control form-control-sm col-md-4 text-right-align" id="d_amount_lcy" name="d_amount_lcy"
                               min="0" step="0.01"
                               type="number" readonly tabindex="-1">
                    </div>
                </div>
            </div>

            <!--  TODO: EMERGENCY CHANGE -->
            <div class="form-group row hidden" id="chalanRow">
                <div class="col-md-2">
                    <label class="col-form-label" for="d_chalan_no">Chalan No</label>
                </div>
                <div class="col-md-6">
                    <div class="form-group row mb-0">
                        <input maxlength="25" class="form-control form-control-sm col-md-4" id="d_chalan_no" name="d_chalan_no" type="text">
                        <span class="col-md"></span>
                        <label class="col-md-3 col-form-label" for="d_chalan_date_field">Chalan Date</label>
                        <div class="input-group date d_chalan_date col-md-4 pl-0 pr-0"
                             id="d_chalan_date"
                             data-target-input="nearest">
                            <input type="text" autocomplete="off" onkeydown="return false"
                                   name="d_chalan_date"
                                   id="d_chalan_date_field"
                                   class="form-control form-control-sm datetimepicker-input"
                                   data-target="#d_chalan_date"
                                   data-toggle="datetimepicker"
                                   value="{{ old('d_chalan_date', isset($data['insertedData']->d_chalan_date) ?  $data['insertedData']->d_chalan_date : '') }}"
                                   data-predefined-date="{{ old('d_chalan_date', isset($data['insertedData']->d_chalan_date) ?  $data['insertedData']->d_chalan_date : '') }}"
                                   placeholder="DD-MM-YYYY">
                            <div class="input-group-append d_chalan_date" data-target="#d_chalan_date"
                                 data-toggle="datetimepicker">
                                <div class="input-group-text">
                                    <i class="bx bx-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-4 d-flex justify-content-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" name="without_chalan"
                               id="withoutChalan">
                        <span class="form-check-label" for="withoutChalan">
                            Without Chalan
                        </span>
                    </div>
                </div>
            </div>
            <!--  TODO: EMERGENCY CHANGE -->

            <div class="row">
                <div class="col-md-12">
                    <label>In Words</label><textarea readonly class="form-control form-control-sm" id="d_amount_word" tabindex="-1"></textarea>
                </div>
            </div>
            {{--<div class="form-group row">
                <label for="d_narration" class="required col-md-2 col-form-label">Narration</label>

                <textarea required name="d_narration" class="required form-control form-control-sm col-md-6 "
                          id="d_narration"></textarea>

            </div>--}}
        </fieldset>
    </div>
    <div class="row mt-1">
        <fieldset class="col-md-12 border pl-1 pr-1">
            <legend class="w-auto" style="font-size: 12px; font-weight: bold">CREDIT ACCOUNT</legend>
            <div class="form-group row">
                <label class="required col-md-2 col-form-label" for="c_account_id">Account ID</label>

                <div class="form-group row col-md-6 pl-0 mr-1">
                    <div class="input-group col-md-5">
                        <input name="account_id" class="form-control form-control-sm" value="" type="number" id="c_account_id" maxlength="10" oninput="maxLengthValid(this)"
                               onfocusout="addZerosInAccountId(this)"
                               onkeyup="resetCreditAccountField()">
                        {{--<div class="input-group-append">
                            <button type="button" class="input-group-text d_showAccountListModal"><i
                                    class="bx bx-mouse"></i>Select
                            </button>
                        </div>--}}
                    </div>
                    <div class="col-md-5">
                        <button class="btn btn-sm btn-primary searchAccount" id="searchAccount" type="button"
                                tabindex="-1"><i class="bx bx-search font-size-small"></i><span class="align-middle ml-25">Search</span>
                        </button>
                    </div>
                </div>

                <label class="col-md-2 col-form-label" for="c_account_balance">Account Balance</label>
                <input class="form-control form-control-sm col-md-2 text-right-align" id="c_account_balance" tabindex="-1"
                       name="c_account_balance"
                       type="text" readonly>
            </div>
            {{--<div class="form-group row">
                <label class="required col-md-2 col-form-label" for="c_account_id">Account ID</label>
                <div class="form-group row col-md-6 pl-0 mr-1">
                    <div class="input-group col-md-5">
                        <input name="account_id" class="form-control form-control-sm" value="" type="number"
                               id="c_account_id">

                        --}}{{--<div class="input-group-append">
                            <button type="button" class="input-group-text d_showAccountListModal"><i
                                    class="bx bx-mouse"></i>Select
                            </button>
                        </div>--}}{{--
                    </div>
                    <div class="col-md-5">
                        <button class="btn btn-primary searchAccount" id="searchAccount" tabindex="-1" type="button"><i
                                class="bx bx-search"></i>Search
                        </button>
                    </div>
                </div>

                --}}{{--<div class="col-md-6 pl-0">
                    <div class="form-group row">
                        <div class="input-group col-md-4">
                            <input name="account_id" class="form-control form-control-sm" value="" type="number" id="c_account_id">
                            --}}{{----}}{{--<div class="input-group-append">
                                <button type="button" class="input-group-text showAccountListModal"><i
                                        class="bx bx-mouse"></i></i>Select
                                </button>
                            </div>--}}{{----}}{{--
                        </div>
                        <div class="col-md-3 d-flex justify-content-end pr-0">
                            <button class="btn btn-primary searchAccount" id="searchAccount" tabindex="-1" type="button"><i
                                    class="bx bx-search"></i>Search
                            </button>
                        </div>
                    </div>
                </div>--}}{{--
            </div>--}}
            <div class="form-group row">
                <label for="c_account_name" class="col-md-2 col-form-label">Account Name</label>
                <input name="c_account_name" class="form-control form-control-sm col-md-6" value="" id="c_account_name" tabindex="-1"
                       readonly>
                <label class="col-md-2 col-form-label" for="c_authorized_balance">Authorized Balance</label>
                <input name="c_authorized_balance" class="form-control form-control-sm text-right-align col-md-2" value="" tabindex="-1"
                       id="c_authorized_balance" readonly>
            </div>
            {{-- <div class="form-group row">
                 <label for="c_account_name" class="col-md-2 col-form-label">Account Name</label>
                 <div class="col-md-6 pl-0 pr-0">
                     <input name="c_account_name" class="form-control form-control-sm" value="" id="c_account_name" readonly tabindex="-1">
                 </div>
             </div>--}}
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="c_account_type">Account Type</label>
                <div class="col-md-6">
                    <div class="form-group row mb-0">
                        <input class="form-control form-control-sm col-md-4" id="c_account_type" name="c_account_type"
                               type="text" readonly tabindex="-1">
                        {{--<label class="col-md-4 col-form-label" for="c_account_balance">Account Balance</label>
                        <input class="form-control form-control-sm col-md-4" id="c_account_balance" name="c_account_balance"
                               type="text" readonly tabindex="-1">--}}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="department_cost_center" class="col-form-label col-md-2">Department/Cost Center</label>
                <div class="col-md-6 pl-0 pr-0">
                    <select  name="department_cost_center" class="form-control form-control-sm make-readonly-bg" id="department_cost_center">
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="c_budget_head" class="col-md-2 col-form-label">Budget Head</label>
                <div class="col-md-6 pl-0 pr-0">
                    <input name="c_budget_head" class="form-control form-control-sm" value="" id="c_budget_head" type="text"
                           readonly tabindex="-1">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="c_currency">Currency</label>
                <div class="col-md-6">
                    <div class="form-group row">
                        <input class="form-control form-control-sm col-md-2" id="c_currency" name="c_currency" type="text" readonly
                               tabindex="-1">
                        <div class="col-md"></div>
                        <label class="required col-md-4 col-form-label" for="c_amount_ccy">Amount CCY</label>
                        <input class="required form-control form-control-sm col-md-4 text-right-align" id="c_amount_ccy" maxlength="17" oninput="maxLengthValid(this)"
                               name="c_amount_ccy" min="0" step="0.01"
                               type="number">
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="c_exchange_rate">Exchange Rate</label>
                <div class="col-md-6">
                    <div class="form-group row">
                        <input class="form-control form-control-sm col-md-2" id="c_exchange_rate" name="c_exchange_rate" type="text"
                               readonly tabindex="-1">
                        <div class="col-md"></div>
                        <label class="required col-md-4 col-form-label" for="c_amount_lcy">Amount LCY</label>
                        <input class="required form-control form-control-sm col-md-4 text-right-align" id="c_amount_lcy"
                               name="c_amount_lcy" min="0" step="0.01"
                               type="number" readonly tabindex="-1">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-info " type="button" tabindex="-1" onclick="addLineRow(this)" data-type="A"
                            data-line="" id="addNewLineBtn"><i class="bx bx-plus-circle font-size-small align-top"></i><span class="align-middle ml-25">ADD</span>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label>In Words</label><textarea readonly class="form-control form-control-sm" id="c_amount_word" tabindex="-1"></textarea>
                </div>
            </div>
            {{--<div class="form-group row">
                <label for="c_narration" class="required col-md-2 col-form-label">Narration</label>
                <textarea name="c_narration" class="required form-control form-control-sm col-md-6 " id="c_narration"></textarea>
                <div class="col-md-2">
                    <button class="btn btn-info " type="button" tabindex="-1" onclick="addLineRow(this)" data-type="A"
                            data-line="" id="addNewLineBtn"><i class="bx bx-plus-circle"></i>ADD
                    </button>
                </div>
            </div>--}}
            <div class="row mt-1">
                <div class="col-md-12 table-responsive">
                    <table class="table table-sm table-hover table-bordered " id="c_account_table">
                        <thead class="thead-dark">
                        <tr>
                            <th width="12%" class="text-center">Account Code</th>
                            <th width="28%" class="text-center">Account Name</th>
                            <th width="5%" class="text-center">Dr/Cr</th>
                            <th width="16%" class="text-center">Amount CCY</th>
                            <th width="16%" class="text-center">Amount LCY</th>
                            <th width="5%" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right-align">Total Amount</td>
                            <td><input type="text" name="total_lcy" id="total_lcy"
                                       class="form-control form-control-sm text-right-align"
                                       readonly tabindex="-1"/></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>

    <section>
        @include('gl.common_file_upload')
    </section>

    <div class="row mt-1">
        <div class="col-md-12 d-flex">
            <button type="submit" disabled class="btn btn-sm btn-success mr-1" id="receiveFormSubmitBtn"><i class="bx bxs-save font-size-small"></i><span class="align-middle ml-25">Save</span></button>
            <button type="reset" class="btn btn-sm btn-dark" onclick="resetTablesDynamicRow();resetDebitAccountField();resetHeaderField();removeAllAttachments()"><i class="bx bx-reset font-size-small"></i><span class="align-middle ml-25">Reset</span></button>
            <h6 class="text-primary ml-2">Last Posting Batch ID
                <span class="badge badge-light-primary badge-pill font-medium-2 align-middle ml-25">{{isset($lastGlTranMst->trans_batch_id) ? $lastGlTranMst->trans_batch_id : '0'}}</span>
            </h6>
        </div>
        {{--<div class="col-md-6 ml-1">
            <h6 class="text-primary">Last Posting Batch ID
                <span
                    class="badge badge-light-primary badge-pill font-medium-2 align-middle">{{isset($lastGlTranMst->trans_batch_id) ? $lastGlTranMst->trans_batch_id : ''}}</span>
            </h6>
            --}}{{--<div class="form-group row ">
                <label class="col-form-label col-md-4" for="last_batch_id">Last Posting Batch ID</label>
                <input type="text" readonly tabindex="-1" class="form-control form-control-sm col-md-4" id="last_batch_id" value="{{ isset($lastGlTranMst->trans_batch_id) ? $lastGlTranMst->trans_batch_id : '' }}" />
            </div>--}}{{--
        </div>--}}
    </div>
</form>
