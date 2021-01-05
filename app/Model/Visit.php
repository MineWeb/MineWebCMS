<?php

class Visit extends AppModel
{

    function getVisits($limit = false, $order = 'DESC')
    {
        $data = $this->find('all', ['limit' => $limit, 'order' => 'id ' . $order]);
        $data['count'] = count($data);
        return $data;
    }

    function getVisitsCount($limit = false, $order = 'DESC')
    {
        return $this->find('count', ['limit' => $limit, 'order' => 'id ' . $order]);
    }

    function getVisitRange($limit)
    {
        $data = [];

        $search = $this->find('all', ['fields' => 'DATE(created),COUNT(*)', 'group' => 'DATE(created)', 'order' => 'id DESC', 'limit' => $limit]);

        foreach ($search as $key => $value) {
            $data[$value[0]['DATE(created)']] = $value[0]['COUNT(*)'];
        }

        return $data;
    }

    function getVisitsByDay($day)
    { // $day au format : date('Y-m-d')
        $data = $this->find('all', ['conditions' => ['created LIKE' => $day . '%']]);
        $data['count'] = count($data);
        return $data;
    }

    function getGrouped($groupBy, $limit = false, $order = 'DESC')
    {
        $data = [];

        $search = $this->find('all', ['fields' => $groupBy . ',COUNT(*)', 'group' => $groupBy, 'order' => 'COUNT(*) ' . $order, 'limit' => $limit]);
        foreach ($search as $key => $value) {

            if ($value['0']['COUNT(*)'] >= 5) {
                $data[$value['Visit'][$groupBy]] = $value['0']['COUNT(*)'];
            }

        }
        return $data;
    }

}
