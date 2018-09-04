<?php
namespace Property\Models;

use Phalcon\Mvc\Model\Metadata;
use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use Property\Classes\PropertyClass;
use Property\Models\ProjectTypes;
use Property\Models\ProjectPropTypes;
use Property\Models\ProjectDetails;
use Property\Models\PropertyUnits;
use Property\Models\PropertyTypes;
use Property\Models\PropertyTenures;
use Property\Models\PropertyDistricts;
use Property\Models\PropertyAgencies;
use Property\Models\PropertyStatus;

class Projects extends \Phalcon\Mvc\Model
{

    public $id;
    public $project_type;
    public $proj_property_type;
    public $project_name;
    public $old_project_name;
    public $chinese_name;
    public $all_number;
    public $street_name;
    public $total_units;
    public $old_total_units;
    public $total_available_units;
    public $unit_type;
    public $available_unit_type;
    public $date_avail_unit_updated;
    public $property_type;
    public $tenure;
    public $top_year;
    public $top_month;
    public $top_date;
    public $district;
    public $planning_region;
    public $planning_area;
    public $status;
    public $status_date;
    public $status2;
    public $status2_date;
    public $psf_ppr;
    public $psm_ppr;
    public $gls_sold_date;
    public $low_millions;
    public $high_millions;
    public $tender_agency;
    public $stb_application_date;
    public $stb_approval_date;
    public $completion_date;
    public $vacant_possession_date;
    public $successful_tenderer;
    public $low_psf;
    public $median_psf;
    public $high_psf;
    public $no_transactions;
    public $transaction_month;
    public $rental_low_psf_pm;
    public $rental_median_psf_pm;
    public $rental_high_psf_pm;
    public $no_of_rentals;
    public $rental_period;
    public $rental_amount;
    public $vacant_date;
    public $furnishing;
    public $original_top;
    public $mrt;
    public $mrt_distance;
    public $mrt_distance_unit;
    public $primary_school_within_1km;
    public $highest_flr;
    public $site_area_sqft;
    public $site_area_sqmt;
    public $plot_ratio;
    public $gfa_sqft;
    public $gfa_sqmt;
    public $developer;
    public $marketing_agency;
    public $project_ref_no;
    public $approved_date;
    public $locality;
    public $top_no;
    public $issue_date;
    public $floor_area;
    public $cost;
    public $development_status;
    public $ds_date;
    public $land_type;
    public $sector;
    public $date_updated;
    public $description;
    public $creation_date;
    public $creation_by;
    public $update_date;
    public $update_by;
    public $project_details_count;
    public $fields;

    public $unit_type_name;
    public $available_unit_type_name;
    public $property_type_name;
    #public $property2_type_name;

    public $tmp_project_type_id;
    public $project_type_name;
    public $project_property_type_name;

    public $action;


    /**
     * [initialize description]
     * @return [type] [description]
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("prop_projects");
        $this->di = \Phalcon\DI::getDefault();
        $this->hasMany('id', '\Property\Models\ProjectDetails', 'project_id',array(
            'foreignKey' => true, 'alias' => 'Project_ProjectDetails', 'reusable' => true
        ));

        $this->belongsTo('project_type', '\Property\Models\ProjectTypes', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_ProjectTypes', 'reusable' => true
        ));

        $this->belongsTo('proj_property_type', '\Property\Models\ProjectPropTypes', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_ProjectPropTypes', 'reusable' => true
        ));

        $this->hasOne('property_type', '\Property\Models\PropertyTypes', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_PropertyTypes', 'reusable' => true
        ));

        $this->hasOne('tenure', '\Property\Models\PropertyTenures', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_PropertyTenures', 'reusable' => true
        ));

        $this->hasOne('district', '\Property\Models\PropertyDistricts', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_PropertyDistricts', 'reusable' => true, 
        ));/*'params' => ['order' => 'name ASC'],*/

