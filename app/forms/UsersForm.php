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

use Property\Models\BaseUsergroup;


class UsersForm extends Form
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

        // Full
        $fullname = new Text('name');
        $fullname->setLabel('');
        $fullname->setAttributes([
          'class' => 'form-control',
          'id'=>$this->RandomString(),
          'placeholder' => 'Full Name'
        ]);
        $fullname->setUserOption('label-width','col-xs-12');
        $fullname->setUserOption('input-width','col-xs-12');
        $fullname->setUserOption('group-req','required');
        $fullname->setFilters(array('striptags', 'trim', 'string'));
        $fullname->addValidators(array(
            new PresenceOf(array(
                "message" => "Fullname is required"
            ))
        ));
        $this->add($fullname);

        // Email
        $email = new Text('email');
        $email->setLabel('');
        $email->setAttributes([
          'class' => 'form-control',
          'id'=>$this->RandomString(),
          'placeholder' => 'Email Address'
        ]);
        $email->setUserOption('label-width','col-xs-12');
        $email->setUserOption('input-width','col-xs-12');
        $email->setUserOption('group-req','required');
        $email->setFilters(array('striptags', 'trim', 'string'));
        $email->addValidators(array(
            new PresenceOf([
                "message" => "Email is required"
            ]),
            new Email([
                "message" => "The e-mail is not valid",
            ])
        ));
        $this->add($email);
        
        // Password
        if($bMode===1) $model->password=null;
        $password = new Password('password');
        $password->setLabel('');
        $password->setAttributes([
          'class' => 'form-control',
          'id'=>$this->RandomString(),
          'placeholder' => ($bMode===1) ? 'New Password' : 'Password'
        ]);
        $password->setUserOption('label-width','col-xs-12');
        $password->setUserOption('input-width','col-xs-12');
        $password->setUserOption('group-req','required');
        $password->setFilters(array('striptags', 'trim', 'string'));
        $password->addValidators(array(
            new PresenceOf(array(
                "message" => "Password is required"
            )),
            new StringLength([
                'min' => 8,
                'messageMinimum' => 'Password is too short. Minimum 8 characters'
            ]),
            new Confirmation([
                'message' => 'Password doesn\'t match confirmation',
                'with' => 'confirmPassword'
            ])
        ));
        $this->add($password);

        // Confirm Password
        $confirm_password = new Password('confirmPassword');
        $confirm_password->setLabel('');
        $confirm_password->setAttributes([
                'class'=>'form-control',
                'id'=>$this->RandomString(),
                'placeholder' => ($bMode===1) ? 'New Confirm Password' : 'Confirm Password'
            ]);
        $confirm_password->setUserOption('label-width','col-xs-12');
        $confirm_password->setUserOption('input-width','col-xs-12');
        $confirm_password->setUserOption('group-req','required');
        $confirm_password->setFilters(array('striptags', 'trim', 'string'));
        $confirm_password->addValidators([
            new PresenceOf([
                'message' => 'The confirmation password is required'
            ]),
        ]);
        $this->add($confirm_password);    

        // Mobile
        $mobile = new Text('mobile');
        $mobile->setLabel('');
        $mobile->setAttributes([
          'class' => 'form-control',
          'id'=>$this->RandomString(),
          'placeholder' => 'Mobile Number'
        ]);
        $mobile->setUserOption('label-width','col-xs-12');
        $mobile->setUserOption('input-width','col-xs-12');
        $mobile->setUserOption('group-req','required');
        $mobile->setFilters(array('striptags', 'trim', 'string'));
        $mobile->addValidators(array(
            new PresenceOf(array(
                "message" => "Mobile is required"
            ))
        ));
        $this->add($mobile);

        // Username
        // $username = new Text('username');
        // $username->setLabel('Username');
        // $username->setAttributes([
        //   'class' => 'form-control',
        //   'placeholder' => 'Username'
        // ]);
        // $username->setUserOption('label-width','col-xs-12');
        // $username->setUserOption('input-width','col-xs-12');
        // $username->setFilters(array('striptags', 'trim', 'string'));
        // $username->addValidators(array(
        //     new PresenceOf(array(
        //         "message" => "Usergroup is required"
        //     ))
        // ));
        // $this->add($username);

        if($bMode===0) {
            // Usergroup
            $ugroup_Query = BaseUsergroup::find(["columns"=>"id,usergroup","conditions"=>"active='Y'"]);
            $usergroup_options = [];
            if($ugroup_Query&&$ugroup_Query->count()>0)
                foreach($ugroup_Query as $key => $value)
                    $usergroup_options[$value->id] = $value->usergroup;
            $usergroup = new Select('usergroup[]', $usergroup_options);
            $usergroup->setLabel('');
            $usergroup->setAttributes([
                    'class'=>'form-control select2',
                    'id'=>$this->RandomString(),
                    //'multiple' => 'multiple',
                    'useEmpty' => true,
                    'emptyText' => '- Select Profile -',
                    'emptyValue' => '',
                ]);
            $usergroup->setUserOption('group-req','');
            $usergroup->setUserOption('label-width','col-xs-12');
            $usergroup->setUserOption('input-width','col-xs-12');
            $usergroup->setFilters(array('striptags', 'trim', 'string'));
            $usergroup->addValidators(array(
                new PresenceOf(array(
                    "message" => "Usergroup is required"
                ))
            ));
            $this->add($usergroup);    
        }

        //Upload File
        $image_label = "Upload Photo";;
        $image = new File('image');
        $image->setLabel('');
        $image->setAttributes([
                "class"=>"form-control file ignore",
                'id'=>$this->RandomString(),
                "data-min-file-count"=>1,
                "data-show-caption"=>"true",
                "placeholder"=>"Upload Photo",
                "data-allowed-file-extensions"=>'["jpg","jpeg","png"]',
                "data-show-preview"=>"true",
                "data-show-details"=>"false",
                "data-show-upload"=>"false",
                "data-browse-label"=>"",
                "data-remove-label"=>"&nbsp;"
            ]);
        $image->setUserOption('group-req','');
        $image->setUserOption('label-width','col-xs-12');
        $image->setUserOption('input-width','col-xs-12');
        $image->setUserOption('has_notes',true);
        $image->setUserOption('notes','* Extensions allowed: ".jpg",".jpeg",".png".');
        $image->setFilters(array('striptags', 'trim', 'string'));
        $this->add($image);

        // File <input type="file" class="filestyle" name="image" data-size="sm" id="uploadImage2" onchange="PreviewImage(2)">
        // $image = new File('image');
        // $image->setLabel('Upload Photo');
        // $image->setAttributes([
        //     'id' => 'uploadImage1',
        //     'class' => 'form-control filestyle',
        //     'placeholder' => 'Upload Image',
        //     'data-size' => 'sm',
        //     'onchange' => 'PreviewImage(2)'
        // ]);
        // $image->setUserOption('label-width','col-xs-12');
        // $image->setUserOption('input-width','col-xs-12');
        // $image->setFilters(array('striptags', 'trim', 'string'));
        // $image->addValidators(array(
        //     new PresenceOf(array(
        //         "message" => "Usergroup is required"
        //     ))
        // ));
        // $this->add($image);       

        // // Remove Image <input type="hidden" name="remove_image"> 
        // $remove_image = new Hidden('remove_image');
        // $remove_image->setUserOption('ishidden','hidden');
        // $this->add($remove_image);   
    }

    private function RandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}