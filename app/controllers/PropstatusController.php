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
use Property\Models\PropertyStatus;
//Forms
use Property\Forms\PropertyStatusForm;


class PropstatusController extends ControllerBase
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
        $this->view->propstatus = PropertyStatus::find(["order" => "name ASC"]);
        $this->view->form = new PropertyStatusForm(null, []);
        $this->view->form_edit = new PropertyStatusForm(null, []);
        $this->view->link_action = 'propstatus/input';
        $this->view->pick("propstatus/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->propstatus = PropertyStatus::find(["order" => "name ASC"]);
        $this->view->pick("propstatus/list");
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
        $propstatus = new PropertyStatus();
        $propstatus->assign($post);
        if ($propstatus->create()) {
            $result = Helpers::notify('success', 'Property Status successfully added.');
        } else {
            $messages = $propstatus->getMessages();
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
        $propstatus = PropertyStatus::findFirstByName($name);
        $propstatus->assign($post);
        if ($propstatus->save()) {
            $result = Helpers::notify('success', 'Property Status successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $propstatus->getMessages();
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
        $propstatus = PropertyStatus::findFirst($name);
        if ($propstatus->delete()) {
            $result = Helpers::notify('success', 'Property Status successfully deleted.');
            $result['name'] = $name;
            $result['close'] = 2;
        } else {
            $messages = $propstatus->getMessages();
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
        $propstatus = PropertyStatus::findFirst([
            "conditions" => "name = :name:",
            "columns" => "name, description",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($propstatus);
    }
}

