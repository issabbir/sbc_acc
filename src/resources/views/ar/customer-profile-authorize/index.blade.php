<?php
/**
 *Created by PhpStorm
 *Created at ১৫/৯/২১ ১:০৯ PM
 */
?>
@extends('layouts.default')

@section('title')
@endsection

@section('header-style')
@endsection

@section('content')
    @include('ar.customer-profile-authorize.form')
@endsection

@section('footer-script')
    <script type="text/javascript">
        $("#ap_customer_search_form").on('submit', function (e) {
            e.preventDefault();
            $('#customerSearch').DataTable().draw();
        });


        let customerTable = $('#customerSearch').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: false,
            ajax: {
                url: APP_URL + '/account-receivable/customer-profile-authorize-datalist',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (params) {
                    // Retrieve dynamic parameters
                    var dt_params = {
                        customerShortName: $('#customer_short_name').val(),
                        customerName: $('#customer_name').val(),
                        approvalStatus: $('#approval_status :selected').val(),
                    };
                    // Add dynamic parameters to the data object sent to the server
                    if (dt_params) {
                        $.extend(params, dt_params);
                    }
                }
            },
            "columns": [
                {"data": "customer_id"},
                {"data": "name"},
                {"data": "short_name"},
                {"data": "address"},
                {"data": "status"},
                {"data": "action", "orderable": false}
            ],
        });
    </script>
@endsection

