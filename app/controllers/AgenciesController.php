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
//CSV 
use League\Csv\Reader;
use League\Csv\Statement;

use Property\Helpers\Helpers;
//Models
use Property\Models\PropertyAgencies;
//Forms
use Property\Forms\AgencyForm;


class AgenciesController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * [indexAction description]
     * @return [type] [description]
     */ #echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function indexAction()
    {
        $this->view->agencies = PropertyAgencies::find(["order" => "name ASC"]);
        $this->view->form = new AgencyForm(null, []);
        $this->view->form_edit = new AgencyForm(null, []);
        $this->view->link_action = 'agencies/input';
        $this->view->pick("agencies/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->agencies = PropertyAgencies::find(["order" => "name ASC"]);
        $this->view->pick("agencies/list");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    /**
     * [inputAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function inputAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();
        $agencies = new PropertyAgencies();
        $agencies->assign($post);
        if ($agencies->create()) {
            $result = Helpers::notify('success', 'Agency successfully added.');
        } else {
            $messages = $agencies->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }
        return json_encode($result);
    }

    /**
     * [updatedAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */ //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    public function updatedAction($name)
    {
        $this->view->disable();
        $post = $this->request->getPost();
        $agencies = PropertyAgencies::findFirstByName($name);
        $agencies->assign($post);
        if ($agencies->save()) {
            $result = Helpers::notify('success', 'Agency successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $agencies->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }
        return json_encode($result);
    }

    /**
     * [deletedAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    public function deletedAction()
    {
        $this->view->disable();
        $name = $this->request->getPost('name');
        $agencies = PropertyAgencies::findFirstByName($name);
        if ($agencies->delete()) {
            $result = Helpers::notify('success', 'Agency successfully deleted.');
            $result['name'] = $name;
            $result['close'] = 2;
        } else {
            $messages = $agencies->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }
        return json_encode($result);
    }

    /**
     * [detailAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */ //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    public function detailAction($name)
    {
        $this->view->disable();
        $agencies = PropertyAgencies::findFirst([
            "conditions" => "name = :name:",
            "columns" => "name, description",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($agencies);
    }
}

