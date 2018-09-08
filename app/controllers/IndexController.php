<?php
#namespace Property\Controllers;

//core
use Phalcon\Mvc\Url;
use Phalcon\Http\Request;
use Property\Helpers\Helpers;
use Property\Library\DataTable;
use Property\Classes\PropertyClass;
//Forms
use Property\Forms\SearchForm;
//Models
use Property\Models\Projects;
use Property\Models\ProjectDetails;
use Property\Models\ProjectPropTypes;
use Property\Models\ProjectTypes;
use Property\Models\PropertyAgencies;
use Property\Models\PropertyDistricts;
use Property\Models\PropertyStatus;
use Property\Models\PropertyTenures;
use Property\Models\PropertyTypes;
use Property\Models\PropertyUnits;

class IndexController extends ControllerBase
{
    /**
     * [initialize description]
     * @return [type] [description]
     */
    public function initialize()
    {
        $this->tag->setTitle('');
        parent::initialize();
    }

    /**
     * [indexAction description]
     * @return [type] [description]
     */
    public function indexAction()
    {
        $this->view->tokenKey = $this->security->getTokenKey();
        $this->view->token = $this->security->getToken();
        $this->view->form = new SearchForm(null, []);
        $this->view->link_action = 'index/process';
        $this->view->form_name = 'search_form';
        $this->view->pick("index/index");
    }

