<?php
namespace Property\Models;

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class BaseUsers extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=32, nullable=false)
     */
    public $username;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $password;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=64, nullable=false)
     */
    public $email;

    /**
     *
     * @var string
     * @Column(type="string", length=15, nullable=false)
     */
    public $mobile;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $image;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    public $usergroup;

    /**
     *
     * @var string
     * @Column(type="string", length=1, nullable=false)
     */
    public $active;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
   

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("base_users");
        
        $this->hasMany('id', __NAMESPACE__ . '\BaseResetPassword', 'usersId', [
            'alias' => 'resetPasswords',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system'
            ]
        ]);    
         
        // $this->belongsTo('usergroup', __NAMESPACE__ . '\BaseUsergroup', 'id', [
        //     'foreignKey' => true, 'alias' => 'profile', 'reusable' => true
        // ]);   
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

    public function beforeSave()
    {
        //Set the username to be email
        $this->username = $this->email;
    }

    public static function sqlEscape($pattern)
    {
        $response = (new self())->getReadConnection()->escapeString($pattern);
        $response = str_replace("'","",$response);
        return $response;
    }
}
