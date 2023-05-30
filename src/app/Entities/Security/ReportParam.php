<?php


namespace App\Entities\Security;


use Illuminate\Database\Eloquent\Model;

class ReportParam extends Model
{
    protected $table = 'app_security.sec_report_params';
    protected $primaryKey = 'report_param_id';
}
