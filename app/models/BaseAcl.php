<?php
namespace Property\Models;

class BaseAcl extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=32, nullable=true)
     */
    public $icon;

    /**
     *
     * @var string
     * @Column(type="string", length=64, nullable=true)
     */
    public $label;

    /**
     *
     * @var string
     * @Column(type="string", length=64, nullable=true)
     */
    public $menu_group;

    /**
     *
     * @var string
     * @Column(type="string", length=11, nullable=true)
     */
    public $parent;

    /**
     *
     * @var string
     * @Column(type="string", length=1, nullable=false)
     */
    public $child;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    public $url;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $controller;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    public $action;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    public $usergroup;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $except;

    /**
     *
     * @var string
     * @Column(type="string", length=1, nullable=false)
     */
    public $active;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("base_acl");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseAcl[]|BaseAcl
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseAcl
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
