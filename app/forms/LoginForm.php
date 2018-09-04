<?php
namespace Property\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Mvc\Model\Validator\StringLength;

class LoginForm extends Form
{

    public function initialize()
    {
        // Email
        $email = new Text('username');
        $email->setLabel('');
        $email->setAttributes([
                'class'=>'form-control',
                'placeholder' => 'Username'
            ]);
        $email->setUserOption('group-req','required');
        $email->setUserOption('has-feedback','has-feedback');
        $email->setUserOption('has-icon','<span class="glyphicon glyphicon-user form-control-feedback"></span>'); 
        $email->setFilters(array('striptags', 'trim', 'string'));
        $email->addValidators([
            new PresenceOf([
                'message' => 'The e-mail is required'
            ]),
            new Email([
                'message' => 'The e-mail is not valid'
            ])
        ]);
        $this->add($email);

        // Password
        $password = new Password('password', [
            'placeholder' => 'Password'
        ]);

        $password = new Password('password');
        $password->setLabel('');
        $password->setAttributes([
                'class'=>'form-control',
                'placeholder' => 'Password'
            ]);
        $password->setUserOption('group-req','required');
        $password->setUserOption('has-feedback','has-feedback'); 
        $password->setUserOption('has-icon','<span class="glyphicon glyphicon-lock form-control-feedback"></span>'); 
        $password->setFilters(array('striptags', 'trim', 'string'));
        $password->addValidator(
            new PresenceOf([
                'message' => 'The password is required'
            ])
        );        
        $password->clear();
        $this->add($password);

        //Remember
        // $remember = new Check('remember');
        // $remember->setLabel('Remember me');
        // $remember->setUserOption('ishidden','');
        // $remember->setUserOption('label-width','col-sm-8');
        // $remember->setUserOption('input-width','col-sm-4');
        // $remember->setAttributes([
        //         'class'=>'form-control',
        //     ]);
        // $this->add($remember);

        // CSRF
        // $csrf = new Hidden($this->security->getTokenKey());
        // $csrf->addValidator(new Identical([
        //     'message' => 'CSRF validation failed',
        //     'class' => 'hidden'
        // ]));
        // $csrf->setFilters(array('striptags', 'trim', 'string'));
        // $csrf->setDefault($this->security->getToken());
        // $csrf->clear();
        // $this->add($csrf);

        // $this->add(new Button('Sign In', [
        //     'class' => 'btn btn-block btn-primary'
        // ]));
    }
}
