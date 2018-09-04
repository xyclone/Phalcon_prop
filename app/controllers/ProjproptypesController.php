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
use Property\Models\ProjectPropTypes;
//Forms
use Property\Forms\ProjPropertyTypesForm;


class ProjproptypesController extends ControllerBase
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
        $this->view->proptypes = ProjectPropTypes::find(["order" => "name ASC"]);
        $this->view->form = new ProjPropertyTypesForm(null, []);
        $this->view->form_edit = new ProjPropertyTypesForm(null, []);
        $this->view->link_action = 'projproptypes/input';
        $this->view->pick("projproptypes/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->proptypes = ProjectPropTypes::find(["order" => "name ASC"]);
        $this->view->pick("projproptypes/list");
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
        $proptypes = new ProjectPropTypes();
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
        $proptypes = ProjectPropTypes::findFirstByName($name);
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
        $proptypes = ProjectPropTypes::findFirstByName($name);
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
        $proptypes = ProjectPropTypes::findFirst([
            "conditions" => "name = :name:",
            "columns" => "name, description",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($proptypes);
    }
}

