<?php
#namespace Property\Controllers;

//core
use Phalcon\Mvc\Url;
use Phalcon\Http\Request;
use Property\Helpers\Helpers;
//Forms
use Property\Forms\SearchForm;

class AgentsController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('');
        parent::initialize();
    }

    public function indexAction()
    {
        // if (empty($this->session->get('user')['username'])) {
        //     return $this->response->redirect('Login');
        // }

        $this->view->form = new UploadAgentForm(null, []);
        $this->view->link_action = 'agents/upload';
        //$this->view->pick("index/index");
    }
}