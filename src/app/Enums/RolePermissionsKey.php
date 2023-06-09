<?php


namespace App\Enums;


class RolePermissionsKey
{
    //Gl Part
    public const CAN_EDIT_GL_JOURNAL_VOUCHER_MAKE = 'CAN_EDIT_GL_JOURNAL_VOUCHER_MAKE';
    public const CAN_EDIT_GL_TRANSFER_VOUCHER_MAKE = 'CAN_EDIT_GL_TRANSFER_VOUCHER_MAKE';
    public const CAN_EDIT_GL_PAYMENT_VOUCHER_MAKE = 'CAN_EDIT_GL_PAYMENT_VOUCHER_MAKE';
    public const CAN_EDIT_GL_RECEIVE_VOUCHER_MAKE = 'CAN_EDIT_GL_RECEIVE_VOUCHER_MAKE';
    public const CAN_BE_ADD_BUDGET_BOOK_TO_GL_JOURNAL_MAKE = 'CAN_BE_ADD_BUDGET_BOOK_TO_GL_JOURNAL_MAKE';

    // Ap Part
    public const CAN_EDIT_AP_INVOICE_MAKE = 'CAN_EDIT_AP_INVOICE_MAKE';
    public const CAN_EDIT_AP_PAYMENT_MAKE = 'CAN_EDIT_AP_PAYMENT_MAKE';

    public const CAN_BE_ADD_BUDGET_BOOK_TO_AP_INVOICE_MAKE = 'CAN_BE_ADD_BUDGET_BOOK_TO_AP_INVOICE_MAKE';

    // Ar Part
    public const CAN_EDIT_AR_INVOICE_MAKE = 'CAN_EDIT_AR_INVOICE_MAKE';
    public const CAN_EDIT_AR_RECEIPT_MAKE = 'CAN_EDIT_AR_RECEIPT_MAKE';

    // Cm Part

    //Budget MGT Part

}
