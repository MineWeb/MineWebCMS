<?php
if(isset($search_psc_msg)) {
  foreach ($search_psc_msg as $key => $value) {
    if($value['PaysafecardMessage']['type'] == 1) {
      echo '<div class="alert alert-success">';
        echo '<b>'.$Lang->get('GLOBAL__SUCCESS').' :</b> ';
        echo $Lang->get('SHOP__PAYSAFECARD_MESSAGE_VALID', array(
          '{AMOUNT}' => $value['PaysafecardMessage']['amount'],
          '{POINTS}' => $value['PaysafecardMessage']['added_points'],
          '{MONEY_NAME}' => $Configuration->getMoneyName()
        ));
      echo '</div>';
    } elseif ($value['PaysafecardMessage']['type'] == 0) {
      echo '<div class="alert alert-danger">';
        echo '<b>'.$Lang->get('GLOBAL__ERROR').' :</b> ';
        echo $Lang->get('SHOP__PAYSAFECARD_MESSAGE_INVALID', array(
          '{AMOUNT}' => $value['PaysafecardMessage']['amount'],
        ));
      echo '</div>';
    }
  }
}
?>
