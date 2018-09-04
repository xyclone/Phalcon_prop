<?php
namespace Property\Library;

use Property\Models\BaseUsers;
use Property\Models\BaseUsergroup;

class AclAction
{

    static private function groupUsers($id)
    {
        $group = BaseUsergroup::findFirst(["conditions" => "active = 'Y' AND id = '$id'"])->toArray();
        return $group;
    }
    
	static private function user($username)
	{
		$user = BaseUsers::findFirst(["conditions"=>"username=:username:","bind"=>['username'=>$username]]);
#echo '<pre>'; var_dump($username); echo '</pre>'; die();           
    	if (!empty($user->usergroup)) {
            $usergroup = explode(',', $user->usergroup);
            foreach ($usergroup as $key => $value) {
                if ($value != '') {
                    if ($key == 1) {
                        $usergroup_data['group'] = $value;
                    }
                    $usergroup_data['array'][$value] = AclAction::groupUsers($value);
                }
            }
        }


        return $usergroup_data;
	}

	static public function aclList($username)
	{
        $access = array(
            'group'     => AclAction::user($username)['group'],
            'usergroup' => AclAction::user($username)['array']
        );

    	return $access;
	}

}