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
use Property\Models\ProjectTypes;
//Forms
use Property\Forms\ProjectTypesForm;


class ProjtypesController extends ControllerBase
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
        $this->view->projtypes = ProjectTypes::find(["order" => "name ASC"]);
        $this->view->form = new ProjectTypesForm(null, []);
        $this->view->form_edit = new ProjectTypesForm(null, []);
        $this->view->link_action = 'projtypes/input';
        $this->view->pick("projtypes/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->projtypes = ProjectTypes::find(["order" => "name ASC"]);
        $this->view->pick("projtypes/list");
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
        $projtypes = new ProjectTypes();
        $projtypes->assign($post);
        if ($projtypes->create()) {
            $result = Helpers::notify('success', 'Project Type successfully added.');
        } else {
            $messages = $projtypes->getMessages();
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
        $projtypes = ProjectTypes::findFirstByName($name);
        $projtypes->assign($post);
        if ($projtypes->save()) {
            $result = Helpers::notify('success', 'Project Type successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $projtypes->getMessages();
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
        $projtypes = ProjectTypes::findFirstByName($name);
        if ($projtypes->delete()) {
            $result = Helpers::notify('success', 'Project Type successfully deleted.');
            $result['name'] = $name;
            $result['close'] = 2;
        } else {
            $messages = $projtypes->getMessages();
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
        $projtypes = ProjectTypes::findFirst([
            "conditions" => "name = :name:",
            "columns" => "name, description",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($projtypes);
    }
}

