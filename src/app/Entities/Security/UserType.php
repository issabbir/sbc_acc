<?php

namespace App\Entities\Security;
use Illuminate\Database\Eloquent\Model;


class UserType extends Model
{
    protected $primaryKey = "user_type_id";
    protected $table = "app_security.sec_user_types";


}
