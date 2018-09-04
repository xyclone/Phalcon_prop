<?php
namespace Property\Models;

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class Uploads extends \Phalcon\Mvc\Model
{

    public $id;
    public $type;
    public $filename;
    public $remakrs;
    public $upload_date;
    public $upload_by;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("uploads");
        $this->di = \Phalcon\DI::getDefault();
    }


    /**
     * [addLog description]
     * @param [type] $user    [description]
     * @param [type] $level   [description]
     * @param [type] $message [description]
     */
    public function addUpload($type, $filename, $remarks)
    {
        $this->type = $type;
        $this->filename = $filename;
        $this->remarks = $remarks;
        try{
            $this->create();
        }catch(\Exception $e){
            error_log('Error: unable to add upload log.');
        }
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
        $this->upload_date = date('Y-m-d H:i:s');
        $this->upload_by = $this->di->get('session')->get('user')['username'];
    }
    
}