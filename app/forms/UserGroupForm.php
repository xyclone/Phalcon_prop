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

use Property\Classes\UsergroupClass;

class UserGroupForm extends Form
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
                $group_id = new Hidden("id");
                $group_id->setDefault($model->id);
                $group_id->setUserOption('ishidden','hidden');
                $this->add($group_id);
            }
        }    	

        // Profile
        $usergroup = new Text('usergroup');
        $usergroup->setLabel('Profile Name');
        $usergroup->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Profile'
        ]);
        $usergroup->setUserOption('group-req','required');
        $usergroup->setUserOption('label-width','col-xs-12');
        $usergroup->setUserOption('input-width','col-xs-12');
        $usergroup->setFilters(array('striptags', 'trim', 'string'));
        $usergroup->addValidators(array(
            new PresenceOf(array(
                "message" => "Usergroup is required"
            ))
        ));
        $this->add($usergroup);

        // Description
        $description = new Textarea('description');
        $description->setLabel('Description');
        $description->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Description...'
        ]);
        $description->setUserOption('group-req','');
        $description->setUserOption('label-width','col-xs-12');
        $description->setUserOption('input-width','col-xs-12');
        $description->setFilters(array('striptags', 'trim', 'string'));
        $description->addValidators(array(
            new PresenceOf(array(
                "message" => "Description is required"
            ))
        ));
        $this->add($description);

        // Icon
        $icon_selections = [];
        $icon_options = UsergroupClass::$icon_options;
        foreach ($icon_options as $key => $value) {
            $icon_selections[$key] = $key;
        }
        $icon = new Select('icon', $icon_selections);
        $icon->setLabel('Icon');
        $icon->setAttributes([
            'class' => 'form-control select2 required',
            'placeholder' => 'Icon',
            'useEmpty' => true,
            'emptyText' => '- Select -',
            'emptyValue' => '',
        ]);
        $icon->setUserOption('group-req','required');
        $icon->setUserOption('label-width','col-xs-12');
        $icon->setUserOption('input-width','col-xs-12');
        $icon->setFilters(array('striptags', 'trim', 'string'));
        $this->add($icon);

        // Color
        // $color = new Text('color');
        // $color->setLabel('Color');
        // $color->setAttributes([
        //   'class' => 'form-control',
        //   'placeholder' => 'Color'
        // ]);
        // $color->setUserOption('group-req','');
        // $color->setUserOption('label-width','col-xs-12');
        // $color->setUserOption('input-width','col-xs-12');
        // $color->setFilters(array('striptags', 'trim', 'string'));
        // $this->add($color);

    }
}