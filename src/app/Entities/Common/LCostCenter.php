<?php


namespace App\Entities\Common;


use Illuminate\Database\Eloquent\Model;

class LCostCenter extends Model
{
    protected $table = "sbcacc.l_cost_center";
    protected $primaryKey = "cost_center_id";

}
