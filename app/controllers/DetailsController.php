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
use Phalcon\Escaper;
use Property\Library\DataTable;

use Property\Classes\PropertyClass;
//Models
use Property\Models\Projects;
use Property\Models\ProjectTypes;
use Property\Models\ProjectPropTypes;
use Property\Models\ProjectDetails;
use Property\Models\PropertyTypes;
use Property\Models\PropertyTenures;
use Property\Models\PropertyDistricts;
use Property\Models\PropertyUnits;


class DetailsController extends ControllerBase
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
        $columns = ProjectDetails::getFields();
        foreach(array_values($columns) as $col) $NameCols[] = str_replace(' ID', '', $col);
        $DataCols = array_keys($columns);       
        foreach ($DataCols as $val) $dtCols[] = ["data"=>$val,'name'=>$val];
        $hidden = ['id', 'creation_date', 'update_date','update_by']; 
        foreach ($DataCols as $key => $val) {
            if (!in_array($val, $hidden)) continue;
            else $hiddenfields[$key] = $val;
        }
        $this->view->hiddenCols = implode(',',array_keys($hiddenfields));
        $this->view->NameCols = $NameCols;
        $this->view->DataCols = $DataCols;
        $this->view->JsonCols = json_encode($dtCols);
        $this->view->JsonUrl = "details/listJson";
    }

    /**
     * [listJsonAction description]
     * @return [type] [description]
     */
    public function listJsonAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) $data = $this->request->getPost();
        $projcolumns = ProjectDetails::getFields();
        $cols = array_keys($projcolumns);
