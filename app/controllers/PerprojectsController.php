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
use Property\Library\DataTable;

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
        $columns = PerProjects::getFields();
        foreach(array_values($columns) as $col) $NameCols[] = $col;
        $DataCols = array_keys($columns);       
        foreach ($DataCols as $val) $dtCols[] = ["data"=>$val,'name'=>$val];
        $hidden = ['id','project_id','creation_date', 'update_date','update_by']; 
        foreach ($DataCols as $key => $val) {
            if (!in_array($val, $hidden)) continue;
            else $hiddenfields[$key] = $val;
        }
        $this->view->hiddenCols = implode(',',array_keys($hiddenfields));
        $this->view->NameCols = $NameCols;
        $this->view->DataCols = $DataCols;
        $this->view->JsonCols = json_encode($dtCols);
        $this->view->JsonUrl = "perprojects/listJson";
    }

    /**
     * [listJsonAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->view->uploads); echo '</pre>'; die();
    public function listJsonAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) $data = $this->request->getPost();
        $perprojcolumns = PerProjects::getFields();
        $cols = array_keys($perprojcolumns);    
        $model = new PerProjects();
        foreach ($cols as $key => $value) {
            $columns[$value] = [
                    'dbc'       => $value,
                    'dtc'       => $value,
                    'search'    => true,
                    'extendIn'  => false,
                ];
            switch ($value) {
                default:
                    $columns[$value]['foreign'] = null;
                    $columns[$value]['fldtype'] = null;
                    $columns[$value]['custom'] = null;
                    break;
            }
        }
        $sql = "";
        $condition = $sql;
        echo DataTable::generateTableV2($data, $columns, $model, $condition);  
    }

    /**
     * [detailsAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */ //echo '<pre>'; var_dump($this->view->uploads); echo '</pre>'; die();
    public function detailsAction($id)
    {
        $this->view->disable();
        $dfields = PropertyClass::$project_per_project_fields;
        $perproject = PerProjects::findFirst($id);
        $response = '<div role="form" class="row">';
        $response .= '<form method="POST" action="details/savePerunit" id="postdetails" data-remote="data-remote" >';
        foreach ($dfields as $fldname => $label) {
            $response .= '<input type="hidden" id="id" name="id" readonly value="'.$perproject->id.'">';
            $response .= '<div class="container m-b-xs">';
                $response .= '<div class="form-group">';
                    $response .= '<div class="col-xs-3"><label for="'.$fldname.'" class="control-label" style="padding-top: 10px;">'.$label.'</label></div>';
                        //foreach ($fields as $fldname) {
                            switch($fldname) {
                                case 'project_type':
                                case 'proj_property_type':
                                case 'project_id':
                                case 'project_name':
                                case 'low_price':
                                case 'median_price':
                                case 'high_price':
                                    $response .= '<div class="col-xs-3">';
                                    $project_value = (!empty($perproject->$fldname)) ? $perproject->$fldname : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project_value.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'" readonly>';
                                    $response .= '</div>';
                                    break;
                                case 'no_of_units':
                                case 'units_sold':
                                case 'units_unsold':
                                case 'share_value':
                                case 'share_amount':
                                case 'mtce_fee':
                                default:
                                    $response .= '<div class="col-xs-3">';
                                    $project_value = (!empty($perproject->$fldname)) ? $perproject->$fldname : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project_value.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                    $response .= '</div>';
                                    break;
                            }
                        //}
                $response .= '</div>';
            $response .= '</div>';
        }  
        $response .= '</form>';   
        $response .= '</div>';
        return $response;
    }

    /**
     * [deletedAction description]
     * @return [type] [description]
     */
    public function deletedAction()
    {
        $this->view->disable();
        $data = $this->request->getPost();
        $id = (int)$data['id'];
        $projects = PerProjects::findFirst($id);
        if($projects&&count($projects)>0) {
            $result = PerProjects::deletePerProject($id);
            if($result) {
                $result = Helpers::notify('success', 'Per Project detail successfully deleted.');
                $result['id'] = $id;
                $result['close'] = 2;
            } else {
                $result = Helpers::notify('error', 'Unable to delete per project details.');
            }
        } else {
            $result = Helpers::notify('error', 'Unable to find per project detail.');
        }
        return json_encode($result);

    }
}