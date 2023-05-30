<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 11:58 AM
 */

namespace App\Entities\Security;

use Illuminate\Database\Eloquent\Model;

class SecUserRoles extends Model
{
    protected $primaryKey = null;
    protected $table = 'app_security.sec_user_roles';

    public function role()
    {
        return $this->belongsTo(Role::class,"role_id","role_id");
    }
}
