<?php
namespace Property\Models;

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use Property\Models\Projects;
use Property\Models\PropertyUnits;

class PerProjects extends \Phalcon\Mvc\Model
{

    public $id;
    public $project_id;
    public $unit_type_id;
    public $low_psf;
    public $median_psf;
    public $high_psf;
    public $no_transactions;
    public $transaction_month;
    public $area_per_unit_type_sqm;
    public $area_per_unit_type_sqf;
    public $no_unit_per_unit_type;
    public $available_unit_type_id;
    public $date_avail_unit_updated;
    public $guide_price;
    public $rental_amount;
    public $vacant_date;
    public $furnishing;

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

        $this->hasOne('unit_type_id', '\Property\Models\PropertyUnits', 'id',array(
            'foreignKey' => true, 'alias' => 'PerProjects_PropertyUnits', 'reusable' => true
        ));

    }


    public static function findDetails($param) 
    {   
        if(!empty($param['unit_type_id'])) {
            if (strpos($param['unit_type_id'], ',') !== false) {
                $Units = explode(",", $param['unit_type_id']);
                foreach ($Units as $unit_name) {
                    $res_units = PropertyUnits::findFirstByName($unit_name);
                    $arr_units[] = $res_units->id;
                }
                $post_units = implode(",",$arr_units);
            } else {
                $res_units = PropertyUnits::findFirstByName($param['unit_type_id']);
                $post_units = $res_units->id;
            }
        }

        $sql = "SELECT perprojects.*
        FROM ".(new self)->getSource()." AS perprojects
        LEFT JOIN ".(new Projects)->getSource()." AS proj ON proj.`id`=perprojects.`project_id`
        WHERE perprojects.`unit_type_id` = ".$post_units;
        foreach ($param as $field => $value) {
            if($field=='unit_type_id') continue;
            elseif($field=='date_avail_unit_updated') {
                $date = \DateTime::createFromFormat('j-M-y', $param[$field]);
                $param[$field] =  $date->format('Y-m-d 00:00:00'); 
            }
            $sql .= " AND perprojects.`".$field."`= '".$param[$field]."'";
        }
        $result = new self();
        $conn = $result->getReadConnection();
        return new Resultset(null, $result, $conn->query($sql)); 
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseUsers[]|BaseUsers
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseUsers
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
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
}