<?php

//Core
use Phalcon\DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View;
use Phalcon\Image\Adapter\Imagick;
use Property\Helpers\Helpers;

use Property\Classes\PropertyClass;
//Models
use Property\Models\PerProjects;


class PerprojectsController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * [indexAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->view->uploads); echo '</pre>'; die();
    public function indexAction()
    {
        $this->view->perprojects = PerProjects::find(["order" => "id ASC"]);    
        $this->view->pick("perprojects/index");
    }

    /**
     * [detailsAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detailsAction($id)
    {
        $this->view->disable();
        $fields = PropertyClass::$project_per_project;
        $perproject = PerProjects::findFirst($id);
        $response = '<div role="form" class="row">';
        $numOfCols = 2;
        $rowCount = 0;
        $bootstrapColWidth = 12 / $numOfCols;
        $i=1; $max = count($fields);
        foreach ($fields as $key => $field) {
            $response .= '<div class="col-sm-'.$bootstrapColWidth.'">';
                $response .= '<div class="form-group">';
                    $response .= '<label for="'.$field.'" class="control-label col-xs-12"></label>';
                        $response .= '<div class="col-xs-12">';
                            $response .= '<div class="input-group"><span class="input-group-addon"><b>'.$this->str_truncate($field, 20).'</b></span>';
                                switch($key) {
                                    case 'project_id':
                                        $project_name = (!empty($perproject->$key)) ? $perproject->PerProjects_Project->project_name : ''; 
                                        $response .= '<input type="text" id="'.$field.'" name="'.$field.'" class="form-control text-right text-strong" readonly value="'.$project_name.'">';
                                        break;
                                    case 'unit_type_id':
                                    case 'available_unit_type_id':
                                        $property_unit = (!empty($perproject->$key)) ? $perproject->PerProjects_PropertyUnits->name : ''; 
                                        $response .= '<input type="text" id="'.$field.'" name="'.$field.'" class="form-control text-right text-strong" readonly value="'.$property_unit.'">';
                                        break; 
                                    case 'date_avail_unit_updated':
                                        $date_value = (!empty($perproject->$key)) ? date("d-M-Y", strtotime($perproject->$key)) : ''; 
                                        $response .= '<input type="text" id="'.$field.'" name="'.$field.'" class="form-control text-right text-strong" readonly value="'.$date_value.'">';
                                        break;   
                                    default:
                                        $project_value = (!empty($perproject->$key)) ? $perproject->$key : ''; 
                                        $response .= '<input type="text" id="'.$field.'" name="'.$field.'" class="form-control text-right text-strong" readonly value="'.$project_value.'">';
                                        break;
                                }
                            $response .= '</div>';
                        $response .= '</div>';
                $response .= '</div>';
            $response .= '</div>';
            $rowCount++;
            if($rowCount % $numOfCols == 0) $response .= '</div><div class="row">';
        }     
        $response .= '</div>';

        return $response;
    }

}