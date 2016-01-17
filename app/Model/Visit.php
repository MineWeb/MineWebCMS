<?php
class Visit extends AppModel {

  function getVisits() {
    $data = $this->find('all');
    $data['count'] = count($data);
    return $data;
  }

  function getVisitsByDay($day) { // $day au format : date('Y-m-d')
    $data = $this->find('all', array('conditions' => array('created LIKE' => $day.'%')));
    $data['count'] = count($data);
    return $data;
  }

  function get($groupBy) {
    $search = $this->find('all', array('fields' => $groupBy.',COUNT(*)', 'group' => $groupBy));
    foreach ($search as $key => $value) {

      $data[$value['Visit'][$groupBy]] = $value['0']['COUNT(*)'];

    }
    return $data;
  }

}
