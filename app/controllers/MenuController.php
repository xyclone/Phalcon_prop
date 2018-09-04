<?php
#namespace Property\Controllers;

use Phalcon\Mvc\view;

use Property\Helpers\Helpers;
use Property\Models\BaseMenu;

class MenuController extends \Phalcon\Mvc\Controller
{

    public function listAction()
    {
        $this->view->group = BaseMenu::find([
            "order" => "id ASC"
        ]);

        $this->view->pick("menu/list");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function inputAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();

        $post['usergroup'] = '["'.implode('", "', $post['usergroup']).'"]';

        $group = new BaseMenu();
        $group->assign($post);
        if ($group->save()) {
            $result = Helpers::notify('success', 'Data successfully saved in the database');
            $result['close'] = 3;
        } else {
            $messages = $group->getMessages();
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

        $group = BaseMenu::findFirst($id);
        if ($group->delete()) {
            $result = Helpers::notify('success', 'Menu Group successfully deleted');
            $result['id'] = $id;
            $result['close'] = 4;
        } else {
            $messages = $group->getMessages();
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

        $group = BaseMenu::findFirst([
            "conditions" => "id = :id:",
            "bind" => [
                "id" => $post['id']
            ]
        ]);
        if ($post['active'] === 'Y') { $m = 'Active'; } else { $m = 'Not Active'; }
        $group->active = $post['active'];
        if ($group->save()) {
            $result = Helpers::notify('success', 'Group status is now ' . $m);
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
            $messagess = $group->getMessages();
            $message   = '';
            foreach ($messagess as $messages) {
                $message .= "$messages <br/>";
            }
            $result = Helpers::notify('error', $message);
        }

        return json_encode($result);
    }

}

