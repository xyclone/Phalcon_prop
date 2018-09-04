<?php
namespace Property\Models;

use Phalcon\Escaper;
use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use Property\Models\Projects;
use Property\Models\PropertyUnits;
use Property\Classes\PropertyClass;

class ProjectDetails extends \Phalcon\Mvc\Model
{
    public $id;
    public $project_type;
    public $proj_property_type;
    public $project_id;
    public $unit_type;
    public $no_units_per_transaction;
    public $street_name;
    public $address;
    public $postal_sector;
    public $postal_code;
    public $district;
    public $number;
    public $level;
    public $stack;
    public $type;
    public $planning_region;
    public $planning_area;
    public $hdb_pte;
    public $top_year;
    public $tenure_id;
    public $tenure2;
    public $property_type;
    public $property2_type;
    public $type_of_sale_per_trxn;
    public $nett_price;
    public $transacted_price;
    public $built_area_per_sqft;
    public $area_sqm;
    public $area_sqf;
    public $area_type;
    public $unit_price_psm;
    public $unit_price_psf;
    public $sale_date;
    public $share_value;
    public $creation_date;
    public $creation_by;
    public $update_date;
    public $update_by;

    public $project_name;
    public $name;
    public $unit_type_name;
    public $property_type_name;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("prop_project_details");
        $this->di = \Phalcon\DI::getDefault();
        
