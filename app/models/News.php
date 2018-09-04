<?php
namespace Property\Models;

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class News extends \Phalcon\Mvc\Model
{

    public $id;
    public $name;
    public $news;
    public $active;
    public $start_date;
    public $stop_date;
    public $creation_date;
    public $creation_by;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("news");
        $this->di = \Phalcon\DI::getDefault();
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
        $this->creation_date = date('Y-m-d H:i:s');
        $this->creation_by = $this->di->get('session')->get('user')['username'];
    }

    public function beforeSave()
    {
        $date = new \DateTime($this->stop_date);
        $this->stop_date = $date->format('Y-m-d 23:59:59');
    }

}