        $this->hasOne('status', '\Property\Models\PropertyStatus', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_PropertyStatus', 'reusable' => true
        ));
        $this->hasOne('status2', '\Property\Models\PropertyStatus', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_PropertyStatus2', 'reusable' => true
        ));

        $this->hasMany('unit_type', '\Property\Models\PropertyUnits', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_PropertyUnits', 'reusable' => true,
        ));

        $this->hasOne('tender_agency', '\Property\Models\PropertyAgencies', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_TenderAgencies', 'reusable' => true
        ));

        $this->hasOne('marketing_agency', '\Property\Models\PropertyAgencies', 'name',array(
            'foreignKey' => true, 'alias' => 'Project_MarketingAgencies', 'reusable' => true
        ));

    }

    /**
     * [afterFetch description]
     * @return [type] [description]
     */
    public function afterFetch()
    {          
        $this->status_date = (!empty($this->status_date)&&strtotime($this->status_date)>=0) ? date("d-M-Y", strtotime($this->status_date)) : "";
        $this->status2_date = (!empty($this->status2_date)&&strtotime($this->status2_date)>=0) ? date("d-M-Y", strtotime($this->status2_date)) : "";
        $this->gls_sold_date = (!empty($this->gls_sold_date)&&strtotime($this->gls_sold_date)>=0) ? date("d-M-Y", strtotime($this->gls_sold_date)) : "";
        $this->stb_application_date = (!empty($this->stb_application_date)&&strtotime($this->stb_application_date)>=0) ? date("d-M-Y", strtotime($this->stb_application_date)) : "";
        $this->stb_approval_date = (!empty($this->stb_approval_date)&&strtotime($this->stb_approval_date)>=0) ? date("d-M-Y", strtotime($this->stb_approval_date)) : "";
        $this->approved_date  = (!empty($this->approved_date)&&strtotime($this->approved_date)>=0) ? date("d-M-Y", strtotime($this->approved_date)) : "";
        $this->date_avail_unit_updated  = (!empty($this->date_avail_unit_updated)&&strtotime($this->date_avail_unit_updated)>=0) ? date("d-M-Y", strtotime($this->date_avail_unit_updated)) : "";
        $this->issue_date  = (!empty($this->issue_date)&&strtotime($this->issue_date)>=0) ? date("d-M-Y", strtotime($this->issue_date)) : "";
        $this->ds_date  = (!empty($this->ds_date)&&strtotime($this->ds_date)>=0) ? date("d-M-Y", strtotime($this->ds_date)) : "";
        $this->vacant_possession_date  = (!empty($this->vacant_possession_date)&&strtotime($this->vacant_possession_date)>=0) ? date("d-M-Y", strtotime($this->vacant_possession_date)) : "";
        $this->date_updated  = (!empty($this->date_updated)&&strtotime($this->date_updated)>=0) ? date("d-M-Y", strtotime($this->date_updated)) : "";
        $this->available_date  = (!empty($this->available_date)&&strtotime($this->available_date)>=0) ? date("d-M-Y", strtotime($this->available_date)) : "";
        $this->completion_date  = (!empty($this->completion_date)&&strtotime($this->completion_date)>=0) ? date("d-M-Y", strtotime($this->completion_date)) : "";
        $this->action = "";
    }

    /**
     * [getFields description]
     * @return [type] [description]
     */
    public static function getFields()
    {
        $class = PropertyClass::$project_fields;
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

    /**
     * [getTotalRows description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public function getTotalRows($param=null)
    {
        $sql = "SELECT COUNT(id) AS `count` FROM ". (new self)->getSource();
        $sql .= (!empty($param['conditions'])) ? " WHERE ".$param['conditions'] : "";
        $result = new self();
        $conn = $result->getReadConnection();
        return new Resultset(null, $result, $conn->query($sql));
    }

    /**
     * [findProjects description]
     * @return [type] [description]
     */
    public static function findProjects()
    {
        $sql = "SELECT proj.* FROM ".(new self)->getSource()." AS proj LEFT JOIN ".(new ProjectTypes)->getSource()." ";
    }

    /**
     * [deleteUnit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function deleteProject($id)
    {
        $result = false;
        $db = \Phalcon\DI::getDefault()->get('db');
        $query = $db->prepare("DELETE FROM ".(new self)->getSource()." WHERE id=".$id.";");
        $project = $query->execute();
        if($project) {
            $query =  $db->prepare("DELETE FROM ".(new ProjectDetails)->getSource()." WHERE project_id=".$id.";");
            $result = $query->execute();
        }
        return $result;
    }

    /**
     * [findProject description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public static function findProject($param)
    {
        $sql = "SELECT proj.*
        FROM ".(new self)->getSource()." AS proj
        LEFT JOIN ".(new ProjectTypes)->getSource()." AS ptypes ON ptypes.`name`=proj.`project_type`
        LEFT JOIN ".(new ProjectPropTypes)->getSource()." AS pptypes ON pptypes.`name`=proj.`proj_property_type`
        WHERE UPPER(REPLACE(proj.`project_name`,'\'','')) = :project_name AND UPPER(proj.`project_type`) = :project_type 
        AND UPPER(proj.`proj_property_type`) = :project_property_type";
        $result = new self();
        $conn = $result->getReadConnection();
        return new Resultset(null, $result, $conn->query($sql, $param));        
    } 
    
    /**
     * [findProject description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public static function findDisplayProject($param)
    {
        $sql = "SELECT proj.*
        FROM ".(new self)->getSource()." AS proj
        LEFT JOIN ".(new ProjectTypes)->getSource()." AS ptypes ON ptypes.`id`=proj.`project_type_id`
        LEFT JOIN ".(new ProjectPropTypes)->getSource()." AS pptypes ON pptypes.`id`=proj.`proj_property_type_id`
        WHERE UPPER(REPLACE(proj.`project_name`,'\'','')) = :project_name AND UPPER(ptypes.`id`) = :project_type 
        AND UPPER(pptypes.`id`) = :project_property_type";
        $result = new self();
        $conn = $result->getReadConnection();
        return new Resultset(null, $result, $conn->query($sql, $param));        
    } 

    /**
     * [findAll description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */ #echo '<pre>'; var_dump($attributes); echo '</pre>'; die();   
    public static function findAll($param)
    {
        $result = new self();
        $metadata = $result->getModelsMetaData();
        $attributes = $metadata->getAttributes($result);
        $binds = [];
        $sql = "SELECT ";
        if($param['type'] != 'rows')
            $sql .= "SQL_CALC_FOUND_ROWS "; 
        $sql .= "proj.* FROM ( SELECT ";
        $sql .= "proj.id, proj.project_name, \n";
        if($param['type'] != 'rows') $sql .= "SQL_CALC_FOUND_ROWS ";
        foreach ($attributes as $key => $value) {
            switch ($value) {
                case 'status_date':
                case 'status2_date':
                case 'date_updated':
                    $sql .= "DATE_FORMAT($value,'%d-%b-%Y') $value, \n";
                    break;
                case 'description':
                    $sql .= 'proj.'.$value;
                    break;
                case 'id':
                case 'project_name':    
                case 'creation_date':
                case 'creation_by':
                case 'update_date':
                case 'update_by':
                    continue;
                    break;
                default:
                    $sql .= "proj.".$value.", \n";
                    break;
            }
        }
        $sql .=" FROM ".(new self)->getSource()." AS proj \n
            LEFT JOIN ".(new ProjectTypes)->getSource()." pt ON pt.name=proj.project_type \n
            LEFT JOIN ".(new ProjectPropTypes)->getSource()." ppt ON ppt.name=proj.proj_property_type \n
            LEFT JOIN ".(new PropertyTypes)->getSource()." pr ON pr.name=proj.property_type \n
            LEFT JOIN ".(new PropertyTenures)->getSource()." ptn ON ptn.name=proj.tenure \n
            LEFT JOIN ".(new PropertyDistricts)->getSource()." dt ON dt.name=proj.district \n
            LEFT JOIN ".(new PropertyStatus)->getSource()." ps ON ps.name=proj.status \n
            LEFT JOIN ".(new PropertyStatus)->getSource()." ps2 ON ps2.name=proj.status2 \n
            LEFT JOIN ".(new PropertyUnits)->getSource()." b \n
                ON (CASE WHEN proj.unit_type IS NOT NULL THEN FIND_IN_SET(b.name, proj.unit_type) ELSE proj.unit_type = b.name END) \n
            LEFT JOIN ".(new PropertyUnits)->getSource()." c \n
                ON (CASE WHEN proj.available_unit_type IS NOT NULL THEN FIND_IN_SET(c.name, proj.available_unit_type) ELSE proj.available_unit_type = c.name END) \n
            LEFT JOIN ".(new PropertyAgencies)->getSource()." ta \n
                ON (CASE WHEN proj.tender_agency IS NOT NULL THEN FIND_IN_SET(ta.name, proj.tender_agency) ELSE proj.tender_agency = ta.name END) \n
            LEFT JOIN ".(new PropertyAgencies)->getSource()." ma \n
                ON (CASE WHEN proj.marketing_agency IS NOT NULL THEN FIND_IN_SET(ma.name, proj.marketing_agency) ELSE proj.marketing_agency = ma.name END) \n";
        $sql .= " GROUP BY proj.id) proj \n";
        if(!empty($param['conditions'])&&count($param['conditions'])>0) {
            $sql .= "WHERE ";$idx = 0; $binds=[];
            foreach($param['conditions'] as $field => $value) {
                $sql .= ($idx > 0) ? "AND " : "";
                if(is_array($value)&&$value['concat_reg']) {
                    $sql .= $value['function']." ";
                } else {
                    $binds[$field] = $value;
                    $sql .= "proj.".$field ." = :".$field." ";
                }
                $idx++;
            }
        }
        $sql .= (!empty($param['order'])) ? "ORDER BY ".$param['order']." " : "";
        if($param['type'] != 'rows')
            $sql .= "LIMIT 1 ";
        else {
            if(!empty($param["limit"])&&!empty($param["offset"])) {
                $sql .= "LIMIT ". $param["limit"]." OFFSET ".$param["offset"]." ";
            } elseif(!empty($param["limit"])&&empty($param["offset"])) {
                $sql .= " LIMIT ". $param["limit"]." ";
            }
        }
        $sql .=";";
        $conn = $result->getReadConnection();
        $conn->query($sql, $binds);
        $count_sql = "SELECT FOUND_ROWS() AS cnt";        
        $final_sql = ($param['type']!='rows') ? $count_sql : $sql;
        return new Resultset(null, $result, $conn->query($final_sql, $binds));     
    }

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
}