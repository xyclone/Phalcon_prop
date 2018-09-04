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

class ProjectsForm extends Form
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


        if(!empty($options['fields'])&&count($options['fields'])>0) {
        	foreach ($options['fields'] as $label => $value) {
        		switch($value[0]) {
        			case 'project_name':
				        $projname = new Text('name');
				        $projname->setLabel('');
				        $projname->setAttributes([
				          'class' => 'form-control required',
				          'placeholder' => 'Project Name'
				        ]);
				        $projname->setUserOption('label-width','col-xs-12');
				        $projname->setUserOption('input-width','col-xs-12');
				        $projname->setFilters(array('striptags', 'trim', 'string'));
				        $this->add($projname);
        			break;

        			default:
        			break;
        		}
        	}
        }
	}
}