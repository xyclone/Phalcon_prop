<?php
namespace Property\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

class ForgotPasswordForm extends Form
{

    public function initialize()
    {
        $email = new Text('email');
        $email->setLabel('');
        $email->setAttributes([
                'class'=>'form-control',
                'placeholder' => 'Email Address'
            ]);
        $email->setUserOption('group-req','required');
        $email->setUserOption('has-feedback','has-feedback');
        $email->setUserOption('has-icon','<span class="glyphicon glyphicon-envelope form-control-feedback"></span>'); 
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

    }
}