#echo '<pre>'; var_dump($data); echo '</pre>'; die();         
        //Sale Date
        $sale_date = (!empty($data['sale_date'])) ?  $data['sale_date'] : '';
        $sale_min = ""; $sale_max = "";
        if(!empty($sale_date)) {
            $sale = explode(" - ", $sale_date);
            $sale_min = date("Y-m-d", strtotime($sale[0]));
            $sale_max = date("Y-m-d", strtotime($sale[1])); 
        }
        $model = new ProjectDetails();
        foreach ($cols as $key => $value) {
            $columns[$value] = [
                    'dbc'       => $value,
                    'dtc'       => $value,
                    'search'    => true,
                    'extendIn'  => false,
                ];
            switch ($value) {
                case 'project_id':
                    $columns[$value]['foreign'] = new Projects();
                    $columns[$value]['primary_key'] = $value;
                    $columns[$value]['foreign_key'] = 'id';
                    $columns[$value]['extend'] = ['ProjectDetails_Projects', 'project_name'];
                    $columns[$value]['fldtype'] = null;
                    $columns[$value]['custom'] = null;
                    break;
                case 'sale_date':
                    $columns[$value]['custom'] = ['start'=>$sale_min,'stop'=>$sale_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
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
     */
    public function detailsAction($id)
    {
        $this->view->disable();
        $dfields = PropertyClass::$project_detail_display_fields;
        $details = ProjectDetails::findFirst($id);

        //ProjectTypes
        $projtypes_options=[''=>'- Select -'];
        $projtypes_opt = ProjectTypes::find(["columns"=>"name"]);
        if($projtypes_opt&&$projtypes_opt->count()>0) {
           foreach ($projtypes_opt as $key => $value) $projtypes_options[$value->name] = $value->name; 
        }  
        //ProjectPropertyTypes
        $projproptypes_options=[''=>'- Select -'];
        $projproptypes_opt = ProjectPropTypes::find(["columns"=>"name"]);
        if($projproptypes_opt&&$projproptypes_opt->count()>0) {
           foreach ($projproptypes_opt as $key => $value) $projproptypes_options[$value->name] = $value->name; 
        }  
        //PropertyUnits
        $units_options=[''=>'- Select -'];
        $units_opt = PropertyUnits::find(["columns"=>"name"]);
        if($units_opt&&$units_opt->count()>0) {
           foreach ($units_opt as $key => $value) $units_options[$value->name] = $value->name; 
        }
        //Tenures
        $tenure_options=[''=>'- Select -'];
        $tenure_opt = PropertyTenures::find(["columns"=>"name"]);
        if($tenure_opt&&$tenure_opt->count()>0) {
            foreach ($tenure_opt as $key => $value) $tenure_options[$value->name] = $value->name;
        }
        //PropertyTypes
        $property_options=[''=>'- Select -'];
        $property_opt = PropertyTypes::find(["columns"=>"DISTINCT name"]);
        if($property_opt&&$property_opt->count()>0) {
            foreach ($property_opt as $key => $value) $property_options[$value->name] = $value->name;
        }
        //Districts
        $district_options=[''=>'- Select -'];
        $district_opt = PropertyDistricts::find(["columns"=>"name"]);
        if($district_opt&&$district_opt->count()>0) {
            foreach ($district_opt as $key => $value) $district_options[$value->name] = $value->name;
        }


        $response = '<div role="form" class="row">';
        $response .= '<form method="POST" action="details/savePerunit" id="postdetails" data-remote="data-remote" >';
        foreach ($dfields as $key => $fields) {
            $response .= '<input type="hidden" id="id" name="id" readonly value="'.$details->id.'">';
            $response .= '<div class="container m-b-xs">';
                $response .= '<div class="form-group">';
                    $response .= '<div class="col-xs-3"><label for="'.$key.'" class="control-label" style="padding-top: 10px;">'.$key.'</label></div>';
                        foreach ($fields as $fldname) {
                            switch($fldname) {
                                case 'project_type':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($projtypes_options as $key => $value) {
                                            $selected = ($details->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';                                    
                                    break;
                                case 'proj_property_type':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($projproptypes_options as $key => $value) {
                                            $selected = ($details->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;                                        
                                case 'project_id':
                                    $response .= '<div class="col-xs-3">';
                                    $project_name = (!empty($details->$fldname)) ? $details->ProjectDetails_Projects->project_name : ''; 
                                    $response .= '<input type="text" id="project_name" name="project_name" class="form-control text-strong" readonly value="'.$project_name.'">';
                                    $response .= '</div>';
                                    break;
                                case 'address':
                                    $response .= '<div class="col-xs-6">';
                                    $address = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" readonly value="'.$address.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                    $response .= '</div>';
                                    break;
                                case 'number':
                                case 'level':
                                case 'stack':
                                    $response .= '<div class="col-xs-2">';
                                    $details_value = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$details_value.'"  placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                    $response .= '</div>';
                                    break;
                                case 'street_name':
                                    $response .= '<div class="col-xs-3">';
                                    $details_value = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$details_value.'" placeholder="'.ucwords($fldname).'">';
                                    $response .= '</div>';
                                    break;
                                case 'property_type':
                                    $response .= '<div class="col-xs-3">';
                                    $property_type = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($property_options as $k => $value) {
                                            $selected = ($property_type==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$k.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;
                                case 'property2_type':
                                    $response .= '<div class="col-xs-3">';
                                    $property_type = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($property_options as $k => $value) {
                                            $selected = ($property_type==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$k.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;
                                case 'unit_type':
                                    $response .= '<div class="col-xs-3">';
                                    $property_unit = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($units_options as $k => $value) {
                                            $selected = ($property_unit==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$k.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break; 
                                case 'tenure':
                                    $response .= '<div class="col-xs-3">';
                                    $tenure_name = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($tenure_options as $k => $value) {
                                            $selected = ($tenure_name==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$k.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;     
                                case 'district':
                                    $response .= '<div class="col-xs-3">';
                                    $district = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($district_options as $k => $value) {
                                            $selected = ($district==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$k.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break; 
                                case 'sale_date':
                                    $response .= '<div class="col-xs-3">';
                                    $date_value = (!empty($details->$fldname)) ? date("d-M-Y", strtotime($details->$fldname)) : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" readonly value="'.$date_value.'">';
                                    $response .= '</div>';
                                    break; 
                                case 'date_avail_unit_updated':
                                    $response .= '<div class="col-xs-3">';
                                    $date_value = (!empty($details->$fldname)) ? date("d-M-Y", strtotime($details->$fldname)) : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$date_value.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                    $response .= '</div>';
                                    break;   
                                case 'area_sqm':
                                    $details_value = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$details_value.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                            $response .= '<span class="input-group-addon"><b>m&sup2;</b></span>';   
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'area_sqf':
                                    $details_value = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$details_value.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">'; 
                                            $response .= '<span class="input-group-addon"><b>ft&sup2;</b></span>'; 
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                default:
                                    $response .= '<div class="col-xs-3">';
                                    $project_value = (!empty($details->$fldname)) ? $details->$fldname : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project_value.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                    $response .= '</div>';
                                    break;
                            }
                        }
                $response .= '</div>';
            $response .= '</div>';
        }  
        $response .= '</form>';   
        $response .= '</div>';
        $response .= '<script>
        // Numeric only control handler
        jQuery.fn.ForceNumericOnly =
        function()
        {
            return this.each(function()
            {
                $(this).keydown(function(e)
                {
                    var key = e.charCode || e.keyCode || 0;
                    return (
                        key == 8 || 
                        key == 9 ||
                        key == 13 ||
                        key == 46 ||
                        key == 110 ||
                        //key == 190 || Disable Decimal
                        (key >= 35 && key <= 40) ||
                        (key >= 48 && key <= 57) ||
                        (key >= 96 && key <= 105));
                });
            });
        };
        $(document).ready(function() {
            $(".select2").select2({theme: "bootstrap", width: "100%", placeholder: "- Select -",});
            $(".select2-disabled").select2({theme: "bootstrap", width: "100%", placeholder: "- Select -",disabled: true});
            $(".numericOnly").ForceNumericOnly();
        });
        </script>';
        return $response;
    }

    /**
     * [savePerunitAction description]
     * @return [type] [description]
     */#echo '<pre>'; var_dump($data); echo '</pre>'; die();  
    public function savePerunitAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) $data = $this->request->getPost(); 
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $saveDetails = ProjectDetails::findFirst(["conditions"=>"id=?1","bind"=>[1=>$data['id']]]);
        if(!$saveDetails) {
            $result = Helpers::notify('error', 'Unable to find project details.');
        } else {
#echo '<pre>'; var_dump($data); echo '</pre>'; die();  
            foreach ($data as $field => $value) {
                switch ($field) {
                    case 'project_name':
                        continue;
                    case 'sale_date':
                        if(!empty($value)) {
                            $date = \DateTime::createFromFormat('d-M-Y', trim($value));
                            $saveDetails->$field = $date->format('Y-m-d');
                        }
                        break;
                    case 'transacted_price':
                        if(!empty($value)) {
                            $saveDetails->$field = $filter->sanitize($value, "int");
                        }
                        break;
                    case 'area_sqf':
                    case 'area_sqm':
                        if(!empty($value)) {
                            $saveDetails->$field = number_format((float)$value, 2, '.', '');
                        }
                        break;
                    default:
                        if(!empty($value)) {
                            $saveDetails->$field = $filter->sanitize($value, "string");
                        }
                        break;
                }
                try {
                    $saveDetails->save();
                    $result = Helpers::notify('success', 'Per Unit details successfully updated.');
                    $result['close'] = 1;
                }  catch(\Exception $e) {
                    error_log("Error: ".$e->getMessage());
                    $result = Helpers::notify('error', 'Unable to save project.');
                }
            }
        }
        return json_encode($result);
    }

    public function transactionsAction($projectId)
    {
        $this->view->disable();
        $trxn_fields = ["project_id"=>"Project Name","address"=>"Address","area_sqf"=>"Area SF","transacted_price"=>"Transacted Price","top_year"=>"TOP Year","sale_date"=>"Sale Date"];


        $project = Projects::findFirst($projectId); 
        $projects = ProjectDetails::find(["columns"=>implode(",",array_keys($trxn_fields)),"conditions"=>"project_id=?1","bind"=>[1=>$projectId]]);
        
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
                                        case 'transacted_price':
                                            $response .= "<td class='text-right' style='padding-right: 10px;'>".number_format($row->transacted_price)."</td>";
                                            break;
                                        case 'sale_date':
                                            $sale_date = (!empty($row->$field)&&strtotime($row->$field)>=0) ? date("d-M-Y", strtotime($row->$field))  : '';
                                            $response .= "<td class='text-center'>$sale_date</td>";
                                            break;
                                        case 'area_sqf':
                                            $response .= "<td class='text-right' style='padding-right: 10px;'>".$row->$field."</td>";
                                            break;
                                        case 'top_year':
                                            $response .= "<td class='text-center'>".$row->$field."</td>";  
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
            $response .= '<div class="container"><div class="col-sm-12 alert alert-warning">No transction found on the project.</div></div>';
        }
        $response .= '</div>';

        $response .="<script>
                    $(function () {
                      $('#table_transactions').DataTable({
                        'lengthMenu': [[-1],['All Rows']]
                        });
                    });
                </script>";
//echo '<pre>'; var_dump($response); echo '</pre>'; die();
        return $response;
    }

    public function deletedAction()
    {
        $this->view->disable();
        $data = $this->request->getPost();
        $id = (int)$data['id'];
        $projects = ProjectDetails::findFirst($id);
        if($projects&&count($projects)>0) {
            $result = ProjectDetails::deleteUnit($id);
            if($result) {
                $result = Helpers::notify('success', 'Project detail successfully deleted.');
                $result['id'] = $id;
                $result['close'] = 2;
            } else {
                $result = Helpers::notify('error', 'Unable to delete project details.');
            }
        } else {
            $result = Helpers::notify('error', 'Unable to find project detail.');
        }
        return json_encode($result);

    }
}