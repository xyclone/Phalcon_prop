<?php
namespace Property\Library;

use Phalcon\Filter;
use Property\Models\BaseUsers;

/**
 * Datatable
 *
 * Helps to generate server side datatable json API
 */
class DataTable
{
    /**
     * [generateTableV2 description]
     * @param  [type]  $get_data     [description]
     * @param  [type]  $columns      [description]
     * @param  [type]  $model        [description]
     * @param  string  $condition    [description]
     * @param  boolean $estimate     [description]
     * @param  [type]  $custfunction [description]
     * @return [type]                [description]
     */ //echo '<pre>'; var_dump($result->toArray()); echo '</pre>'; 
    public static function generateTableV2($get_data, $columns, $model, $condition = '', $estimate = false, $custfunction = NULL, $internal = NULL)
    {
        $buttons = array();
        foreach ($columns as $key => $column) {
            if ((isset($column['button'])) or (isset($column['button_group']))) {
                $buttons[] = $column;
                unset($columns[$key]);
            }
        }
        $search_value = BaseUsers::sqlEscape($get_data['search']['value']);//search value
        $draw = $get_data['draw']; //times of drawing table
        $length = $get_data['length']; //records per page
        $start = $get_data['start'];
        $order_column_id = $get_data['order'][0]['column'];
        $order_column = $get_data['columns'][$order_column_id]['name'];
        $order_dir = $get_data['order'][0]['dir'];
#echo '<pre>'; var_dump($order_column); echo '</pre>';
#echo '<pre>'; var_dump($get_data['columns'][$order_column_id]['name']); echo '</pre>';
        if ($length == '-1') {
            $length = '';
        }
        //generate conditions
        $hasval=0;
        $totalCol = count($get_data['columns']);

// echo '<pre>'; var_dump($get_data['columns']); echo '</pre>';
// echo '<pre>'; var_dump($order_column_id); echo '</pre>';
// echo '<pre>'; var_dump($order_column); echo '</pre>'; die(); 
#$test = 'sorted as default';

        foreach ($get_data['columns'] as $key => $search) {
            //order column
            //$order_column = $get_data['columns'][$order_column_id]['name'];

//echo '<pre>'; var_dump($key); echo '</pre>'; die();   
            if ($key == $order_column) {
                $order_column = $search['data'];
#$asd = 'search data';
            } elseif($order_column_id>0) {
                $order_column = $get_data['columns'][$order_column_id]['name'];
                // if($columns[$search['data']]['dbc']==$get_data['columns'][$order_column_id]['data']) {
                //     //$asd = $columns[$search['data']];
                //     $relTable = $columns[$search['data']]['foreign'];
                //     $forOrder = $columns[$search['data']]['extend'][1];
                //     $foreign_keys = $relTable::find([
                //         "order" =>  $forOrder. ' ' . $order_dir,
                //     ]); 
                //     #$order_column = $relTable.$forOrder;
                //     // $asd = $order_column;
                // }


            } else {
                $asd = 'normal col';
            }/*elseif(!empty($columns[$search['data']]['fldsort'])) {
                $order_column = $columns[$search['data']]['fldsort'];
            }*/

#$test = !empty($columns[$search['data']]['fldsort']) ? $columns[$search['data']]['fldsort'] : $asd;
#echo '<pre>'; var_dump($order_column); echo '</pre>'; die();       

            $keywords = $search['search']['value'];
            if(!empty($keywords) && $columns[$search['data']]['search'] == true) {
                $hasval++;
                if(!empty($condition)) $hasval = 2;
                $condition  .= ($hasval==1) ? " " : " AND ";

                switch ($columns[$search['data']]['fldtype']) {                      
                    case 'int':
                        $tableCol = (!empty($columns[$search['data']]['prxTbl'])) ? $columns[$search['data']]['prxTbl'].'.'.$columns[$search['data']]['dbc'] : $columns[$search['data']]['dbc'];
                        $condition .= self::intSearch($tableCol,$keywords);
                        break;
                    case 'date':
                        $date = $columns[$search['data']]['custom'];
                        $tableCol = (!empty($columns[$search['data']]['prxTbl'])) ? $columns[$search['data']]['prxTbl'].'.'.$columns[$search['data']]['dbc'] : $columns[$search['data']]['dbc'];
                        if (!empty($date['start']) && empty($date['stop']))
                            $condition .= self::dateSearch($tableCol,'ge \''.$date['start'].' 00:00:00\'');
                        elseif (empty($date['start']) && !empty($date['stop']))
                            $condition .= self::dateSearch($tableCol,'le \''.$date['stop'].' 23:59:59\'');
                        elseif (!empty($date['start']) && !empty($date['stop']))
                            $condition .= self::dateSearch($tableCol,'ge \''.$date['start'].' 00:00:00\', le \''.$date['stop'].' 23:59:59\'');
                        else
                            $condition .= self::dateSearch($tableCol,'');
                        break;
                    case '':
                    case NULL:
                    default:
                        if(!empty($columns[$search['data']]['foreign'])) {
                            $tableCol = $columns[$search['data']]['dbc'];
                            $relTable = $columns[$search['data']]['foreign'];
#echo '<pre>1'; var_dump(is_object($relTable)); echo '</pre>'; die();
                            if (is_object($relTable)) { //when foreign is an object or Model
                                $order_column = $columns[$search['data']]['primary_key']; //change order column
                                /*(!empty($columns[$search['data']]['foreign_key'])) 
                                ? $columns[$search['data']]['foreign_key'] :*/
#$test = 'sorted as reTable';
                                $tableCol = $columns[$search['data']]['primary_key'];
                                $forField = (!empty($columns[$search['data']]['extend'][2])) ? $columns[$search['data']]['extend'][2] : "";
                                $forOrder = (!empty($columns[$search['data']]['foreign_search'])) ? $columns[$search['data']]['foreign_search'] : $columns[$search['data']]['extend'][1];
                                $forExtend = ($columns[$search['data']]['extendIn']) ? $columns[$search['data']]['extendIn'] : "";

                                if (!empty($forField) && is_array($forField)) {
                                    foreach ($forField as $field) {
                                        $stringArr[] = self::strSearch($field,$keywords);  
                                    }
                                     
                                    $stringCondition = implode(' OR ', $stringArr);
                                    $searchCols = ["username", "CONCAT(firstname,' ',lastname) AS ".$columns[$search['data']]['extend'][1] ];
                                    //$searchCols = "CONCAT(firstname,' ',lastname) AS ".$columns[$search['data']]['extend'][1];
                                    //"CONCAT(firstname,' ',lastname) AS ".$columns[$search['data']]['extend'][1];
#echo '<pre>2'; var_dump($order_column); echo '</pre>'; die(); 
                                } elseif($forExtend) {
//echo '<pre>'; var_dump($columns[$search['data']]['primary_key']); echo '</pre>'; die();
                                    $forOrder = $searchCols = $columns[$search['data']]['foreign_key'];
                                    // $foreign_keys = $relTable::find([
                                    //     "conditions" => "$searchCols LIKE '%$keywords%'",
                                    //     'columns' => $columns[$search['data']]['extendFld']." AS ".$order_column,
                                    //     "order" =>  $searchCols. ' ' . $order_dir,
                                    // ]);
                                    // if($foreign_keys&&$foreign_keys->count()>0) {
                                    //     foreach ($foreign_keys as $key => $value) {
                                            
                                    //     }
                                    // }

                                    $stringCondition = "$searchCols LIKE '%$keywords%'";
                                    $searchCols = $columns[$search['data']]['extendFld'];

//echo '<pre>'; var_dump($foreign_keys->toArray()); echo '</pre>';   
// echo '<pre>'; var_dump($searchCols); echo '</pre>';
// echo '<pre>'; var_dump($forOrder); echo '</pre>';
// echo '<pre>'; var_dump($order_dir); echo '</pre>';                                  
//echo '<pre>'; var_dump("=="); echo '</pre>'; die();
                                } else {
                                    $stringCondition = self::strSearch($columns[$search['data']]['extend'][1],$keywords);
                                   
                                    if(!empty($columns[$search['data']]['foreign_search'])) {
                                        $stringCondition = self::strSearch($columns[$search['data']]['foreign_search'],$keywords); 
                                    }
                                    $searchCols = $columns[$search['data']]['foreign_key'];
// echo '<pre>2'; var_dump($stringCondition); echo '</pre>'; die();                                     
// echo '<pre>2'; var_dump($searchCols); echo '</pre>'; die();    
                                }

                                if($model!=$relTable) {
//echo '<pre>'; var_dump(is_array($searchCols)); echo '</pre>'; die();                                    
                                    if(is_array($searchCols)) {
                                        $foreign_keys = $relTable::find([
                                            "conditions" => $stringCondition,
                                            'columns' => implode (", ", $searchCols),
                                            "order" =>  $forOrder. ' ' . $order_dir,
                                        ]);
                                    } else {
                                        $foreign_keys = $relTable::find([
                                            "conditions" => $stringCondition,
                                            'columns' => $searchCols,
                                            "order" =>  $forOrder. ' ' . $order_dir,
                                        ]);


                                    }
//echo '<pre>'; var_dump($foreign_keys->toArray()); echo '</pre>'; die();   
                                    $item_array = [];
                                    if($foreign_keys && $foreign_keys->count()>0) {
                                        foreach ($foreign_keys->toArray() as $fitem) {
//echo '<pre>'; var_dump(!in_array($fitem[(!empty($searchCols) ? $searchCols : $columns[$search['data']]['primary_key'])], $item_array)); echo '</pre>'; die();                                            
                                            if(!in_array($fitem[(!empty($searchCols) ? $searchCols : $columns[$search['data']]['primary_key'])], $item_array))
                                                $item_array[] = $fitem[(!empty($searchCols) ? $searchCols : $columns[$search['data']]['primary_key'])];
                                        }

                                        if (array_product(array_map('is_numeric', $item_array))) {
                                            $imp = implode( ",", $item_array );
                                        } else {
                                            $imp = "'" . implode ( "', '", $item_array ) . "'";
                                        }

                                        if($forExtend) {
                                            $ind=0;
                                            foreach ($item_array as $key => $value) {
                                                $condition .= ($ind>=1) ? " OR " : "";
                                                $condition .= "FIND_IN_SET($value,$tableCol)";
                                                $ind++;
                                            }
                                        } else {
                                            $condition .= $tableCol ." IN ($imp)";
                                        }
                                    } else {
                                        //$condition .= $tableCol ." LIKE '%". $keywords."%' ";
                                        $condition .= $tableCol ." = '". self::generateRandomString(60) ."'";              
                                    }
                                } else {
                                    $condition = $stringCondition;
                                }
                            } else if (is_null($relTable)) {
                                $condition .= $tableCol ." = ". $tableCol; 
                            } else { //if Foreign data is an array
                                $arrData = $columns[$search['data']]['foreign'];
                                #echo '<pre>'; var_dump($tableCol); echo '</pre>'; die();
                                $condition .= self::arrSearch($arrData,$tableCol,$keywords);
                                //echo '<pre>'; var_dump($condition); echo '</pre>'; die();
                            }
                        } else{
                            $tableCol = (!empty($columns[$search['data']]['prxTbl'])) ? $columns[$search['data']]['prxTbl'].'.'.$columns[$search['data']]['dbc'] : $columns[$search['data']]['dbc'];
// echo '<pre>ELSE '; var_dump($tableCol); echo '</pre>';
// echo '<pre>ELSE '; var_dump($keywords); echo '</pre>'; die();                                  
                            $condition .= self::strSearch($tableCol,$keywords);
                        }
                        break;
                }
            }
        }
// echo '<pre>'; var_dump($get_data['order'][0]['column']); echo '</pre>';
#echo '<pre>'; var_dump($relTable); echo '</pre>'; die();    
        #$order_column = ($order_column=='district_id') ? 'district_name' : $order_column; 
#echo '<pre>'; var_dump($condition); echo '</pre>'; die();   
        if($custfunction == NULL) {
            try {
                $result = $model::find([
                "conditions" => $condition,
                "limit" => $length,
                "offset" => $start,
                "order" => $order_column . ' ' . $order_dir
                ]);  
            } catch(\Exception $e) {
                $result = false;
            } 
       } elseif($internal!=NULL) {
            $result = $model::find($internal);       
       } else {
            try {
                $result = $model::$custfunction(["type"=>"rows",
                "conditions" => $condition,
                "limit" => $length,
                "offset" => $start,
                "order" => $order_column . ' ' . $order_dir]);
            } catch(\Exception $e) {
                $result = false;
            }        
       }
#echo '<pre>'; var_dump($result->toArray()); echo '</pre>'; die();
        #$result = $result->getProjects(["order"=>"name $order_dir"]);
#$result->getDistrictId(['order' => "name $order_dir"]);
#echo '<pre>'; var_dump($result); echo '</pre>'; die();       
// echo '<pre>'; var_dump($order_column); echo '</pre>';       
#echo '<pre>'; var_dump($result); echo '</pre>'; die(); 
//echo '<pre>'; var_dump($result->toArray()); echo '</pre>'; die();  
        //prepare data
        $data = [];
        if($result && count($result) >0) { //[jason] changed for findAuth returns. pls check if count works with other $custfunction
            $data = self::formatRows($columns,$result);
        }
        //get total records
        if($estimate){
            $total = (new $model)->getRowCount();
            $filter_count = 10000; //test only. show total found for huge table ???
        } else{
            if($custfunction == NULL) {
                // if(!$model::find()) { 
                //   $total = $filter_count = 0;
                // } else {
                    if (method_exists($model, 'getTotalRows') && is_callable(array($model, 'getTotalRows'))) {
                        $total = (new $model)->getTotalRows()[0]->count;                         
                        $filter_count = (new $model)->getTotalRows(['conditions' => $condition])[0]->count;                     
                    } else {
                        $total = $model::find()->count();
                        $filter_count = $model::find(['conditions' => $condition])->count();
                    }                    
                //}  
            } else {
                if (method_exists($model, 'getTotalRows') && is_callable(array($model, 'getTotalRows'))) {
                    $total = (new $model)->getTotalRows()[0]->count;
                    $filter_count = $model::$custfunction(["type"=>"total", "conditions"=>$condition, "limit"=>1, "offset"=>0])[0]->cnt;
                }
                else {
                    $total = $model::$custfunction(["type"=>"total", "conditions"=>$condition, "limit"=>1, "offset"=>0])[0]->cnt;
                    $filter_count = $model::$custfunction(["type" => "rows", "conditions" => $condition])->count();
                }
            }
        }

        $return_arr = array();
        $return_arr['draw'] = $draw;
        $return_arr['recordsTotal'] = (empty($data)) ? 0 : $total;
        $return_arr['recordsFiltered'] = (empty($data)) ? 0 : $filter_count;
        $return_arr['data'] = $data;
        return json_encode($return_arr);
    }

