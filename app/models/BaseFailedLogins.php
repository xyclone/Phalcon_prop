<?php
namespace Property\Models;

use Phalcon\Mvc\Model;

/**
 * FailedLogins
 * This model registers unsuccessfull logins registered and non-registered users have made
 */
class BaseFailedLogins extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $usersId;

    /**
     *
     * @var string
     */
    public $ipAddress;

    /**
     *
     * @var integer
     */
    public $attempted;

    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("base_failed_logins");

        $this->belongsTo('usersId', __NAMESPACE__ . '\BaseUsers', 'id', [
            'alias' => 'user'
        ]);
    }
}
