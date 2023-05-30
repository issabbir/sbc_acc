<?php


namespace App\Entities\Gl;


use Illuminate\Database\Eloquent\Model;

class LFiscalPeriod extends Model
{
    protected $table = 'sbcacc.l_fiscal_period';
    protected $primaryKey = 'fiscal_period_id';
}
