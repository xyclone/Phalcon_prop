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
                                    $response .= '<div class="col-xs-3">';
                                    $project_value = (!empty($perproject->$fldname)) ? $perproject->$fldname : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project_value.'" readonly>';
                                    $response .= '</div>';
                                    break;
                                case 'low_price':
                                case 'median_price':
                                case 'high_price':
                                    $response .= '<div class="col-xs-3">';
                                    $project_value = (!empty($perproject->$fldname)) ? $perproject->$fldname : ''; 
                                        $response .= '<div class="input-group">';
                                            $response .= '<span class="input-group-addon"><b>S$</b></span>'; 
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.number_format($project_value).'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'" readonly>';
                                        $response .= '</div>';
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

    public function transactionsAction($projectId)
    {
        $this->view->disable();
        $trxn_fields = ["project_id"=>"Project Name","unit_type"=>"Unit Type","area_sqft"=>"Area ft&sup2;","low_price"=>"Low Price","median_price"=>"Median Price","high_price"=>"High Price","no_of_units"=>"No of Units","units_sold"=>"Units Sold","units_unsold"=>"Units Unsold"];


        $project = PerProjects::findFirst($projectId); 
        $projects = PerProjects::find(["columns"=>implode(",",array_keys($trxn_fields)),"conditions"=>"project_id=?1","bind"=>[1=>$projectId]]);
        
        $response = '<div role="form" class="container">';
        if($projects&&$projects->count()>0) {
            $headers = array_values($trxn_fields);
            $columns = array_keys($trxn_fields);
            $response .= '<div class="table-responsive">
                        <table id="table_transactions" class="table table-bordered table-hover">
                            <thead>
                                <tr>';
                                    foreach ($headers as $header) {
                                        $response .= "<th class='text-center'>$header</th>";
                                    }    
            $response .=        '</tr>
                            </thead>
                            <tbody id="list">';
                            foreach ($projects as $key => $row) {
            $response .=        '<tr>';
                                foreach ($columns as $field) {
                                    switch ($field) {
                                        case 'project_id':
                                            $response .= "<td>".$project->project_name."</td>";
                                            break;
                                        case 'no_of_units':
                                        case 'units_sold':
                                        case 'units_unsold':
                                            $response .= "<td class='text-center'>".number_format($row->$field)."</td>";
                                            break;
                                        case 'low_price':
                                        case 'median_price':
                                        case 'high_price':
                                            $response .= "<td class='text-right' style='padding-right: 10px;'>".number_format($row->$field)."</td>";
                                            break;
                                        case 'area_sqft':
                                            $response .= "<td class='text-right' style='padding-right: 10px;'>".$row->$field."</td>";
                                            break;
                                        default:
                                            $response .= "<td>".$row->$field."</td>";  
                                            break;
                                    }                           
                                }                                
            $response .=        '</tr>';
                            }
            $response .=    '</tbody>
                        </table>
                    </div>';
        } else {
            $response .= '<div class="container"><div class="col-sm-12 alert alert-warning">No per project found on the project.</div></div>';
        }
        $response .= '</div>';

        $response .="<script>
                    $(function () {
                      $('#table_transactions').DataTable({
                        'lengthMenu': [[-1],['All Rows']]
                        });
                    });
                </script>";
        return $response;
    }
}