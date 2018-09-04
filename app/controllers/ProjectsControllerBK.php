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

class ProjectsController extends ControllerBase
{
    public $images_dir = IMG_PATH;
    public function initialize()
    {
#echo '<pre>'; var_dump('test'); echo '</pre>'; die();           
        parent::initialize();
    }

    /**
     * [indexAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->view->uploads); echo '</pre>'; die();
    public function indexAction()
    {
echo '<pre>'; var_dump('test'); echo '</pre>'; die();         
        $columns = Projects::getFields();
        //$columns = $this->replace_key($columns,'unit_type_id','unit_type_name');
        //$columns = $this->replace_key($columns,'available_unit_type_id','available_unit_type_name');
        //$columns = $this->replace_key($columns,'property_type_id','property_type_name');
        #$columns = $this->replace_key($columns,'property_type2_id','property2_type_name');
        #$columns = $this->replace_key($columns,'district_id','district_name');
        #$columns = $this->replace_key($columns,'project_type_id','project_type_name');
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
        $this->view->JsonUrl = "projects/listJson";
        // $fields = PropertyClass::$project_display_fields;
        // $this->view->form_edit = new ProjectsForm(null, ['mode'=>'update','fields'=>$fields]);
    }

    //echo '<pre>'; var_dump($cols); echo '</pre>'; die(); 
    public function listJsonAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) $data = $this->request->getPost();

        $projcolumns = Projects::getFields();

#echo '<pre>'; var_dump($projcolumns); echo '</pre>'; die(); 
        //$projcolumns = $this->replace_key($projcolumns,'unit_type_id','unit_type_name');
        //$projcolumns = $this->replace_key($projcolumns,'available_unit_type_id','available_unit_type_name');
        //$projcolumns = $this->replace_key($projcolumns,'property_type_id','property_type_name');
        #$projcolumns = $this->replace_key($projcolumns,'property_type2_id','property2_type_name');
        #$projcolumns = $this->replace_key($projcolumns,'district_id','district_name');

        #$projcolumns = $this->replace_key($projcolumns,'project_type_id','project_type_name');
        #$projcolumns = $this->replace_key($projcolumns,'creation_by','action');
        $cols = array_keys($projcolumns);
        $model = new Projects();
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
     * [imagesHtmlAction description]
     * @return [type] [description]
     */
    public function imagesHtmlAction($projType,$projPtype,$projid)
    {
        $this->view->disable();
        if (!$this->request->get())  return $this->redirectBack();

        //$data = $this->request->get();
        $folder = $projid."_".$projType."_".$projPtype;
        $images_dir = $this->images_dir.$folder.'/';

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
                $imagethumbnail = $this->fileUrlLink($image, $projid);
                $dataAttr="";
                $imgInfo = UploadClass::getImageInfo2($this->fileUrlLink($image, $projid));
                if(empty($imgInfo['height']) && empty($imgInfo['width'])) {
                    $imgInfo = UploadClass::getImageInfo($this->fileUrlLink($image, $projid));
                }
                //Html5Lightbox
                $responseHtml .= '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 placeholder">';
                //&nbsp;<a href="#" data-toggle="modal" data-target="#modal-ajax-handler" data-action="Delete Image" data-id="'.$this->fileBaseName($image).'" data-name="'.$this->fileBaseName($image).'" class="btn btn-xs btn-danger"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
                $responseHtml .= '<div class="imgSizeInfo">&nbsp;&nbsp;<span>'.$imgInfo['width'].'x'.$imgInfo['height'].' </span></div>';
                $responseHtml .= '<a href="'.$this->fileUrlLink($image, $projid).'" class="html5lightbox" data-group="mygroup"  data-thumbnail="'.$this->fileUrlLink($image, $projid).'" '.$dataAttr.'>';
                $responseHtml .= '<div class="thumbnail_new img-responsive" style="background-image: url('.$imagethumbnail.');"></div>';
                $responseHtml .= '</a>';
                $responseHtml .= '</div>';   
            }
            $responseHtml .= "</div>";
        } else {
            $responseHtml .= "<div class='container'><div class='col-sm-12 alert alert-warning'>No images attached to the project.</div></div>";
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
     *///echo '<pre>'; var_dump($project_query); echo '</pre>'; die(); 
    public function details_Action($id)
    {
        $this->view->disable();
        //$fields = PropertyClass::$project_display_fields;
        $project = Projects::findFirst([
            "conditions" => "id = :id:",
            "bind" => [
                "id" => $id
            ]
        ]);
        return json_encode($project);
    }

    public function detailsAction($id)
    {
        $this->view->disable();

        $fields = PropertyClass::$project_display_fields;
        unset($fields['id']);
        $project_query = Projects::findAll(["type"=>"rows","conditions" =>["id"=>$id]]);      
        $project = $project_query[0];

        //Districts
        $district_options=[''=>'- Select -'];
        $district_opt = PropertyDistricts::find(["columns"=>"id,name"]);
        if($district_opt&&$district_opt->count()>0) {
            foreach ($district_opt as $key => $value) $district_options[$value->id] = $value->name;
        }
        //Tenures
        $tenure_options=[''=>'- Select -'];
        $tenure_opt = PropertyTenures::find(["columns"=>"id,name"]);
        if($tenure_opt&&$tenure_opt->count()>0) {
            foreach ($tenure_opt as $key => $value) $tenure_options[$value->id] = $value->name;
        }
        //PropertyTypes
        $property_options=[''=>'- Select -'];
        $property_opt = PropertyTypes::find(["columns"=>"DISTINCT id,name"]);
        if($property_opt&&$property_opt->count()>0) {
            foreach ($property_opt as $key => $value) $property_options[$value->id] = $value->name;
        }
        //PropertyUnits
        $units_options=[''=>'- Select -'];
        $units_opt = PropertyUnits::find(["columns"=>"id,name"]);
        if($units_opt&&$units_opt->count()>0) {
           foreach ($units_opt as $key => $value) $units_options[$value->id] = $value->name; 
        }
        //ProjectTypes
        $projtypes_options=[''=>'- Select -'];
        $projtypes_opt = ProjectTypes::find(["columns"=>"id,name"]);
        if($projtypes_opt&&$projtypes_opt->count()>0) {
           foreach ($projtypes_opt as $key => $value) $projtypes_options[$value->id] = $value->name; 
        }  
        //ProjectPropertyTypes
        $projproptypes_options=[''=>'- Select -'];
        $projproptypes_opt = ProjectPropTypes::find(["columns"=>"id,project_property_type","conditions"=>"project_type_id=?1","bind"=>[1=>$project->project_type_id]]);
        if($projproptypes_opt&&$projproptypes_opt->count()>0) {
           foreach ($projproptypes_opt as $key => $value) $projproptypes_options[$value->id] = $value->project_property_type; 
        }  
        //PropertyAgencies
        $agencies_options=[''=>'- Select -'];
        $agencies_opt = PropertyAgencies::find(["columns"=>"id,name"]);
        if($agencies_opt&&$agencies_opt->count()>0) {
           foreach ($agencies_opt as $key => $value) $agencies_options[$value->id] = $value->name; 
        }  
        //Status
        $status_options=[''=>'- Select -'];
        $status_opt = PropertyStatus::find(["columns"=>"id,name"]);
        if($status_opt&&$status_opt->count()>0) {
           foreach ($status_opt as $key => $value) $status_options[$value->id] = $value->name; 
        }  


        $response = '<div role="form" class="row">';
        $response .= '<form method="POST" action="projects/saveProject" id="postprojects" data-remote="data-remote" >';
        foreach ($fields as $key => $field) {
            $response .= '<input type="hidden" id="id" name="id" readonly value="'.$project->id.'">';
            $response .= '<div class="container m-b-xs">';
                $response .= '<div class="form-group">';
                    $response .= '<div class="col-xs-3"><label for="'.$key.'" class="control-label" style="padding-top: 10px;">'.$key.'</label></div>';
                        $cnt = count($field);
                        foreach ($field as $fldname) {
                            switch($fldname) { 
                                case 'district_name':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($district_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'tenure_name':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($tenure_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>'; 
                                    break;
                                case 'property_type_name':
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
                                    // $response .= '<script>
                                    //     $(".select2").select2({theme: "bootstrap", width: "100%", placeholder: "- Select -",});
                                    //     $("#'.$fldname.'").select2().select2('.$str_selected.');
                                    // </script>';
                                    $response .= '</div>'; 
                                    break;
                                case 'unit_type_name':
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
                                case 'project_type_name':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($projtypes_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;
                                case 'proj_property_type_name':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<select id="'.$fldname.'" name="'.$fldname.'" class="form-control select2">';    
                                        foreach ($projproptypes_options as $key => $value) {
                                            $selected = ($project->$fldname==$value) ? 'selected' : '';
                                            $response .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                        }
                                    $response .= '</select>';
                                    $response .= '</div>';
                                    break;
                                case 'available_unit_type_name':
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
                                case 'status_name':
                                case 'status2_name':
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
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong datepick" value="'.$date_value.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                    $response .= '</div>';
                                    break;
                                case 'description':
                                    $response .= '<div class="col-xs-9">';
                                    $response .= '<textarea name="'.$fldname.' id="'.$fldname.'" class="form-control text-strong" rows="4" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">'.$project->$fldname.'</textarea>';    
                                    $response .= '</div>'; 
                                    break;
                                case 'developer':
                                    $response .= '<div class="col-xs-6">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';    
                                    $response .= '</div>'; 
                                    break;
                                case 'successful_tenderer':
                                    $response .= '<div class="col-xs-9">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';    
                                    $response .= '</div>'; 
                                    break;
                                case 'mrt_distance_km':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';  
                                            $response .= '<span class="input-group-addon"><b>km.</b></span>';
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'primary_school_within_1km':
                                    $response .= '<div class="col-xs-9">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';  
                                    $response .= '</div>'; 
                                    break;
                                case 'top_year':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<span class="input-group-addon"><b>Year</b></span>';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';  
                                        $response .= '</div>';
                                    $response .= '</div>';
                                    break;
                                case 'top_month':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<span class="input-group-addon"><b>Month</b></span>';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';  
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'top_date':
                                    $response .= '<div class="col-xs-3">';
                                        $response .= '<div class="input-group">';
                                            $response .= '<span class="input-group-addon"><b>Date</b></span>';
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';  
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
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">'; 
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
                                            $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
                                            $response .= '<span class="input-group-addon"><b>m&sup2;</b></span>';   
                                        $response .= '</div>';
                                    $response .= '</div>'; 
                                    break;
                                case 'project_name':
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" readonly value="'.$project->$fldname.'">';
                                    $response .= '</div>';
                                    break;
                                default:
                                    $response .= '<div class="col-xs-3">';
                                    $response .= '<input type="text" id="'.$fldname.'" name="'.$fldname.'" class="form-control text-strong" value="'.$project->$fldname.'" placeholder="'.ucwords(str_replace('_',' ',$fldname)).'">';
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
            
            $(".numericOnly").ForceNumericOnly();
            $(document).on("keydown", "#status_date, #status2_date, #gls_sold_date, #date_avail_unit_updated, #vacant_date, #stb_application_date, #stb_approval_date, #approved_date, #issue_date, #completion_date, #ds_date, #vacant_possession_date, #date_updated, #available_date", function(e) {
                var code = (e.keyCode || e.which);
                if(code===8 || code===46 || code===37 || code===38 || code===39) return false;
                e.preventDefault();
            });
            $("#status_date, #status2_date, #gls_sold_date, #date_avail_unit_updated, #vacant_date, #stb_application_date, #stb_approval_date, #approved_date, #issue_date, #completion_date, #ds_date, #vacant_possession_date, #date_updated, #available_date").datetimepicker({
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
            //unset($projectName);
            $updateDetails = false;
            //$saveProject = Projects::findFirst(["conditions"=>"id=?1","bind"=>[1=>$projectName[0]->id]]);
            foreach ($data as $field => $value) {
                switch ($field) {
                    case 'district_name':
                        $saveProject->district_id = $value;
                        break;
                    case 'tenure_name':
                        $saveProject->tenure_id = $value;
                        break;
                    case 'property_type_name':
                        if(!empty($value)&&is_array($value)) {
                            $saveProject->property_type_id = implode(',', $value);
                        }
                        break;
                    case 'project_type_name':
                        if($saveProject->project_type_id!=$value) $updateDetails = true;
                        $saveProject->project_type_id = $value;
                        break;
                    case 'proj_property_type_name':
                        $saveProject->proj_property_type_id = $value;
                        break;
                    case 'unit_type_id':
                    case 'available_unit_type_id':
                        if(!empty($value)&&is_array($value)) {
                            $saveProject->$field = implode(',', $value);
                        }
                        break;
                    case 'tender_agency':
                    case 'marketing_agency':
                        if(!empty($value)&&is_array($value)) {
                            $saveProject->$field = implode(',', $value);
                        }
                        break;
                    case 'status_name':
                        $saveProject->status_id = $value;
                        break;
                    case 'status2_name':
                        $saveProject->status2_id = $value;
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
                        if(!empty($value)) {
                            $date = \DateTime::createFromFormat('j-M-Y', trim($value));
                            $saveProject->$field = $date->format('Y-m-d');
                        }                    
                        break;
                    case 'no_transactions':
                    case 'no_of_rentals':
                    case 'highest_flr':
                    case 'project_ref_no':
                    case 'top_no':
                    case 'top_date':
                        if($value>0) {
                            $saveProject->$field = $filter->sanitize($value, "int");
                        } else {
                            $saveProject->$field = NULL;
                        }
                        break;
                    case 'mrt_distance':
                    case 'low_psf':
                    case 'median_psf':
                    case 'high_psf':
                    case 'floor_area':
                    case 'cost':
                    case 'rental_low_psf_pm':
                    case 'rental_median_psf_pm':
                    case 'rental_high_psf_pm':
                        if($value>0) {
                            $saveProject->$field = (float)$value;
                        } else {
                            $saveProject->$field = NULL;
                        }
                        break;
                    case 'site_area_sqft':
                        if(!empty($value)&&empty($data['site_area_sqmt'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->site_area_sqmt = (float)($value*0.092903);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'site_area_sqmt':
                        if(!empty($value)&&empty($data['site_area_sqft'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->site_area_sqft = (float)($value*10.7639);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'gfa_sqft':
                        if(!empty($value)&&empty($data['gfa_sqmt'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->gfa_sqmt = (float)($value*0.092903);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'gfa_sqmt':
                        if(!empty($value)&&empty($data['gfa_sqft'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->gfa_sqft = (float)($value*10.7639);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'non_residential_area_sqft':
                        if(!empty($value)&&empty($data['non_residential_area_sqm'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->non_residential_area_sqm = (float)($value*0.092903);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'non_residential_area_sqm':
                        if(!empty($value)&&empty($data['non_residential_area_sqft'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->non_residential_area_sqft = (float)($value*10.7639);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'office_sqft':
                        if(!empty($value)&&empty($data['office_sqm'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->office_sqm = (float)($value*0.092903);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'office_sqm':
                        if(!empty($value)&&empty($data['office_sqft'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->office_sqft = (float)($value*10.7639);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'retail_sqft':
                        if(!empty($value)&&empty($data['retail_sqm'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->retail_sqm = (float)($value*0.092903);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'retail_sqm':
                        if(!empty($value)&&empty($data['retail_sqft'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->retail_sqft = (float)($value*10.7639);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'factory_sqft':
                        if(!empty($value)&&empty($data['factory_sqm'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->factory_sqm = (float)($value*0.092903);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'factory_sqm':
                        if(!empty($value)&&empty($data['factory_sqft'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->factory_sqft = (float)($value*10.7639);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'warehouse_sqft':
                        if(!empty($value)&&empty($data['warehouse_sqm'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->warehouse_sqm = (float)($value*0.092903);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    case 'warehouse_sqm':
                        if(!empty($value)&&empty($data['warehouse_sqft'])) {
                            $saveProject->$field = (float)$value;
                            $saveProject->warehouse_sqft = (float)($value*10.7639);
                        } elseif(!empty($value)) {
                            $saveProject->$field = (float)$value;
                        }
                        break;
                    default:
                        $saveProject->$field =  $filter->sanitize($value, "string");
                        break;
                }
                
                try {
                    $saveProject->save();
                    if($updateDetails) $this->updateDetails(['project_id'=>$data['id'],'project_type_id'=>$data['project_type_name']]);
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

    private function updateDetails($param)
    {
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $project_id = $filter->sanitize($param['project_id'], "int");
        $project_type_id = $filter->sanitize($param['project_type_id'], "int");
        ProjectDetails::updateDetails(['project_id'=>$project_id,'project_type_id'=>$project_type_id]);
    }
}