    /**
     * [formatRows description]
     * @param  [type] $columns [description]
     * @param  [type] $data    [description]
     * @return [type]          [description]
     */ //echo '<pre>'; var_dump($result->toArray()); echo '</pre>'; 
    public static function formatRows($columns,$data) {


// echo '<pre>'; var_dump($data); echo '</pre>';          
// die();
        foreach ($data as $row) {
            $arr = [];
            foreach ($columns as $field => $column) {
                //extend
                if (isset($column['extend'])) {
                    $temp = $row;
                    $tempArr = [];
                    $item = $column['extend'][0];
                    $extfield = $column['extend'][1];


//if($column['dbc']=='project_type_id' || $column['dbc']=='proj_property_type_id') continue;



                    if(!empty($column['implode']) || !empty($column['concat']) || !empty($column['counter'])) {
                        if ($temp->$item) {
                            $counter = 1;
                            $count = $temp->$item->count();
                            foreach ($temp->$item as $key => $value)
                                if(!empty($value->$field))
                                    if(!empty($column['concat']))
                                        $tempArr[] = $value->$field;
                                    else
                                        $tempArr = $value->$field;
                            if(is_array($tempArr) && count($tempArr)>0)
                                $temp = implode(', ', $tempArr);
                            elseif(is_string($tempArr))
                                $temp = $tempArr;
                            else
                                $temp = "";
                        } else {
                            $temp = "";
                            break;
                        }
                    } elseif (!empty($column['implodevalues']) || !empty($column['concat'])) {
                        if ($temp->$item) {
                            $counter = 1;
                            $count = $temp->$item->count();
                            foreach ($temp->$item as $key => $value) {
// echo '<pre>'; var_dump($key); echo '</pre>';
// echo '<pre>'; var_dump($value); echo '</pre>';
                            }

// echo '<pre>'; var_dump($column['dbc']); echo '</pre>';
                
// die();

                        } else {
                            $temp = "";
                            break;
                        }
                    } else {
                        if (!empty($temp->$item->$extfield)) {
                            $temp = $temp->$item->$extfield;
                        } else {
                            $colname = $column['dbc'];
                            $temp = $row->$colname;
                        }
                    }
                } elseif (isset($column['convert'])) {
                    $colname = $column['dbc'];
                    $class = new $column['convert'][0]();
                    $temp = $class->$column['convert'][1]($row->$colname);
                } else {
                    $colname = $column['dbc'];
                    $temp = $row->$colname;
                }

//echo '<pre>'; var_dump($temp); echo '</pre>'; die();

                //postfix string
                if(isset($column['postfix']) && !empty($column['postfix'])) {
                  $temp = $temp . $column['postfix'];
                }
                //replace string
                if(isset($column['replace']) && !empty($column['replace_with'])) {
                    if(strlen($temp)>2 && !empty($temp))
                        $temp = substr($temp, 0, 1).str_repeat('*', strlen($temp) - 2).substr($temp, strlen($temp) - 1, 1);
                    else
                        $temp = $temp;
                }
                //alias
                if(isset($column['alias']) and is_array($column['alias'])){
                    if(isset($column['alias'][$temp])) $temp = $column['alias'][$temp];
                }
                $arr[$field] = $temp;
            }
            $response[] = $arr;
        }
//echo '<pre>'; var_dump($response); echo '</pre>'; die();            
        return $response;
    }

