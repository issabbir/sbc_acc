<?php


namespace App\Entities\Common;


use Illuminate\Database\Eloquent\Model;

class LBillRegister extends Model
{
    protected $table = 'sbcacc.l_bill_register';
    protected $primaryKey = 'bill_reg_id';
}
