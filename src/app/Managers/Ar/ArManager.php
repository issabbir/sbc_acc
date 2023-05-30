<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 09/12/2021
 * Time: 2:27 PM
 */
namespace App\Managers\Ar;
use App\Contracts\Ar\ArContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ArManager implements ArContract
{

    // Method ap manager

    /*** Add this one method start -Pavel: 24-03-22 ***/
    public function findRoleWiseUser($workflowMasterId, $stepRoleKey, $userID)
    {
        $roleWiseUser = '';
        $query = <<<QUERY
SELECT WT.WORKFLOW_TEMPLATE_ID,
       WT.WORKFLOW_MASTER_ID,
       WT.STEP_ROLE_KEY,
       SEC.USER_ID     LOGIN_USER_ID,
       SEC.ROLE_ID     LOGIN_ROLE_ID
  FROM sbc_dev.WORKFLOW_TEMPLATE     WT,
       APP_SECURITY.SEC_USER_ROLES  SEC,
       APP_SECURITY.SEC_ROLE        SR,
       APP_SECURITY.SEC_USERS       SU
 WHERE     WT.WORKFLOW_MASTER_ID = :p_workflow_master_id
       AND SR.ROLE_ID = SEC.ROLE_ID
       AND SR.ROLE_KEY = WT.STEP_ROLE_KEY
       AND SU.USER_ID = SEC.USER_ID
       ---PARAMETER PART----
       AND WT.STEP_ROLE_KEY = :p_step_role_key
       AND SU.USER_ID =  :p_user_id
QUERY;

        $roleWiseUser = DB::selectOne($query, ['p_workflow_master_id' => $workflowMasterId, 'p_step_role_key' => $stepRoleKey, 'p_user_id' => $userID]);
        return $roleWiseUser;
    }
    /*** Add this two method end -Pavel: 24-03-22 ***/
}
