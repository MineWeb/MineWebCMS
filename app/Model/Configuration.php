<?php
class Configuration extends AppModel {

  public $dataConfig;

  private function getData() {
    if (empty($this->dataConfig)) {
      $config = $this->find('first');
      if (!isset($config['Configuration']))
          return [];
      $this->dataConfig = $config['Configuration'];
    }
    return $this->dataConfig;
  }

  public function getAll() {
    return $this->getData();
  }

  public function getMoneyName($plural = true) {
    return ($plural) ? $this->getData()['money_name_plural'] : $this->getData()['money_name_singular'];
  }

  public function getKey($key) {
    return (isset($this->getData()[$key])) ? $this->getData()[$key] : false;
  }

  public function setKey($key, $value) {
    $this->read(null, 1);
    $this->set(array($key => $value));
    return $this->save();
  }

  public function getFirstAdministrator() {
    return ClassRegistry::init('User')->find('first', array('conditions' => array('rank' => '4')))['User']['pseudo'];
  }

  public function getInstalledDate() {
    return ClassRegistry::init('User')->find('first', array('conditions' => array('rank' => '4')))['User']['created'];
  }

}
