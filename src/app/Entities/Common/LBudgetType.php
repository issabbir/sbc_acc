<?php


namespace App\Entities\Common;


use Illuminate\Database\Eloquent\Model;

class LBudgetType extends Model
{
    protected $table = 'sbcacc.l_budget_type';
    protected $primaryKey = 'budget_type_id';
}
