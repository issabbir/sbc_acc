<?php
/**
 *Created by PhpStorm
 *Created at ১৩/৯/২১ ৪:৫৮ PM
 */
?>
@extends("layouts.default")

@section('title')
@endsection

@section('header-style')
    <style type="text/css" rel="stylesheet">
        .form-group {
            margin-bottom: 5px;
        }

        .text-right-align {
            text-align: right;
        }

        /*.bootstrap-datetimepicker-widget table td.active, .bootstrap-datetimepicker-widget table td.active:hover {
             background-color: transparent;
             color: #727E8C;
             text-shadow: 0 0 #f3f0f0;
        }*/
        .select2-container--classic .select2-selection--single, .select2-container--default .select2-selection--single {
            min-height: calc(1.1em + .94rem + 3.7px);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 22px;
        }

        .max-w-14 {
            max-width: 14% !important;
        }

        .max-w-15 {
            max-width: 15% !important;
        }

        .max-w-30 {
            max-width: 30% !important;
        }

        .max-w-12_5 {
            max-width: 12.5% !important;
        }

        .w-86 {
            width: 86% !important;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form id="fdr_maturity_form" action="#" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <h5 style="text-decoration: underline">FDR MATURITY TRANSACTION</h5>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-7">
                        <div class="row">
                            <input type="hidden" id="maturity_id"
                                   value="{{$transaction->maturity_trans_id}}">
                            <label for="transaction_type" class="col-md-3 col-form-label ml-1">Transaction Type</label>
                            <div class="col-md-8 pl-0">
                                <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->maturity_trans_type_name}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                            <label for="investment_type" class="col-md-5 col-form-label">Investment Type</label>
                            <div class="make-select2-readonly-bg col-md-6 pl-0 pr-0">
                                <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->investment_type_name}}">
                            </div>
                        </div>
                    </div>
                </div>


                <fieldset class="border pl-1 pr-1">
                    <legend class="w-auto" style="font-size: 15px;">Transaction Reference</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="fiscal_year" class="required col-md-3 col-form-label">Fiscal Year</label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->fiscal_year}}">
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="period" class="required col-md-3 col-form-label">Posting Period</label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->posting_period}}">
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="posting_date_field" class="required col-md-3 col-form-label ">Posting Date</label>
                                <div class="input-group date posting_date col-md-5 {{isset($transaction) ? "make-readonly" : ''}}"
                                     id="posting_date"
                                     data-target-input="nearest">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->trans_date)}}">
                                    <div class="input-group-append posting_date" data-target="#posting_date"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text">
                                            <i class="bx bxs-calendar font-size-small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="document_date_field" class="required col-md-3 col-form-label pr-0">Document Date</label>
                                <div class="input-group   col-md-5 {{isset($transaction) ? "make-readonly" : ''}}">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->document_date)}}">
                                    <div class="input-group-append document_date" data-target="#document_date"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text">
                                            <i class="bx bxs-calendar font-size-small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="document_number"
                                       class="required col-md-3 col-form-label">Document No</label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->document_no}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row justify-content-end {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="department" class="col-form-label col-md-4 required ">Dept/Cost Center</label>
                                <div class="col-md-6 {{isset($transaction) ? "make-select2-readonly-bg" : ''}}">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->department_name}}">
                                </div>
                            </div>
                            <div class="form-group row d-flex justify-content-end">
                                <label for="bill_section" class="required col-md-4 col-form-label">Bill Section</label>
                                <div class="col-md-6">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->bill_section}}">
                                </div>
                            </div>
                            <div class="form-group row d-flex justify-content-end {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="bill_register" class="required col-md-4 col-form-label">Bill Register</label>
                                <div class="col-md-6 {{isset($transaction) ? "make-select2-readonly-bg" : ''}}">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->bill_register}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row make-readonly">
                        <label for="narration" class="required col-md-2 col-form-label" style="max-width: 12.5%">Narration</label>
                        <div class="col-md-10">
                    <textarea maxlength="500" required name="narration"
                              class="required form-control form-control-sm {{isset($transaction) ? "make-readonly-bg" : ''}}"
                              id="narration">{{  $transaction->narration }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border pl-1 pr-1">
                    <legend class="w-auto" style="font-size: 15px;">FDR Information</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label class="col-form-label col-md-3">Investment ID</label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->investment_id}}">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary btn-sm searchFdr"
                                            id="{{isset($transaction) ? "" : 'fdr_search'}}" type="button"
                                            {{isset($transaction) ? "disabled" : ''}}
                                            tabindex="-1"><i class="bx bx-search font-size-small "></i>
                                        <span class="align-middle">Search</span>
                                    </button>
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="bank_id" class="required col-md-3 col-form-label max-w-12">Bank</label>
                                <div
                                    class="col-md-9">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->bank_code."-".$transaction->bank_name}}">
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="branch_id" class="required col-md-3 col-form-label max-w-12">Branch Name </label>
                                <div
                                    class="col-md-9">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->branch_name}}">
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="fdr_number" class="required col-md-3 col-form-label max-w-12">FDR No </label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->fdr_no}}">
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="amount" class="required col-md-3 col-form-label max-w-12">Amount </label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->investment_amount}}">
                                </div>
                            </div>
                            <div class="row make-readonly">
                                <label for="amount_word" class="col-form-label col-md-3 max-w-12 pr-0">Amount in words</label>
                                <div class="col-md-9">
                        <textarea rows="2" id="amount_word" class="form-control form-control-sm make-readonly-bg"
                                  readonly>{{$transaction->investment_amount_inword}}</textarea>
                                </div>
                            </div>
                            <div class="row make-readonly">
                                <label class="col-md-3 col-form-label max-w-12 pr-0" for="investment_status">Investment
                                    Status</label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->investment_status_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row d-flex justify-content-end">
                                <label for="investment_date_field" class="required col-md-4 col-form-label max-w-30">Investment
                                    Date</label>
                                <div class="input-group col-md-5 make-readonly"
                                     id="investment_date">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->investment_date)}}">
                                    <div class="input-group-append investment_date">
                                        <div class="input-group-text">
                                            <i class="bx bx-calendar font-size-small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <label for="term_period_type" class="required col-md-4 col-form-label max-w-30">Term Period </label>
                                <div class="col-md-2 pr-0">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->term_period_no}}">
                                </div>

                                <div class="col-md-3">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->term_period_code}}">
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <label for="term_period_days" class="required col-md-4 col-form-label max-w-30">Term Period
                                    (Days) </label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->term_period_days}}">
                                </div>
                            </div>
                            <div class="row  d-flex justify-content-end">
                                <label for="maturity_date_field" class="required col-md-4 col-form-label max-w-30 ">Maturity
                                    Date</label>
                                <div class="input-group maturity_date col-md-5 make-readonly"
                                     id="maturity_date">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->maturity_date)}}">
                                    <div class="input-group-append maturity_date">
                                        <div class="input-group-text">
                                            <i class="bx bx-calendar font-size-small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end make-readonly">
                                <label for="interest_rate" class="required col-md-4 col-form-label max-w-30">Interest Rate </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->interest_rate}}">
                                        <div class="input-group-append">
                                            <div class="input-group-text font-size-small">
                                                %
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-10 text-right">
                                    <h5 style="text-decoration: underline">Last Renewal Information:</h5>
                                </div>
                            </div>
                            <div class="row  d-flex justify-content-end make-readonly">
                                <label for="last_renewal_date_field" class="col-md-4 col-form-label max-w-30 ">Renewal
                                    Date</label>
                                <div class="input-group last_renewal_date col-md-5 make-readonly"
                                     id="last_renewal_date">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->last_renewal_date)}}">
                                    <div class="input-group-append last_renewal_date">
                                        <div class="input-group-text">
                                            <i class="bx bx-calendar font-size-small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <label for="last_renewal_amount" class="col-md-4 col-form-label max-w-30">Renewal Amount </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->last_renewal_amount}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row  d-flex justify-content-end make-readonly">
                                <label for="last_maturity_date_field" class="col-md-4 col-form-label max-w-30 ">Maturity
                                    Date</label>
                                <div class="input-group maturity_date col-md-5 make-readonly"
                                     id="last_maturity_date">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->last_renewal_maturity_date)}}">
                                    <div class="input-group-append last_maturity_date">
                                        <div class="input-group-text">
                                            <i class="bx bx-calendar font-size-small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end make-readonly">
                                <label for="last_interest_rate" class="col-md-4 col-form-label max-w-30">Interest Rate </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->last_renewal_interest_rate}}">
                                        <div class="input-group-append">
                                            <div class="input-group-text font-size-small">
                                                %
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border pl-1 pr-1">
                    <legend class="w-auto" style="font-size: 15px;">Interest Information at Maturity</legend>
                    <div class="row d-flex justify-content-end">
                        <div class="col-md-3 text-right">
                            <div class="form-group">
                                <label for="maturity_gross_interest" class="col-form-label">Gross Interest</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->maturity_gross_interest_amount}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <div class="form-group">
                                <label for="maturity_source_tax" class="col-form-label">Sources Tax</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->maturity_source_tax_amount}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-right">
                            <div class="form-group">
                                <label for="maturity_excise_duty" class="col-form-label">Excise Duty</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->maturity_excise_duty_amount}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <div class="form-group">
                                <label for="maturity_net_interest" class="col-form-label">Net Interest</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->maturity_net_interest_amount}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border pl-1 pr-1">
                    <legend class="w-auto" style="font-size: 15px;">Last Year Interest Provision Information</legend>
                    <div class="row d-flex justify-content-end">
                        <div class="col-md-2 text-right">
                            <div class="form-group">
                                <label for="last_pro_days" class="col-form-label">No Of Days</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->provision_no_of_day}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-right">
                            <div class="form-group">
                                <label for="last_pro_gross_interest" class="col-form-label">Gross Interest</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->provision_gross_interest}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <div class="form-group">
                                <label for="last_pro_source_tax" class="col-form-label">Sources Tax</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->provision_source_tax}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-right">
                            <div class="form-group">
                                <label for="last_pro_excise_duty" class="col-form-label">Excise Duty</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->provision_excise_duty}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <div class="form-group">
                                <label for="last_pro_net_interest" class="col-form-label">Net Interest</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->provision_net_interest}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                @if(($transaction->maturity_trans_type_id == \App\Enums\Common\LFdrMaturityTransType::RENEWAL) || ($transaction->maturity_trans_type_id == \App\Enums\Common\LFdrMaturityTransType::RENEWAL_AND_SPLIT))
                <fieldset class="border pl-1 pr-1 {{--d-none--}}" id="crInformation">
                    <legend class="w-auto" style="font-size: 15px;">Current Renewal Information</legend>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="current_renewal_date_field" class="col-form-label">Renewal
                                Date</label>
                            <div
                                class="input-group date current_renewal_date make-readonly"
                                id="current_renewal_date"
                                data-target-input="nearest">
                                <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->curr_renewal_date)}}">
                                <div class="input-group-append current_renewal_date" data-target="#current_renewal_date"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text">
                                        <i class="bx bxs-calendar font-size-small"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="current_renewal_amount" class="col-form-label">Renewal Amount </label>
                            <div class="">
                                <div class="input-group">
                                    <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->curr_renewal_amount}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="current_maturity_date_field"
                                   class="col-form-label">Maturity
                                Date</label>
                            <div class="input-group current_maturity_date make-readonly"
                                 id="current_maturity_date">
                                <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->curr_renewal_maturity_date)}}">
                                <div class="input-group-append current_maturity_date">
                                    <div class="input-group-text">
                                        <i class="bx bxs-calendar font-size-small"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="current_interest_rate" class="col-form-label">Interest Rate </label>
                            <div class="">
                                <div class="input-group">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->curr_renewal_interest_rate}}">
                                    <div class="input-group-append">
                                        <div class="input-group-text font-size-small">
                                            %
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                @endif
                @if(count($splitInfo) > 0)
                <fieldset class="border pl-1 pr-1" id="">
                    <legend class="w-auto" style="font-size: 15px;">Current Renewal With New Split Information</legend>
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                <tr>
                                    <th width="15%">Investment Date</th>
                                    <th width="20%">FDR No</th>
                                    <th width="35%" class="text-right-align">Amount</th>
                                    <th width="15%">Interest Rate</th>
                                    <th width="15%">Expiry Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalAmount = 0;
                                        @endphp
                                    @foreach($splitInfo as $info)
                                        <tr>
                                            <td class="make-readonly-bg">{{\App\Helpers\HelperClass::dateConvert($info->investment_date)}}</td>
                                            <td class="make-readonly-bg">{{$info->fdr_no}}</td>
                                            <td class="text-right-align make-readonly-bg">{{$info->investment_amount}}</td>
                                            <td class="make-readonly-bg">{{$info->interest_rate}}</td>
                                            <td class="make-readonly-bg">{{\App\Helpers\HelperClass::dateConvert($info->investment_date)}}</td>
                                        </tr>
                                        @php
                                            $totalAmount += $info->investment_amount;
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right">Total</td>
                                    <td><input type="text" name="total_fdr_amount" value="@php echo $totalAmount; @endphp"
                                               class="form-control form-control-sm text-right-align"
                                               readonly tabindex="-1"/></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </fieldset>
                @endif
                <fieldset class="border pl-1 pr-1">
                    <legend class="w-auto" style="font-size: 15px;">P.O. Amount</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="po_number" class="col-md-3 col-form-label max-w-30">P.O. Number </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" type="text" readonly value="{{$transaction->pay_order_no}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row {{isset($transaction) ? "make-readonly" : ''}}">
                                <label for="po_date_field" class="col-md-3 col-form-label max-w-30 ">P.O. Date</label>
                                <div class="input-group  col-md-5 {{isset($transaction) ? "make-readonly" : ''}}">
                                    <input class="form-control form-control-sm" type="text" readonly value="{{\App\Helpers\HelperClass::dateConvert($transaction->pay_oder_date)}}">
                                    <div class="input-group-append"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text">
                                            <i class="bx bxs-calendar font-size-small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row d-flex justify-content-end make-readonly">
                                <label for="po_principal_amount" class="col-md-3 col-form-label max-w-30 pr-0">Principal
                                    Amount </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->principal_amt}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end make-readonly">
                                <label for="po_interest_amount" class="col-md-3 col-form-label max-w-30">Interest Amount </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->interest_amt}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end make-readonly">
                                <label for="total_po_amount" class="col-md-3 col-form-label max-w-30">Total Amount </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm text-right-align" type="text" readonly value="{{$transaction->total_amount}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row make-readonly">
                                <label for="total_po_amount_word" class="col-form-label col-md-7 text-right">Amount In Words</label>
                                <div class="input-group col-md-5">
                        <textarea type="text" id="total_po_amount_word" rows="3" readonly
                                  class="form-control form-control-sm pl-0 pr-0 make-readonly-bg"
                                  name="total_po_amount_word">{{$transaction->principal_amt_inword}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </fieldset>
                <fieldset class="border pl-1 pr-1">
                    <legend class="w-auto" style="font-size: 15px;">Contra Account(Bank/Other GL)</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label for="cr_account_name" class="col-md-3 col-form-label">Account Name</label>
                                <div class="col-md-9">
                                    <select name="cr_account_name" readonly=""
                                            class="form-control form-control-sm cr_account_name {{isset($transaction) ? "make-readonly-bg" : ''}}"
                                            id="cr_account_name">
                                        @foreach($contraAcc as $acc)
                                            <option {{($acc->gl_acc_id == $transaction->investment_contra_gl_id) ? 'selected' : ''}} value="{{$acc->gl_acc_id}}">{{$acc->gl_acc_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row make-readonly">
                                <label class="col-md-3 col-form-label" for="cr_account_type">Account Type</label>
                                <div class="col-md-5">
                                    <input class="form-control form-control-sm" id="cr_account_type" name="cr_account_type"
                                           type="text" readonly tabindex="-1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row d-flex justify-content-end make-readonly">
                                <label class="col-md-3 col-form-label max-w-30 pr-0" for="cr_account_balance">Account
                                    Balance</label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control form-control-sm text-right-align" id="cr_account_balance"
                                               tabindex="-1"
                                               name="cr_account_balance"
                                               type="text" readonly>
                                        <div class="input-group-append">
                                <span class="input-group-text" style="font-size: 13px"
                                      id="cr_account_balance_type"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end make-readonly">
                                <label class="col-md-3 col-form-label max-w-30 pr-0" for="cr_authorized_balance">Auth.
                                    Balance</label>
                                <div class="input-group col-md-5">
                                    <input name="cr_authorized_balance" style="height: auto;"
                                           class="form-control form-control-sm text-right-align"
                                           value=""
                                           tabindex="-1"
                                           id="cr_authorized_balance" readonly>
                                    <div class="input-group-append">
                            <span class="input-group-text" style="font-size: 13px"
                                  id="cr_authorized_balance_type"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border pl-1 pr-1">
                    <legend class="w-auto" style="font-size: 15px;">TRANSACTION VIEW</legend>
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            @include('cm.cm-common.opening_preview_table')
                            {{--<table class="table table-sm table-bordered" id="transaction_view">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="12%">GL Account ID</th>
                                        <th width="58%">GL Account Name</th>
                                        <th width="15%" class="text-right">Debit</th>
                                        <th width="15%" class="text-right">Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>--}}
                        </div>
                    </div>
                </fieldset>

                <div class="row mt-1">
                    <div class="col-md-12 d-flex justify-content-start">

                        @if(isset($transaction))
                            <div class="d-none" id="print_btn"></div>
                            <a href="{{route('fdr-maturity.index')}}" type="submit" class="btn btn-sm btn-dark mr-1">
                                <i
                                    class="bx bx-arrow-back font-size-small align-top"></i><span
                                    class="align-middle m-25">Back</span>
                            </a>

                        @else
                            <button type="button" class="btn btn-sm btn-dark mr-1" disabled id="maturity_preview">
                                <i
                                    class="bx bx-show font-size-small align-top"></i><span class="align-middle m-25">Preview</span>
                            </button>
                            <button type="submit" class="btn btn-sm btn-success mr-1" disabled id="fdr_maturity_submit_btn">
                                <i class="bx bx-save font-size-small align-top"></i><span class="align-middle m-25">Save</span>
                            </button>

                            <button type="button" class="btn btn-sm btn-dark" id="reset_form">
                                <i class="bx bx-reset font-size-small align-top"></i><span class="align-middle ml-25">Reset</span>
                            </button>
                            <a href="#" class="btn btn-sm btn-info ml-1 d-none" id="print_btn">
                                <i
                                    class="bx bx-printer font-size-small align-top"></i><span class="align-middle m-25">Print</span>
                            </a>
                        @endif

                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="card">
        <div class="card-header pb-0"><h4 class="card-title mb-0">INVESTMENT TRANSACTION LISTING</h4>
            <hr>
        </div>
        <div class="card-body">
            <fieldset class="border p-2">
                <legend class="w-auto text-bold-600" style="font-size: 15px;">Search Criteria</legend>
                <div class="row">
                    <div class="col-md-3 form-group ">
                        <label for="li_investment_type" class="col-form-label">Investment Type</label>
                        <div class="make-select2-readonly-bg">
                            <select class="custom-select form-control form-control-sm select2" name="li_investment_type"
                                    id="li_investment_type">
                                @foreach($investmentTypes as $type)
                                    {{old('li_investment_type', \App\Helpers\HelperClass::getUserCurrentInvestType(\Illuminate\Support\Facades\Auth::id())) == $type->investment_type_id ? 'selected' : ''}}
                                    <option value="{{$type->investment_type_id}}">{{$type->investment_type_name}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="col-md-2 form-group">
                        <label for="li_fiscal_year" class="required col-form-label">Fiscal Year</label>
                        <select required name="li_fiscal_year"
                                class="form-control form-control-sm required"
                                id="li_fiscal_year">
                            <option value="">&lt;Select&gt;</option>
                            @foreach($fiscalYear as $year)
                                <option value="{{$year->fiscal_year_id}}">{{$year->fiscal_year_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="li_period" class="required col-form-label">Posting Period</label>
                        <select required name="li_period" class="form-control form-control-sm" id="li_period">
                        </select>
                    </div>
                    <div class="col-md-3 form-group ">
                        <label for="li_approval_status" class="col-form-label">Approval Status</label>
                        <select class="form-control form-control-sm" name="li_approval_status"
                                id="li_approval_status">
                            <option value="">&lt;Select&gt;</option>
                            <option value="P">Pending</option>
                            <option value="A">Approved</option>
                        </select>
                    </div>
                    {{--<div class="col-md-1">
                        <button class="btn btn-sm btn-primary" id="li_opening_search" style="margin-top: 33px;">Search
                        </button>
                    </div>--}}
                </div>
            </fieldset>
            <div class="table-responsive">
                <table id="maturity_list" class="table table-sm table-striped table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th width="10%">Posting Date</th>
                        <th width="18%">Bank</th>
                        <th width="22%">Branch</th>
                        <th width="15%">FDR No.</th>
                        <th width="15%">Amount</th>
                        <th width="12%" class="text-center">Auth Status</th>
                        <th width="8%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function fiscalYearGetsPostingPeriod() {
            $("#fiscal_year").on('change', function () {
                getPostingPeriod($("#fiscal_year :selected").val(), '{{route("ajax.get-current-posting-period")}}', setPostingPeriod, $("#period").data('preperiod'));
            });
        }
        getPostingPeriod($("#fiscal_year :selected").val(), '{{route("ajax.get-current-posting-period")}}', setPostingPeriod, $("#period").data('preperiod'));

        function setPostingPeriod(periods) {
            $("#period").html(periods);
            $("#period").trigger('change');
        }



        function reloadFdrListTable() {
            $('#fdr_list').DataTable().draw();
        }

        function getCrAccountDetail(){
            $("#cr_account_name").on('change', function () {
                let response = $.ajax({
                    url: APP_URL + "/general-ledger/ajax/bank-account-details/" + $(this).find(':selected').val(),
                });

                response.done(function (data) {
                    $("#cr_account_type").val(data.gl_type_name);
                    $("#cr_account_balance").val(data.account_balance);
                    $("#cr_account_balance_type").html(data.account_balance_type);
                    $("#cr_authorized_balance").val(data.authorize_balance);
                    $("#cr_authorized_balance_type").html(data.authorize_balance_type);
                });

                response.fail(function (jqXHR, textStatus) {
                    console.log("Something went wrong.");
                });
            })
        }

        function resetCreditInfo() {
            resetField([
                "#cr_account_type",
                "#cr_account_balance",
                "#cr_authorized_balance"
            ]);
            $("#cr_account_balance_type").html("");
            $("#cr_authorized_balance_type").html("");
        }

        function fdrMaturityTransactionLists() {
            function reloadMaturityListTable() {
                $('#maturity_list').DataTable().draw();
            }

            let maturityList = $('#maturity_list').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ordering: false,
                ajax: {
                    url: '{{route("fdr-maturity.fdr-maturity-search-datalist")}}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: function (params) {
                        params.investmentType = $('#li_investment_type :selected').val();
                        params.fiscalYear = $('#li_fiscal_year :selected').val();
                        params.period = $('#li_period :selected').val();
                        params.approvalStatus = $('#li_approval_status :selected').val();
                    }
                },
                columns: [
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'bank', name: 'bank'},
                    {data: 'branch', name: 'branch'},
                    {data: 'fdr_no', name: 'fdr_no'},
                    {data: 'amount', name: 'amount'},
                    {data: 'auth_status', name: 'auth_status'},
                    {data: 'action', name: 'Action'},
                ],
                createdRow: function (row, data, index) {
                    $('td', row).eq(4).addClass("text-right");
                }
            });

            $("#li_investment_type,#li_period, #li_approval_status").on('change', function () {
                reloadMaturityListTable();
            })
            $("#li_fiscal_year").on('change', function () {
                reloadMaturityListTable();
                getPostingPeriod($("#li_fiscal_year :selected").val(), '{{route("ajax.get-current-posting-period")}}', setPostingPeriod);

                function setPostingPeriod(periods) {
                    $("#li_period").html(periods);
                }
            })
        }

        $("#transaction_type").on('change', function () {
            openCloseCurrentInformation();
            enableDisableSaveBtn();
        })

        $(document).ready(function () {
            getCrAccountDetail();
            $("#cr_account_name").trigger("change");

            fiscalYearGetsPostingPeriod();
            fdrMaturityTransactionLists();
        })
    </script>
@endsection

