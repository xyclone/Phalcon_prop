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
use Property\Models\PropertyDistricts;
//Forms
use Property\Forms\DistrictsForm;


class DistrictsController extends ControllerBase
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
        $this->view->districts = PropertyDistricts::find(["order" => "name ASC"]);
        $this->view->form = new DistrictsForm(null, []);
        $this->view->form_edit = new DistrictsForm(null, []);
        $this->view->link_action = 'districts/input';
        $this->view->pick("districts/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->districts = PropertyDistricts::find(["order" => "name ASC"]);
        $this->view->pick("districts/list");
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
        $districts = new PropertyDistricts();
        $districts->assign($post);
        if ($districts->create()) {
            $result = Helpers::notify('success', 'District successfully added.');
        } else {
            $messages = $districts->getMessages();
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
        $districts = PropertyDistricts::findFirstByName($name);
        $districts->assign($post);
        if ($districts->save()) {
            $result = Helpers::notify('success', 'District successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $districts->getMessages();
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
        $districts = PropertyDistricts::findFirstByName($name);
        if ($districts->delete()) {
            $result = Helpers::notify('success', 'District successfully deleted.');
            $result['name'] = $name;
            $result['close'] = 2;
        } else {
            $messages = $districts->getMessages();
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
        $districts = PropertyDistricts::findFirst([
            "conditions" => "name = :name:",
            "columns" => "name, description",
            "bind" => [
                "name" => $name
            ]
        ]);
        return json_encode($districts);
    }
}

