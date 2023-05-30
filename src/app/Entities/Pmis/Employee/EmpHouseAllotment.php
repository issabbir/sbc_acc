<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LBuildingType;
use Illuminate\Database\Eloquent\Model;

class EmpHouseAllotment extends Model
{
    protected $table = 'pmis.emp_house_allotement';
    protected $primaryKey = 'house_allotement_id';

    protected $with = ['building_type'];

    public function building_type()
    {
        return $this->belongsTo(LBuildingType::class, 'building_type_id');
    }
}
