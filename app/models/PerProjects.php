<?php
namespace Property\Models;

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use Property\Models\Projects;
use Property\Models\ProjectDetails;
use Property\Models\ProjectTypes;
use Property\Models\ProjectPropTypes;
use Property\Classes\PropertyClass;

class PerProjects extends \Phalcon\Mvc\Model
{
    public $id;
    public $project_id;
    public $project_name;
    public $project_type;
    public $proj_property_type;
    public $area_sqft;
    public $unit_type;
    public $low_price;
    public $median_price;
    public $high_price;
    public $creation_date;
    public $creation_by;
    public $update_date;
    public $update_by;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db');
        $this->setSource("prop_per_projects");
        $this->di = \Phalcon\DI::getDefault();
        $this->belongsTo('project_id', '\Property\Models\Projects', 'id',array(
            'foreignKey' => true, 'alias' => 'PerProjects_Project', 'reusable' => true
        ));
    }

    /**
     * [getFields description]
     * @return [type] [description]
     */
    public static function getFields()
    {
        $class = PropertyClass::$project_per_project_fields;
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
     * [findProject description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public static function findPerProject($param)
    {
        $sql = "SELECT *
        FROM ".(new self)->getSource()." AS pp
        WHERE UPPER(REPLACE(`project_name`,'\'','')) = :project_name AND UPPER(`project_type`) = :project_type AND area_sqft = :area_sqft
        AND `project_id` = :project_id AND UPPER(`proj_property_type`) = :project_property_type AND unit_type = :unit_type";
        $result = new self();
        $conn = $result->getReadConnection();
        return new Resultset(null, $result, $conn->query($sql, $param));        
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

    public function beforeSave()
    {
        //Set the update date
        $this->update_date = date('Y-m-d H:i:s');
        $this->update_by = $this->di->get('session')->get('user')['username'];
    }    

    public static function deletePerProject($id)
    {
        $db = \Phalcon\DI::getDefault()->get('db');
        //return $db;
        $query = $db->prepare("DELETE FROM ".(new self)->getSource()." WHERE id=".(int)$id.";");
        $result = $query->execute();
        return $result;
    }    
}