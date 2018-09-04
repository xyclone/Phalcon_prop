<?php
namespace Property\Models;


class BaseUsergroup extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=64, nullable=false)
     */
    public $usergroup;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", length=16, nullable=false)
     */
    public $icon;

    /**
     *
     * @var string
     * @Column(type="string", length=24, nullable=false)
     */
    public $color;

    /**
     *
     * @var string
     * @Column(type="string", length=1, nullable=false)
     */
    public $active;

    public function getProfile()
    {
        return $this->usergroup;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("base_usergroup");
        // $this->belongsTo('id', __NAMESPACE__ . '\BaseUsers', 'usergroup', [
        //     'foreignKey' => true, 'alias' => 'profile', 'reusable' => true
        // ]);             
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseUsergroup[]|BaseUsergroup
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseUsergroup
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