    /**
     * [processAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->view->uploads); echo '</pre>'; die();
    public function process2Action()
    {
        if (!$this->request->isPost())  return $this->redirectBack();
        //if (!$this->security->checkToken())  return $this->redirectBack();

        $data = $this->request->getPost();
        //foreach ($data as $key => $value) $data[$key] = $this->filter->sanitize($value, "string");

        $sql = ""; $binds = [];
        if(!empty($data['project_type'])) {
            $binds['project_type'] = $data['project_type'];
        }
        if(!empty($data['project_property_type'])) {
            $binds['proj_property_type'] = $data['project_property_type'];
        }
        if(!empty($data['project_name'])) {
            $binds['project_name'] = $data['project_name'];
        }
        if(!empty($data['planning_region'])) {
            $binds['planning_region'] = $data['planning_region'];
        }
        if(!empty($data['planning_area'])) {
            $binds['planning_area'] = $data['planning_area'];
        }
        if(!empty($data['property_type'])) {
            $binds['property_type'] = $data['property_type'];
        }
        if(!empty($data['unit_type'])) {
            $binds['unit_type'] = $data['unit_type'];
            if(is_array($data['unit_type'])&&count($data['unit_type'])>0) {
                $binds['unit_type'] = ["concat_reg"=>true,"function"=>"CONCAT(',',proj.unit_type,',') REGEXP ',(".implode ("|",$data['unit_type'])."),'"];
            }
        }
        if(!empty($data['tenure'])) {
            $binds['tenure'] = $data['tenure'];
        }
        if(!empty($data['street_name'])) {
            $binds['street_name'] = $data['street_name'];
        }
        if(!empty($data['primary_school_within_1km'])) {
            $binds['primary_school_within_1km'] = $data['primary_school_within_1km'];
        }

        $DataCols = array_keys(PropertyClass::$project_fields);
        foreach ($DataCols as $val) $dtCols[] = ["data"=>$val,'name'=>$val];
        $raw = PropertyClass::$project_fields; $NameCols=[];
        $visCols = ['id', 'project_name', 'median_psf', 'status_date'];
        foreach ($raw as $rkey => $rvalue) {
            if (in_array($rkey, $visCols)) {
                $NameCols[$rkey] = $rvalue;
            } else continue;
        }
        $hiddenfields = array_diff($DataCols, ['id', 'project_name', 'median_psf', 'status_date']);   
        $this->view->visCols = $NameCols;
        $this->view->hiddenCols = implode(',',array_keys($hiddenfields));
        $this->view->NameCols = array_values(PropertyClass::$project_fields);
        $this->view->DataCols = array_keys(PropertyClass::$project_fields);
        $this->view->JsonCols = json_encode($dtCols);
        $projects = Projects::findAll(["type"=>"rows","conditions"=>$binds,"order" => "proj.id ASC"]);   
        $this->view->projects = ($projects&&$projects->count()>0) ? $projects->toArray() : [];
    }

    /**
     * [processAction description]
     * @return [type] [description]
     */
    public function processAction()
    {
        if (!$this->request->isPost())  return $this->redirectBack();
        //if (!$this->security->checkToken())  return $this->redirectBack();

        $data = $this->request->getPost();
#echo '<pre>'; var_dump($data); echo '</pre>'; die();
#echo '<pre>'; var_dump($this->security->checkToken()); echo '</pre>'; die(); 
        $sql = ""; $binds = [];
        if(!empty($data['project_type'])) {
            $binds['project_type'] = $data['project_type'];
        }
        if(!empty($data['proj_property_type'])) {
            $binds['proj_property_type'] = $data['proj_property_type'];
        }
        if(!empty($data['project_name'])) {
            $binds['project_name'] = '%'.$data['project_name'].'%';
        }
        if(!empty($data['district'])) {
            $binds['district'] = $data['district'];
        }
        if(!empty($data['planning_area'])) {
            $binds['planning_area'] = $data['planning_area'];
        }
        if(!empty($data['property_type'])) {
            $binds['property_type'] = $data['property_type'];
        }
        if(!empty($data['unit_type'])) {
            if(is_array($data['unit_type'])&&count($data['unit_type'])>0)
                $binds['unit_type'] = '%'.implode(',',$data['unit_type']).'%';
        }
        if(!empty($data['min_budget'])) {
            $binds['min_budget'] = $data['min_budget'];
        }
        if(!empty($data['max_budget'])) {
            $binds['max_budget'] = $data['max_budget'];
        }
        if(!empty($data['tenure'])) {
            $binds['tenure'] = $data['tenure'];
        }
        if(!empty($data['mrt'])) {
            $binds['mrt'] = $data['mrt'];
        }
        if(!empty($data['top_year'])) {
            $binds['top_year'] = $data['top_year'];
        }
        if(!empty($data['street_name'])) {
            $binds['street_name'] = '%'.$data['street_name'].'%';
        }
        if(!empty($data['primary_school_within_1km'])) {
            if(is_array($data['primary_school_within_1km'])&&count($data['primary_school_within_1km'])>0)
                $binds['primary_school_within_1km'] = '%'.implode(',',$data['primary_school_within_1km']).'%';
        }
        if(!empty($data['total_units'])) {
            $binds['total_units'] = $data['total_units'];
        }
        //PerProject
        

        $DataCols = array_keys(PropertyClass::$project_fields);
        foreach ($DataCols as $val) $dtCols[] = ["data"=>$val,'name'=>$val];
        $raw = PropertyClass::$project_fields; $NameCols=[];
        $visCols = ['project_name', 'status', 'status_date', 'median_psf', 'no_transactions', 'transaction_month', 'total_units', 'mrt', 'mrt_distance_km', 'district', 'planning_area', 'street_name', 'tenure', 'top_year', 'property_type', 'unit_type', 'primary_school_within_1km'];
        foreach ($raw as $rkey => $rvalue) {
            if (in_array($rkey, $visCols)) {
                $NameCols[$rkey] = $rvalue;
            } else continue;
        }


        $idx=0; $conditions=""; $skipMaxBudget = false; $skipMaxArea = false;
        foreach ($data as $field => $value) {
            if(!empty($value)) {
                                
                switch ($field) {
                    case 'project_type':
                    case 'proj_property_type':
                    case 'district':
                    case 'planning_area':
                    case 'property_type':
                    case 'tenure':
                    case 'mrt':
                        $conditions .= ($idx>0) ? " AND " : "";   
                        $conditions .= $field ." IN ('".implode("','", $binds[$field])."') ";
                        unset($binds[$field]);
                        break;
                    case 'project_name':
                    case 'unit_type':
                    case 'primary_school_within_1km':
                    case 'street_name':
                        $conditions .= ($idx>0) ? " AND " : "";   
                        $conditions .= $field ." LIKE :".$field." ";
                        break;
                    case 'total_units':
                    case 'top_year':
                        $conditions .= ($idx>0) ? " AND " : "";   
                        $tu = explode("-",$data[$field]);
                        $start = $tu[0]; $stop = $tu[1];
                        $conditions .= $field ." BETWEEN ".$start." AND ".$stop." ";
                        unset($binds[$field]);
                        break;
                    case 'mrt_distance_km':
                        $conditions .= ($idx>0) ? " AND " : "";   
                        if(strpos($data[$field], "-")) {
                            $km = explode("-",$data[$field]);
                            $start = $km[0]/1000; $stop = $km[1]/1000;
                            $conditions .= $field ." BETWEEN ".$start." AND ".$stop." ";
                        } else {
                            $value = $value/1000;
                            $conditions .= $field ." <= ".$value." ";
                        }
                        break;
                    case 'min_budget':
                        if(!empty($data['min_budget'])) {
                            $conditions .= ($idx>0) ? " AND " : "";  
                            if(!empty($data['max_budget'])) {
                                $skipMaxBudget = true;
                                $conditions .= " ((".(int)$data['max_budget']." < `low_price`) OR \n";
                                $conditions .= " (".(int)$data['min_budget']." > `low_price`) AND (".(int)$data['min_budget']." > `high_price`)) \n";
                            } else {
                                $conditions .= " ".(int)$data['min_budget']." BETWEEN `low_price` AND `high_price` \n";
                            }
                        }
                        unset($binds[$field]);
                        break;
                    case 'max_budget':
                        if(!empty($data['max_budget'])&&$skipMaxBudget!=true) {
                            $conditions .= ($idx>0) ? " AND " : "";
                            $conditions .= " ".(int)$data['max_budget']." BETWEEN `low_price` AND `high_price` \n";
                        }
                        unset($binds[$field]);
                        break;
                    case 'min_area':
                        if(!empty($data['min_area'])) {
                            $conditions .= ($idx>0) ? " AND " : "";
                            if(!empty($data['max_area'])) {
                                $skipMaxArea = true;
                                $conditions .= "`area_sqft` BETWEEN ".(int)$data['min_area']." AND ".(int)$data['max_area']." \n";
                                unset($binds['max_area']);
                            } else {
                                $conditions .= "`area_sqft` > ".(int)$data['min_area']." \n";
                            }
                        }
                        unset($binds[$field]);
                        break;
                    case 'max_area':
                        if(!empty($data['max_area'])&&$skipMaxArea!=true) {
                            $conditions .= ($idx>0) ? " AND " : "";
                            $conditions .= "`area_sqft` < ".(int)$data['max_area']." \n";
                        }
                        unset($binds[$field]);
                        break;
                    default:
                        $conditions .= ($idx>0) ? " AND " : "";   
                        $conditions .= $field ." = :".$field." ";
                        break;
                }
            }
            $idx++;
        }
        $hiddenfields = array_diff($DataCols, $visCols);   
        $this->view->visCols = $NameCols;
        $this->view->hiddenCols = implode(',',array_keys($hiddenfields));
        $this->view->NameCols = array_values(PropertyClass::$project_fields);
        $this->view->DataCols = array_keys(PropertyClass::$project_fields);
        $this->view->JsonCols = json_encode($dtCols);
        //$projects = Projects::find(["columns"=>implode(",",$visCols),"conditions"=>$conditions,"bind"=>$binds,"order"=>"id ASC"]);
        $projects = Projects::findAll(["type"=>"rows","conditions"=>$conditions,"bind"=>$binds,"order"=>"id ASC"]);
#echo '<pre>'; var_dump($projects); echo '</pre>'; die();     
        $this->view->projects = ($projects&&$projects->count()>0) ? $projects->toArray() : [];
    }
    
