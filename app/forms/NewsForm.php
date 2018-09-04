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

class NewsForm extends Form
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

        // Name
        $name = new Text('name');
        $name->setLabel('');
        $name->setAttributes([
          'class' => 'form-control required',
          'placeholder' => 'Title'
        ]);
        $name->setUserOption('label-width','col-xs-12');
        $name->setUserOption('input-width','col-xs-12');
        $name->setFilters(array('striptags', 'trim', 'string'));
        $this->add($name);

        // Link
        $link = new Text('link');
        $link->setLabel('');
        $link->setAttributes([
          'class' => 'form-control required',
          'placeholder' => 'Link'
        ]);
        $link->setUserOption('label-width','col-xs-12');
        $link->setUserOption('input-width','col-xs-12');
        $link->setFilters(array('striptags', 'trim', 'string'));
        $this->add($link);

        // Message
        $message = new Textarea('news');
        $message->setLabel('');
        $message->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Message...'
        ]);
        $message->setUserOption('label-width','col-xs-12');
        $message->setUserOption('input-width','col-xs-12');
        $message->setFilters(array('striptags', 'trim', 'string'));
        $this->add($message);

        // Start Date
        $start_date = new Text('start_date');
        $start_date->setLabel('');
        $start_date->setAttributes([
          'class' => 'form-control required',
          'placeholder' => 'Start Date'
        ]);
        $start_date->setUserOption('group-addon-prefix', '<i class="fa fa-calendar fa-fw" aria-hidden="true"></i>');
        $start_date->setUserOption('label-width','col-xs-12');
        $start_date->setUserOption('input-width','col-xs-12');
        $start_date->setFilters(array('striptags', 'trim', 'string'));
        $this->add($start_date);

        // Start Date
        $stop_date = new Text('stop_date');
        $stop_date->setLabel('');
        $stop_date->setAttributes([
          'class' => 'form-control required',
          'placeholder' => 'Start Date'
        ]);
        $stop_date->setUserOption('group-addon-prefix', '<i class="fa fa-calendar fa-fw" aria-hidden="true"></i>');
        $stop_date->setUserOption('label-width','col-xs-12');
        $stop_date->setUserOption('input-width','col-xs-12');
        $stop_date->setFilters(array('striptags', 'trim', 'string'));
        $this->add($stop_date);
    }
}