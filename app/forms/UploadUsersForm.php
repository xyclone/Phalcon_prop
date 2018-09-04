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

use Property\Models\BaseUsergroup;


class UploadUsersForm extends Form
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

        //Upload File
        $csvFile = new File('csv_file');
        $csvFile->setLabel('');
        $csvFile->setAttributes([
                "class"=>"form-control file",
                "data-min-file-count"=>1,
                "data-allowed-file-extensions"=>'["csv"]',
                "data-show-preview"=>"false",
                "data-show-details"=>"false",
                "data-show-upload"=>"false",
                "data-browse-label"=>"",
                "data-remove-label"=>"&nbsp;"
            ]);
        $csvFile->setUserOption('group-req','');
        $csvFile->setUserOption('label-width','col-xs-12');
        $csvFile->setUserOption('input-width','col-xs-12');
        $csvFile->setUserOption('has_notes',true);
        $csvFile->setUserOption('notes','* Extensions allowed: ".csv"');
        $csvFile->setFilters(array('striptags', 'trim', 'string'));
        $this->add($csvFile);
	}
}