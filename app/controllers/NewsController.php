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
use Property\Models\News;
//Forms
use Property\Forms\NewsForm;


class NewsController extends ControllerBase
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
        $this->view->news = News::find(["order" => "id ASC"]);
        $this->view->form = new NewsForm(null, []);
        $this->view->form_edit = new NewsForm(null, []);
        $this->view->link_action = 'news/input';
        $this->view->pick("news/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->news = News::find(["order" => "id ASC"]);
        $this->view->pick("news/list");
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
        $news = new News();
        $news->assign($post);
        if ($news->create()) {
            $result = Helpers::notify('success', 'News successfully added.');
        } else {
            $messages = $news->getMessages();
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
    public function updatedAction($id)
    {
        $this->view->disable();
        $post = $this->request->getPost();
        $news = News::findFirst($id);
        $news->assign($post);
        if ($news->save()) {
            $result = Helpers::notify('success', 'News successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $news->getMessages();
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
        $id = $this->request->getPost('id');
        $news = News::findFirst($id);
        if ($news->delete()) {
            $result = Helpers::notify('success', 'News successfully deleted.');
            $result['id'] = $id;
            $result['close'] = 2;
        } else {
            $messages = $news->getMessages();
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
    public function detailAction($id)
    {
        $this->view->disable();
        $news = News::findFirst([
            "conditions" => "id = :id:",
            "columns" => "id, name, link, news, start_date, stop_date",
            "bind" => [
                "id" => $id
            ]
        ]);
        return json_encode($news);
    }
}

