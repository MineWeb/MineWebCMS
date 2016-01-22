<?php
class Visit extends AppModel {

  function getVisits($limit = false, $order = 'DESC') {
    $data = $this->find('all', array('limit' => $limit, 'order' => 'id '.$order));
    $data['count'] = count($data);
    return $data;
  }

  function getVisitsByDay($day) { // $day au format : date('Y-m-d')
    $data = $this->find('all', array('conditions' => array('created LIKE' => $day.'%')));
    $data['count'] = count($data);
    return $data;
  }

  function get($groupBy, $limit = false, $order = 'DESC') {
    $search = $this->find('all', array('fields' => $groupBy.',COUNT(*)', 'group' => $groupBy, 'order' => 'id '.$order, 'limit' => $limit));
    foreach ($search as $key => $value) {

      if($value['0']['COUNT(*)'] >= 5) {
        $data[$value['Visit'][$groupBy]] = $value['0']['COUNT(*)'];
      }

    }
    return $data;
  }

}
