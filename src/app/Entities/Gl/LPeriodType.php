<?php


namespace App\Entities\Gl;


use Illuminate\Database\Eloquent\Model;

class LPeriodType extends Model
{
    protected $table = 'sbcacc.l_period_type';
    protected $primaryKey = 'period_type_code';
    protected $keyType = 'string';
}
