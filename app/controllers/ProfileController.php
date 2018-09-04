<?php
#namespace Property\Controllers;

//Core
use Phalcon\DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View;
use Phalcon\Image\Adapter\Imagick;

use Property\Helpers\Helpers;
//CSV 
use League\Csv\Reader;
use League\Csv\Statement;

//Models
use Property\Models\BaseUsers;
//Forms
use Property\Forms\UsersForm;
use Property\Forms\UploadUsersForm;

use Property\Classes\UsersClass;


class ProfileController extends ControllerBase
{
    public function initialize()
    {
        //$this->tag->setTitle('All New Property - Users');
        parent::initialize();
    }

    public function indexAction()
    {

		$model = BaseUsers::findFirst(["conditions" => "email=:email:", "bind"=>["email"=>$this->session->get('user')['username']]]);
		//echo '<pre>'; var_dump($model->toArray()); echo '</pre>'; die();
        //$this->view->form = new UsersForm($model, ["mode"=>"update"]);

        $this->view->setVars([
			'tokenKey' => $this->security->getTokenKey(),
			'token' => $this->security->getToken(),
			'form' => new UsersForm($model, ["mode"=>"update"]),
			'link_action' => 'profile/update',
			'link_back' => '/'
		]);

    }
}