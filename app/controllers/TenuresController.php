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
use Property\Models\PropertyTenures;
//Forms
use Property\Forms\TenuresForm;


class TenuresController extends ControllerBase
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
        $this->view->tenures = PropertyTenures::find(["order" => "name ASC"]);
        $this->view->form = new TenuresForm(null, []);
        $this->view->form_edit = new TenuresForm(null, []);
        $this->view->link_action = 'tenures/input';
        $this->view->pick("tenures/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->tenures = PropertyTenures::find(["order" => "name ASC"]);
        $this->view->pick("tenures/list");
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
        $tenures = new PropertyTenures();
        $tenures->assign($post);
        if ($tenures->create()) {
            $result = Helpers::notify('success', 'Tenure successfully added.');
        } else {
            $messages = $tenures->getMessages();
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
        $tenures = PropertyTenures::findFirstByName($name);
        $tenures->assign($post);
        if ($tenures->save()) {
            $result = Helpers::notify('success', 'Tenure successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $tenures->getMessages();
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
        $tenures = PropertyTenures::findFirstByName($name);
        if ($tenures->delete()) {
            $result = Helpers::notify('success', 'Tenure successfully deleted.');
            $result['name'] = $name;
            $result['close'] = 2;
        } else {
            $messages = $tenures->getMessages();
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
        $tenures = PropertyTenures::findFirst([
            "conditions" => "name = :name:",
            "columns" => "name, description",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($tenures);
    }
}

