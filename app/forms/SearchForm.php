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
use Property\Models\Projects;
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
        // $bMode = 0; //0:new, 1:update,
        // if (isset($options['mode'])) {
        //     if($options['mode'] == 'update'){
        //         $bMode = 1;
        //         $user_id = new Hidden("id");
        //         $user_id->setDefault($model->id);
        //         $user_id->setUserOption('ishidden','hidden');
        //         $this->add($user_id);
        //     }
        // }    	

        // =======================
        // Project Type
        $projects_options = [];
        $projects_query = ProjectTypes::find(["columns"=>"name","order"=>"name asc"]);
        foreach ($projects_query as $key => $value)
            $projects_options[$value->name] = $value->name;
        $project_type = new Select('project_type[]', $projects_options);
        $project_type->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Project Type',
            'multiple' => true,
            'required' => 'required',
            'useEmpty'  => false,
            'emptyText' => '- Select Project Type -',
            'emptyValue'=> '',
        ]);
        $project_type->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $project_type->setUserOption('input-width','col-xs-12');
        $project_type->setFilters(array('striptags', 'trim', 'string'));
        $project_type->setDefault('New Sale');
        $this->add($project_type);

        // =======================
        // Project Property Type
        $projprop_type_options=[];
        $projproptype_query = ProjectPropTypes::find(["columns"=>"name","order"=>"name asc"]);
        foreach ($projproptype_query as $key => $value)
            $projprop_type_options[$value->name] = $value->name;
        $project_property_type = new Select('proj_property_type[]', $projprop_type_options);
        $project_property_type->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Select Project Property Type',
            'required' => 'required',
            'multiple' => true,
            'useEmpty'  => false,
            'emptyText' => '- Select Project Property Type -',
            'emptyValue'=> '',
        ]);
        $project_property_type->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $project_property_type->setUserOption('input-width','col-xs-12');
        $project_property_type->setDefault('Residential');
        $project_property_type->setFilters(array('striptags', 'trim', 'string'));
        $this->add($project_property_type);

        // =======================
        // Project Name
        $project_name = new Text('project_name');
        $project_name->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Project Name'
        ]);
        $project_name->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $project_name->setUserOption('input-width','col-xs-12');
        $project_name->setFilters(array('striptags', 'trim', 'string'));
        $project_name->addValidators(array(
            new PresenceOf(array(
                "message" => "Project Name is required"
            ))
        ));
        $this->add($project_name);

        // =======================
        // Total Units
        $total_units = new Hidden('total_units');
        $total_units->setLabel('Total no. of units:');
        $total_units->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Total Units',
        ]);
        $total_units->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $total_units->setUserOption('label-width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $total_units->setUserOption('input-width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $total_units->setUserOption('is-slider',true);
        $total_units->setUserOption('div_slider','TU_state');
        $total_units->setUserOption('div_adv_slide','TU_advance_slide');
        $total_units->setUserOption('slider_min', 0);
        $total_units->setUserOption('slider_max', 1000);
        $total_units->setUserOption('slider_step', 5);
        $total_units->setUserOption('slider_val1', 10);
        $total_units->setUserOption('slider_val2', 100);
        $total_units->setFilters(array('striptags', 'trim', 'string'));
        $this->add($total_units);        

/*
data-slider-min="10" data-slider-max="1000" data-slider-step="5" data-slider-value="[250,450]"
<div class="form-control">
    Filter by price interval: <b>€ 10</b>
     <div class="slider slider-horizontal" id="">
        <div class="slider-track">
            <div class="slider-track-low" style="left: 0px; width: 21.2121%;"></div>
            <div class="slider-selection" style="left: 21.2121%; width: 36.3636%;"></div>
            <div class="slider-track-high" style="right: 0px; width: 42.4242%;"></div>
        </div>
        <div class="tooltip tooltip-main top" role="presentation" style="left: 39.3939%;">
            <div class="tooltip-arrow"></div><div class="tooltip-inner">220 : 580</div>
        </div>
        <div class="tooltip tooltip-min top" role="presentation" style="left: 21.2121%; display: none;">
            <div class="tooltip-arrow"></div>
            <div class="tooltip-inner">220</div>
        </div>
        <div class="tooltip tooltip-max top" role="presentation" style="left: 57.5758%; display: none;">
            <div class="tooltip-arrow"></div>
            <div class="tooltip-inner">580</div>
        </div>
        <div class="slider-handle min-slider-handle round" role="slider" aria-valuemin="10" aria-valuemax="1000" aria-valuenow="220" tabindex="0" style="left: 21.2121%;"></div>
        <div class="slider-handle max-slider-handle round" role="slider" aria-valuemin="10" aria-valuemax="1000" aria-valuenow="580" tabindex="0" style="left: 57.5758%;"></div>
    </div>
    <input id="ex2" type="text" class="span2" value="220,580" data-slider-min="10" data-slider-max="1000" data-slider-step="5" data-slider-value="[250,450]" data-value="220,580" style="display: none;"> <b>€ 1000</b>
           
</div>
 */

        // =======================
        // Districts
        $prop_district_options = [];
        $prop_district_query = PropertyDistricts::find(["columns"=>"name,description","order"=>"name asc"]);
        foreach ($prop_district_query as $key => $value)
            $prop_district_options[$value->name] = $value->name;
        $district = new Select('district[]', $prop_district_options);
        $district->setAttributes([
            'id' => 'district',
            'class' => 'form-control select2',
            'placeholder' => 'Select District',
            'multiple'  => true,
        ]);
        $district->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $district->setUserOption('input-width','col-xs-12');
        $district->setFilters(array('striptags', 'trim', 'string'));
        $this->add($district);

        // =======================
        // Planning Area
        $planning_area = new Select('planning_area[]',[]);
        $planning_area->setAttributes([
            'id' => 'planning_area',
            'class' => 'form-control select2',
            'placeholder' => 'Planning Area',
            'multiple'  => true,
        ]);
        $planning_area->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $planning_area->setUserOption('input-width','col-xs-12');
        $planning_area->setFilters(array('striptags', 'trim', 'string'));
        $this->add($planning_area);

        // =======================
        // Property Type
        $properties_options = [];
        $properties_query = PropertyTypes::find(["columns"=>"name","order"=>"name asc"]);
        foreach ($properties_query as $key => $value)
            $properties_options[$value->name] = $value->name;
        $property_type = new Select('property_type[]', $properties_options);
        $property_type->setAttributes([
            'id' => 'property_type',
            'class' => 'form-control select2',
            'placeholder' => 'Property Type',
            'multiple'  => true,
        ]);
        $property_type->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $property_type->setUserOption('input-width','col-xs-12');
        $property_type->setFilters(array('striptags', 'trim', 'string'));
        $this->add($property_type);

        // =======================
        // Unit Type
        $units_options = [];
        $units_query = PropertyUnits::find(["columns"=>"name,description","order"=>"name asc"]);
        foreach ($units_query as $key => $value)
            $units_options[$value->name] = $value->name;
        $unit_type = new Select('unit_type[]', $units_options);
        $unit_type->setAttributes([
            'id' => 'unit_type',
            'class' => 'form-control select2',
            'multiple'  => true,
            'placeholder' => 'Unit Type',
        ]);
        $unit_type->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $unit_type->setUserOption('input-width','col-xs-12');
        $unit_type->setFilters(array('striptags', 'trim', 'string'));
        $this->add($unit_type);
        
        // =======================
        // Min Budget
        $min_budget = new Text('min_budget');
        $min_budget->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Min Budget'
        ]);
        $min_budget->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $min_budget->setUserOption('input-width','col-xs-12');
        $min_budget->setUserOption('postfix-addon', true);
        $min_budget->setUserOption('postfix-label', 'SGD$');
        $min_budget->setFilters(array('striptags', 'trim', 'string'));
        $this->add($min_budget);

        // =======================
        // Max Budget
        $max_budget = new Text('max_budget');
        $max_budget->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Max Budget'
        ]);
        $max_budget->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $max_budget->setUserOption('input-width','col-xs-12');
        $max_budget->setUserOption('postfix-addon', true);
        $max_budget->setUserOption('postfix-label', 'SGD$');
        $max_budget->setFilters(array('striptags', 'trim', 'string'));
        $this->add($max_budget);

        // =======================
        // Min Area (sf)
        $min_area = new Text('min_area');
        $min_area->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Min Area (sqft)'
        ]);
        $min_area->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $min_area->setUserOption('input-width','col-xs-12');
        $min_area->setUserOption('postfix-addon', true);
        $min_area->setUserOption('postfix-label', 'ft&sup2;');
        $min_area->setFilters(array('striptags', 'trim', 'string'));
        $this->add($min_area);

        // =======================
        // Max Area (sf)
        $max_area = new Text('max_area');
        $max_area->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Max Area (sqft)'
        ]);
        $max_area->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $max_area->setUserOption('input-width','col-xs-12');
        $max_area->setUserOption('postfix-addon', true);
        $max_area->setUserOption('postfix-label', 'ft&sup2;');
        $max_area->setFilters(array('striptags', 'trim', 'string'));
        $this->add($max_area);
        
        // =======================
        // Tenure
        $tenures_options=[];
        $tenures_query = PropertyTenures::find();
        foreach ($tenures_query as $key => $value)
            $tenures_options[$value->name] = $value->name;
        $tenure = new Select('tenure[]', $tenures_options);
        $tenure->setAttributes([
            'id' => 'tenure',
            'class' => 'form-control select2',
            'placeholder' => 'Tenure',
            'multiple' => true,
            'useEmpty'  => true,
            'emptyText' => '- Select Tenure -',
            'emptyValue'=> '',
        ]);
        $tenure->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $tenure->setUserOption('input-width','col-xs-12');
        $tenure->setFilters(array('striptags', 'trim', 'string'));
        $this->add($tenure);

        // =======================
        // TOP
        $top_year_query = Projects::findTopYear();
        $top_year = new Hidden('top_year');
        $top_year->setLabel('Top Year:');
        $top_year->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Top Year',
        ]);
        $top_year->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $top_year->setUserOption('label-width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $top_year->setUserOption('input-width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $top_year->setUserOption('is-slider',true);
        $top_year->setUserOption('div_slider','TY_state');
        $top_year->setUserOption('div_adv_slide','TY_advance_slide');
        if($top_year_query&&$top_year_query->count()>0) {
            $top_year->setUserOption('slider_min', (int)$top_year_query[0]->top_min);
            $top_year->setUserOption('slider_max', (int)$top_year_query[0]->top_max);
            $top_year->setUserOption('slider_step', 1);
            $top_year->setUserOption('slider_val1', (int)$top_year_query[0]->top_min);
            $top_year->setUserOption('slider_val2', (int)date("Y"));
        }
        $top_year->setFilters(array('striptags', 'trim', 'int'));
        $this->add($top_year);   


        // =======================
        // MRT
        $mrt_options = [];
        $mrt_query = Projects::find(["columns"=>"DISTINCT mrt","conditions"=>"mrt IS NOT NULL"]);
        foreach ($mrt_query as $key => $value)  $mrt_options[$value->mrt] = $value->mrt; 
        asort($mrt_options);
        $mrt = new Select('mrt[]', $mrt_options);
        $mrt->setAttributes([
            'id' => 'mrt',
            'class' => 'form-control select2',
            'placeholder' => 'MRT/LRT',
            'multiple' => true,
            'useEmpty'  => true,
            'emptyText' => '- Select MRT -',
            'emptyValue'=> '',
        ]);
        $mrt->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $mrt->setUserOption('input-width','col-xs-12');
        $mrt->setFilters(array('striptags', 'trim', 'string'));
        $this->add($mrt);      

        // =======================
        // MRT Distance
        $mrt_distance_options = ['0-100'=>'0 - 100','101-200'=>'101 - 200','201-300'=>'201 - 300','301-400'=>'301 - 400','401-500'=>'401 - 500'];
        $mrt_distance = new Select('mrt_distance_km', $mrt_distance_options);
        $mrt_distance->setAttributes([
            'class' => 'form-control select2',
            'placeholder' => 'Any Range',
            'useEmpty'  => true,
            'emptyText' => '- Select MRT Distance -',
            'emptyValue'=> '',
        ]);
        $mrt_distance->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $mrt_distance->setUserOption('input-width','col-xs-12');
        $mrt_distance->setUserOption('postfix-addon', true);
        $mrt_distance->setUserOption('postfix-label', 'meters');
        $mrt_distance->setFilters(array('striptags', 'trim', 'string'));
        $this->add($mrt_distance);         

        // =======================
        // Primary School 
        $primary_school_options = [];
        $primary_school = new Select('primary_school_within_1km[]', $primary_school_options);
        $primary_school->setAttributes([
            'id' => 'primary_school_within_1km',
            'class' => 'form-control select2',
            'placeholder' => 'Primary School within 1km',
            'multiple' => true,
            'useEmpty'  => true,
            'emptyText' => '- Select Primary School -',
            'emptyValue'=> '',
        ]);
        $primary_school->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $primary_school->setUserOption('input-width','col-xs-12');
        $primary_school->setFilters(array('striptags', 'trim', 'string'));
        $this->add($primary_school); 

        // =======================
        // Street Name
        $street_name = new Text('street_name');
        $street_name->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Street Name'
        ]);
        $street_name->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $street_name->setUserOption('input-width','col-xs-12');
        $street_name->setFilters(array('striptags', 'trim', 'string'));
        $this->add($street_name);       

        // =======================
        // Transactions
        // $transaction = new Check('transaction');
        // $transaction->setLabel('Transactions');
        // $transaction->setAttributes([
        //   'class' => 'form-control input-sm',
        //   'placeholder' => 'Transactions'
        // ]);
        // $transaction->setUserOption('width','pull-right col-xs-6');
        // $transaction->setUserOption('input-width','col-xs-12');
        // $transaction->setUserOption('funkyCheckbox', true);
        // $transaction->setFilters(array('striptags', 'trim', 'string'));
        // $this->add($transaction);  

        // =======================
        // Status
        // $status = new Check('status');
        // $status->setLabel('Status');
        // $status->setAttributes([
        //   'class' => 'form-control input-sm',
        //   'placeholder' => 'Transactions'
        // ]);
        // $status->setUserOption('width','col-xs-6');
        // $status->setUserOption('input-width','col-xs-12');
        // $status->setUserOption('funkyCheckbox', true);
        // $status->setFilters(array('striptags', 'trim', 'string'));
        // $this->add($status);  
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