<?php
#namespace Property\Controllers;

use Phalcon\Mvc\View;
use Property\Helpers\Helpers;
use Property\Models\BaseMenu;
use Property\Models\BaseAcl;
use Property\Models\BaseUsergroup;

class AclController extends ControllerBase
//extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        parent::initialize();
    }

    public function indexAction()
    {
        $this->view->acl = BaseAcl::find(["order" => "url ASC"]);
    	$this->view->group = BaseMenu::find(["order" => "menu_group ASC"]);
    	$this->view->usergroup = BaseUsergroup::find();
        $this->view->pick("acl/index");
    }

    public function listAction()
    {
        $this->view->acl = BaseAcl::find(["order" => "url ASC"]);
        $this->view->usergroup = BaseUsergroup::find();
        $this->view->pick("acl/list");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function inputAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();

        $usergroup = implode(',', $post['usergroup']);
        
        $post['usergroup'] = ','.$usergroup.',';
        $post['action']    = $post['actions'];
        
        if (empty($post['except'])) {
            unset($post['actions']);
            unset($post['except']);
        } else {
            unset($post['actions']);
        }

        $acl = new BaseAcl();
        $acl->assign($post);
        if ($acl->save()) {
            $result = Helpers::notify('success', 'Successfully saved in the database');
        } else {
            $messages = $acl->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }

        return json_encode($result);
    }

    public function updatedAction($id)
    {
        $this->view->disable();
        $post = $this->request->getPost();
        $acl  = BaseAcl::findFirst($id);

        $post['action'] = $post['actions'];
        
        unset($post['usergroup']);
        unset($post['actions']);
        
        $acl->assign($post);
        if ($acl->save()) {
            $result = Helpers::notify('success', 'Successfully updated');
            $result['close'] = 1;
        } else {
            $messages = $acl->getMessages();
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

        $acl = BaseAcl::findFirst($id);
        if ($acl->delete()) {
            $result = Helpers::notify('success', 'Successfully deleted');
            $result['id'] = $id;
            $result['close'] = 2;
        } else {
            $messages = $acl->getMessages();
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

        $acl = BaseAcl::findFirst([
            "conditions" => "id = :id:",
            "bind" => [
                "id" => $post['id']
            ]
        ]);
        if ($post['active'] === 'Y') { $m = 'Active'; } else { $m = 'Not Active'; }
        $acl->active = $post['active'];
        if ($acl->save()) {
            $result = Helpers::notify('success', 'Acl status is now ' . $m);
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
            $messagess = $acl->getMessages();
            $message   = '';
            foreach ($messagess as $messages) {
                $message .= "$messages <br/>";
            }
            $result = Helpers::notify('error', $message);
        }

        return json_encode($result);
    }

    public function accessAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();

        $acl = BaseAcl::findFirst([
            "conditions" => "id = :id:",
            "bind" => [
                "id" => $post['id']
            ]
        ]);
        $aclArray = explode(',', $acl->usergroup);
        if (in_array($post['usergroup'], $aclArray)) {
            $arraySearch = array_search($post['usergroup'], $aclArray);
            unset($aclArray[$arraySearch]);
            $result = implode(',', $aclArray);
        } else {
            $result  = $acl->usergroup;
            $result .= $post['usergroup'].',';
        }
        $acl->usergroup = $result;
        if ($acl->save()) {
            $notify = Helpers::notify('success', 'Successfully saved in the database');
        } else {
            $messagess = $acl->getMessages();
            $message   = '';
            foreach ($messagess as $messages) {
                $message .= "$messages <br/>";
            }
            $notify = Helpers::notify('error', $message);
        }

        return json_encode($notify);
    }

    public function exceptAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();
        $acl  = BaseAcl::findFirst($post['id']);

        $acl->except = $post['except'];
        if ($acl->save()) {
            $notify = Helpers::notify('success', 'Successfully saved in the database');
        } else {
            $messages = $acl->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $notify = Helpers::notify('error', $m);
        }
        return json_encode($notify);
    }

    public function detailAction($id)
    {
        $this->view->disable();
        $acl  = BaseAcl::findFirst($id);
        return json_encode($acl);
    }

}

