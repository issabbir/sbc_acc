<?php


namespace App\Entities\Common;


use Illuminate\Database\Eloquent\Model;

class LFdrInvestmentStatus extends Model
{
    protected $table = 'sbcacc.l_fdr_investment_status';
    protected $primaryKey = 'status_id';
}
