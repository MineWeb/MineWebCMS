<?php
class Navbar extends AppModel {

  public function afterFind($results, $primary = false) {

    if(!empty($results)) {

      foreach ($results as $key => $value) {

        if($value['Navbar']['url'] == "#") {
          $results[$key]['Navbar']['url'] = array('type' => 'submenu');
        } else {
          $results[$key]['Navbar']['url'] = json_decode($value['Navbar']['url'], true);
        }

      }

    }

    return $results;

  }

}
