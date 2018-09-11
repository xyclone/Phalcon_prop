<?php

//Core
use Phalcon\DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View;
use Phalcon\Escaper;
use Phalcon\Image\Adapter\Imagick;
use Property\Helpers\Helpers;
use Property\Library\DataTable;
use Property\Classes\PropertyClass;
use Property\Classes\UploadClass;
//Models
use Property\Models\Projects;
use Property\Models\ProjectDetails;
use Property\Models\ProjectTypes;
use Property\Models\ProjectPropTypes;
use Property\Models\PropertyAgencies;
use Property\Models\PropertyDistricts;
use Property\Models\PropertyStatus;
use Property\Models\PropertyTenures;
use Property\Models\PropertyTypes;
use Property\Models\PropertyUnits;
use Property\Models\MrtStations;
//Form
use Property\Form\ProjectsForm;

class AllprojectsController extends ControllerBase
{
    public $images_dir = IMG_PATH;
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
        $columns = Projects::getFields();
        //unset($columns['id']);
        $columns = PropertyClass::moveKeyBefore($columns, 'project_type', 'project_name');
#echo '<pre>'; var_dump($columns); echo '</pre>'; die();        
        foreach(array_values($columns) as $col) $NameCols[] = str_replace(' ID', '', $col);
        $DataCols = array_keys($columns);       
        foreach ($DataCols as $val) $dtCols[] = ["data"=>$val,'name'=>$val];
        $hidden = ['id','creation_date', 'update_date','update_by']; 
        foreach ($DataCols as $key => $val) {
            if (!in_array($val, $hidden)) continue;
            else $hiddenfields[$key] = $val;
        }
        $this->view->hiddenCols = implode(',',array_keys($hiddenfields));
        $this->view->NameCols = $NameCols;
        $this->view->DataCols = $DataCols;
        $this->view->JsonCols = json_encode($dtCols);
        $this->view->JsonUrl = "allprojects/listJson";
    }

    /**
     * [listJsonAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->view->uploads); echo '</pre>'; die();
    public function listJsonAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) $data = $this->request->getPost();
        $projcolumns = Projects::getFields();
        //unset($projcolumns['id']);
        $projcolumns = PropertyClass::moveKeyBefore($projcolumns, 'project_type', 'project_name');
        $cols = array_keys($projcolumns);
#echo '<pre>'; var_dump($data); echo '</pre>'; die();        
        //Avail Date
        $available_date = (!empty($data['available_date'])) ?  $data['available_date'] : '';
        $available_min = ""; $available_max = "";
        if(!empty($available_date)) {
            $available = explode(" - ", $available_date);
            $available_min = date("Y-m-d", strtotime($available[0]));
            $available_max = date("Y-m-d", strtotime($available[1])); 
        }
        //Avail Unit Date
        $available_unit_date = (!empty($data['available_unit_date'])) ?  $data['available_unit_date'] : '';
        $available_unit_min = ""; $available_unit_max = "";
        if(!empty($available_unit_date)) {
            $available_unit = explode(" - ", $available_unit_date);
            $available_unit_min = date("Y-m-d", strtotime($available_unit[0]));
            $available_unit_max = date("Y-m-d", strtotime($available_unit[1])); 
        }        
        //Status Date
        $status_date = (!empty($data['status_date'])) ?  $data['status_date'] : '';
        $status_min = ""; $status_max = "";
        if(!empty($status_date)) {
            $status = explode(" - ", $status_date);
            $status_min = date("Y-m-d", strtotime($status[0]));
            $status_max = date("Y-m-d", strtotime($status[1])); 
        }   
        //Status2 Date
        $status2_date = (!empty($data['status2_date'])) ?  $data['status2_date'] : '';
        $status2_min = ""; $status2_max = "";
        if(!empty($status2_date)) {
            $status2 = explode(" - ", $status2_date);
            $status2_min = date("Y-m-d", strtotime($status2[0]));
            $status2_max = date("Y-m-d", strtotime($status2[1])); 
        } 
        //GLS Date
        $gls_sold_date = (!empty($data['gls_sold_date'])) ?  $data['gls_sold_date'] : '';
        $gls_sold_min = ""; $gls_sold_max = "";
        if(!empty($gls_sold_date)) {
            $gls_sold = explode(" - ", $gls_sold_date);
            $gls_sold_min = date("Y-m-d", strtotime($gls_sold[0]));
            $gls_sold_max = date("Y-m-d", strtotime($gls_sold[1])); 
        } 
        //STB Appln Date
        $stb_application_date = (!empty($data['stb_application_date'])) ?  $data['stb_application_date'] : '';
        $stb_application_min = ""; $stb_application_max = "";
        if(!empty($stb_application_date)) {
            $stb_application = explode(" - ", $stb_application_date);
            $stb_application_min = date("Y-m-d", strtotime($stb_application[0]));
            $stb_application_max = date("Y-m-d", strtotime($stb_application[1])); 
        } 
        //STB Appvl Date
        $stb_approval_date = (!empty($data['stb_approval_date'])) ?  $data['stb_approval_date'] : '';
        $stb_approval_min = ""; $stb_approval_max = "";
        if(!empty($stb_approval_date)) {
            $stb_approval = explode(" - ", $stb_approval_date);
            $stb_approval_min = date("Y-m-d", strtotime($stb_approval[0]));
            $stb_approval_max = date("Y-m-d", strtotime($stb_approval[1])); 
        }    
        //Completion Date
        $completion_date = (!empty($data['completion_date'])) ?  $data['completion_date'] : '';
        $completion_min = ""; $completion_max = "";
        if(!empty($completion_date)) {
            $completion = explode(" - ", $completion_date);
            $completion_min = date("Y-m-d", strtotime($completion[0]));
            $completion_max = date("Y-m-d", strtotime($completion[1])); 
        }        
        //Vacan Possession Date
        $vacant_possession_date = (!empty($data['vacant_possession_date'])) ?  $data['vacant_possession_date'] : '';
        $vacant_possession_min = ""; $vacant_possession_max = "";
        if(!empty($vacant_possession_date)) {
            $vacant_possession = explode(" - ", $vacant_possession_date);
            $vacant_possession_min = date("Y-m-d", strtotime($vacant_possession[0]));
            $vacant_possession_max = date("Y-m-d", strtotime($vacant_possession[1])); 
        }
        //Approved Date
        $approved_date = (!empty($data['approved_date'])) ?  $data['approved_date'] : '';
        $approved_min = ""; $approved_max = "";
        if(!empty($approved_date)) {
            $approved = explode(" - ", $approved_date);
            $approved_min = date("Y-m-d", strtotime($approved[0]));
            $approved_max = date("Y-m-d", strtotime($approved[1])); 
        }
        //Issued Date
        $issue_date = (!empty($data['issue_date'])) ?  $data['issue_date'] : '';
        $issue_min = ""; $issue_max = "";
        if(!empty($issue_date)) {
            $issue = explode(" - ", $issue_date);
            $issue_min = date("Y-m-d", strtotime($issue[0]));
            $issue_max = date("Y-m-d", strtotime($issue[1])); 
        }
        //DS Date
        $ds_date = (!empty($data['ds_date'])) ?  $data['ds_date'] : '';
        $ds_min = ""; $ds_max = "";
        if(!empty($ds_date)) {
            $ds = explode(" - ", $ds_date);
            $ds_min = date("Y-m-d", strtotime($ds[0]));
            $ds_max = date("Y-m-d", strtotime($ds[1])); 
        }
        //Transaction Month
        $transaction_month = (!empty($data['transaction_month'])) ?  $data['transaction_month'] : '';
        $transaction_min = ""; $transaction_max = "";
        if(!empty($transaction_month)) {
            $transaction = explode(" - ", $transaction_month);
            $transaction_min = date("Y-m-d", strtotime($transaction[0]));
            $transaction_max = date("Y-m-d", strtotime($transaction[1])); 
        }
        //Update Date
        $date_updated = (!empty($data['date_updated'])) ?  $data['date_updated'] : '';
        $updated_min = ""; $updated_max = "";
        if(!empty($date_updated)) {
            $updated = explode(" - ", $date_updated);
            $updated_min = date("Y-m-d", strtotime($updated[0]));
            $updated_max = date("Y-m-d", strtotime($updated[1])); 
        }
       
        $model = new Projects();
        foreach ($cols as $key => $value) {
            $columns[$value] = [
                    'dbc'       => $value,
                    'dtc'       => $value,
                    'search'    => true,
                    'extendIn'  => false,
                    'prxTbl'    => false,
                ];
            switch ($value) {
                case 'available_date':
                    $columns[$value]['custom'] = ['start'=>$available_min,'stop'=>$available_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'date_avail_unit_updated':
                    $columns[$value]['custom'] = ['start'=>$available_unit_min,'stop'=>$available_unit_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'status_date':
                    $columns[$value]['custom'] = ['start'=>$status_min,'stop'=>$status_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'status2_date':
                    $columns[$value]['custom'] = ['start'=>$status2_min,'stop'=>$status2_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'gls_sold_date':
                    $columns[$value]['custom'] = ['start'=>$gls_sold_min,'stop'=>$gls_sold_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'stb_application_date':
                    $columns[$value]['custom'] = ['start'=>$stb_application_min,'stop'=>$stb_application_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'stb_approval_date':
                    $columns[$value]['custom'] = ['start'=>$stb_approval_min,'stop'=>$stb_approval_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'completion_date':
                    $columns[$value]['custom'] = ['start'=>$completion_min,'stop'=>$completion_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'vacant_possession_date':
                    $columns[$value]['custom'] = ['start'=>$vacant_possession_min,'stop'=>$vacant_possession_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'approved_date':
                    $columns[$value]['custom'] = ['start'=>$approved_min,'stop'=>$approved_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'issue_date':
                    $columns[$value]['custom'] = ['start'=>$issue_min,'stop'=>$issue_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'ds_date':
                    $columns[$value]['custom'] = ['start'=>$ds_min,'stop'=>$ds_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'transaction_month':
                    $columns[$value]['custom'] = ['start'=>$transaction_min,'stop'=>$transaction_max];
                    $columns[$value]['fldtype'] = 'date';
                    $columns[$value]['foreign'] = null;
                    break;
                case 'date_updated':
                    $columns[$value]['custom'] = ['start'=>$updated_min,'stop'=>$updated_max];
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
#echo '<pre>'; var_dump($columns); echo '</pre>'; die();
        echo DataTable::generateTableV2($data, $columns, $model, $condition);   
    }

    /**
     * [imagesHtmlAction description]
     * @return [type] [description]
     */
    public function imagesHtmlAction($projid) //$projType,$projPtype,
    {
        $this->view->disable();
        if (!$this->request->get())  return $this->redirectBack();

        $projType="";$projPtype="";
        $project = Projects::findFirst($projid);
        if($project) {
            $projType = $project->project_type;
            $projPtype = $project->proj_property_type;
        }

        $folder = str_replace(' ','_',$projid."_".$projType."_".$projPtype); //$projid."_".$projType."_".$projPtype;
        $images_dir = $this->images_dir.'/'.$folder.'/';
#echo '<pre>'; var_dump($images_dir); echo '</pre>'; die();
        $allowed_ext = implode(",", UploadClass::$allowed_ext);
        $images = (!empty($images_dir)) ? glob($images_dir . "*.{".$allowed_ext."}", GLOB_BRACE) : [];             
        $urlLink = ""; $responseHtml = "";

        $responseHtml .= "
        <style>
        .imgOpts { 
            display: inline-block;
            position: absolute;
            width: 70px;
            background-color: rgba(255,255,255,0.4);
            padding: 2px;
            overflow:hidden;
            z-index: 99;
        }
        .imgSizeInfo {
            display: inline-block;
            position: absolute;
            width: 90%;
            background-color: rgba(255,255,255,0.75);
            padding: 2px;
            overflow:hidden;
            z-index: 99;
            bottom: 0;
            margin: 0 5px 3px 0!important;
            padding-bottom: 2px;
        }
        .imgSizeInfo span {
            width: 100%;
            position: relative;
            text-align: right;
        }

        .placeholder {
            margin-top: 15px!important;
        }
        .sourceValue {
            position: relative;
            display: inline-block;
            width: 5px;
            color: rgba(255,255,255,0.4);
            z-index: 1;
            float: right;
        }
        .copylink {
            width: 117px !important;
            margin-left: 3px;
            margin-bottom: 3px;
        }
        .thumbnail_new {
            background-color: black;
            width: 225px;
            height: 175px;
            display: inline-block;
            background-size: contain;
            background-position: center center;
            background-repeat: no-repeat;
        }

        #html5-watermark {
            position: absolute !important;
            top: auto !important;
            left: auto !important; 
            right: 10px !important;   
            bottom: 56px !important;
        }
        </style>
        ";
        
        if(count($images)>0 ) {
            $responseHtml .= "<div class='row'>";
            foreach ($images as $key => $image) {
                $ext = substr($image, strrpos($image, '.')+1);
                $imagethumbnail = $this->fileUrlLink($image, $folder);
                $basename = $this->fileBaseName($image);
                $dataAttr="";
                $imgInfo = UploadClass::getImageInfo2($this->fileUrlLink($image, $folder));
                if(empty($imgInfo['height']) && empty($imgInfo['width'])) {
                    $imgInfo = UploadClass::getImageInfo($this->fileUrlLink($image, $folder));
                }

                //Html5Lightbox
                $responseHtml .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 placeholder">';
                    $responseHtml .= '<div class="imgSizeInfo">&nbsp;<span>'.$basename.' </span></div>';
                        $responseHtml .= '<a href="'.$this->fileUrlLink($image, $folder).'" class="html5lightbox" data-group="mygroup"  data-thumbnail="'.$this->fileUrlLink($image, $folder).'" '.$dataAttr.'>';
                            $responseHtml .= '<div class="thumbnail_new img-responsive" style="background-image: url('.$imagethumbnail.');"></div>';
                        $responseHtml .= '</a>';
                $responseHtml .= '</div>';  
            }
            $responseHtml .= "</div>";
        } else {
            $responseHtml .= "<div class='row-fluid'><div class='col-sm-12 alert alert-warning'>No images attached to the project.</div></div>";
        }
        
        //Initialize Lightbox
        $responseHtml .= '<script>
            $(document).ready(function() {
                $(".html5lightbox").html5lightbox();
            });
        </script>';
        
        $this->response->setContent($responseHtml);
        return $this->response;
    }

    /**
     * [projectJsonAction description]
     * @return [type] [description]
     */
    public function projectJsonAction()
    {
        $projects = Projects::find(["order" => "id ASC"]);   
        $resData = json_decode($result["data"], TRUE);
        $totalData = $totalFiltered = count($resData);
        //Setting Data for Datatable
        $json_data = [  "draw" => intval( $data['draw'] ),  
                        "recordsTotal" => intval( $totalData ),  
                        "recordsFiltered" => intval( $totalFiltered ), 
                        "data" => $resData ];
        $this->response->setContentType('application/json');
        $this->response->setJsonContent($json_data);
        return $this->response;
    }

    /**
     * [deletedAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    public function deletedAction()
    {
        $this->view->disable();
        $data = $this->request->getPost();
        $id = (int)$data['projectid'];
        $projects = Projects::findFirst($id);
        if($projects&&count($projects)>0) {
            $result = Projects::deleteProject($id);
            if($result) {
                $result = Helpers::notify('success', 'Project successfully deleted.');
                $result['id'] = $id;
                $result['close'] = 2;
            } else {
                $result = Helpers::notify('error', 'Unable to delete project.');
            }
        } else {
            $result = Helpers::notify('error', 'Unable to find project.');
        }
        return json_encode($result);
    }

    /**
     * [detailsAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */ //echo '<pre>'; var_dump($post); echo '</pre>'; die();
    public function detailsAction($id)
    {
        $this->view->disable();
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();

        $fields = PropertyClass::$project_display_fields;
        unset($fields['id']);

        $id = $filter->sanitize($id, "int");
        $project = Projects::findFirst($id);
        //Districts
        $district_options=[''=>'- Select -'];
        $district_opt = PropertyDistricts::find(["columns"=>"name"]);
        if($district_opt&&$district_opt->count()>0) {
            foreach ($district_opt as $key => $value) $district_options[$value->name] = $value->name;
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
        //PropertyUnits
        $units_options=[''=>'- Select -'];
        $units_opt = PropertyUnits::find(["columns"=>"name"]);
        if($units_opt&&$units_opt->count()>0) {
           foreach ($units_opt as $key => $value) $units_options[$value->name] = $value->name; 
        }
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
        //PropertyAgencies
        $agencies_options=[''=>'- Select -'];
        $agencies_opt = PropertyAgencies::find(["columns"=>"name"]);
        if($agencies_opt&&$agencies_opt->count()>0) {
           foreach ($agencies_opt as $key => $value) $agencies_options[$value->name] = $value->name; 
        }  
        //Status
        $status_options=[''=>'- Select -'];
        $status_opt = PropertyStatus::find(["columns"=>"name"]);
        if($status_opt&&$status_opt->count()>0) {
           foreach ($status_opt as $key => $value) $status_options[$value->name] = $value->name; 
        }  

        $response = '<div role="form" class="row">';
        $response .= '<form method="POST" action="allprojects/saveProject" id="postprojects" data-remote="data-remote" >';
        foreach ($fields as $key => $field) {
            $response .= '<input type="hidden" id="id" name="id" readonly value="'.$project->id.'">';
            $response .= '<div class="container m-b-xs">';
                $response .= '<div class="form-group">';
                    $response .= '<div class="col-xs-3"><label for="'.$key.'" class="control-label" style="padding-top: 10px;">'.$key.'</label></div>';
                        $cnt = count($field);
                        foreach ($field as $fldname) {
                            switch($fldname) { 
                                case 'district':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($district_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'tenure':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($tenure_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'property_type':
                                    $sel_proptype = [];
                                    if(!empty($project->$fldname)) {
                                        if(strpos($project->$fldname, ",")) {
                                            $sel_proptype = explode(",", $project->$fldname);
                                        } else {
                                            $sel_proptype[] = $project->$fldname;
                                        }
                                    }   
                                    $response .= '<div class="col-xs-6">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'[]" class="form-control select2" multiple>';    
                                        $property_options = array_unique($property_options);
                                        foreach ($property_options as $key => $value) {
                                            $selected = (!empty($sel_proptype)&&in_array($value,$sel_proptype)) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>'; 
                                    $response .= '</div>'; 
                                    break;
                                case 'unit_type':
                                    $sel_unittype = [];
                                    if(!empty($project->$fldname)) {
                                        if(strpos($project->$fldname, ",")) {
                                            $sel_unittype = explode(",", $project->$fldname);

                                        } else {
                                            $sel_unittype[] = $project->$fldname;
                                        }
                                    }   
                                    $response .= '<div class="col-xs-9">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'[]" class="form-control select2" multiple>';    
                                        foreach ($units_options as $key => $value) {
                                            $selected = (!empty($sel_unittype)&&in_array($value,$sel_unittype)) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'project_type':                             
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($projtypes_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;
                                case 'proj_property_type':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($projproptypes_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;
                                case 'available_unit_type':
                                    $sel_unittype = [];
                                    if(!empty($project->$fldname)) {
                                        if(strpos($project->$fldname, ",")) {
                                            $sel_unittype = explode(",", $project->$fldname);
                                        } else {
                                            $sel_unittype[] = $project->$fldname;
                                        }
                                    } 
                                    $response .= '<div class="col-xs-6">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'[]" class="form-control select2" multiple>';    
                                        foreach ($units_options as $key => $value) {
                                            $selected = (!empty($sel_unittype)&&in_array($value,$sel_unittype)) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'tender_agency':
                                    $sel_tagency = [];
                                    if(!empty($project->$fldname)) {
                                        if(strpos($project->$fldname, ",")) {
                                            $sel_tagency = explode(",", $project->$fldname);
                                        } else {
                                            $sel_tagency[] = $project->$fldname;
                                        }
                                    } 
                                    $response .= '<div class="col-xs-6">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'[]" class="form-control select2" multiple>';    
                                        foreach ($agencies_options as $key => $value) {
                                            $selected = (!empty($sel_tagency)&&in_array($value,$sel_tagency)) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'marketing_agency':
                                    $sel_magency = [];
                                    if(!empty($project->$fldname)) {
                                        if(strpos($project->$fldname, ",")) {
                                            $sel_magency = explode(",", $project->$fldname);
                                        } else {
                                            $sel_magency[] = $project->$fldname;
                                        }
                                    } 
                                    $response .= '<div class="col-xs-6">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'[]" class="form-control select2" multiple>';    
                                        foreach ($agencies_options as $key => $value) {
                                            $selected = (!empty($sel_magency)&&in_array($value,$sel_magency)) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'status':
                                case 'status2':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($status_options as $key => $value) {
                                            $selected = (!empty($project->$fldname)&&$project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;
                                case 'status_date':
                                case 'status2_date':
                                case 'gls_sold_date':
                                case 'date_avail_unit_updated':
                                case 'vacant_date':
                                case 'stb_application_date':
                                case 'stb_approval_date':
                                case 'approved_date':
                                case 'issue_date':
                                case 'ds_date':
                                case 'vacant_possession_date':
                                case 'date_updated':
                                case 'completion_date':
                                case 'available_date':
                                    $response .= '<div class="col-xs-3">'; //$project->$fldname date("d-M-Y", strtotime($project->$fldname))
                                    $date_value = (!empty($project->$fldname)&&strtotime($project->$fldname)>=0) ? date("d-M-Y", strtotime($project->$fldname))  : ''; 
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong datepick" value="'.$date_value.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                    $response .= '</div>';
                                    break;
                                case 'description':
                                    $response .= '<div class="col-xs-9">';
                                    $response .= '<textarea name="'.$fldname.' id="'.$fldname.'" class="form-control text-strong" rows="4" >'.$project->$fldname.'</textarea>';   //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                    $response .= '</div>'; 
                                    break;
                                case 'developer':
                                    $response .= '<div class="col-xs-6">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                    $response .= '</div>'; 
                                    break;
                                case 'successful_tenderer':
                                    $response .= '<div class="col-xs-9">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                    $response .= '</div>'; 
                                    break;
                                case 'mrt_distance_km':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                            $response .= '<span class="input-group-addon"><b>km.</b></span>';
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'primary_school_within_1km':
                                    $response .= '<div class="col-xs-9">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                    $response .= '</div>'; 
                                    break;
                                case 'top_year':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<span class="input-group-addon"><b>Year</b></span>';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                        $response .= '</div>';
                                    $response .= '</div>';
                                    break;
                                case 'top_month':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<span class="input-group-addon"><b>Month</b></span>';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'top_date':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<span class="input-group-addon"><b>Date</b></span>';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'site_area_sqft':
                                case 'gfa_sqft':
                                case 'non_residential_area_sqft':
                                case 'office_sqft':
                                case 'retail_sqft':
                                case 'factory_sqft':
                                case 'warehouse_sqft':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                            $response .= '<span class="input-group-addon"><b>ft&sup2;</b></span>'; 
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'site_area_sqmt':
                                case 'gfa_sqmt':
                                case 'non_residential_area_sqm':
                                case 'office_sqm':
                                case 'retail_sqm':
                                case 'factory_sqm':
                                case 'warehouse_sqm':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
                                            $response .= '<span class="input-group-addon"><b>m&sup2;</b></span>';   
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'low_psf':
                                case 'median_psf':
                                case 'high_psf':
                                case 'no_transactions':
                                case 'project_name':
                                case 'transaction_month':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" readonly value="'.$project->$fldname.'">';
                                    $response .= '</div>';
                                    break;
                                default:
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" >'; //placeholder="'.ucwords(str_replace('_',' ',$fldname)).'"
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
        jQuery.fn.ForceNumericOnly = function() {
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
            
            $(".numericOnly").ForceNumericOnly();
            $(document).on("keydown", "#status_date, #status2_date, #gls_sold_date, #date_avail_unit_updated, #vacant_date, #stb_application_date, #stb_approval_date, #approved_date, #issue_date, #completion_date, #ds_date, #vacant_possession_date, #date_updated, #available_date, #transaction_month", function(e) {
                var code = (e.keyCode || e.which);
                if(code===8 || code===46 || code===37 || code===38 || code===39) return false;
                e.preventDefault();
            });
            $("#status_date, #status2_date, #gls_sold_date, #date_avail_unit_updated, #vacant_date, #stb_application_date, #stb_approval_date, #approved_date, #issue_date, #completion_date, #ds_date, #vacant_possession_date, #date_updated, #available_date, #transaction_month").datetimepicker({
                format:"d-M-Y",
                timepicker: false
            }); 
        });
        </script>';

        return $response;
    }

    /**
     * [saveProjectAction description]
     * @return [type] [description]
     */#echo '<pre>'; var_dump($test); echo '</pre>'; die(); 
    public function saveProjectAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) $data = $this->request->getPost();   
   
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $id = $filter->sanitize($data['id'], "int");
        $saveProject = Projects::findFirst(['conditions'=>'id=?1','bind'=>[1=>$id]]);  
        if(!$saveProject) {
            $result = Helpers::notify('error', 'Unable to find project.');
        } else {                   
            $updateDetails = false;
            foreach ($data as $field => $value) {
                switch ($field) {
                    case 'low_psf':
                    case 'median_psf':
                    case 'high_psf':
                    case 'no_transactions':
                    
                        continue;
                        break;
                    case 'project_type':
                        if($saveProject->$field!=$value) $updateDetails = true;
                        $saveProject->$field = (!empty($value)) ? $value : NULL;
                        break;
                    case 'property_type':
                    case 'unit_type':
                    case 'available_unit_type':
                    case 'tender_agency':
                    case 'marketing_agency':
                        if(!empty($value)&&is_array($value)) {
                            $saveProject->$field =  implode(',', $value);
                        }
                        break;
                    case 'district':
                    case 'tenure':
                    case 'proj_property_type':
                    case 'status':
                    case 'status2':
                        if(!empty($value)) {
                            $saveProject->$field = $filter->sanitize($value, "string");
                        }
                        break;
                    case 'description_id':
                        if(!empty($value)) {
                            $saveProject->description = $filter->sanitize($value, "string");
                        }
                        break;
                    case 'status2_date':
                    case 'gls_sold_date':
                    case 'stb_application_date':
                    case 'stb_approval_date':
                    case 'status_date':
                    case 'approved_date':
                    case 'date_avail_unit_updated':
                    case 'issue_date':
                    case 'ds_date':
                    case 'vacant_possession_date':
                    case 'date_updated':
                    case 'completion_date':
                    case 'transaction_month':
                        if(!empty($value)) {
                            $date = \DateTime::createFromFormat('d-M-Y', trim($value));
                            $saveProject->$field = $date->format('Y-m-d');
                        }              
                        break;
                    case 'no_of_rentals':
                    case 'highest_flr':
                    case 'top_no':
                    case 'top_date':
                        if(!empty($value)) {
                            $saveProject->$field = $filter->sanitize($value, "int");
                        }
                        break;
                    case 'mrt_distance':
                    case 'floor_area':
                    case 'cost':
                    case 'rental_low_psf_pm':
                    case 'rental_median_psf_pm':
                    case 'rental_high_psf_pm':
                        if(!empty($value)&&$value>0) {
                            $saveProject->$field = number_format((float)$value, 2, '.', '');
                        }
                        break;
                    case 'site_area_sqft':
                    case 'site_area_sqmt':
                    case 'gfa_sqft':
                    case 'gfa_sqmt':
                    case 'non_residential_area_sqft':
                    case 'non_residential_area_sqm':
                    case 'office_sqft':
                    case 'office_sqm':
                    case 'retail_sqft':
                    case 'retail_sqm':
                    case 'factory_sqft':
                    case 'factory_sqm':
                    case 'warehouse_sqft':
                    case 'warehouse_sqm':
                        if(!empty($value)&&$value>0) {
                            $saveProject->$field = number_format((float)$value, 2, '.', '');
                        }
                        break;
                    default:
                        if(!empty($value)) {
                            $saveProject->$field =  $filter->sanitize($value, "string");
                        }
                        break;
                }
                
                try {
                    $saveProject->save();
                    if($updateDetails) ProjectDetails::updateDetails(['project_id'=>$id,'project_type'=>$data['project_type']]);
                    //$this->updateDetails(['project_id'=>$id,'project_type'=>$data['project_type']]);
                    $result = Helpers::notify('success', 'Project successfully updated.');
                    $result['close'] = 1;
                }  catch(\Exception $e) {
                    error_log("Error: ".$e->getMessage());
                    $result = Helpers::notify('error', 'Unable to save project.');
                }
            }
            //$projectName->project_name = $data['project_name'];
        }
        return json_encode($result);

    }

    /**
     * [updateDetails description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    private function updateDetails($param)
    {
        $filter = new \Phalcon\Filter();
        $project_id = $filter->sanitize($param['project_id'], "int");
        $project_type = $filter->sanitize($param['project_type'], "string");
        ProjectDetails::updateDetails(['project_id'=>$project_id,'project_type'=>$project_type]);
    }
}
