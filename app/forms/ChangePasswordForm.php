<?php
namespace Property\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class ChangePasswordForm extends Form
{

    public function initialize()
    {

        $password = new Password('password');
        $password->setLabel('');
        $password->setAttributes([
                'class'=>'form-control',
                'placeholder' => 'Password'
            ]);
        $password->setUserOption('group-req','required');
        $password->setUserOption('has-feedback','has-feedback');
        $password->setUserOption('has-icon','<span class="glyphicon glyphicon-glyphicon-lock form-control-feedback"></span>'); 
        $password->setFilters(array('striptags', 'trim', 'string'));
        $password->addValidators([
            new PresenceOf([
                'message' => 'Password is required'
            ]),
            new StringLength([
                'min' => 8,
                'messageMinimum' => 'Password is too short. Minimum 8 characters'
            ]),
            new Confirmation([
                'message' => 'Password doesn\'t match confirmation',
                'with' => 'confirmPassword'
            ])
        ]);
        $this->add($password);        

        $confirm_password = new Password('confirmPassword');
        $confirm_password->setLabel('');
        $confirm_password->setAttributes([
                'class'=>'form-control',
                'placeholder' => 'Confirm Password'
            ]);
        $confirm_password->setUserOption('group-req','required');
        $confirm_password->setUserOption('has-feedback','has-feedback');
        $confirm_password->setUserOption('has-icon','<span class="glyphicon glyphicon-glyphicon-lock form-control-feedback"></span>'); 
        $confirm_password->setFilters(array('striptags', 'trim', 'string'));
        $confirm_password->addValidators([
            new PresenceOf([
                'message' => 'The confirmation password is required'
            ]),
        ]);
        $this->add($confirm_password);      
    }
}
