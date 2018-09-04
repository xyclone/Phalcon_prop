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
    public function initialize()
    {
        $this->tag->setTitle('');
        parent::initialize();
    }

    public function indexAction()
    {
        // if (empty($this->session->get('user')['username'])) {
        //     return $this->response->redirect('Login');
        // }
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
    public function processAction()
    {
        if (!$this->request->isPost())  return $this->redirectBack();
        if (!$this->security->checkToken())  return $this->redirectBack();

        $data = $this->request->getPost();
        //foreach ($data as $key => $value) $data[$key] = $this->filter->sanitize($value, "string");
   
        $sql = ""; $binds = [];
        if(!empty($data['project_type'])) {
            $binds['project_type_id'] = $data['project_type'];
        }
        if(!empty($data['project_property_type'])) {
            $binds['proj_property_type_id'] = $data['project_property_type'];
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
        if(!empty($data['property_type_id'])) {
            $binds['property_type_id'] = $data['property_type_id'];
        }
        if(!empty($data['unit_type'])) {
            $binds['unit_type_id'] = $data['unit_type'];
            if(is_array($data['unit_type'])&&count($data['unit_type'])>0) {
                $binds['unit_type_id'] = ["concat_reg"=>true,"function"=>"CONCAT(',',proj.unit_type_id,',') REGEXP ',(".implode ("|",$data['unit_type'])."),'"];
            }
        }
        if(!empty($data['available_unit_type'])) {
            $binds['available_unit_type_id'] = $data['available_unit_type'];
            $binds['available_unit_type_id'] = $data['available_unit_type'];
            if(is_array($data['available_unit_type'])&&count($data['available_unit_type'])>0) {
                $binds['available_unit_type_id'] = ["concat_reg"=>true,"function"=>"CONCAT(',',proj.available_unit_type_id,',') REGEXP ',(".implode ("|",$data['available_unit_type'])."),'"];
            }
        }
        if(!empty($data['tenure_id'])) {
            $binds['tenure_id'] = $data['tenure_id'];
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
        $visCols = ['id', 'project_name', 'median_psf', 'status_name', 'status_date'];
        foreach ($raw as $rkey => $rvalue) {
            if (in_array($rkey, $visCols)) {
                $NameCols[$rkey] = $rvalue;
            } else continue;
        }
//echo '<pre>'; var_dump($NameCols); echo '</pre>'; die();  
        $hiddenfields = array_diff($DataCols, ['id', 'project_name', 'median_psf', 'status_name', 'status_date']);   
        $this->view->visCols = $NameCols;
        $this->view->hiddenCols = implode(',',array_keys($hiddenfields));
        $this->view->NameCols = array_values(PropertyClass::$project_fields);
        $this->view->DataCols = array_keys(PropertyClass::$project_fields);
        $this->view->JsonCols = json_encode($dtCols);
        $projects = Projects::findAll(["type"=>"rows","conditions"=>$binds,"order" => "proj.id ASC"]);
        $this->view->projects = ($projects&&$projects->count()>0) ? $projects->toArray() : [];
 // echo '<pre>'; var_dump($this->view->DataCols); echo '</pre>'; 
 // echo '<pre>'; var_dump($this->view->JsonCols); echo '</pre>'; 
 // echo '<pre>'; var_dump($this->view->projects); echo '</pre>'; die();

    }

    public function error403Action()
    {
    	$this->response->setHeader('HTTP/1.1 403','Forbidden');
        $this->view->pick("errors/403");
    }


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

