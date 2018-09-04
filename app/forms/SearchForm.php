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
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\StringLength;

use Property\Models\BaseUsergroup;
use Property\Models\ProjectPropTypes;
use Property\Models\ProjectTypes;
use Property\Models\PropertyTenures;
use Property\Models\PropertyTypes;
use Property\Models\PropertyUnits;
use Property\Models\PropertyDistricts;
use Property\Classes\PropertyClass;


class SearchForm extends Form
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
        $projects_query = ProjectTypes::find(["columns"=>"name","order"=>"name asc"]);
        foreach ($projects_query as $key => $value)
            $projects_options[$value->name] = $value->name;
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
        $project_type->setUserOption('width','col-xs-12');
        //$project_type->setUserOption('label-width','col-xs-12');
        $project_type->setUserOption('input-width','col-xs-12');
        $project_type->setFilters(array('striptags', 'trim', 'string'));
        $project_type->setDefault(1);
        $this->add($project_type);

        // Project Property Type
        $projprop_type_options=[];
        $projproptype_query = ProjectPropTypes::find(["columns"=>"name","order"=>"name asc"]);
        foreach ($projproptype_query as $key => $value)
            $projprop_type_options[$value->name] = $value->name;
        $projprop_type = new Select('project_type', $projprop_type_options);
        $projprop_type->setLabel('Project Type');
        $projprop_type->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Project Type',
            'required' => 'required',
            'useEmpty'  => true,
            'emptyText' => '- Select Project Property Type -',
            'emptyValue'=> '',
        ]);
        $projprop_type->setUserOption('width','col-xs-12');
        //$projprop_type->setUserOption('label-width','col-xs-12');
        $projprop_type->setUserOption('input-width','col-xs-12');
        $projprop_type->setFilters(array('striptags', 'trim', 'string'));
        $projprop_type->setDefault(1);
        $this->add($projprop_type);
        // $project_prop_type = ProjectPropTypes::find(["columns"=>"DISTINCT(project_type) project_type","order"=>"project_type asc"]);
        // //echo '<pre>'; var_dump($project_prop_type); echo '</pre>'; die();
        // if($project_prop_type) {
        //     foreach($project_prop_type as $prop => $pt) {
        //         $project_prop_query = ProjectPropTypes::find(["columns"=>"project_property_type,project_property_type",
        //             "conditions"=>"project_type=:pt:","order"=>"project_property_type desc",
        //             "bind"=>["pt"=>$pt->project_type]
        //         ]);
        //         $prop_type_options[$pt->project_type] = [];
        //         //$prop_type_options[$prop]["children"] = [];
        //         if($project_prop_query&&$project_prop_query->count()>0) {
        //             foreach($project_prop_query as $key => $field) {
        //                 $prop_type_options[$pt->project_type][$key] = ['id'=>$field->project_property_type,'text'=>$field->project_property_type];
        //             }
        //         }
        //     }
        // }
        // $prop_type_fieldoptions = new Hidden('prop_type_fieldoptions');
        // $prop_type_fieldoptions->setUserOption('ishidden', 'hidden');
        // $prop_type_fieldoptions->setFilters(array('striptags', 'trim', 'string'));
        // $prop_type_fieldoptions->setDefault(json_encode($prop_type_options));
        // $this->add($prop_type_fieldoptions);

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
        $project_property_type->setUserOption('width','col-xs-12');
        //$project_property_type->setUserOption('label-width','col-xs-12');
        $project_property_type->setUserOption('input-width','col-xs-12');
        $project_property_type->setFilters(array('striptags', 'trim', 'string'));
        $this->add($project_property_type);

        // Project Name
        $project_name = new Text('project_name');
        $project_name->setLabel('Project Name');
        $project_name->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Project Name'
        ]);
        $project_name->setUserOption('width','col-xs-6');
        //$project_name->setUserOption('label-width','col-xs-6');
        $project_name->setUserOption('input-width','col-xs-12');
        $project_name->setFilters(array('striptags', 'trim', 'string'));
        $project_name->addValidators(array(
            new PresenceOf(array(
                "message" => "Usergroup is required"
            ))
        ));
        $this->add($project_name);

        // Districts
        $prop_district_options = [];
        $prop_district_query = PropertyDistricts::find(["columns"=>"name,description","order"=>"name asc"]);
        foreach ($prop_district_query as $key => $value)
            $prop_district_options[$value->name] = $value->name . " (".self::truncate($value->description, 25).")";
        $district = new Select('districts', $prop_district_options);
        $district->setLabel('District');
        $district->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Select District',
            'multiple'  => 'multiple',
            'useEmpty'  => true,
            'emptyText' => '- Select District -',
            'emptyValue'=> '',
        ]);
        $district->setUserOption('width','col-xs-6');
        //$district->setUserOption('label-width','col-xs-6');
        $district->setUserOption('input-width','col-xs-12');
        $district->setFilters(array('striptags', 'trim', 'string'));
        $district->addValidators(array(
            new PresenceOf(array(
                "message" => "District is required"
            ))
        ));
        $this->add($district);

        // Planning Region
        $planning_region = new Text('planning_region');
        $planning_region->setLabel('Planning Region');
        $planning_region->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Planning Region',
        ]);
        $planning_region->setUserOption('width','col-xs-6');
        //$planning_region->setUserOption('label-width','col-xs-6');
        $planning_region->setUserOption('input-width','col-xs-12');
        $planning_region->setFilters(array('striptags', 'trim', 'string'));
        $planning_region->addValidators(array(
            new PresenceOf(array(
                "message" => "Planning Region is required"
            ))
        ));
        $this->add($planning_region);

        // Planning Area
        $planning_area = new Text('planning_area');
        $planning_area->setLabel('Planning Area');
        $planning_area->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Planning Area',
        ]);
        $planning_area->setUserOption('width','col-xs-6');
        //$planning_area->setUserOption('label-width','col-xs-6');
        $planning_area->setUserOption('input-width','col-xs-12');
        $planning_area->setFilters(array('striptags', 'trim', 'string'));
        $planning_area->addValidators(array(
            new PresenceOf(array(
                "message" => "Planning Area is required"
            ))
        ));
        $this->add($planning_area);

        // Property Type
        $properties_options = [];
        $properties_query = PropertyTypes::find(["columns"=>"name","order"=>"name asc"]);
        foreach ($properties_query as $key => $value)
            $properties_options[$value->name] = $value->name;
        $property_type = new Select('property_type_id', $properties_options);
        $property_type->setLabel('Property Type');
        $property_type->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Property Type',
            'useEmpty'  => true,
            'emptyText' => '- Select Property Type -',
            'emptyValue'=> '',
        ]);
        $property_type->setUserOption('width','col-xs-6');
        //$property_type->setUserOption('label-width','col-xs-6');
        $property_type->setUserOption('input-width','col-xs-12');
        $property_type->setFilters(array('striptags', 'trim', 'string'));
        $property_type->addValidators(array(
            new PresenceOf(array(
                "message" => "Property Type is required"
            ))
        ));
        $this->add($property_type);

        // Unit Type
        $units_options = [];
        $units_query = PropertyUnits::find(["columns"=>"name,description","order"=>"name asc"]);
        foreach ($units_query as $key => $value)
            $units_options[$value->name] = $value->name;
        $unit_type = new Select('unit_type[]', $units_options);
        $unit_type->setLabel('Unit Type');
        $unit_type->setAttributes([
            'id' => 'unit_type',
            'class' => 'form-control select2',
            'multiple' => 'multiple',
            'placeholder' => 'Unit Type',
            'useEmpty'  => true,
            'emptyText' => '- Select Unit Type -',
            'emptyValue'=> '',
        ]);
        $unit_type->setUserOption('width','col-xs-6');
        //$unit_type->setUserOption('label-width','col-xs-6');
        $unit_type->setUserOption('input-width','col-xs-12');
        $unit_type->setFilters(array('striptags', 'trim', 'string'));
        $unit_type->addValidators(array(
            new PresenceOf(array(
                "message" => "Unit Type is required"
            ))
        ));
        $this->add($unit_type);
       

        // Min Budget
        $min_budget = new Text('min_budget');
        $min_budget->setLabel('Min Budget');
        $min_budget->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Min Budget'
        ]);
        $min_budget->setUserOption('width','col-xs-6');
        //$min_budget->setUserOption('label-width','col-xs-6');
        $min_budget->setUserOption('input-width','col-xs-12');
        $min_budget->setFilters(array('striptags', 'trim', 'string'));
        $min_budget->addValidators(array(
            new PresenceOf(array(
                "message" => "Min Budget is required"
            ))
        ));
        $this->add($min_budget);

        // Max Budget
        $max_budget = new Text('max_budget');
        $max_budget->setLabel('Max Budget');
        $max_budget->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Max Budget'
        ]);
        $max_budget->setUserOption('width','col-xs-6');
        //$max_budget->setUserOption('label-width','col-xs-6');
        $max_budget->setUserOption('input-width','col-xs-12');
        $max_budget->setFilters(array('striptags', 'trim', 'string'));
        $max_budget->addValidators(array(
            new PresenceOf(array(
                "message" => "Max Budget is required"
            ))
        ));
        $this->add($max_budget);

        // Min Area (sf)
        $min_area = new Text('min_area');
        $min_area->setLabel('Min Area (sf)');
        $min_area->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Min Area (sf)'
        ]);
        $min_area->setUserOption('width','col-xs-6');
        //$min_area->setUserOption('label-width','col-xs-6');
        $min_area->setUserOption('input-width','col-xs-12');
        $min_area->setUserOption('postfix-addon', true);
        $min_area->setUserOption('postfix-label', 'ft&sup2;');
        $min_area->setFilters(array('striptags', 'trim', 'string'));
        $min_area->addValidators(array(
            new PresenceOf(array(
                "message" => "Min Area (sf) is required"
            ))
        ));
        $this->add($min_area);

        // Max Area (sf)
        $max_area = new Text('max_area');
        $max_area->setLabel('Max Area (sf)');
        $max_area->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Max Area (sf)'
        ]);
        $max_area->setUserOption('width','col-xs-6');
        //$max_area->setUserOption('label-width','col-xs-6');
        $max_area->setUserOption('input-width','col-xs-12');
        $max_area->setUserOption('postfix-addon', true);
        $max_area->setUserOption('postfix-label', 'ft&sup2;');
        $max_area->setFilters(array('striptags', 'trim', 'string'));
        $max_area->addValidators(array(
            new PresenceOf(array(
                "message" => "Max Area (sf) is required"
            ))
        ));
        $this->add($max_area);
  
        // Tenure
        $tenures_options=[];
        $tenures_query = PropertyTenures::find();
        foreach ($tenures_query as $key => $value)
            $tenures_options[$value->name] = $value->name;
        $tenure = new Select('tenure_id', $tenures_options);
        $tenure->setLabel('Tenure');
        $tenure->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Tenure',
            'useEmpty'  => true,
            'emptyText' => '- Select Tenure -',
            'emptyValue'=> '',
        ]);
        $tenure->setUserOption('width','col-xs-6');
        //$tenure->setUserOption('label-width','col-xs-6');
        $tenure->setUserOption('input-width','col-xs-12');
        $tenure->setFilters(array('striptags', 'trim', 'string'));
        $tenure->addValidators(array(
            new PresenceOf(array(
                "message" => "Tenure is required"
            ))
        ));
        $this->add($tenure);

        // TOP
        $top = new Text('top');
        $top->setLabel('TOP');
        $top->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'TOP'
        ]);
        $top->setUserOption('width','col-xs-6');
        //$top->setUserOption('label-width','col-xs-6');
        $top->setUserOption('input-width','col-xs-12');
        $top->setFilters(array('striptags', 'trim', 'string'));
        $top->addValidators(array(
            new PresenceOf(array(
                "message" => "TOP is required"
            ))
        ));
        $this->add($top);      

        // MRT
        $mrt_options = [];
        $mrt_query = PropertyClass::$mrt_stations;
        foreach ($mrt_query as $key => $value)
            $mrt_options[$value['code']] = $value['en']." (".$value['code'].")"; 
        $mrt = new Select('mrt', $mrt_options);
        $mrt->setLabel('MRT/LRT');
        $mrt->setAttributes([
          'class' => 'form-control select2',
          'placeholder' => 'MRT/LRT',
          'multiple' => true
        ]);
        $mrt->setUserOption('width','col-xs-6');
        //$mrt->setUserOption('label-width','col-xs-6');
        $mrt->setUserOption('input-width','col-xs-12');
        $mrt->setFilters(array('striptags', 'trim', 'string'));
        $mrt->addValidators(array(
            new PresenceOf(array(
                "message" => "MRT/LRT is required"
            ))
        ));
        $this->add($mrt);      

        // Street Name
        $street_name = new Text('street_name');
        $street_name->setLabel('Street Name');
        $street_name->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Street Name'
        ]);
        $street_name->setUserOption('width','col-xs-6');
        //$street_name->setUserOption('label-width','col-xs-6');
        $street_name->setUserOption('input-width','col-xs-12');
        $street_name->setFilters(array('striptags', 'trim', 'string'));
        $street_name->addValidators(array(
            new PresenceOf(array(
                "message" => "Street Name is required"
            ))
        ));
        $this->add($street_name);      

        // Primary School 
        $primary_school = new Text('primary_school_within_1km');
        $primary_school->setLabel('Primary School');
        $primary_school->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Primary School within 1km'
        ]);
        $primary_school->setUserOption('width','col-xs-6');
        //$primary_school->setUserOption('label-width','col-xs-12');
        $primary_school->setUserOption('input-width','col-xs-12');
        $primary_school->setFilters(array('striptags', 'trim', 'string'));
        $primary_school->addValidators(array(
            new PresenceOf(array(
                "message" => "Street Name is required"
            ))
        ));
        $this->add($primary_school);     

        // Total Units
        $total_units = new Text('total_units');
        $total_units->setLabel('Total #Units');
        $total_units->setAttributes([
          'class' => 'form-control',
          'placeholder' => 'Total #Units'
        ]);
        $total_units->setUserOption('width','col-xs-6');
        //$total_units->setUserOption('label-width','col-xs-6');
        $total_units->setUserOption('input-width','col-xs-12');
        $total_units->setFilters(array('striptags', 'trim', 'string'));
        $total_units->addValidators(array(
            new PresenceOf(array(
                "message" => "Total #Units is required"
            ))
        ));
        $this->add($total_units);   

         // Transactions
        $transaction = new Check('transaction');
        $transaction->setLabel('Transactions');
        $transaction->setAttributes([
          'class' => 'form-control input-sm',
          'placeholder' => 'Transactions'
        ]);
        $transaction->setUserOption('width','pull-right col-xs-6');
        //$transaction->setUserOption('label-width','col-xs-6');
        $transaction->setUserOption('input-width','col-xs-12');
        $transaction->setUserOption('funkyCheckbox', true);
        $transaction->setFilters(array('striptags', 'trim', 'string'));
        $transaction->addValidators(array(
            new PresenceOf(array(
                "message" => "Total #Units is required"
            ))
        ));
        $this->add($transaction);  

         // Transactions
        $status = new Check('status');
        $status->setLabel('Status');
        $status->setAttributes([
          'class' => 'form-control input-sm',
          'placeholder' => 'Transactions'
        ]);
        $status->setUserOption('width','col-xs-6');
        //$status->setUserOption('label-width','col-xs-6');
        $status->setUserOption('input-width','col-xs-12');
        $status->setUserOption('funkyCheckbox', true);
        $status->setFilters(array('striptags', 'trim', 'string'));
        $status->addValidators(array(
            new PresenceOf(array(
                "message" => "Total #Units is required"
            ))
        ));
        $this->add($status);  
    }

    /**
     * [truncate description]
     * @param  [type] $str   [description]
     * @param  [type] $width [description]
     * @return [type]        [description]
     */
    private static function truncate($str, $width) {
        return strtok(wordwrap($str, $width, " ...\n"), "\n");
    }
}