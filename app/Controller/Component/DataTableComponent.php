<?php

/**
 * This component provides compatibility between the dataTables jQuery plugin and CakePHP 2
 * @author chris
 * @package DataTableComponent
 * @link http://www.datatables.net/release-datatables/examples/server_side/server_side.html parts of code borrowed from dataTables example
 * @since version 1.1.1
Copyright (c) 2013 Chris Nizzardini

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
 */
class DataTableComponent extends Component{

    private $model;
    private $controller;
    private $times = array();
    public $conditionsByValidate = 0;
    public $emptyElements = 0;
    public $fields = array();
    public $mDataProp = false;

    public function __construct(){

    }

    public function initialize(Controller $controller){
        $this->controller = $controller;
        $modelName = $this->controller->modelClass;
        $this->model = $this->controller->{$modelName};
    }

/**
 * returns dataTables compatible array - just json_encode the resulting aray
 * @param object $controller optional
 * @param object $model optional
 * @return array
 */
    public function getResponse($controller = null, $model=null){

        /**
         * it is no longer necessary to pass in a controller or model
         * this is handled in the initialize method
         * $controller is disregarded.
         * $model is only necessary if you are using a model from a different controller such as if you are in
         * a CustomerController but your method is displaying data from an OrdersModel.
         */

        $this->setTimes('Pre','start','Preproccessing of conditions');

        if($model != null){
            if(is_string($model)){
                $this->model = $this->controller->{$model};
            }
            else{
                $this->model = $model;
                unset($model);
            }
        }

        $conditions = isset($this->controller->paginate['conditions']) ? $this->controller->paginate['conditions'] : null;

        $isFiltered = false;

        if( !empty($conditions) ){
            $isFiltered = true;
        }

        if(isset($this->controller->request->query)){
            $httpGet = $this->controller->request->query;
        }

        // check for ORDER BY in GET request
        if(isset($httpGet) && isset($httpGet['iSortCol_0']) && $httpGet['sEcho'] != 1){
            $orderBy = $this->getOrderByStatements();
            if(!empty($orderBy)){
                $this->controller->paginate = array_merge($this->controller->paginate, array('order'=>$orderBy));
            }
        }

        // check for WHERE statement in GET request
        if(isset($httpGet) && !empty($httpGet['sSearch'])){
            $conditions = $this->getWhereConditions();

            if( !empty($this->controller->paginate['contain']) ){
                $this->controller->paginate = array_merge_recursive($this->controller->paginate, array('contain'=>$conditions));
            }
            else{
                $this->controller->paginate = array_merge_recursive($this->controller->paginate, array('conditions'=>array('AND'=>$conditions)));
            }
            $isFiltered = true;
        }

        $this->setTimes('Pre','stop');
        $this->setTimes('Count All','start','Counts all records in the table');
        // @todo avoid multiple queries for finding count, maybe look into "SQL CALC FOUND ROWS"
        // get full count
        $this->model->recursive = -1;
        $total = $this->model->find('count');
        $this->setTimes('Count All','stop');
        $this->setTimes('Filtered Count','start','Counts records that match conditions');
        $parameters = $this->controller->paginate;

        if($isFiltered){
            $filteredTotal = $this->model->find('count',$parameters);
        }
        $this->setTimes('Filtered Count','stop');
        $this->setTimes('Find','start','Cake Find');

        // set sql limits
        if( isset($this->controller->request->query['iDisplayStart']) && $this->controller->request->query['iDisplayLength'] != '-1' ){
            $start = $this->controller->request->query['iDisplayStart'];
            $length = $this->controller->request->query['iDisplayLength'];
            $parameters['limit'] = $length;
            $parameters['offset'] = $start;
        }

        // execute sql select
        $data = $this->model->find('all', $parameters);

        $this->setTimes('Find','stop');
        $this->setTimes('Response','start','Formatting of response');
        // dataTables compatible array
        $response = array(
            'sEcho' => isset($this->controller->request->query['sEcho']) ? intval($this->controller->request->query['sEcho']) : 1,
            'iTotalRecords' => $total,
            'iTotalDisplayRecords' => $isFiltered === true ? $filteredTotal : $total,
            'aaData' => array()
        );

        // return data
        if(!$data){
            return $response;
        }
        else{
            foreach($data as $i){
                if($this->mDataProp == true){
                    $response['aaData'][] = $i;
                }
                else{
                    $tmp = $this->getDataRecursively($i);
                    if($this->emptyElements > 0){
                        $tmp = array_pad($tmp,count($tmp)+$this->emptyElements,'');
                    }
                    $response['aaData'][] = array_values($tmp);
                }
            }
        }
        $this->setTimes('Response','stop');
        return $response;
    }

/**
 * returns sql order by string after converting dataTables GET request into Cake style order by
 * @param void
 * @return string
 */
    private function getOrderByStatements(){

        if( !isset($this->controller->paginate['fields']) && !empty($this->controller->paginate['contain']) && empty($this->fields) ){
            throw new Exception("Missing field and/or contain option in Paginate. Please set the fields so I know what to order by.");
        }

        $orderBy = '';

        $fields = !empty($this->fields) ? $this->fields : $this->controller->paginate['fields'];

        // loop through sorting columbns in GET
        //for ( $i=0 ; $i<intval( $this->controller->request->query['iSortingCols'] ) ; $i++ ){
            // if column is found in paginate fields list then add to $orderBy
            if( $this->mDataProp == true ){
                $direction = $this->controller->request->query['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
                $mDataProp = $this->controller->request->query['iSortCol_0'];
                $orderBy = $this->controller->request->query['mDataProp_'.$mDataProp].' '.$direction.', ';
            }
            else if( !empty($fields) && isset($this->controller->request->query['iSortCol_0']) ){
                $direction = $this->controller->request->query['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
                $orderBy = $fields[ $this->controller->request->query['iSortCol_0'] ].' '.$direction.', ';
            }
        //}

        if(!empty($orderBy)){
            return substr($orderBy,0, -2);
        }

        return $orderBy;
    }

/**
 * returns sql conditions array after converting dataTables GET request into Cake style conditions
 * will only search on fields with bSearchable set to true (which is the default value for bSearchable)
 * @param void
 * @return array
 */
    private function getWhereConditions(){

        if( $this->mDataProp == false && !isset($this->controller->paginate['fields']) && empty($this->fields) ){
            throw new Exception("Field list is not set. Please set the fields so I know how to build where statement.");
        }

        $conditions = array();


        if($this->mDataProp == true){
            for($i=0;$i<$this->controller->request->query['iColumns'];$i++){
                if(!isset($this->controller->request->query['bSearchable_'.$i]) || $this->controller->request->query['bSearchable_'.$i] == true){
                    $fields[] = $this->controller->request->query['mDataProp_'.$i];
                }
            }
        }
        else if(!empty($this->fields) || !empty($this->controller->paginate['fields']) ){
            $fields = !empty($this->fields) ? $this->fields : $this->controller->paginate['fields'];
        }

        foreach($fields as $x => $column){

            // only create conditions on bSearchable fields
            if( $this->controller->request->query['bSearchable_'.$x] == 'true' ){

                if($this->mDataProp == true){
                    $conditions['OR'][] = array(
                        $this->controller->request->query['mDataProp_'.$x].' LIKE' => '%'.$this->controller->request->query['sSearch'].'%'
                    );
                }
                else{

                    list($table, $field) = explode('.', $column);

                    // attempt using definitions in $model->validate to build intelligent conditions
                    if( $this->conditionsByValidate == 1 && array_key_exists($column,$this->model->validate) ){

                        if( !empty($this->controller->paginate['contain']) ){
                            if(array_key_exists($table, $this->controller->paginate['contain']) && in_array($field, $this->controller->paginate['contain'][$table]['fields'])){
                                $conditions[$table]['conditions'][] = $this->conditionByDataType($column);
                            }
                        }
                        else{
                            $conditions['OR'][] = $this->conditionByDataType($column);
                        }
                    }
                    else{

                        if( !empty($this->controller->paginate['contain']) ){
                            if(array_key_exists($table, $this->controller->paginate['contain']) && in_array($field, $this->controller->paginate['contain'][$table]['fields'])){
                                $conditions[$table]['conditions'][] = $column.' LIKE "%'.$this->controller->request->query['sSearch'].'%"';
                            }
                        }
                        else{
                            $conditions['OR'][] = array(
                                $column.' LIKE' => '%'.$this->controller->request->query['sSearch'].'%'
                            );
                        }
                    }
                }
            }
        }
        return $conditions;
    }

/**
 * looks through the models validate array to determine to create conditions based on datatype, returns condition array.
 * to enable this set $this->DataTable->conditionsByValidate = 1.
 * @param string $field
 * @return array
 */
    private function conditionByDataType($field){
        foreach($this->model->validate[$field] as $rule => $j){
            switch($rule){
                case 'boolean':
                case 'numeric':
                case 'naturalNumber':
                    $condition = array($field => $this->controller->request->query['sSearch']);
                    break;
            }
        }
        return $condition;
    }

/**
 * finds data recursively and returns a flattened key => value pair array
 * second parameter is not required and only used in callbacks to self
 * @param array $data
 * @param string $key
 * @return array
 */
    private function getDataRecursively($data,$key=null){
        $fields = array();

        // note: the chr() function is used to produce the arrays index to make sorting via ksort() easier.

        // loop through cake query result
        foreach($data as $x => $i){
            // go recursive
            if(is_array($i)){
                //if(!array_key_exists($x,$this->model->hasMany)){
                    $fields = array_merge($fields,$this->getDataRecursively($i,$x));
                //}
            }
            // check if component was given fields explicitely
            else if( !empty($this->fields) ){
                if(in_array("$key.$x", $this->fields)){
                    $index = array_search("$key.$x",$this->fields);
                    //echo "$key.$x = $index = $i \n";
                    // index needs to be a string so array_merge handles it properly
                    $fields[chr($index)] = "$i";
                }
                else{
                    //echo "$key.$x (NOT FOUND) \n";
                }
            }
            // dimension is not multi-dimensionable so add to $fields
            else if(isset($this->controller->paginate['fields'])){
                if(in_array("$key.$x", $this->controller->paginate['fields'])){
                    $index = array_search("$key.$x", $this->controller->paginate['fields']);
                    // index needs to be a string so array_merge handles it properly
                    $fields[chr($index)] = "$i";
                }
            }
            // will try to include all results but this will likely not work for you
            else{
                $fields["$key.$x "] = "$i";
            }
        }
        ksort($fields);
        //var_dump($fields);
        return $fields;
    }

/**
 * setTimes method - adds to timer of settings[timed] = true
 * @param string $key
 * @param string $action (start or stop)
 * @param string $desc (optional)
 * @param string $line
 */
    private function setTimes($key,$action,$desc=''){
        if(isset($this->settings) && isset($this->settings['timer']) && $this->settings['timer'] == true){
            $this->times[$key][$action] = array(
                'action' => $action,
                'time' => microtime(true),
                'description' => $desc
            );
        }
    }

/**
 * getTimes method - returns an array of the components benchmarks
 * @return type
 */
    public function getTimes(){
        $times = array();
        $componentStart = 0;
        foreach($this->times as $x => $i){
            $start = $end = $desc = $startLine = $endLine = 0;
            foreach($i as $j){
                if($j['action'] == 'start'){
                    $start = $j['time'];
                    $desc = $j['description'];
                    if($componentStart == 0){
                        $componentStart = $start;
                    }
                }
                else if($j['action'] == 'stop'){
                    $end = $j['time'];
                }
                if($start > 0 && $end > 0){
                    $times[$x] = array(
                        'description' => $desc,
                        'time' => round(($end - $start),4),
                    );
                }
            }
        }

        if(isset($this->settings) && isset($this->settings['timer']) && $this->settings['timer'] == true){
            $times['TOTAL'] = array(
                'time' => round(($end - $componentStart),4)
            );
        }

        return $times;
    }
}