    /**
     * [getplanningareaAction description]
     * @return [type] [description]
     */
    public function getplanningareaAction()
    {
        $this->view->disable();
        $response=[];
        //if (!$this->security->checkToken())  return $this->redirectBack();
        if (!$this->request->get()) {  return $this->redirect('index'); }
        $data = $this->request->get();
        if(is_array($data['district'])&&count($data['district'])>0) {
            $planning_areas = Projects::find(["columns"=>"DISTINCT planning_area ","conditions"=>"district IN ({dist:array}) AND planning_area IS NOT NULL",
                "bind"=>["dist"=>$data['district']]]);
            if($planning_areas&&$planning_areas->count()>0) {
                foreach ($planning_areas as $key => $pa) {
                    $response[$pa->planning_area] = $pa->planning_area;
                }
            }
        }
        $this->response->setJsonContent($response);
        return $this->response;
    }

    /**
     * [getmrtdistanceAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($data); echo '</pre>'; die();   
    // public function getmrtdistanceAction()
    // {
    //     $this->view->disable();
    //     $response=[];
    //     if (!$this->request->get()) {  return $this->redirect('index'); }
    //     $data = $this->request->get();
    //     if(is_array($data['district'])&&count($data['district'])>0) {
    //         $planning_areas = Projects::find(["columns"=>"DISTINCT planning_area ","conditions"=>"district IN ({dist:array}) AND planning_area IS NOT NULL",
    //             "bind"=>["dist"=>$data['district']]]);
    //         if($planning_areas&&$planning_areas->count()>0) {
    //             foreach ($planning_areas as $key => $pa) {
    //                 $response[$pa->planning_area] = $pa->planning_area;
    //             }
    //         }
    //     }
    //     $this->response->setJsonContent($response);
    //     return $this->response;
    // }

    /**
     * [getprimaryschoolAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($data); echo '</pre>'; die(); 
    public function getprimaryschoolAction()
    {
        $this->view->disable();
        $response=[];
        if (!$this->request->get()) {  return $this->redirect('index'); }
        $data = $this->request->get();
        if(is_array($data['district'])&&count($data['district'])>0) {
            $primary_schools = Projects::find(["columns"=>"district, primary_school_within_1km","conditions"=>"district IN ({dist:array}) AND primary_school_within_1km IS NOT NULL", "bind"=>["dist"=>$data['district']]]);
            if($primary_schools&&$primary_schools->count()>0) {
                foreach ($primary_schools as $key => $ps) {
                    if(strpos($ps->primary_school_within_1km, ",")) {
                        $pri_schools = explode(",",$ps->primary_school_within_1km);
                        foreach ($pri_schools as $primary_school) {
                            if(!empty($primary_school)) 
                                $response[$primary_school] = $primary_school ." (".$ps->district.")";
                        }
                    } else {
                        $response[$ps->primary_school_within_1km] = $ps->primary_school_within_1km ." (".$ps->district.")";
                    }
                }
            }
        }
        $this->response->setJsonContent($response);
        return $this->response;
    }

    /**
     * [error403Action description]
     * @return [type] [description]
     */
    public function error403Action()
    {
    	$this->response->setHeader('HTTP/1.1 403','Forbidden');
        $this->view->pick("errors/403");
    }

    /**
     * [groupsessionAction description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function groupsessionAction($id)
    {
        $data = $this->session->get('acl')['usergroup'];
        foreach ($data as $key => $value) {
            if ($value['id'] === $id) {
                $_SESSION['acl']['group'] = $id;
                break;
            }
        }
        $this->response->redirect('');
    }
}