        $this->belongsTo('project_type', '\Property\Models\ProjectTypes', 'name',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_ProjectTypes', 'reusable' => true
        ));

        $this->belongsTo('proj_property_type', '\Property\Models\ProjectPropTypes', 'name',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_ProjectPropTypes', 'reusable' => true
        ));        

        $this->belongsTo('project_id', '\Property\Models\Projects', 'id',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_Projects', 'reusable' => true
        ));

        $this->hasOne('unit_type', '\Property\Models\PropertyUnits', 'name',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_PropertyUnits', 'reusable' => true
        ));

        $this->hasOne('district', '\Property\Models\PropertyDistricts', 'name',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_PropertyDistricts', 'reusable' => true
        ));

        $this->hasOne('tenure', '\Property\Models\PropertyTenures', 'name',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_PropertyTenures', 'reusable' => true
        ));

        $this->hasOne('property_type', '\Property\Models\PropertyTypes', 'name',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_PropertyTypes', 'reusable' => true
        ));

        $this->hasOne('property2_type', '\Property\Models\PropertyTypes', 'name',array(
            'foreignKey' => true, 'alias' => 'ProjectDetails_PropertyType2', 'reusable' => true
        ));

        // $this->hasOne('project_name', '\Property\Models\Projects', 'id',array(
        //     'foreignKey' => true, 'alias' => 'ProjectDetails_PropertyName', 'reusable' => true
        // ));
    }


    /**
     * [afterFetch description]
     * @return [type] [description]
     */
    public function afterFetch()
    {
        $this->project_details_count = ProjectDetails::find(["conditions" => "project_id=?1", "bind"=>[1=>$this->id]])->count();

        $this->project_name = (new self)->getProjectName(['id'=>$this->id,'pid'=>$this->project_id]);
        
        //$this->unit_type_name = (new self)->getUnitNames(['type'=>'unit_type_id','pid'=>$this->id,'utid'=>$this->unit_type_id]);
        //$this->property_type_name = (new self)->getPropertyTypeName(['type'=>'property_type_id','pid'=>$this->id,'propid'=>$this->property_type_id]);
        $this->sale_date = (!empty($this->sale_date)&&strtotime($this->sale_date)>=0) ? date("d-M-Y", strtotime($this->sale_date)) : "";

        $this->transacted_price = number_format($this->transacted_price);

    }

    /**
     * [getFields description]
     * @return [type] [description]
     */
    public static function getFields()
    {
        $class = PropertyClass::$project_detail_fields;
        $result = new self(); $response = [];
        $metadata = $result->getModelsMetaData();
        $attributes = $metadata->getAttributes($result);
        foreach ($attributes as $key => $field) {
            if(array_key_exists ($field, $class))
                $response[$field] = $class[$field];
            else
                $response[$field] = str_replace('_',' ',ucfirst($field));
        }
        return $response;
    }

    public function getProjectName($p)
    {
        $filter = new \Phalcon\Filter();
        $id = $filter->sanitize($p['id'], "int");
        $pid = $filter->sanitize($p['pid'], "int");
        $response = "";
        $model = new self();
        $conn = $model->getReadConnection();
        if(!empty($pid)) {
            $sql = "SELECT proj.project_name project_name ";
            $sql .= "FROM ".(new self)->getSource()." AS det ";
            $sql .= "LEFT JOIN ".(new Projects)->getSource()." proj ON det.project_id = proj.id ";
            $sql .= "WHERE det.id=$id; ";
            $result = new Resultset(null, $model, $conn->query($sql));
            if($result&&$result->count()>0) {
                $res = $result->toArray();
                $response = (string)$res[0]['project_name'];
            }
        }
        return $response;
    }

    /**
     * [getUnitNames description]
     * @param  [type] $p [description]
     * @return [type]    [description]
     */
    public function getUnitNames($p)
    {
        $filter = new \Phalcon\Filter();
        $pid = $filter->sanitize($p['pid'], "int");
        $type = $filter->sanitize($p['type'], "string");
        $utid = $filter->sanitize($p['utid'], "string");
        $response = "";
        $model = new self();
        $conn = $model->getReadConnection();
        if(!empty($utid)) {
            $sql = "SELECT (CASE WHEN det.$type IS NOT NULL THEN GROUP_CONCAT(DISTINCT punits.name ORDER BY punits.id) ELSE NULL END) `$type` ";
            $sql .= "FROM ".(new self)->getSource()." AS det ";
            $sql .= "LEFT JOIN ".(new PropertyUnits)->getSource()." punits ON (CASE WHEN det.$type IS NOT NULL THEN FIND_IN_SET(punits.id, det.$type) ELSE det.$type = punits.id END) ";
            $sql .= "WHERE det.id=$pid; ";
            $result = new Resultset(null, $model, $conn->query($sql));
            if($result&&$result->count()>0) {
                $res = $result->toArray();
                $response = (string)$res[0][$type];
            }
        }
        return $response;
    }

    /**
     * [getPropertyTypeName description]
     * @param  [type] $p [description]
     * @return [type]    [description]
     */
    public function getPropertyTypeName($p)
    {
        $filter = new \Phalcon\Filter();
        $pid = $filter->sanitize($p['pid'], "int");
        $type = $filter->sanitize($p['type'], "string");
        $propid = $filter->sanitize($p['propid'], "string");
        $response = "";
        $model = new self();
        $conn = $model->getReadConnection();
        if(!empty($propid)) {
            $sql = "SELECT (CASE WHEN det.$type IS NOT NULL THEN GROUP_CONCAT(DISTINCT ptypes.name ORDER BY ptypes.id) ELSE NULL END) `$type` ";
            $sql .= "FROM ".(new self)->getSource()." AS det ";
            $sql .= "LEFT JOIN ".(new PropertyTypes)->getSource()." ptypes ON (CASE WHEN det.$type IS NOT NULL THEN FIND_IN_SET(ptypes.id, det.$type) ELSE det.$type = ptypes.id END) ";
            $sql .= "WHERE det.id=$pid; ";
            $result = new Resultset(null, $model, $conn->query($sql));
            if($result&&$result->count()>0) {
                $res = $result->toArray();
                $response = (string)$res[0][$type];
            }
        }
        return $response;
    }    

    /**
     * [getTotalRows description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public function getTotalRows($param=null)
    {
        //$sql = $sql .= " SQL_CALC_FOUND_ROWS ";
        //$sql = "SELECT COUNT(id) AS `count` FROM ". (new self)->getSource();
        $sql = "SELECT SQL_CALC_FOUND_ROWS id FROM ". (new self)->getSource();
        $sql .= (!empty($param['conditions'])) ? " WHERE ".$param['conditions']." " : " ";
        $sql .= "LIMIT 1";
        $result = new self();
        $conn = $result->getReadConnection();
        $conn->query($sql);
        $count_sql = "SELECT FOUND_ROWS() AS `count`"; 
        return new Resultset(null, $result, $conn->query($count_sql));
    }

    public static function deleteUnit($id)
    {
        $db = \Phalcon\DI::getDefault()->get('db');
        //return $db;
        $query = $db->prepare("DELETE FROM ".(new self)->getSource()." WHERE id=".(int)$id.";");
        $result = $query->execute();
        return $result;
    }

    public static function findDetails($param) 
    {   
        $esp = new Escaper();
        $sql =  "SELECT details.* FROM ".(new self)->getSource()." AS details ";
        $sql .= "LEFT JOIN ".(new Projects)->getSource()." AS proj ON proj.`id`=details.`project_id` ";
        $sql .= "WHERE details.`project_id` = ".(int)$param['project_id']." ";
        foreach ($param as $field => $value)
            $sql .= (!empty($value)) ? "AND details.`".$field."`= '". $esp->escapeHtml($param[$field]) ."' " : " ";
        $result = new self();
        $conn = $result->getReadConnection();
        return new Resultset(null, $result, $conn->query($sql)); 
    }

    /**
     * [findAll description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    // public static function findAll($param)
    // {
    //     $result = new self();
    //     $metadata = $result->getModelsMetaData();
    //     $attributes = $metadata->getAttributes($result);
    //     $binds = [];
    //     $sql = "SELECT ";
    //     if($param['type'] != 'rows') $sql .= "SQL_CALC_FOUND_ROWS ";
    //     foreach ($attributes as $key => $value) {
    //         switch ($value) {
    //             case 'project_id':
    //                 $sql .= "proj.project_name $value, ";
    //                 break;
    //             case 'unit_type_id':
    //                 $sql .= "pun.name $value, ";
    //                 break;
    //             case 'district_id':
    //                 $sql .= "dit.name $value, ";
    //                 break;
    //             case 'tenure_id':
    //                 $sql .= "ten.name $value, ";
    //                 break;
    //             case 'property_type_id':
    //                 $sql .= "ptp.name $value, ";
    //                 break;
    //             case 'share_value':
    //                 $sql .= 'det.'.$value;
    //                 break;
    //             case 'creation_date':
    //             case 'creation_by':
    //             case 'update_date':
    //             case 'update_by':
    //                 continue;
    //                 break;
    //             default:
    //                 $sql .= 'det.'.$value.', ';
    //                 break;
    //         }
    //     }       
    //     $sql .=" FROM ".(new self)->getSource()." AS det
    //         LEFT JOIN ".(new Projects)->getSource()." proj ON proj.id=det.project_id
    //         LEFT JOIN ".(new PropertyUnits)->getSource()." pun ON pun.id=det.unit_type_id 
    //         LEFT JOIN ".(new PropertyDistricts)->getSource()." dit ON dit.id=det.district_id 
    //         LEFT JOIN ".(new PropertyTenures)->getSource()." ten ON ten.id=det.tenure_id 
    //         LEFT JOIN ".(new PropertyTypes)->getSource()." ptp ON ptp.id=det.property_type_id ";
    //     //if(!empty($param['conditions'])&&count($param['conditions'])>0) {
    //     $sql .= (!empty($param['conditions'])&&count($param['conditions'])>0) ? "WHERE ".$param['conditions']." " : "";
    //     $sql .= (!empty($param['order'])) ? "ORDER BY ".$param['order']." " : "";
    //     if($param['type'] != 'rows')
    //         $sql .= "LIMIT 1 ";
    //     else {
    //         if(!empty($param["limit"])&&!empty($param["offset"])) {
    //             $sql .= "LIMIT ". $param["limit"]." OFFSET ".$param["offset"]." ";
    //         } elseif(!empty($param["limit"])&&empty($param["offset"])) {
    //             $sql .= " LIMIT ". $param["limit"]." ";
    //         }
    //     }
    //     $sql .=";";
    //     $conn = $result->getReadConnection();
    //     $conn->query($sql);
    //     $count_sql = "SELECT FOUND_ROWS() AS cnt"; 
    //     $final_sql = ($param['type']!='rows') ? $count_sql : $sql;
    //     $conn = $result->getReadConnection();
    //     return new Resultset(null, $result, $conn->query($final_sql));   
    // }

    /**
     * [beforeCreate description]
     * @return [type] [description]
     */
    public function beforeCreate()
    {
        //Set the creation date
        $this->creation_date = date('Y-m-d H:i:s');
        $this->creation_by = $this->di->get('session')->get('user')['username'];
    }

    /**
     * [beforeSave description]
     * @return [type] [description]
     */
    public function beforeSave()
    {
        //Set the update date
        $this->update_date = date('Y-m-d H:i:s');
        $this->update_by = $this->di->get('session')->get('user')['username'];
    }    

    /**
     * [updateDetails description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public function updateDetails($param)
    {
        $db = \Phalcon\DI::getDefault()->get('db');
        $query = $db->prepare("UPDATE ".(new self)->getSource()." SET project_type='".$param['project_type']."' WHERE project_id=".(int)$param['project_id'].";");
        $result = $query->execute();
        return $result;
    }
}