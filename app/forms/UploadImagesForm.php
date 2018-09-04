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
use Property\Models\Projects;
use Property\Models\ProjectTypes;
use Property\Models\ProjectPropTypes;

class UploadImagesForm extends Form
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

        // Project Type
        $projects_options = [];
        $projects_query = ProjectTypes::find(["columns"=>"id,name","order"=>"id asc"]);
        foreach ($projects_query as $key => $value) $projects_options[$value->id] = $value->name;
        $project_type = new Select('project_type', $projects_options);
        $project_type->setLabel('Project Type');
        $project_type->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Project Type',
            'required' => 'required',
            'useEmpty'  => true,
            'emptyText' => '- Select Project Type -',
            'emptyValue'=> '',
        ]);
        $project_type->setUserOption('label-width','col-xs-12');
        $project_type->setUserOption('input-width','col-xs-12');
        $project_type->setFilters(array('striptags', 'trim', 'string'));
        $project_type->setDefault(1);
        $this->add($project_type);

        // Project Property Type
        $prop_type_options=[];
        $project_prop_type = ProjectPropTypes::find(["columns"=>"DISTINCT(project_type_id) project_type_id","order"=>"project_type_id asc"]);
        //echo '<pre>'; var_dump($project_prop_type); echo '</pre>'; die();
        if($project_prop_type) {
            foreach($project_prop_type as $prop => $pt) {
                $project_prop_query = ProjectPropTypes::find(["columns"=>"id,project_property_type",
                    "conditions"=>"project_type_id=:pt:","order"=>"id desc",
                    "bind"=>["pt"=>$pt->project_type_id]
                ]);
                $prop_type_options[$pt->project_type_id] = [];
                //$prop_type_options[$prop]["children"] = [];
                if($project_prop_query&&$project_prop_query->count()>0) {
                    foreach($project_prop_query as $key => $field) {
                        $prop_type_options[$pt->project_type_id][$key] = ['id'=>$field->id,'text'=>$field->project_property_type];
                    }
                }
            }
        }
        $prop_type_fieldoptions = new Hidden('prop_type_fieldoptions');
        $prop_type_fieldoptions->setUserOption('ishidden', 'hidden');
        $prop_type_fieldoptions->setFilters(array('striptags', 'trim', 'string'));
        $prop_type_fieldoptions->setDefault(json_encode($prop_type_options));
        $this->add($prop_type_fieldoptions);

        //Actual Select
        $project_property_type = new Select('project_property_type', []);
        $project_property_type->setLabel('Project Property Type');
        $project_property_type->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Select Project Property Type',
            'required' => 'required',
            'useEmpty'  => true,
            'emptyText' => '- Select Project Property Type -',
            'emptyValue'=> '',
        ]);
        $project_property_type->setUserOption('label-width','col-xs-12');
        $project_property_type->setUserOption('input-width','col-xs-12');
        $project_property_type->setFilters(array('striptags', 'trim', 'string'));
        $this->add($project_property_type);

        // Project
        $proj_options = [];
        $proj_query = Projects::find(["columns"=>"id,project_name","order"=>"project_name ASC"]);
        if($proj_query&&$proj_query->count()>0) {
            foreach ($proj_query as $key => $value) {
                $proj_options[$value->id] = $value->project_name;
            }
        }
        $project = new Select('project_id', $proj_options);
        $project->setLabel('');
        $project->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => '- Select Project -',
            'useEmpty'  => true,
            'emptyText' => '- Select Project -',
            'emptyValue'=> '',
        ]);
        $project->setUserOption('label-width','col-xs-12');
        $project->setUserOption('input-width','col-xs-12');
        $project->setFilters(array('striptags', 'trim', 'string'));
        $this->add($project);

        //Upload File
        $image_label = "Upload Project Images";;
        $image = new File('images[]');
        $image->setLabel('');
        $image->setAttributes([
                "id"=>"images",
                "multiple" => true,
                "class"=>"form-control file",
                "data-min-file-count"=>1,
                "data-show-caption"=>"true",
                "placeholder"=>"Upload Photo",
                "data-allowed-file-extensions"=>'["jpg","png","jpeg"]',
                "data-show-preview"=>"false",
                "data-show-details"=>"false",
                "data-show-upload"=>"false",
                "data-browse-label"=>"",
                "data-remove-label"=>"&nbsp;"
            ]);
        $image->setUserOption('group-req','');
        $image->setUserOption('label-width','col-xs-12');
        $image->setUserOption('input-width','col-xs-12');
        $image->setUserOption('has_notes',true);
        $image->setUserOption('notes','* Extensions allowed: "jpg","png","jpeg".');
        $image->setFilters(array('striptags', 'trim', 'string'));
        $this->add($image);
    }
}