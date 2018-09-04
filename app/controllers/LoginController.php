<?php
#namespace Property\Controllers;

//core
use Phalcon\Mvc\Url;
use Phalcon\Http\Request;
use Phalcon\Mvc\View;

use Property\Helpers\Helpers;
use Property\Models\BaseUsers;
use Property\Models\BaseResetPassword;
use Property\Models\AdminLogs;

use Property\Models\BaseEmailConfirmations;
use Property\Models\BasePasswordChanges;
use Property\Library\AclAction;
use Property\Auth\Exception as AuthException;
use Property\Forms\LoginForm;
use Property\Forms\ForgotPasswordForm;
use Property\Forms\ChangePasswordForm;



class LoginController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('');
        #parent::initialize();
    }

    public function loginAction($error = null)
    {
        // if ($error === 'user') {
        //     $this->view->erorrSend = Helpers::errorSend('user');
        // } elseif ($error === 'token') {
        //     $this->view->erorrSend = Helpers::errorSend('token');
        // } else {
        //     $this->view->erorrSend = '';
        // }
        // $this->view->form = new LoginForm();
        // $this->view->pick("login/index");
        // $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $form = new LoginForm();
        try {
            if (!$this->request->isPost()) {
                if ($this->auth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            } else {
                if (!$this->security->checkToken()) {
                    $this->flash->error("Invalid Token");
                    return $this->redirectBack();
                }                             
                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                } else {
                    $this->auth->check([
                        'username' => $this->request->getPost('username'),
                        'password' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ]);
                    (new AdminLogs)->addLog($this->request->getPost('username'), 'Login', 'Login successfully.');
                    return $this->response->redirect('/');
                }
            }
        } catch (AuthException $e) {
            $this->flash->error($e->getMessage());
        }
        $this->view->setVars([
            'tokenKey' => $this->security->getTokenKey(),
            'token' => $this->security->getToken(),
            'form' => $form
        ]);
        $this->view->pick("login/index");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

    }

    public function processAction()
    {
     
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
               
            $username    = $this->request->getPost('username');
            $password    = $this->request->getPost('password');
            // Find the user in the database
            $user = BaseUsers::findFirst(
                [
                    "columns" => "username, email, password, image",
                    "conditions" => "(username = :username: OR email = :username:)",
                    "bind" => [
                        "username" => $post['username']
                    ]
                ]
            );

            if ($this->security->checkHash($post['password'], $user->password)) {
                //data_session_yang_dikirim
                $aclArray  = AclAction::aclList($user->username);
                $userArray = [
                    'username' => $user->username,
                    'image'    => $user->image,
                ];
            
                $this->session->set('acl', $aclArray);
                $this->session->set('user', $userArray);
                return $this->response->redirect('index/index');
            }

            $this->flashSession->error("Invalid Login");
            return $this->response->redirect('login/error/user');
        }
    }


    /**
     * Shows the forgot password form
     */
    public function forgotPasswordAction($error = null)
    {
        $this->view->erorrSend = (!empty($error)) ? Helpers::errorSend($error) : '';
        $form = new ForgotPasswordForm();
        $this->view->form = $form;
        $this->view->pick("login/forgotPassword");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }


    //echo '<pre>'; var_dump($this->request->getPost()); echo '</pre>'; die(); 
    public function postForgotPasswordAction()
    {
        if ($this->request->isPost()) {
            $form = new ForgotPasswordForm();
            // Send emails only is config value is set to true
            if ($this->getDI()->get('config')->useMail) {
                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flashSession->error($message);
                    }
                    return $this->response->redirect('login/forgot/account');
                } else {
                    $user = BaseUsers::findFirstByEmail($this->request->getPost('email'));                    
                    if (!$user) {
                        return $this->response->redirect('login/forgot/account');
                    } else {
                        $resetPassword = new BaseResetPassword();
                        $resetPassword->usersId = $user->id;
                        if ($resetPassword->save()) {
                            return $this->response->redirect('login/forgot/reset');
                        } else {
                            foreach ($resetPassword->getMessages() as $message) {
                                $this->flashSession->error($message);
                            }
                        }
                    }
                }
            } else {
                $this->flashSession->warning('Emails are currently disabled. Change config key "useMail" to true to enable emails.');
            }
        }
        return $this->response->redirect('login/forgot/account');        
    }


    /**
     * Confirms an e-mail, if the user must change thier password then changes it
     */
    public function confirmEmailAction()
    {
        $code = $this->dispatcher->getParam('code');
        $confirmation = BaseEmailConfirmations::findFirstByCode($code);
        if (!$confirmation) {
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        if ($confirmation->confirmed != 'N') {
            return $this->dispatcher->forward([
                'controller' => 'session',
                'action' => 'login'
            ]);
        }
        $confirmation->confirmed = 'Y';
        $confirmation->user->active = 'Y';
        /**
         * Change the confirmation to 'confirmed' and update the user to 'active'
         */
        if (!$confirmation->save()) {
            foreach ($confirmation->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        /**
         * Identify the user in the application
         */
        //$this->auth->authUserById($confirmation->user->id);

        /**
         * Check if the user must change his/her password
         */
        if ($confirmation->user->mustChangePassword == 'Y') {
            $this->flash->success('The email was successfully confirmed. Now you must change your password');
            return $this->dispatcher->forward([
                'controller' => 'login',
                'action' => 'changePassword'
            ]);
        }
        $this->flash->success('The email was successfully confirmed');
        return $this->dispatcher->forward([
            'controller' => 'login',
            'action' => 'index'
        ]);
    }
    
    /**
     * [resetPasswordAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->request->getPost()); echo '</pre>'; die(); 
    public function resetPasswordAction()
    {
        
        $email = $this->dispatcher->getParam('email');
        $resetAccount = BaseUsers::findFirstByEmail($email);
        if(!$resetAccount) {
            $this->flash->error('Invalid email address.');
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }

        $code = $this->dispatcher->getParam('code');
        $resetPassword = BaseResetPassword::findFirstByCode($code);
        if (!$resetPassword) {
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }
        if ($resetPassword->reset != 'N') {
            return $this->dispatcher->forward([
                'controller' => 'login',
                'action' => ''
            ]);
        }
        $resetPassword->reset = 'Y';
//echo '<pre>'; var_dump($resetPassword->reset); echo '</pre>'; die();         
        /**
         * Change the confirmation to 'reset'
         */
        if (!$resetPassword->save()) {
            foreach ($resetPassword->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'index'
            ]);
        }

        /**
         * Identify the user in the application
         */
        //$this->auth->authUserById($resetPassword->usersId);
        $user = BaseUsers::findFirstById($resetPassword->usersId);
        $aclArray  = AclAction::aclList($user->username);
        $userArray = [
            'username' => $user->username,
            'image'    => $user->image,
        ];
        $this->session->set('acl', $aclArray);
        $this->session->set('user', $userArray);

        $this->flash->warning('Please reset your password');
        //return $this->response->redirect('Login/changePassword');
        return $this->dispatcher->forward([
            'controller' => 'login',
            'action' => 'changePassword',
        ]);
    }

    /**
     * Users must use this action to change its password
     */ //echo '<pre>'; var_dump($this->request->getPost()); echo '</pre>'; die(); 
    public function changePasswordAction($error = null)
    {
        $form = new ChangePasswordForm();
        $this->view->erorrSend = '';
        if ($this->request->isPost()) {
            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                $account = $this->session->get('user')['username'];
                $user = BaseUsers::findFirstByUsername($account);             
                $user->password = $this->security->hash($this->request->getPost('password'));
                $passwordChange = new BasePasswordChanges();
                $passwordChange->userId = $user->id;
                $passwordChange->ipAddress = $this->request->getClientAddress();
                $passwordChange->userAgent = $this->request->getUserAgent();
                if (!$passwordChange->save()&&!$user->save()) {
                    //$this->flash->error($passwordChange->getMessages());
                    return $this->response->redirect('login/change/error');
                } else {
                    //$this->flash->success($passwordChange->getMessages());
                    $result = Helpers::notify('success', 'User password successfully updated.');
                    return $this->response->redirect('/index/index');
                    // return $this->dispatcher->forward([
                    //     'controller' => 'index',
                    //     'action' => 'index',
                    // ]);
                }
            }
        }
// echo '<pre>'; var_dump('test'); echo '</pre>'; 
// die();         
        $this->view->form = $form;
        $this->view->pick("login/changePassword");
        $this->view->setRenderLevel(View::LEVEL_BEFORE_TEMPLATE);
    }
    
    public function logoutAction()
    {
        $this->view->disable();
        (new AdminLogs)->addLog($this->session->get('user')['username'], 'Logout', 'Logout successfully.');
        $this->session->destroy();
        $this->security->hash(rand());
        $this->response->redirect('Login');
    }

}

