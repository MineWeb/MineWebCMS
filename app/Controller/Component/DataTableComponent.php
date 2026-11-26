<?php

/**
 * This component provides compatibility between the dataTables jQuery plugin and CakePHP 2
 * @author chris
 * @package DataTableComponent
 * @link http://www.datatables.net/release-datatables/examples/server_side/server_side.html parts of code borrowed from dataTables example
 * @since version 1.1.1
 * Copyright (c) 2013 Chris Nizzardini
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class DataTableComponent extends Component
{

    public $conditionsByValidate = 0;
    public $emptyElements = 0;
    public $fields = [];
    public $mDataProp = false;
    private $model;
    private $controller;
    private $times = [];

    public function __construct()
    {

    }

    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
        $modelName = $this->controller->modelClass;
        $this->model = $this->controller->{$modelName};
    }

    /**
     * returns dataTables compatible array - just json_encode the resulting aray
     * @param object $controller optional
     * @param object $model optional
     * @return array
     * @throws Exception
     */
    public function getResponse($controller = null, $model = null)
    {

        /**
         * it is no longer necessary to pass in a controller or model
         * this is handled in the initialize method
         * $controller is disregarded.
         * $model is only necessary if you are using a model from a different controller such as if you are in
         * a CustomerController but your method is displaying data from an OrdersModel.
         */

        $this->setTimes('Pre', 'start', 'Preproccessing of conditions');

        if ($model != null) {
            if (is_string($model)) {
                $this->model = $this->controller->{$model};
            } else {
                $this->model = $model;
                unset($model);
            }
        }

        $conditions = isset($this->controller->paginate['conditions']) ? $this->controller->paginate['conditions'] : null;

        $isFiltered = false;

        if (!empty($conditions)) {
            $isFiltered = true;
        }

        $query = $this->controller->request->query;
        $fields = $this->getColumnFields($query);
        $sort = isset($query['iSortCol_0']) ? $query['iSortCol_0'] : null;
        if ((is_string($sort) || is_int($sort)) && isset($fields[$sort]) && (!isset($query['sEcho']) || $query['sEcho'] != 1)) {
            $direction = isset($query['sSortDir_0']) && $query['sSortDir_0'] === 'asc' ? 'ASC' : 'DESC';
            $this->controller->paginate = array_merge($this->controller->paginate, ['order' => [$fields[$sort] => $direction]]);
        }

        if (isset($query['sSearch']) && is_string($query['sSearch']) && $query['sSearch'] !== '') {
            $conditions = $this->getWhereConditions($fields);

            if (!empty($this->controller->paginate['contain'])) {
                $this->controller->paginate = array_merge_recursive($this->controller->paginate, ['contain' => $conditions]);
            } else {
                $this->controller->paginate = array_merge_recursive($this->controller->paginate, ['conditions' => ['AND' => $conditions]]);
            }
            $isFiltered = true;
        }

        $this->setTimes('Pre', 'stop');
        $this->setTimes('Count All', 'start', 'Counts all records in the table');
        // @todo avoid multiple queries for finding count, maybe look into "SQL CALC FOUND ROWS"
        // get full count
        $this->model->recursive = -1;
        $total = $this->model->find('count');
        $this->setTimes('Count All', 'stop');
        $this->setTimes('Filtered Count', 'start', 'Counts records that match conditions');
        $parameters = $this->controller->paginate;

        if ($isFiltered) {
            $filteredTotal = $this->model->find('count', $parameters);
        }
        $this->setTimes('Filtered Count', 'stop');
        $this->setTimes('Find', 'start', 'Cake Find');

        // set sql limits
        if (isset($this->controller->request->query['iDisplayStart']) && $this->controller->request->query['iDisplayLength'] != '-1') {
            $start = $this->controller->request->query['iDisplayStart'];
            $length = $this->controller->request->query['iDisplayLength'];
            $parameters['limit'] = $length;
            $parameters['offset'] = $start;
        }

        // execute sql select
        $data = $this->model->find('all', $parameters);

        $this->setTimes('Find', 'stop');
        $this->setTimes('Response', 'start', 'Formatting of response');
        // dataTables compatible array
        $response = [
            'sEcho' => isset($this->controller->request->query['sEcho']) ? intval($this->controller->request->query['sEcho']) : 1,
            'iTotalRecords' => $total,
            'iTotalDisplayRecords' => $isFiltered === true ? $filteredTotal : $total,
            'aaData' => []
        ];

        // return data
        if (!$data) {
            return $response;
        } else {
            foreach ($data as $i) {
                if ($this->mDataProp == true) {
                    $response['aaData'][] = $i;
                } else {
                    $tmp = $this->getDataRecursively($i);
                    if ($this->emptyElements > 0) {
                        $tmp = array_pad($tmp, count($tmp) + $this->emptyElements, '');
                    }
                    $response['aaData'][] = array_values($tmp);
                }
            }
        }
        $this->setTimes('Response', 'stop');
        return $response;
    }

    /**
     * setTimes method - adds to timer of settings[timed] = true
     * @param string $key
     * @param string $action (start or stop)
     * @param string $desc (optional)
     * @param string $line
     */
    private function setTimes($key, $action, $desc = '')
    {
        if (isset($this->settings) && isset($this->settings['timer']) && $this->settings['timer'] == true) {
            $this->times[$key][$action] = [
                'action' => $action,
                'time' => microtime(true),
                'description' => $desc
            ];
        }
    }

    private function getColumnFields($query)
    {
        $fields = $this->fields;
        if (!$fields && !empty($this->controller->paginate['fields'])) {
            $fields = $this->controller->paginate['fields'];
        }
        if (!$this->mDataProp) {
            return $fields;
        }
        if (!$fields) {
            foreach (array_keys($this->model->schema()) as $field) {
                $fields[] = $this->model->alias . '.' . $field;
            }
        }

        $columns = [];
        foreach ($query as $key => $field) {
            if (preg_match('/^mDataProp_([0-9]+)\z/', $key, $matches) && in_array($field, $fields, true)) {
                $columns[$matches[1]] = $field;
            }
        }
        return $columns;
    }

    private function getWhereConditions($fields)
    {
        $conditions = [];
        $query = $this->controller->request->query;
        foreach ($fields as $index => $column) {
            if (!isset($query['bSearchable_' . $index]) || !in_array($query['bSearchable_' . $index], ['true', true], true)) {
                continue;
            }
            $condition = [$column . ' LIKE' => '%' . $query['sSearch'] . '%'];
            if (!$this->mDataProp && $this->conditionsByValidate && isset($this->model->validate[$column])) {
                $condition = $this->conditionByDataType($column);
            }
            if ($this->mDataProp || empty($this->controller->paginate['contain'])) {
                $conditions['OR'][] = $condition;
                continue;
            }
            $field = explode('.', $column, 2);
            if (isset($field[1], $this->controller->paginate['contain'][$field[0]]['fields'])
                && in_array($field[1], $this->controller->paginate['contain'][$field[0]]['fields'], true)) {
                $conditions[$field[0]]['conditions'][] = $condition;
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
    private function conditionByDataType($field)
    {
        $condition = [];
        foreach ($this->model->validate[$field] as $rule => $j) {
            switch ($rule) {
                case 'boolean':
                case 'numeric':
                case 'naturalNumber':
                    $condition = [$field => $this->controller->request->query['sSearch']];
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
    private function getDataRecursively($data, $key = null)
    {
        $fields = [];

        // note: the chr() function is used to produce the arrays index to make sorting via ksort() easier.

        // loop through cake query result
        foreach ($data as $x => $i) {
            // go recursive
            if (is_array($i)) {
                //if(!array_key_exists($x,$this->model->hasMany)){
                $fields = array_merge($fields, $this->getDataRecursively($i, $x));
                //}
            } // check if component was given fields explicitely
            else if (!empty($this->fields)) {
                if (in_array("$key.$x", $this->fields)) {
                    $index = array_search("$key.$x", $this->fields);
                    //echo "$key.$x = $index = $i \n";
                    // index needs to be a string so array_merge handles it properly
                    $fields[chr($index)] = "$i";
                }
            } // dimension is not multi-dimensionable so add to $fields
            else if (isset($this->controller->paginate['fields'])) {
                if (in_array("$key.$x", $this->controller->paginate['fields'])) {
                    $index = array_search("$key.$x", $this->controller->paginate['fields']);
                    // index needs to be a string so array_merge handles it properly
                    $fields[chr($index)] = "$i";
                }
            } // will try to include all results but this will likely not work for you
            else {
                $fields["$key.$x "] = "$i";
            }
        }
        ksort($fields);
        //var_dump($fields);
        return $fields;
    }

    /**
     * getTimes method - returns an array of the components benchmarks
     * @return array
     */
    public function getTimes()
    {
        $times = [];
        $componentStart = 0;
        $end = 0;
        foreach ($this->times as $x => $i) {
            $start = $desc = 0;
            foreach ($i as $j) {
                if ($j['action'] == 'start') {
                    $start = $j['time'];
                    $desc = $j['description'];
                    if ($componentStart == 0) {
                        $componentStart = $start;
                    }
                } else if ($j['action'] == 'stop') {
                    $end = $j['time'];
                }
                if ($start > 0 && $end > 0) {
                    $times[$x] = [
                        'description' => $desc,
                        'time' => round(($end - $start), 4),
                    ];
                }
            }
        }

        if (isset($this->settings) && isset($this->settings['timer']) && $this->settings['timer'] == true) {
            $times['TOTAL'] = [
                'time' => round(($end - $componentStart), 4)
            ];
        }

        return $times;
    }
}
