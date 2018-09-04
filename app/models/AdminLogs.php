<?php
namespace Property\Models;
use Phalcon\Mvc\Model;

class AdminLogs extends \Phalcon\Mvc\Model
{

    public $id;
    public $username;
    public $access;
    public $ip;
    public $remarks;
    public $access_date;

    /**
     * [initialize description]
     * @return [type] [description]
     */
	public function initialize()
	{
        $this->setConnectionService('db');
	    $this->setSource("logs");
	}

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseUsers[]|BaseUsers
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseUsers
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * [beforeCreate description]
     * @return [type] [description]
     */
    public function beforeCreate()
    {
        //Set the creation date
        $this->access_date = date('Y-m-d H:i:s');
    }

    /**
     * [addLog description]
     * @param [type] $user    [description]
     * @param [type] $level   [description]
     * @param [type] $message [description]
     */
    public function addLog($user, $access, $remarks)
    {
        $this->username = $user;
        $this->access   = $access;
        $this->ip       = $this->getUserIp();
        $this->remarks  = $remarks;
        try{
            $this->create();
        }catch(\Exception $e){
        }
    }

    /**
     * [getIP description]
     * @return [type] [description]
     */
    private function getUserIp()
    {
        $ip_keys = array('REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED');
        $IPs = [];
        foreach($ip_keys as $val){
            if(isset($_SERVER[$val])) $IPs["$val"] = $_SERVER[$val];
        }
        return trim(implode(' ', $IPs));
    }
}
