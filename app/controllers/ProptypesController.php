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
//Models
use Property\Models\PropertyTypes;
//Forms
use Property\Forms\PropertyTypesForm;


class ProptypesController extends ControllerBase
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
        $this->view->proptypes = PropertyTypes::find(["order" => "name ASC"]);
        $this->view->form = new PropertyTypesForm(null, []);
        $this->view->form_edit = new PropertyTypesForm(null, []);
        $this->view->link_action = 'proptypes/input';
        $this->view->pick("proptypes/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->proptypes = PropertyTypes::find(["order" => "name ASC"]);
        $this->view->pick("proptypes/list");
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
        $proptypes = new PropertyTypes();
        $proptypes->assign($post);
        if ($proptypes->create()) {
            $result = Helpers::notify('success', 'Property Type successfully added.');
        } else {
            $messages = $proptypes->getMessages();
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
        $proptypes = PropertyTypes::findFirstByName($name);
        $proptypes->assign($post);
        if ($proptypes->save()) {
            $result = Helpers::notify('success', 'Property Type successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $proptypes->getMessages();
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
        $proptypes = PropertyTypes::findFirstByName($name);
        if ($proptypes->delete()) {
            $result = Helpers::notify('success', 'Property Type successfully deleted.');
            $result['name'] = $name;
            $result['close'] = 2;
        } else {
            $messages = $proptypes->getMessages();
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
        $proptypes = PropertyTypes::findFirst([
            "conditions" => "name = :name:",
            "columns" => "name, description",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($proptypes);
    }
}

