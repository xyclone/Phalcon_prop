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
use Property\Models\PropertyUnits;
//Forms
use Property\Forms\PropertyUnitsForm;


class PropunitsController extends ControllerBase
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
        $this->view->propunits = PropertyUnits::find(["order" => "name ASC"]);
        $this->view->form = new PropertyUnitsForm(null, []);
        $this->view->form_edit = new PropertyUnitsForm(null, []);
        $this->view->link_action = 'propunits/input';
        $this->view->pick("propunits/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->propunits = PropertyUnits::find(["order" => "name ASC"]);
        $this->view->pick("propunits/list");
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
        $propunits = new PropertyUnits();
        $propunits->assign($post);
        if ($propunits->create()) {
            $result = Helpers::notify('success', 'Project Type successfully added.');
        } else {
            $messages = $propunits->getMessages();
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
        $propunits = PropertyUnits::findFirstByName($name);
        $propunits->assign($post);
        if ($propunits->save()) {
            $result = Helpers::notify('success', 'Project Type successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $propunits->getMessages();
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
        $propunits = PropertyUnits::findFirstByName($name);
        if ($propunits->delete()) {
            $result = Helpers::notify('success', 'Project Type successfully deleted.');
            $result['name'] = $name;
            $result['close'] = 2;
        } else {
            $messages = $propunits->getMessages();
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
        $propunits = PropertyUnits::findFirst([
            "columns" => "name, description",
            "conditions" => "name = :name:",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($propunits);
    }
}

