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

use Property\Models\Uploads;


class UploadProjectForm extends Form
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
                $user_id = new Hidden("id");
                $user_id->setDefault($model->id);
                $user_id->setUserOption('ishidden','hidden');
                $this->add($user_id);
            }
        } 

        // Upload Type
        $type_options = ['project'=>'Projects','per_project'=>'Details Per Project','per_unit'=>'Details Per Unit'];
        $type = new Select('type', $type_options);
        $type->setLabel('');
        $type->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => '- Select Upload Type -',
            'useEmpty'  => true,
            'emptyText' => '- Select Tenure -',
            'emptyValue'=> '',
        ]);
        $type->setUserOption('label-width','col-xs-12');
        $type->setUserOption('input-width','col-xs-12');
        $type->setFilters(array('striptags', 'trim', 'string'));
        $this->add($type);

        //Upload File
        $csvs_label = "Upload Project";;
        $csvs = new File('csvs');
        $csvs->setLabel('');
        $csvs->setAttributes([
                "class"=>"form-control file",
                "data-min-file-count"=>1,
                "data-show-caption"=>"true",
                "placeholder"=>"Upload Photo",
                "data-allowed-file-extensions"=>'["csv"]',
                "data-show-preview"=>"false",
                "data-show-details"=>"false",
                "data-show-upload"=>"false",
                "data-browse-label"=>"",
                "data-remove-label"=>"&nbsp;"
            ]);
        $csvs->setUserOption('group-req','');
        $csvs->setUserOption('label-width','col-xs-12');
        $csvs->setUserOption('input-width','col-xs-12');
        $csvs->setUserOption('has_notes',true);
        $csvs->setUserOption('notes','* Extensions allowed: ".csv".');
        $csvs->setFilters(array('striptags', 'trim', 'string'));
        $this->add($csvs);
    }
}