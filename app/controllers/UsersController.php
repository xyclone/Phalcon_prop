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
use Property\Models\BaseUsergroup;
use Property\Models\BaseUsers;
//Forms
use Property\Forms\UsersForm;
use Property\Forms\UploadUsersForm;

use Property\Classes\UsersClass;


class UsersController extends ControllerBase
{
    public function initialize()
    {
        //$this->tag->setTitle('All New Property - Users');
        parent::initialize();
    }

    /**
     * [indexAction description]
     * @return [type] [description]
     */ #echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function indexAction()
    {
        $query = BaseUsers::find(["order" => "name ASC"]);
        if($query&&$query->count()>0) {
            foreach ($query as $key => $user) {
                $users->$key = new StdClass();
                $usergroup = BaseUsergroup::findFirst((int)str_replace(",","",$user->usergroup));
                $users->$key->id = $user->id;
                $users->$key->username = $user->username;
                $users->$key->name = $user->name;
                $users->$key->email = $user->email;
                $users->$key->mobile = $user->mobile;
                $users->$key->active = $user->active;
                $users->$key->usergroup = $user->usergroup;
                $users->$key->groupname = $usergroup->usergroup;
            }
        }
        $this->view->setVars([
            'users' => $users,
            'usergroup' => BaseUsergroup::find(["order" => "id ASC"]),
            'form' => new UsersForm(null, []),
            'jsform' => new UsersForm(null, []),
            'link_action' => 'users/input',
            'formUpload' => new UploadUsersForm(null, []),
            'link_upload' => 'users/upload',
            'form_name' => 'users_list'
        ]);
        $this->view->pick("users/index");
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $query = BaseUsers::find(["order" => "name ASC"]);
        if($query&&$query->count()>0) {
            foreach ($query as $key => $user) {
                $users->$key = new StdClass();
                $usergroup = BaseUsergroup::findFirst((int)str_replace(",","",$user->usergroup));
                $users->$key->id = $user->id;
                $users->$key->username = $user->username;
                $users->$key->name = $user->name;
                $users->$key->email = $user->email;
                $users->$key->mobile = $user->mobile;
                $users->$key->active = $user->active;
                $users->$key->usergroup = $user->usergroup;
                $users->$key->groupname = $usergroup->usergroup;
            }
        }
        $this->view->users = $users;
        $this->view->pick("users/list");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    /**
     * [inputAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function inputAction()
    {
        $this->view->disable();
        $fileName = 'users.png';
        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getSize() > 0) {
                    $fileName = md5(uniqid(rand(), true)).'.'.$file->getExtension();
                    $file->moveTo(MOVE_PHOTO . '/users/' . $fileName);
                    $image = new Imagick(MOVE_PHOTO . '/users/' . $fileName);
                    $image->resize(236, 315)->save();
                }
            }
        }
        
        $post = $this->request->getPost();
//error_log(json_encode($post));
        $usergroup = implode(',', $post['usergroup']);
        $post['usergroup'] = ','.$usergroup.',';
        $post['image']     = $fileName;
        $post['password']  = $this->security->hash($post['password']);
        $users = new BaseUsers();
        $users->assign($post);
        if ($users->save()) {
            $result = Helpers::notify('success', 'User successfully updated.');
        } else {
            $messages = $users->getMessages();
            $m = '';
            foreach ($messages as $message) {
                $m .= "$message <br/>";
            }
            $result = Helpers::notify('error', $m);
        }
        return json_encode($result);
    }

    /**
     * [uploadAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump('here'); echo '</pre>'; die();      
    public function uploadAction()
    {    
        $this->view->disable();
        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                $delimiter = $this->detectDelimiter($file->getTempName());
                if ($file->getSize() > 0) {
                    $fileName = $file->getTempName();
                    $csv = Reader::createFromPath($fileName, 'r');
                    $csv->setHeaderOffset(0); //set the CSV header offset
                    //get 25 records starting from the 11th row
                    $stmt = new Statement();

                    $records = $stmt->process($csv);
                    foreach ($records as $index => $record) {
                        
                        if(is_array($record)) {
                            foreach ($record as $head => $res) {
                                $field_values = explode($delimiter, $res);
                                $fields = explode($delimiter, $head);
                                foreach ($fields as $key => $fld) {
                                    //$realField = UsersClass::$userFields[$field];
                                    $insert[$index][UsersClass::$userFields[$fld]] = $field_values[$key];
                                }               
                            }
                        }
                    }
                }
            }
        }
             
        //Save to DB
        if(!empty($insert)&&count($insert)>0) {
            $inserted=0;$totalinsert=count($insert);
            foreach ($insert as $count => $post) {
                $groupid = BaseUsergroup::findFirstByUsergroup($post['usergroup']);
                $post['usergroup'] = ','.$groupid->id.',';
                $post['image']     = 'users.png';
                $post['password']  = $this->security->hash($post['password']); 
                $users = new BaseUsers();
                $users->assign($post);
                if ($users->save()) {
                    $inserted++;
                }
            }
        }

        if($inserted>0) {
            $result = Helpers::notify('success', $inserted.' User(s) successfully added.');
        } else {
            // $messages = $users->getMessages();
            // $m = '';
            // foreach ($messages as $message) {
            //     $m .= "$message <br/>";
            // }
            $result = Helpers::notify('error', 'Error updating database.');
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

        if ($this->request->hasFiles() == true) {           
            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getSize() > 0) {
                    $fileName = md5(uniqid(rand(), true)).'.'.$file->getExtension();
                    $file->moveTo(MOVE_PHOTO . '/users/' . $fileName);
                    $image = new Imagick(MOVE_PHOTO . '/users/' . $fileName);
                    $image->resize(236, 315)->save();
                } else {
                    $fileName = $this->request->getPost('remove_image');
                }   
            }
        }
        
        $post = $this->request->getPost();

        $usergroup         = implode(',', $post['usergroup']);
        $post['usergroup'] = ','.$usergroup.',';
        $post['image']     = $fileName;
        $post['password']  = $this->security->hash($post['password']);

        $users = BaseUsers::findFirst($id);
        $users->assign($post);
        if ($users->save()) {
            $result = Helpers::notify('success', 'User is successfully updated.');
            $result['close'] = 1;
        } else {
            $messages = $users->getMessages();
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
        $usergroup = BaseUsers::findFirst($id);
        if ($usergroup->delete()) {
            $result = Helpers::notify('success', 'User is successfully deleted.');
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

    /**
     * [statusAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    public function statusAction()
    {
        $this->view->disable();
        $post = $this->request->getPost();

        $users = BaseUsers::findFirst([
            "conditions" => "id = :id:",
            "bind" => [
                "id" => $post['id']
            ]
        ]);
        if ($post['active'] === 'Y') { $m = 'Active'; } else { $m = 'Not Active'; }
        $users->active = $post['active'];
        if ($users->save()) {
            $result = Helpers::notify('success', 'User status is ' . $m . '.');
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
            $messagess = $users->getMessages();
            $message   = '';
            foreach ($messagess as $messages) {
                $message .= "$messages <br/>";
            }
            $result = Helpers::notify('error', $message);
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
        $users = BaseUsers::findFirst([
            "conditions" => "id = :id:",
            "columns" => "id, username, name, email, mobile, image, usergroup",
            "bind" => [
                "id" => $id
            ]
        ]);
        $users->remove_image = $users->image;
        return json_encode($users);
    }


}