    /**
     * [intSearch description]
     * @param  [type] $field  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public static function intSearch($field,$params) {
        if(!empty($field) && !empty($params)) {
            //Format interger
            $find = ['gt','lt','ge','le','eq','ne','>','<','>=','<=','!','!='];
            $replace = ['>','<','>=','<=','=','!=','>','<','>=','<=','!=','!='];
            if (strpos($params, ',') !== false) {
                $response = "";
                $keywords = explode(",", $params);
                $counter=1;
                foreach ($keywords as $keyword) {
                    if($counter>1){
                        //Check for AND or OR
                        if (strpos($keyword, 'OR') !== false) {
                            $keyword = trim(str_replace("OR","",$keyword));
                            $response .= " OR $field ";
                        } else {
                            $response .= " AND $field ";
                        }

                        $response .= self::getIntegerMatch($keyword);
                    }else {
                        $response .= "$field ";
                        $response .= self::getIntegerMatch($keyword);
                    }
                    $counter++;
                }
            } else {
                $response = "$field ";
                $response .= self::getIntegerMatch($params);
            }
        } else {
            $response = "$field = $field";
        }
        return $response;
    }

    /**
     * [getIntegerMatch description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private static function getIntegerMatch($params) {
        $find = ['gt','lt','ge','le','eq','ne','>','<','>=','<=','!','!='];
        $replace = ['>','<','>=','<=','=','!=','>','<','>=','<=','!=','!='];
        if (!preg_match("/^[\>\<\>=\<=\!\= ]{0,}+[0-9]/", $params)){
            $response = $params;
        } else {
            $pattern = "/(\d+)/";
            $int_arr = preg_split($pattern, $params, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            if(count($int_arr) > 2) {
                $response = $params;
            } else {
                $response = "";
                if(count($int_arr)==1 && is_numeric($int_arr[0])){
                    $response .= "= ".$int_arr[0];
                }
                elseif(count($int_arr)>1){
                    $response .= trim(str_replace($find, $replace, $int_arr[0])).$int_arr[1];
                }
                else{
                    $response = $params;
                }
            }
        }
        return $response;
    }

    /**
     * [strSearch description]
     * @param  [type] $field  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public static function strSearch($field,$params)
    {
        if(!empty($field) && !empty($params)) {
            if (strpos($params, ',') !== false) {
                $response = "";
                $keywords = explode(",", $params);
                $counter=1;
                foreach ($keywords as $keyword) {
                    if($counter>1) {
                        //Check for AND or OR
                        if (strpos($keyword, 'OR') !== false) {
                            $keyword = trim(str_replace("OR","",$keyword));
                            $response .= " OR $field ";
                        } else {
                            $response .= " AND $field ";
                        }
                        $response .= self::getStringMatch($keyword);
                    } else {
                        $response = "$field ";
                        $response .= self::getStringMatch($keyword);
                    }
                    $counter++;
                }
            }else {
                $response = "LOWER($field) ";
                $response .= self::getStringMatch($params);
            }
        } else {
            $response = "$field = $field";
        }
        return $response;
    }

    /**
     * [getStringSearch description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private static function getStringMatch($params) {
        $response = "";
        $filter = new Filter();
        $params = $filter->sanitize($params, "string");
        //Check for NOT or DEFAULT
        if (strpos($params, "NOT") !== false) {
            $params = trim(str_replace("NOT","",$params));
            $response .= "NOT LIKE ";
        } else {
            $response .= "LIKE ";
        }
        //Check string
        if (strpos($params, '"') !== false) {
            $params = str_replace("\"","",$params);
            if (strpos($params, '*') !== false) {
                $wildcards = explode("*", $params);
                $response .= "LOWER('%";
                $response .= str_replace("*","%",$params);
                $response .= "%')";
            } else {
                $response .= "LOWER('%". $params ."%')";
            }
        } else {
            if (strpos($params, '*') !== false) {
                $wildcards = explode("*", $params);
                $response .= "LOWER('%";
                foreach ($wildcards as $key) {
                    $response .= trim($key) ."%";
                }
                $response .= "')";
            } else {
                $response .= "LOWER('%". trim($params) ."%')";
            }
        }
        return $response;
    }

    /**
     * [dateSearch description]
     * @param  [type] $field  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public static function dateSearch($field,$params) {
        if(!empty($field) && !empty($params)) {
            $response = "$field ";
            //Format interger
            $find = ['gt','lt','ge','le','eq','ne','>','<','>=','<=','!='];
            $replace = ['>','<','>=','<=','=','!=','>','<','>=','<=','!='];
            if (strpos($params, ',') !== false) {
                $keywords = explode(",", $params);
                $counter=1;
                foreach ($keywords as $keyword) {
                    if($counter>1){
                        //Check for AND or OR
                        if (strpos($keyword, 'OR') !== false) {
                            $keyword = trim(str_replace("OR","",$keyword));
                            $response .= " OR $field ";
                        } else {
                            $response .= " AND $field ";
                        }
                        $response .= trim(str_replace($find, $replace, $keyword));
                    }else {
                        $response .= trim(str_replace($find, $replace, $keyword));
                    }
                    $counter++;
                }
            } else {
                $response .= trim(str_replace($find, $replace, $params));
            }
        } else {
            $response = "$field = $field";
        }
        return $response;
    }


    //==================================
    //ADDED MAR 12, 2017 FOR DataTableV2
    //==================================
    /**
     * [arrSearch description]
     * @param  [type] $field  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public static function arrSearch($arrayData,$field,$params) {
        if(!empty($field) && !empty($params)) {
            if (strpos($params, ',') !== false) {
                $response = "";
                $keywords = explode(",", $params);
                $counter=1;
                foreach ($keywords as $keyword) {
                    if($counter>1) {
                        //Check for AND or OR
                        if (strpos($keyword, 'OR') !== false) {
                            $keyword = trim(str_replace("OR","",$keyword));
                            $response .= " OR $field ";
                        } else {
                            $response .= " AND $field ";
                        }
                        $response .= self::getArrayMatch($arrayData,$keyword);
                    } else {
                        $response = "$field ";
                        $response .= self::getArrayMatch($arrayData,$keyword);
                    }
                    $counter++;
                }
            }else {
                $response = "$field ";
                $response .= self::getArrayMatch($arrayData,$params);
            }
        } else {
            $response = "$field = $field";
        }
        return $response;
    }

    /**
     * [getArrayMatch description]
     * @param  [type] $arrayData [description]
     * @param  [type] $params    [description]
     * @return [type]            [description]
     */
    private static function getArrayMatch($arrayData,$params) {
        $response = "";
        //Check for NOT or DEFAULT
        if (strpos($params, "NOT") !== false) {
            $params = trim(str_replace("NOT","",$params));
            $response .= "NOT IN ";
        } else {
            $response .= "IN ";
        }
        //Check string
        if (strpos($params, '"') !== false) {
            $params = str_replace("\"","",$params);
            if (strpos($params, '*') !== false) {
                $wildcards = explode("*", $params);
                $response .= "(";
                foreach ($wildcards as $key) {
                    if(count(self::arrayToKey($arrayData,$key))>0)
                        $response .= implode(',',self::arrayToKey($arrayData,$key));
                    else
                        $response .= 0;//trim($key);
                }
                $response .= ")";
            } else {
                $response .= "(";
                    if(count(self::arrayToKey($arrayData,$params))>0)
                        $response .= implode(',',self::arrayToKey($arrayData,$params));
                    else
                        $response .= 0;//trim($key);
                $response .= ")";
            }
        } else {
            if (strpos($params, '*') !== false) {
                $wildcards = explode("*", $params);
                $response .= "(";
                foreach ($wildcards as $key) {
                    if(count(self::arrayToKey($arrayData,$key))>0)
                        $response .= implode(',',self::arrayToKey($arrayData,$key));
                    else
                        $response .= 0;//trim($key);
                }
                $response .= ")";
            } else {
                $response .= "(";
                    if(count(self::arrayToKey($arrayData,$params))>0)
                        $response .= implode(',',self::arrayToKey($arrayData,$params));
                    else
                        $response .= 0;//trim($key);
                $response .= ")";
            }
        }
        return $response;
    }

    /**
     * [arrayToKey description]
     * @param  [type] $arrayData [description]
     * @param  [type] $keyword   [description]
     * @return [type]            [description]
     */
    private static function arrayToKey($arrayData,$keyword) {
        $matches = [];
        foreach($arrayData as $key => $item)
            if(strpos(strtolower($item), strtolower($keyword)) !== false)
                $matches[] = $key;
        return $matches;
    }

    /**
     * [generateRandomString description]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    private static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    //=========================================
    // ./END ADDED MAR 12, 2017 FOR DataTableV2
    //=========================================

}
