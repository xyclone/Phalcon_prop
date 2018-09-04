<?php
namespace Property\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Textarea;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\File;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class AgencyForm extends Form
{
    /**
     * [initialize description]
     * @param  [type] $entity  [description]
     * @param  [type] $options [description]
     * @return [type]          [description]
     */ //echo '<pre>'; var_dump($model); echo '</pre>'; die();  
    public function initialize($model = null, $options = null)
    {
        $bMode = 0; //0:new, 1:update,
        if (isset($options['mode'])) {
            if($options['mode'] == 'update'){
                $bMode = 1;
            }
        }    	

        // Full
        $name = new Text('name');
        $name->setLabel('');
        $name->setAttributes([
          'class' => 'form-control required',
          'placeholder' => 'Name'
        ]);
        $name->setUserOption('label-width','col-xs-12');
        $name->setUserOption('input-width','col-xs-12');
        $name->setFilters(array('striptags', 'trim', 'string'));
        $this->add($name);

        // Email
        $description = new Text('description');
        $description->setLabel('');
        $description->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Description'
        ]);
        $description->setUserOption('label-width','col-xs-12');
        $description->setUserOption('input-width','col-xs-12');
        $description->setFilters(array('striptags', 'trim', 'string'));
        $this->add($description);
    }
}