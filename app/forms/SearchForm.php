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
use Property\Classes\SearchClass;


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
        $project_name_options=[];
        $project_name_query = Projects::find(["columns"=>"id, project_name","order"=>"project_name asc"]);
        foreach ($project_name_query as $key => $value)
            $project_name_options[$value->project_name] = $value->project_name;
        $project_name = new Select('project_name[]', $project_name_options);
        $project_name->setAttributes([
            'id' => 'project_name',
            'class' => 'form-control select2',
            'placeholder' => 'Project Name',
            'multiple' => true,
            'useEmpty'  => false,
            'emptyText' => '- Project Name -',
            'emptyValue'=> '',
        ]);
        $project_name->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $project_name->setUserOption('input-width','col-xs-12');
        $project_name->setFilters(array('striptags', 'trim', 'string'));
        $this->add($project_name);        
        // $project_name = new Text('project_name');
        // $project_name->setAttributes([
        //     'class' => 'form-control',
        //     'placeholder' => 'Project Name'
        // ]);
        // $project_name->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        // $project_name->setUserOption('input-width','col-xs-12');
        // $project_name->setFilters(array('striptags', 'trim', 'string'));
        // $project_name->addValidators(array(
        //     new PresenceOf(array(
        //         "message" => "Project Name is required"
        //     ))
        // ));
        // $this->add($project_name);

        // =======================
        // Total Units
        $query_units_min = Projects::findFirst(["columns"=>"MIN(total_units) total_units_min","limit"=>1]);   
        $query_units_max = Projects::findFirst(["columns"=>"MAX(total_units) total_units_max","limit"=>1]);

        $total_units_min = new Text('total_units_min');
        $total_units_min->setLabel('Min Units');
        $total_units_min->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Total Units Min',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $total_units_min->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $total_units_min->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $total_units_min->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $total_units_min->setUserOption('is-touchspin', true);
        $total_units_min->setUserOption('prefix-label', 'Min Units');
        $total_units_min->setUserOption('value_min', $query_units_min->total_units_min);
        $total_units_min->setUserOption('value_max', $query_units_max->total_units_max);
        $total_units_min->setUserOption('value_interval', 5);
        $total_units_min->setFilters(array('striptags', 'trim', 'int'));
        $total_units_min->setDefault((int)$query_units_min->total_units_min+5);
        $this->add($total_units_min);  

        $total_units_max = new Text('total_units_max');
        $total_units_max->setLabel('Max Units');
        $total_units_max->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Total Units Max',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $total_units_max->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $total_units_max->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $total_units_max->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $total_units_max->setUserOption('is-touchspin', true);
        $total_units_max->setUserOption('prefix-label', 'Max Units');
        $total_units_max->setUserOption('value_min', $query_units_min->total_units_min);
        $total_units_max->setUserOption('value_max', $query_units_max->total_units_max);
        $total_units_max->setUserOption('value_interval', 5);
        $total_units_max->setFilters(array('striptags', 'trim', 'int'));
        $total_units_max->setDefault((int)$query_units_max->total_units_max-5);
        $this->add($total_units_max);  

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
        $planning_area_options = [];
        $query_planning_area = Projects::find(["columns"=>"DISTINCT CONCAT(planning_area,' (',district,')') planning_area","conditions"=>"CONCAT(planning_area,' (',district,')') IS NOT NULL","order"=>"planning_area ASC"]);
        if($query_planning_area&&$query_planning_area->count()) {
            foreach ($query_planning_area as $key => $field) {
                $planning_area_options[$field->planning_area] = $field->planning_area;
            }
        }
        $planning_area = new Select('planning_area[]', $planning_area_options);
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
        $properties_query = Projects::find(["columns"=>"property_type","conditions"=>"property_type IS NOT NULL"]);
        if($properties_query&&$properties_query->count()>0) {
            foreach ($properties_query as $key => $field) {
                if(strpos($field->property_type, ",")) {
                    $prop_types = array_map('trim', explode(',', $field->property_type));
                    foreach ($prop_types as $prop_type) {
                        $properties_options[$prop_type] = $prop_type;
                    }
                } else {
                    $properties_options[$field->property_type] = $field->property_type;
                }
            }
        }

        ksort($properties_options);       
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
        $units_query = (new SearchClass)->unitOptions();
        foreach ($units_query as $key => $value)
            $units_options[$key] = $key;
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
            'placeholder' => 'Min Budget',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $min_budget->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $min_budget->setUserOption('input-width','col-xs-12');
        $min_budget->setUserOption('postfix-addon', true);
        $min_budget->setUserOption('postfix-label', 'S$');
        $min_budget->setFilters(array('striptags', 'trim', 'int'));
        $this->add($min_budget);

        // =======================
        // Max Budget
        $max_budget = new Text('max_budget');
        $max_budget->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Max Budget',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $max_budget->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $max_budget->setUserOption('input-width','col-xs-12');
        $max_budget->setUserOption('postfix-addon', true);
        $max_budget->setUserOption('postfix-label', 'S$');
        $max_budget->setFilters(array('striptags', 'trim', 'int'));
        $this->add($max_budget);

        // =======================
        // Min Area (sf)
        $min_area = new Text('min_area');
        $min_area->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Min Area (sqft)',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $min_area->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $min_area->setUserOption('input-width','col-xs-12');
        $min_area->setUserOption('postfix-addon', true);
        $min_area->setUserOption('postfix-label', 'ft&sup2;');
        $min_area->setFilters(array('striptags', 'trim', 'int'));
        $this->add($min_area);

        // =======================
        // Max Area (sf)
        $max_area = new Text('max_area');
        $max_area->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Max Area (sqft)',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $max_area->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $max_area->setUserOption('input-width','col-xs-12');
        $max_area->setUserOption('postfix-addon', true);
        $max_area->setUserOption('postfix-label', 'ft&sup2;');
        $max_area->setFilters(array('striptags', 'trim', 'int'));
        $this->add($max_area);
        
        // =======================
        // PSF
        $psf_query = Projects::findPsfMinMax();
        $psf_min = new Text('psf_min');
        $psf_min->setLabel('Min PSF');
        $psf_min->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'PSF Min',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $psf_min->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $psf_min->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $psf_min->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $psf_min->setUserOption('is-touchspin', true);
        $psf_min->setUserOption('value_min', (int)$psf_query[0]->psf_min);
        $psf_min->setUserOption('value_max', (int)$psf_query[0]->psf_max);
        $psf_min->setUserOption('value_interval', 1);
        $psf_min->setFilters(array('striptags', 'trim', 'int'));
        //$psf_min->setDefault((int)$psf_query[0]->psf_min);
        $this->add($psf_min);  

        $psf_max = new Text('psf_max');
        $psf_max->setLabel('Max PSF');
        $psf_max->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'PSF Max',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $psf_max->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $psf_max->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $psf_max->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $psf_max->setUserOption('is-touchspin', true);
        $psf_max->setUserOption('value_min', (int)$psf_query[0]->psf_min);
        $psf_max->setUserOption('value_max', (int)$psf_query[0]->psf_max);
        $psf_max->setUserOption('value_interval', 1);
        $psf_max->setFilters(array('striptags', 'trim', 'int'));
        //$psf_max->setDefault((int)$psf_query[0]->psf_max);
        $this->add($psf_max); 

        // =======================
        // TOP
        // $top_year_query = Projects::findTopYear();
        // $top_year = new Hidden('top_year');
        // $top_year->setLabel('Top Year:');
        // $top_year->setAttributes([
        //     'class' => 'form-control',
        //     'placeholder' => 'Top Year',
        // ]);
        // $top_year->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        // $top_year->setUserOption('label-width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        // $top_year->setUserOption('input-width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        // $top_year->setUserOption('is-slider',true);
        // $top_year->setUserOption('div_slider','TY_state');
        // $top_year->setUserOption('div_adv_slide','TY_advance_slide');
        // if($top_year_query&&$top_year_query->count()>0) {
        //     $top_year->setUserOption('slider_min', (int)$top_year_query[0]->top_min);
        //     $top_year->setUserOption('slider_max', (int)$top_year_query[0]->top_max);
        //     $top_year->setUserOption('slider_step', 1);
        //     $top_year->setUserOption('slider_val1', (int)$top_year_query[0]->top_min);
        //     $top_year->setUserOption('slider_val2', (int)date("Y"));
        // }
        // $top_year->setFilters(array('striptags', 'trim', 'int'));
        // $this->add($top_year);   

        $top_year_query = Projects::findTopYear();
        $top_year_min = new Text('top_year_min');
        $top_year_min->setLabel('Min TOP');
        $top_year_min->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Top Year Min',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $top_year_min->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $top_year_min->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $top_year_min->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $top_year_min->setUserOption('is-touchspin', true);
        $top_year_min->setUserOption('prefix-label', 'Min TOP');
        $top_year_min->setUserOption('value_min', (int)$top_year_query[0]->top_min);
        $top_year_min->setUserOption('value_max', (int)$top_year_query[0]->top_max);
        $top_year_min->setUserOption('value_interval', 1);
        $top_year_min->setFilters(array('striptags', 'trim', 'int'));
        $top_year_min->setDefault((int)$top_year_query[0]->top_min);
        $this->add($top_year_min);  

        $top_year_max = new Text('top_year_max');
        $top_year_max->setLabel('Max TOP');
        $top_year_max->setAttributes([
            'class' => 'form-control',
            'placeholder' => 'Top Year Max',
            'onkeypress' => 'return isNumberKey(event)',
        ]);
        $top_year_max->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $top_year_max->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $top_year_max->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $top_year_max->setUserOption('is-touchspin', true);
        $top_year_max->setUserOption('prefix-label', 'Max TOP');
        $top_year_max->setUserOption('value_min', (int)$top_year_query[0]->top_min);
        $top_year_max->setUserOption('value_max', (int)$top_year_query[0]->top_max);
        $top_year_max->setUserOption('value_interval', 1);
        $top_year_max->setFilters(array('striptags', 'trim', 'int'));
        $top_year_max->setDefault((int)$top_year_query[0]->top_max);
        $this->add($top_year_max); 

        // =======================
        // Tenure
        $tenures_options=[];
        $tenures_query = (new SearchClass)->tenureOptions();
        foreach ($tenures_query as $key => $value)
            $tenures_options[$key] = $key;
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
        $mrt->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $mrt->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $mrt->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $mrt->setFilters(array('striptags', 'trim', 'string'));
        $this->add($mrt);      

        // =======================
        // MRT Distance
        $mrt_distance_options = [
            '50'=>'50m',
            '100'=>'100m',
            '150'=>'150m',
            '200'=>'200m',
            '250'=>'250m',
            '300'=>'300m',
            '350'=>'350m',
            '400'=>'400m',
            '450'=>'450m',
            '500'=>'500m',
            '600'=>'600m',
            '700'=>'700m',
            '800'=>'800m',
            '900'=>'900m',
            '1000'=>'1km',
            '2000'=>'> 1km',];
        $mrt_distance = new Select('mrt_distance_km[]', $mrt_distance_options);
        $mrt_distance->setAttributes([
            'id' => 'mrt_distance_km',
            'class' => 'form-control select2',
            'placeholder' => 'MRT Distance',
            'multiple' => true,
            'useEmpty'  => true,
            'emptyText' => '- Select MRT Distance -',
            'emptyValue'=> '',
        ]);
        $mrt_distance->setUserOption('width','col-xs-12 col-sm-12 col-md-3 col-lg-3');
        $mrt_distance->setUserOption('label-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $mrt_distance->setUserOption('input-width','col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $mrt_distance->setFilters(array('striptags', 'trim', 'string'));
        $this->add($mrt_distance);         

        // =======================
        // Primary School 
        $primary_school_options = [];
        $query_primary = Projects::find(["columns"=>"primary_school_within_1km","conditions"=>"primary_school_within_1km IS NOT NULL"]);
        if($query_primary&&$query_primary->count()>0) {
            foreach ($query_primary as $key => $field) {
                if(strpos($field->primary_school_within_1km, ",")) {
                    $primary = array_map('trim', explode(',', $field->primary_school_within_1km));
                    foreach ($primary as $pmschool) {
                        $primary_school_options[$pmschool] = $pmschool;
                    }
                } else {
                    $primary_school_options[$field->primary_school_within_1km] = $field->primary_school_within_1km;
                }
            }
        }

        ksort($primary_school_options);
#echo '<pre>'; var_dump($primary_school_options); echo '</pre>'; die();    
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
        $street_name_options=[];
        $street_name_query = Projects::find(["columns"=>"street_name","conditions"=>"street_name IS NOT NULL", "order"=>"street_name asc"]);
        foreach ($street_name_query as $key => $value) $street_name_options[$value->street_name] = $value->street_name;
        $street_name = new Select('street_name[]', $street_name_options);
        $street_name->setAttributes([
            'id' => 'street_name',
            'class' => 'form-control select2',
            'placeholder' => 'Street Name',
            'multiple' => true,
        ]);
        $street_name->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        $street_name->setUserOption('input-width','col-xs-12');
        $street_name->setFilters(array('striptags', 'trim', 'string'));
        $this->add($street_name);     

        // $street_name = new Text('street_name');
        // $street_name->setAttributes([
        //     'class' => 'form-control',
        //     'placeholder' => 'Street Name'
        // ]);
        // $street_name->setUserOption('width','col-xs-12 col-sm-12 col-md-6 col-lg-6');
        // $street_name->setUserOption('input-width','col-xs-12');
        // $street_name->setFilters(array('striptags', 'trim', 'string'));
        // $this->add($street_name);       

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