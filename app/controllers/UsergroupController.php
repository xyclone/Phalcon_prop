<?php
#namespace Property\Controllers;

use Phalcon\Mvc\View;

use Property\Helpers\Helpers;
use Property\Models\BaseUsergroup;

use Property\Forms\UserGroupForm;

class UsergroupController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('');
        parent::initialize();
    }

    public function indexAction()
    {
        // $this->view->usergroup = BaseUsergroup::find([
        //     "order" => "id ASC"
        // ]);
        // $this->view->form = new UserGroupForm(null, []);
        // $this->view->link_action = 'usergroup/input';      
        // $this->view->pick("usergroup/index");

        $this->view->setVars([
            'usergroup' =>  BaseUsergroup::find(["order" => "id ASC"]),
            'form' => new UserGroupForm(null, []),
            'link_action' => 'usergroup/input',
            'form_name' => 'usersgroup_list'
        ]);
        $this->view->pick("usergroup/index");

    }

    public function listAction()
    {
        $this->view->usergroup = BaseUsergroup::find([
            "order" => "id ASC"
        ]);

        $this->view->pick("usergroup/list");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    public function inputAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();

        $usergroup = new BaseUsergroup();
        $usergroup->assign($post);
        if ($usergroup->save()) {
            $result = Helpers::notify('success', 'Usergroup successfully updated.');
        } else {
            $messages = $usergroup->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }
        return json_encode($result);
    }

    //echo '<pre>'; var_dump($usergroup); echo '</pre>'; die();  
    public function updatedAction($id)
    {
        $this->view->disable();
        $post = $this->request->getPost();
        
        $usergroup = BaseUsergroup::findFirst($id);
        $usergroup->assign($post);
        if ($usergroup->save()) {
            $result = Helpers::notify('success', 'Usergroup is successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $usergroup->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }
        return json_encode($result);
    }

    public function deletedAction()
    {
        $this->view->disable();
        $id = $this->request->getPost('id');

        $usergroup = BaseUsergroup::findFirst($id);
        if ($usergroup->delete()) {
            $result = Helpers::notify('success', 'Usergroup is successfully deleted.');
            $result['id'] = $id;
            $result['close'] = 2;
        } else {
            $messages = $usergroup->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }
        return json_encode($result);
    }

    public function statusAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();

        $usergroup = BaseUsergroup::findFirst([
            "conditions" => "id = :id:",
            "bind" => [
                "id" => $post['id']
            ]
        ]);
        if ($post['active'] === 'Y') { $m = 'Active'; } else { $m = 'Not Active'; }
        $usergroup->active = $post['active'];
        if ($usergroup->save()) {
            $result = Helpers::notify('success', 'Usergroup status is ' . $m);
            if ($post['active'] === 'N') {
                $result['i']      = 'text-danger';
                $result['bg']     = 'bg-red';
                $result['status'] = 'not active';
                $result['active'] = 'Y';
            } else {
                $result['i']      = 'text-success';
                $result['bg']     = 'bg-green';
                $result['status'] = 'active';
                $result['active'] = 'N';
            }
        } else {
            $messagess = $usergroup->getMessages();
            $message   = '';
            foreach ($messagess as $messages) {
                $message .= "$messages <br/>";
            }
            $result = Helpers::notify('error', $message);
        }

        return json_encode($result);
    }

}

