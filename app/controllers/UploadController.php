<?php

//Core
use Phalcon\DI;
use Phalcon\DI\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\View;
use Phalcon\Image\Adapter\Imagick;
use Phalcon\Escaper;

use Property\Helpers\Helpers;
//CSV 
use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\Statement;
//Classes
use Property\Classes\PropertyClass;
use Property\Classes\UploadClass;
use Property\Classes\ToStringEnabledClass;
//Models
use Property\Models\AdminLogs;
use Property\Models\Uploads;
use Property\Models\Projects;
use Property\Models\PerProjects;
use Property\Models\ProjectDetails;
use Property\Models\ProjectTypes;
use Property\Models\ProjectPropTypes;
use Property\Models\PropertyAgencies;
use Property\Models\PropertyDistricts;
use Property\Models\PropertyStatus;
use Property\Models\PropertyTenures;
use Property\Models\PropertyTypes;
use Property\Models\PropertyUnits;
//Forms
use Property\Forms\UploadProjectForm;
use Property\Forms\UploadImagesForm;

class UploadController extends ControllerBase
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
        $this->view->setVars([
            'tokenKey' => $this->security->getTokenKey(),
            'token' => $this->security->getToken(),
            'uploads' => Uploads::find(["order" => "id ASC"]),
            'form' => new UploadProjectForm(null, []),
            'link_action' => 'upload/process',
            'form_name' => 'project_upload',
        ]);
    }

    /**
     * [imagesAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->request->hasFiles()); echo '</pre>'; die();  
    public function imagesAction()
    {
        $this->view->disable();
        if (!$this->request->isPost())  return $this->redirectBack();
        //if (!$this->security->checkToken())  return json_encode(Helpers::notify('warning', 'Invalid token id.'));
        $data = $this->request->getPost();
        $project_folder = $this->images_dir.$data['project_id'];
        if(!is_dir($project_folder)) mkdir($project_folder, 0755);
 
        $inserted=0; 
        if ($this->request->hasFiles() == true) {
#echo '<pre>'; var_dump($this->request->getUploadedFiles()); echo '</pre>'; die();  
            foreach ($this->request->getUploadedFiles() as $file) {                              
                if ($file->getSize() > 0) {
                    $fileUploaded = $file->getName();
                    $fileName = $file->getTempName();           
                    if($file->moveTo($project_folder.'/'. $fileUploaded)) {
                        $inserted++;
                    }
                }
            }
        }
        if($inserted>0) {
            $project_info = Projects::findFirst((int)$data['project_id']);
            $addUpload = new Uploads;
            $addUpload->type = $project_info->project_name."[images]";
            $addUpload->filename = $project_info->project_name."[images]";
            $addUpload->remarks = json_encode(['project'=>$project_info->project_name,'items'=>$inserted]);
            if ($addUpload->save() !== false) {
                $result = Helpers::notify('success', $inserted.' Item(s) successfully imported.');
            }
        } else {
            $result = Helpers::notify('error', 'Error uploading images.');
        }
        return json_encode($result);
    }

    /**
     * [processAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($this->request->hasFiles()); echo '</pre>'; die();    
    public function processAction()
    {
        $this->view->disable();
        if (!$this->request->isPost())  return $this->redirectBack();
        if (!$this->security->checkToken())  return json_encode(Helpers::notify('warning', 'Invalid token id.'));
        $data = $this->request->getPost();
        switch ($data['type']) {
            case 'project':
                $dbFields = UploadClass::$project_fields;
                break;
            case 'per_project':
                $dbFields = UploadClass::$project_per_project;
                break;
            case 'per_unit':
                $dbFields = UploadClass::$project_detail_fields;
                break;
        }    
        $insert=[];
        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                $delimiter = $this->detectDelimiter($file->getTempName());                                  
                if ($file->getSize()>0) {
                    $fileUploaded = $file->getName();
                    $fileName = $file->getTempName();
                    $csv = Reader::createFromPath($fileName,'r');
                    
                    $csv->setHeaderOffset(0); //set the CSV header offset                  
                    $stmt = new Statement();
                    $records = $stmt->process($csv);  
                  
                    $insert=[];                                  
                    foreach ($records as $index => $record) {
                        if(is_array($record)) {
                            $ind=0;
                            foreach ($record as $head => $res) {
                                $field_values = explode($delimiter, $res);
                                //$fields = explode($delimiter, $head);
                                $fields = preg_split("@$delimiter@", $head, NULL, PREG_SPLIT_NO_EMPTY); 
                                if(count($dbFields)!=count($fields)) {
                                    $result = Helpers::notify('error', 'Error importing data, invalid CSV.');
                                    return json_encode($result);
                                }

                                foreach ($fields as $key => $fld) {               
                                    $field_key = array_search($fld, $dbFields);
                                    if($field_key=="chinese_name") {
                                        $value_encode = !empty(html_entity_decode($field_values[$key])) ? html_entity_decode($field_values[$key]) : '' ;
                                    } else {
                                        $value_encode = !empty($field_values[$key]) ? $field_values[$key] : '';
                                    }
                                    $insert[$index][$field_key] = trim($value_encode);
                                }
                                $ind++;
                            }  
                        }
                    }
                }
            }
        }
#echo '<pre>'; var_dump($data); echo '</pre>'; die(); 
        switch ($data['type']) {
            case 'project':
                $inserted = $this->addProjects($insert);
                break;
            case 'per_project':
                $inserted = $this->addPerProject($insert);
                break;
            case 'per_unit':
                $inserted = $this->addProjectsDetails($insert);
                break;
        }

        if(is_array($inserted)&&$inserted['status']=="Error") {
            $addUpload = new Uploads;
            $addUpload->type = $data['type'];
            $addUpload->filename = $fileUploaded;
            $addUpload->remarks = json_encode(['status'=>$inserted['status'],'message'=>$inserted['message']]);
            if ($addUpload->save() !== false) {
                $result = Helpers::notify('error', 'Error importing '.$data['type'].'.');
            }
        } else {
            if($inserted>0) {
                $addUpload = new Uploads;
                $addUpload->type = $data['type'];
                $addUpload->filename = $fileUploaded;
                $addUpload->remarks = json_encode(['items inserted'=>$inserted]);
                if ($addUpload->save() !== false) {
                    $result = Helpers::notify('success', $inserted.' Item(s) successfully imported.');
                }
            } else {
                $result = Helpers::notify('error', 'Error importing '.$data['type'].'.');
            }
        }
        return json_encode($result);
    }


    /**
     * [addProjects description]
     * @param [type] &$insert [description]
     */ //echo '<pre>'; var_dump($projectName->count()); echo '</pre>'; die(); 
    private function addProjects($insert)
    {
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $inserted=0;$totalinsert=count($insert); $start=1; $errorLogs=[];
        if(!empty($insert)&&count($insert)>0) {
            foreach ($insert as $count => $post) {             
                $project_Name = (strpos($post['project_name'], "'") !== false) ? strtoupper(preg_replace("/'/", "", $post['project_name'])) : strtoupper($filter->sanitize($post['project_name'], "string" ));
                $projectName = Projects::findProject(['project_name'=>$project_Name,'project_type'=>strtoupper($post['project_type']),'project_property_type'=>strtoupper($post['proj_property_type'])]);  
#echo '<pre>'; var_dump($projectName->toArray()); echo '</pre>'; die();                 
                if($projectName->count()===0) {
                    $process = 'create';
                    unset($projectName);
                    $projectName = new Projects();
                    $projectName->project_name = trim($post['project_name']);
                } else {
                    $process = 'update';
                    $projId = $projectName[0]->id;
                    unset($projectName);
                    $projectName = Projects::findFirst(["conditions"=>"id=?0","bind"=>[$projId]]);
                }
                foreach ($post as $field => $value) {
                    $value = $filter->sanitize($value, "string");
                    switch ($field) {
                        case 'project_name':
                            continue;
                            break;
                        case 'project_type':
                            //Project Types
                            $projectType = ProjectTypes::findFirstByName(trim($value));
                            if(!$projectType) {
                                unset($projectType);
                                $projectType = new ProjectTypes();
                                $projectType->name = $value;
                                $projectType->description = $value;
                                if ($projectType->save()) {
                                    $projectName->project_type = $projectType->name;
                                }
                            } else {
                                 $projectName->project_type = $projectType->name;
                            } // ./Project Types
                            break;
                        case 'proj_property_type':
                            //Project Property Type ID
                            if(!empty($value)) {
                                $projPropertyType = ProjectPropTypes::findFirstByName(trim($value));
                                if(!$projPropertyType) {
                                    unset($projPropertyType);
                                    $projPropertyType = new ProjectPropTypes();
                                    $projPropertyType->name = $post['project_property_type'];
                                    $projPropertyType->description = $post['project_property_type'];
                                    if ($projPropertyType->save()) {
                                        $projectName->$field = $projPropertyType->name;
                                    }
                                } else {
                                     $projectName->$field = $projPropertyType->name;
                                }
                            } // ./Project Property Type ID 
                            break;
                        case 'all_number':
                        case 'successful_tenderer':
                        case 'description':
                        case 'mrt_distance_km':
                        case 'mrt':
                        case 'primary_school_within_1km':
                        case 'locality':
                        case 'low_millions':
                        case 'high_millions':
                        case 'developer':
                            if(!empty($value)) {
                                if (strpos($value, '|') !== false) {
                                    $projectName->$field = str_replace('|',',', $value);
                                } else {
                                    $projectName->$field = $value;
                                }
                            }
                            break;
                        case 'unit_type':
                            //Unit Type ID
                            $propUnitNames = [];
                            if(!empty($value)) {
                                if (strpos($value, '|') !== false) {
                                    $allUnits = array_map('trim', explode('|', $value));
                                    foreach ($allUnits as $key => $unit) {
                                        $propertyUnits = PropertyUnits::findFirstByName($unit);
                                        if(!$propertyUnits) {
                                            unset($propertyUnits);
                                            $propertyUnits = new PropertyUnits();
                                            $propertyUnits->name = $unit;
                                            $propertyUnits->description = $unit;
                                            if ($propertyUnits->save()) {
                                                $propUnitNames[] = $propertyUnits->name;
                                            }
                                        } else {
                                            $propUnitNames[] = $propertyUnits->name;
                                        }
                                    }
                                    $projectName->$field = implode(",",$propUnitNames);
                                } else {
                                    $propertyUnits = PropertyUnits::findFirstByName(trim($value));
                                    if(!$propertyUnits) {
                                        unset($propertyUnits);
                                        $propertyUnits = new PropertyUnits();
                                        $propertyUnits->name = $value;
                                        $propertyUnits->description = $value;
                                        if ($propertyUnits->save()) {
                                            $propUnitNames[] = $propertyUnits->name;
                                        }
                                    } else {
                                        $propUnitNames[] = $propertyUnits->name;
                                    }
                                    $projectName->$field = implode(",",$propUnitNames);
                                }
                            } // ./Unit Type ID
                            break;
                        case 'available_unit_type':
                            //Available Unit Type ID
                            $propUnitNames = [];
                            if(!empty($value)) {
                                if (strpos($value, '|') !== false) {
                                    $allUnits = array_map('trim', explode('|', $value));
                                    foreach ($allUnits as $key => $unit) {
                                        $propertyUnits = PropertyUnits::findFirstByName($unit);
                                        if(!$propertyUnits) {
                                            unset($propertyUnits);
                                            $propertyUnits = new PropertyUnits();
                                            $propertyUnits->name = $unit;
                                            $propertyUnits->description = $unit;
                                            if ($propertyUnits->save()) {
                                                $propUnitNames[] = $propertyUnits->name;
                                            }
                                        } else {
                                            $propUnitNames[] = $propertyUnits->name;
                                        }
                                    }
                                    $result_propUnits = array_unique($propUnitNames);
                                    $projectName->$field = implode(",",$propUnitNames);
                                } else {
                                    $propertyUnits = PropertyUnits::findFirstByName(trim($value));
                                    if(!$propertyUnits) {
                                        unset($propertyUnits);
                                        $propertyUnits = new PropertyUnits();
                                        $propertyUnits->name = $value;
                                        $propertyUnits->description = $value;
                                        if ($propertyUnits->save()) {
                                            $propUnitNames[] = $propertyUnits->name;
                                        }
                                    } else {
                                        $propUnitNames[] = $propertyUnits->name;
                                    }
                                    $result_propUnits = array_unique($propUnitNames);
                                    $projectName->$field = implode(",",$result_propUnits);
                                }
                            }// ./Unit Type ID
                            break;
                        case 'tender_agency':
                        case 'marketing_agency':
                            //Tender/Masrketing Agency
                            $propAgencyNames=[];
                            if(!empty($value)) {
                                if (strpos($value, '|') !== false) {
                                    $allAgencies = array_map('trim', explode('|', $value));
                                    foreach ($allAgencies as $key => $agency) {
                                        $propertyAgency = PropertyAgencies::findFirstByName($agency);
                                        if(!$propertyAgency) {
                                            unset($propertyAgency);
                                            $propertyAgency = new PropertyAgencies();
                                            $propertyAgency->name = $agency;
                                            $propertyAgency->description = $agency;
                                            if ($propertyAgency->save()) {
                                                $propAgencyNames[] = $filter->sanitize($propertyAgency->name, "string");
                                            }
                                        } else {
                                            $propAgencyNames[] = $filter->sanitize($propertyAgency->name, "string");
                                        }
                                    }
                                    $result_propAgencies = array_unique($propAgencyNames);
                                    $projectName->$field = implode(",",$result_propAgencies);
                                } else {
                                    $propertyAgency = PropertyAgencies::findFirstByName(trim($value));
                                    if(!$propertyAgency) {
                                        unset($propertyAgency);
                                        $propertyAgency = new PropertyAgencies();
                                        $propertyAgency->name = $value;
                                        $propertyAgency->description = $value;
                                        if ($propertyAgency->save()) {
                                            $projectName->$field = $filter->sanitize($propertyAgency->name, "string");
                                        }
                                    } else {
                                         $projectName->$field = $filter->sanitize($propertyAgency->name, "string");
                                    }
                                }
                            } // ./Tender/Masrketing Agency
                            break;
                        case 'property_type':
                            //Property Type ID
                            $propTypeNames = [];
                            if(!empty($value)) {
                                if (strpos($value, '|') !== false) {
                                    $allPropTypes = array_map('trim', explode('|', $value));
                                    foreach ($allPropTypes as $key => $propType) {
                                        $propertyTypes = PropertyTypes::findFirstByName($propType);
                                        if(!$propertyTypes) {
                                            unset($propertyTypes);
                                            $propertyTypes = new PropertyTypes();
                                            $propertyTypes->name = $propType;
                                            $propertyTypes->description = $propType;
                                            if ($propertyTypes->save()) {
                                                $propTypeNames[] = $propertyTypes->name;
                                            }
                                        } else {
                                            $propTypeNames[] = $propertyTypes->name;
                                        }
                                    }
                                    $projectName->$field = implode(",",$propTypeNames);
                                } else {
                                    $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                                    if(!$propertyTypes) {
                                        unset($propertyTypes);
                                        $propertyTypes = new PropertyTypes();
                                        $propertyTypes->name = $value;
                                        $propertyTypes->description = $value;
                                        if ($propertyTypes->save()) {
                                            $propTypeNames[] = $propertyTypes->name;
                                        }
                                    } else {
                                        $propTypeNames[] = $propertyTypes->name;
                                    }
                                    $projectName->$field = implode(",",$propTypeNames);
                                }
                            }// ./Property Type ID   
                            break;
                        case 'tenure':
                            //Tenure ID
                            if(!empty($value)) {
                                $propertyTenure = PropertyTenures::findFirstByName(trim($value));
                                if(!$propertyTenure) {
                                    unset($propertyTenure);
                                    $propertyTenure = new PropertyTenures();
                                    $propertyTenure->name = $value;
                                    $propertyTenure->description = $value;
                                    if ($propertyTenure->save()) {
                                        $projectName->$field = $filter->sanitize($propertyTenure->name, "string");
                                    }
                                } else {
                                     $projectName->$field = $filter->sanitize($propertyTenure->name, "string");
                                }
                            }// ./Tenure ID
                            break;
                        case 'district':
                            //District ID
                            if(!empty($value)) {
                                $propertyDistrict = PropertyDistricts::findFirstByName(trim($value));
                                if(!$propertyDistrict) {
                                    unset($propertyDistrict);
                                    $propertyDistrict = new PropertyDistricts();
                                    $propertyDistrict->name = $value;
                                    $propertyDistrict->description = $value;
                                    if ($propertyDistrict->save()) {
                                        $projectName->$field = $filter->sanitize($propertyDistrict->name, "string");
                                    }
                                } else {
                                     $projectName->$field = $filter->sanitize($propertyDistrict->name, "string");
                                }
                            }// ./District ID
                            break;
                        case 'status':
                        case 'status2':
                            //Status ID
                            if(!empty($value)) {
                                $PropertyStatus = PropertyStatus::findFirstByName(trim($value));
                                if(!$PropertyStatus) {
                                    unset($PropertyStatus);
                                    $PropertyStatus = new PropertyStatus();
                                    $PropertyStatus->name = $value;
                                    $PropertyStatus->description = $value;
                                    if ($PropertyStatus->save()) {
                                        $projectName->$field = $filter->sanitize($PropertyStatus->name, "string");
                                    }
                                } else {
                                     $projectName->$field = $filter->sanitize($PropertyStatus->name, "string");
                                }
                            }// ./Status ID
                            break;
                        case 'status_date':
                        case 'status2_date':
                        case 'gls_sold_date':
                        case 'stb_application_date':
                        case 'stb_approval_date':
                        case 'approved_date':
                        case 'date_avail_unit_updated':
                        case 'issue_date':
                        case 'ds_date':
                        case 'vacant_possession_date':
                        case 'date_updated':
                        case 'available_date':
                        case 'completion_date':
                        case 'transaction_month':
                            if(!empty($value)) {
                                $date = \DateTime::createFromFormat('j-M-Y', trim($value));
                                $projectName->$field = $date->format('Y-m-d');
                            }
                            break;    
                        case 'no_transactions':
                        case 'no_of_rentals':
                        case 'highest_flr':
                        case 'top_no':
                        case 'detached_house':
                        case 'semi_detached_house':
                        case 'terrace_house':
                        case 'apt_condo':
                        case 'shops':
                        case 'childcare':
                        case 'total_units':
                        case 'old_total_units':
                            if(!empty($value)) {
                                $projectName->$field = $filter->sanitize($value, "int"); // (int)$value;
                            }
                            break;
                        case 'mrt_distance':
                        case 'low_psf':
                        case 'median_psf':
                        case 'high_psf':
                            if(!empty($value)) {
                                $projectName->$field = (float)$value;
                            }
                            break;
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
                            if(!empty($value)) {
                                $projectName->$field = number_format((float)$value, 2, '.', '');
                            }
                            break;
                        default:
                            if(!empty($value)) {
                                $projectName->$field =  $filter->sanitize($value, "string"); //trim($value);
                            }
                            break;
                    }
                }
                try {
                    if($process=="create") {
                        if($projectName->create()!==false) {
                            $inserted++;
                        } else {
                            $errorLogs[] = "Error insert at row $start (project: ".$post['project_name'].") (project_type: ".$post['project_type'].") (proj_property_type: ".$post['proj_property_type'].") (message: Project Already Exist)";
                        }
                    } else {
                        if($projectName->save()!==false) {
                            $inserted++;
                        } else {
                            $errorLogs[] = "Error insert at row $start (project: ".$post['project_name'].") (project_type: ".$post['project_type'].") (proj_property_type: ".$post['proj_property_type'].") (message: Project Already Exist)";
                        }
                    }
                }  catch(\Exception $e) {
                    $errorLogs[] = "Error insert at row $start (project: ".$post['project_name'].") (project_type: ".$post['project_type'].") (proj_property_type: ".$post['proj_property_type'].") (message: ".$e->getMessage().")";
                }
                $start++;
            }
        }
        return $inserted;
    }

    /**
     * [addProjectsDetails description]
     * @param [type] $insert  [description]
     * @param [type] $details [description]
     */ //echo '<pre>'; var_dump($projectDetails->count()); echo '</pre>'; die(); 
    private function addProjectsDetails($insert) 
    {   
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $inserted=0;$totalinsert=count($insert); $start=1; $errorLogs=[];
        if(!empty($insert)&&count($insert)>0) {
            foreach ($insert as $count => $post) {
                $project_Name = (strpos($post['project_id'], "'") !== false) ? strtoupper(preg_replace("/'/", "", $post['project_id'])) : strtoupper($filter->sanitize($post['project_id'], "string" ));
                $projectName = Projects::findProject(['project_name'=>$project_Name,'project_type'=>strtoupper($post['project_type']),'project_property_type'=>strtoupper($post['proj_property_type'])]);                           
                $response = ($projectName&&$projectName->count()>0) ? $this->updateProject($projectName,$post) : $this->addNewProject($post);
            
                $date = \DateTime::createFromFormat('j-M-Y', trim($post['sale_date']));
                $sale_date = $date->format('Y-m-d 00:00:00');
                $param = ['project_id'=>$response['project_id'],'sale_date'=>$sale_date,'address'=>$post['address']];
                $projectDetails = ProjectDetails::findDetails($param);                                   
                if($projectDetails->count()===0) {
                    unset($projectDetails);
                    $process = "create";
                    $projectDetails = new ProjectDetails();
                    $projectDetails->project_id = (int)$response['project_id'];
                } else {
                    $process = "update";
                    $pdetails = $projectDetails->toArray();
                    $pdid = $pdetails[0]['id'];
                    unset($projectDetails);
                    $projectDetails = ProjectDetails::findFirst($pdid);
                    $projectDetails->project_id = (int)$response['project_id'];
                }
                $projectDetails->project_type = $response['project_type'];
                $projectDetails->proj_property_type = $response['proj_property_type'];

                foreach ($post as $field => $value) {
                    $value = $esp->escapeHtml($value);
                    switch ($field) {
                        case 'project_id':
                        case 'project_type':
                        case 'proj_property_type':
                            continue;
                            break;
                        case 'unit_type':
                            //Unit Type ID
                            if(!empty($value)) {
                                $propertyUnits = PropertyUnits::findFirstByName(trim($value));
                                if(!$propertyUnits) {
                                    unset($propertyUnits);
                                    $propertyUnits = new PropertyUnits();
                                    $propertyUnits->name = $value;
                                    $propertyUnits->description = $value;
                                    if ($propertyUnits->save()) {
                                        $projectDetails->$field = $propertyUnits->name;
                                    }
                                } else {
                                     $projectDetails->$field = $propertyUnits->name;
                                }
                            }// ./Unit Type ID
                            break;
                        case 'property_type':
                        case 'property2_type':
                            //Property Type ID
                            if(!empty($value)) {
                                $propertyType = PropertyTypes::findFirstByName(trim($value));
                                if(!$propertyType) {
                                    unset($propertyType);
                                    $propertyType = new PropertyTypes();
                                    $propertyType->name = $value;
                                    $propertyType->description = $value;
                                    if ($propertyType->save()) {
                                        $projectDetails->$field = $propertyType->name;
                                    }
                                } else {
                                     $projectDetails->$field = $propertyType->name;
                                }
                            } // ./Property Type ID   
                            break;
                        case 'district':
                            //District ID
                            if(!empty($value)) {
                                $propertyDistrict = PropertyDistricts::findFirstByName(trim($value));
                                if(!$propertyDistrict) {
                                    unset($propertyDistrict);
                                    $propertyDistrict = new PropertyDistricts();
                                    $propertyDistrict->name = $value;
                                    $propertyDistrict->description = $value;
                                    if ($propertyDistrict->save()) {
                                        $projectDetails->$field = $propertyDistrict->name;
                                    }
                                } else {
                                     $projectDetails->$field = $propertyDistrict->name;
                                }
                            } // ./District ID
                            break;
                        case 'tenure':
                            //Tenure ID
                            if(!empty($value)) {
                                $propertyTenure = PropertyTenures::findFirstByName(trim($value));
                                if(!$propertyTenure) {
                                    unset($propertyTenure);
                                    $propertyTenure = new PropertyTenures();
                                    $propertyTenure->name = $value;
                                    $propertyTenure->description = $value;
                                    if ($propertyTenure->save()) {
                                        $projectDetails->$field = $propertyTenure->name;
                                    }
                                } else {
                                     $projectDetails->$field = $propertyTenure->name;
                                }
                            } // ./Tenure ID
                            break;
                        case 'area_sqf':
                            if(!empty($value)) {
                                $projectDetails->$field = (float)$value;
                                //$projectDetails->area_sqm = (float)($value/10.764);
                            }
                            break;
                        case 'area_sqm':
                            if(!empty($value)) {
                                $projectDetails->$field = (float)$value;
                                //$projectDetails->area_sqf = (float)($value*10.764);
                            }
                            break;
                        case 'built_area_per_sqft':
                        case 'unit_price_psm':
                        case 'unit_price_psf':
                        case 'transacted_price';
                            if(!empty($value)) {
                                $projectDetails->$field = (float)$value;
                            }
                            break;
                        case 'available_unit_type_id':
                        case 'share_value':
                        case 'postal_code':
                        case 'postal_sector':
                            if(!empty($value)) {
                                $projectDetails->$field = (int)$value;
                            }
                            break;
                        case 'stack':
                            if(!empty($value)) {
                                if(strpos($value, "'") !== false)
                                    $projectDetails->$field = str_replace("'","",$value);
                                else
                                    $projectDetails->$field = $value;
                            }
                            break;
                        case 'sale_date':
                            if(!empty($value)) {
                                $date = \DateTime::createFromFormat('j-M-Y', trim($value));
                                $projectDetails->$field = $date->format('Y-m-d');
                            }
                            break; 
                        default:
                            if(!empty($value)) {
                                $projectDetails->$field = (string)$value;
                            }
                            break;
                    }
                }

                try {
                    if($process=="create") {
                        if($projectDetails->create()!==false) {
                            $inserted++;
                        } else {
                            $errorLogs[] = "Error insert at row $start (address: ".$post['address'].") (sale_date: ".$post['sale_date'].") (message: Already Exist)";
                        }
                    } else {
                        if($projectDetails->save()!==false) {
                            $inserted++;
                        } else {
                            $errorLogs[] = "Error insert at row $start (address: ".$post['address'].") (sale_date: ".$post['sale_date'].") (message: Already Exist)";
                        }
                    }
                }  catch(\Exception $e) {
                    $errorLogs[] = "Error insert at row $start (address :".$post['address'].") (sale_date :".$post['sale_date'].") (message :".$e->getMessage().")";
                }
                $start++;
            }
        }
        if(!empty($errorLogs)&&count($errorLogs)>0) (new AdminLogs)->addLog($this->session->get('user')['username'], 'Insert Project Details', implode( "<br>", $errorLogs ));
        return $inserted;
    }

    /**
     * [addPerProject description]
     * @param [type] $insert [description]
     */ //echo '<pre>'; var_dump($insert); echo '</pre>'; die();    
    private function addPerProject($insert) 
    {
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $inserted=0;$totalinsert=count($insert);
        if(!empty($insert)&&count($insert)>0) {
            foreach ($insert as $count => $post) {  
                $project_Name = (strpos($post['project_id'], "'") !== false) ? strtoupper(preg_replace("/'/", "", $post['project_id'])) : strtoupper($filter->sanitize($post['project_id'], "string" ));
                $projectName = Projects::findFirst(["conditions"=>"UPPER(project_name)=?1","bind"=>[1=>$project_Name]]);                 
                if(!$projectName) {
                    continue;
                } else {
                    $param = ['project_id'=>$projectName->id,'unit_type'=>$post['unit_type'],
                        'transaction_month'=>$post['transaction_month'],'date_avail_unit_updated'=>$post['date_avail_unit_updated'],
                        'area_per_unit_type_sqm'=>$post['area_per_unit_type_sqm'],'area_per_unit_type_sqf'=>$post['area_per_unit_type_sqf']];
                    $perProjects = PerProjects::findDetails($param);                
                    if($perProjects->count()===0) {
                        unset($perProjects);
                        $perProjects = new PerProjects();
                        $perProjects->project_id = (int)$projectName->id;
                    } else {
                        $ppid = $perProjects[0]->id;
                        unset($perProjects);
                        $perProjects = PerProjects::findFirst($ppid);
                        $perProjects->project_id = (int)$projectName->id;
                    }
 
                    foreach ($post as $field => $value) {
                        $value = $filter->sanitize($value, "string");
                        switch ($field) {
                            case 'project_id':
                                continue;
                                break;
                            case 'unit_type':
                                //Unit Type ID
                                $propUnitIds = [];
                                $value = $esp->escapeHtml($value);
                                if(!empty($value)) {
                                    if (strpos($value, '|') !== false) {
                                        $allUnits = explode("|", $value);
                                        foreach ($allUnits as $key => $unit) {
                                            $propertyUnits = PropertyUnits::findFirstByName(trim($unit));
                                            if(!$propertyUnits) {
                                                unset($propertyUnits);
                                                $propertyUnits = new PropertyUnits();
                                                $propertyUnits->name = $unit;
                                                $propertyUnits->description = $unit;
                                                if ($propertyUnits->save()) {
                                                    $propUnitIds[] = $propertyUnits->name;
                                                }
                                            } else {
                                                $propUnitIds[] = $propertyUnits->name;
                                            }
                                        }
                                        $perProjects->field = implode(",",$propUnitIds);
                                    } else {
                                        $propertyUnits = PropertyUnits::findFirstByName(trim($value));
                                        if(!$propertyUnits) {
                                            unset($propertyUnits);
                                            $propertyUnits = new PropertyUnits();
                                            $propertyUnits->name = $value;
                                            $propertyUnits->description = $value;
                                            if ($propertyUnits->save()) {
                                                $propUnitIds[] = $propertyUnits->name;
                                            }
                                        } else {
                                            $propUnitIds[] = $propertyUnits->name;
                                        }
                                        $perProjects->field = implode(",",$propUnitIds);
                                    }
                                } else {
                                    $perProjects->$field = NULL;
                                } // ./Unit Type ID
                                break;
                            case 'median_psf':
                            case 'low_psf':
                            case 'high_psf':
                                if(!empty($value)) {
                                    $perProjects->$field = (float)$value;
                                } else {
                                    $perProjects->$field = NULL;
                                } 
                                break;
                            case 'no_transactions':
                            case 'no_unit_per_unit_type':
                            case 'available_unit_type':
                                if(!empty($value)) {
                                    $perProjects->$field = (int)$value;
                                } else {
                                    $perProjects->$field = NULL;
                                } 
                                break;
                            case 'date_avail_unit_updated':
                                if(!empty($value)) {
                                    $date = \DateTime::createFromFormat('j-M-y', $value);
                                    $perProjects->$field = $date->format('Y-m-d');
                                } else {
                                    $perProjects->$field = NULL;
                                } 
                                break;
                            default:
                                if(!empty($value)) {
                                    $perProjects->$field = $esp->escapeHtml($value);
                                } else {
                                    $perProjects->$field = NULL;
                                } 
                                break;
                        }
                    }
                    try {
                        $perProjects->save();
                        $inserted++;
                    }  catch(\Exception $e) {
                        error_log("error: $start ".$perProjects->id . " ".$post['project_id']);
                        error_log("Error: ".$e->getMessage());
                        exit;
                    }
                }
            }
        }
        return $inserted;
    }


    /**
     * [addNewProject description]
     * @param [type] $post [description]
     */ //echo '<pre>'; var_dump($projectName); echo '</pre>'; die();   
    private function addNewProject($post)
    {
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $response = false;
        $projectName = new Projects();  
        $inserted=0; $start=1;               
        foreach ($post as $field => $value) {
            $value = $filter->sanitize($value, "string");
            switch ($field) {
                case 'project_id':
                    $projectName->project_name = trim($post['project_id']);
                    break;
                case 'project_type':
                    //Project Types
                    $projectType = ProjectTypes::findFirstByName(trim($value));
                    if(!$projectType) {
                        unset($projectType);
                        $projectType = new ProjectTypes();
                        $projectType->name = $value;
                        $projectType->description = $value;
                        if ($projectType->save()) {
                            $projectName->$field = $projectType->name;
                        }
                    } else {
                         $projectName->$field = $projectType->name;
                    } // ./Project Types
                    break;
                case 'proj_property_type':
                    //Project Property Type ID
                    if(!empty($value)) {
                        $projPropertyType = ProjectPropTypes::findFirstByName(trim($value));              
                        if(!$projPropertyType) {
                            unset($projPropertyType);
                            $projPropertyType = new ProjectPropTypes();
                            $projPropertyType->name = $value;
                            $projPropertyType->description = $value;
                            if ($projPropertyType->save()) {
                                $projectName->$field = $projPropertyType->name;
                            }
                        } else {
                             $projectName->proj_property_type = $projPropertyType->name;
                        }
                    } // ./Project Property Type ID 
                    break;
                case 'district':
                    //District ID
                    if(!empty($value)) {
                        $propertyDistrict = PropertyDistricts::findFirstByName(trim($value));
                        if(!$propertyDistrict) {
                            unset($propertyDistrict);
                            $propertyDistrict = new PropertyDistricts();
                            $propertyDistrict->name = $value;
                            $propertyDistrict->description = $value;
                            if ($propertyDistrict->save()) {
                                $projectName->$field = $propertyDistrict->name;
                            }
                        } else {
                             $projectName->$field = $propertyDistrict->name;
                        }
                    }// ./District ID
                    break;
                case 'tenure':
                    //Tenure ID
                    if(!empty($value)) {
                        $propertyTenure = PropertyTenures::findFirstByName(trim($value));
                        if(!$propertyTenure) {
                            unset($propertyTenure);
                            $propertyTenure = new PropertyTenures();
                            $propertyTenure->name = $value;
                            $propertyTenure->description = $value;
                            if ($propertyTenure->save()) {
                                $projectName->$field = $propertyTenure->name;
                            }
                        } else {
                             $projectName->$field = $propertyTenure->name;
                        }
                    }// ./Tenure ID
                    break;
                case 'property_type':
                    //Property Type ID
                    if(!empty($value)) {
                        if($value==="Apt/Condo") {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $projectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                            $projectName->top_year = trim($esp->escapeHtml($post['top_year']));
                        } elseif(($value==="Landed"||$value==="Strata-Landed")&&$post['project_type']=="Resale") {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $projectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                            $projectName->top_year = trim($esp->escapeHtml('Unknown'));
                        } elseif(($value==="Landed"||$value==="Strata-Landed")&&$post['project_type']=="New Sale") {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $projectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                            $projectName->top_year = trim($post['top_year']);
                        } else {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $projectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                        }
                    } // ./Property Type ID
                    break;
                case 'planning_region':
                case 'planning_area':
                case 'street_name':
                case 'top_year':
                    if(!empty($value)) {
                        $projectName->$field = trim($value);
                    }
                    break;
                default:
                    continue;
                    break;
            }
        }    

        try {
            $projectName->save();
            $addprojectName = Projects::findFirst($projectName->id);
            $response = ['project_id'=>$addprojectName->id,
                        'project_type'=>$addprojectName->project_type,
                        'proj_property_type'=>$addprojectName->proj_property_type];
        }  catch(\Exception $e) {
            error_log("error: $start ".$projectName->id . " ".$post['project_id']);
            error_log("Error: ".$e->getMessage());
            $response = false;
        }
     
        return $response;
    }

    /**
     * [updateProject description]
     * @param  [type] $projectName [description]
     * @param  [type] $post        [description]
     * @return [type]              [description]
     */
    private function updateProject($projectName,$post)
    {
        $filter = new \Phalcon\Filter();
        $esp = new Escaper();
        $response = false;
        try { 
            $exstprojectName = Projects::findFirst(["conditions"=>"id=?0 AND project_type=?1 AND proj_property_type=?2",
                "bind"=>[$projectName[0]->id, $projectName[0]->project_type, $projectName[0]->proj_property_type]]);           
            foreach ($post as $field => $value) {
                $value = $filter->sanitize($value, "string");
                switch ($field) {
                    case 'property_type':
                        //Property Type ID
                        $propTypeIds = [];
                        if($value==="Apt/Condo") {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $exstprojectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                            $exstprojectName->top_year = trim($esp->escapeHtml($post['top_year']));
                        } elseif(($value==="Landed"||$value==="Strata-Landed")&&$post['project_type']=="Resale") {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $exstprojectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                            $exstprojectName->top_year = trim($esp->escapeHtml('Unknown'));
                        } elseif(($value==="Landed"||$value==="Strata-Landed")&&$post['project_type']=="New Sale") {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $exstprojectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                            $exstprojectName->top_year = trim($esp->escapeHtml($post['top_year']));
                        } else {
                            $propertyTypes = PropertyTypes::findFirstByName(trim($value));
                            if($propertyTypes) {
                                $projectName->$field = $propertyTypes->name;
                            } else {
                                unset($propertyTypes);
                                $propertyTypes = new PropertyTypes();
                                $propertyTypes->name = $value;
                                $propertyTypes->description = $value;
                                if ($propertyTypes->save()) {
                                    $projectName->$field = $propertyTypes->name;
                                }
                            }
                        } // ./Property Type ID
                        break;
                    case 'district':
                        if(!empty($value)) {
                            $propertyDistrict = PropertyDistricts::findFirstByName(trim($value));
                            if(!$propertyDistrict) {
                                unset($propertyDistrict);
                                $propertyDistrict = new PropertyDistricts();
                                $propertyDistrict->name = $value;
                                $propertyDistrict->description = $value;
                                if ($propertyDistrict->save()) {
                                    $exstprojectName->$field = $propertyDistrict->name;
                                }
                            } else {
                                 $exstprojectName->$field = $propertyDistrict->name;
                            }
                        }/* else {
                            $exstprojectName->$field = NULL;
                        }*/
                        break;
                    case 'tenure':
                        if(!empty($value)) {
                            $propertyTenure = PropertyTenures::findFirstByName(trim($value));
                            if(!$propertyTenure) {
                                unset($propertyTenure);
                                $propertyTenure = new PropertyTenures();
                                $propertyTenure->name = $value;
                                $propertyTenure->description = $value;
                                if ($propertyTenure->save()) {
                                    $exstprojectName->$field = $propertyTenure->name;
                                }
                            } else {
                                 $exstprojectName->$field = $propertyTenure->name;
                            }
                        }/* else {
                            $exstprojectName->$field = NULL;
                        }*/
                        break;
                    case 'planning_region':
                    case 'planning_area':
                    case 'street_name':
                    case 'top_year':
                        if(!empty($value)) {
                            $exstprojectName->$field = trim($value);
                        }/* else {
                            $exstprojectName->$field = NULL;
                        }*/
                        break;
                    case 'project_id':
                    case 'project_type':
                    case 'proj_property_type':
                    default:
                        continue;
                        break;
                }
            }

            try {
                $exstprojectName->save();
                $response = ['project_id'=>$exstprojectName->id,
                            'project_type'=>$exstprojectName->project_type,
                            'proj_property_type'=>$exstprojectName->proj_property_type];
            }  catch(\Exception $e) {
                error_log("error: $start ".$exstprojectName->id . " ".$post['project_id']);
                error_log("Error: ".$e->getMessage());
                $response = false;
            }
        }  catch(\Exception $e) {
            error_log("Error: ".$e->getMessage());
            $response = false;
        }
        return $response;
    }

    /**
     * [newtokenAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function newtokenAction() 
    {
        $this->view->disable(); $response = false;
        if($this->request->getMethod()==='GET') {
            $response = ['tokenKey' => $this->security->getTokenKey(),'token' => $this->security->getToken()];
        }
        return json_encode($response);
    }

    /**
     * [listAction description]
     * @return [type] [description]
     */ //echo '<pre>'; var_dump($user->id); echo '</pre>'; die();
    public function listAction()
    {
        $this->view->uploads = Uploads::find(["order" => "id DESC","limit"=>15]);
        $this->view->pick("upload/list");
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

}

