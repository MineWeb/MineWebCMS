<?php
class UtilComponent extends Object {

  private $controller;

  private $to;
  private $from;
  private $subject;
  private $message;
  private $typeSend = 'default';

  private $smtpOptions = array();

	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  function beforeRedirect() {}
  function initialize(&$controller) {$this->controller =& $controller;}
  function startup(&$controller) {}

  /*
    MAIL
  */

  public function prepareMail($to, $subject, $message) {

    $configuration = $this->controller->Configuration;

    $this->to = $to;

    $this->subject = $subject.' | '.$configuration->get('name');

    $this->message = $message;

    $this->from = $configuration->get('name').' <'.$configuration->get('email').'>';

    $this->typeSend = (!$configuration->get('email_send_type') || $configuration->get('email_send_type') != 2) ? 'default' : 'smtp';

    if($this->typeSend == "smtp") {

      $this->smtpOptions['host'] = $configuration->get('smtpHost'); // smtp.sendgrid.net - ssl://smtp.gmail.com
      $this->smtpOptions['port'] = $configuration->get('smtpPort'); // 587 - 465
      $this->smtpOptions['username'] = $configuration->get('smtpUsername'); // Eywek
      $this->smtpOptions['password'] = $configuration->get('smtpPassword'); // motdepasse
      $this->smtpOptions['timeout'] = '30';
      //$this->smtpOptions['client'] = ''; // mineweb.org

    }

    return $this;
  }

  public function sendMail() {

    $this->Email = $this->controller->Components->load('Email');

    if($this->typeSend == "smtp") {
		  $this->Email->smtpOptions = $this->smtpOptions;
      $this->Email->delivery = 'smtp';
    }

		$this->Email->from = $this->from;
		$this->Email->to = $this->to;
		$this->Email->subject = $this->subject;
		$this->Email->template = 'default';
		$this->Email->sendAs = 'html';

		return $this->Email->send($this->message);

  }

  public function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
  }

  public function isValidImage($request, $extensions = array('png'), $width_max = false, $height_max = false, $max_size = false) {

    App::import('Component', 'LangComponent'); // le component
    $Lang = new LangComponent;

    if(empty($request->params['form']['image']['name'])) {
      return array('status' => false, 'msg' => $Lang->get('FORM__EMPTY_IMG'));
    }

    if(!$request->params['form']['image']['size'] || !$request->params['form']['image']['tmp_name']) {
      return array('status' => false, 'msg' => $Lang->get('FORM__NOT_UPLOADED'));
    }

    $extension = pathinfo($request->params['form']['image']['name'], PATHINFO_EXTENSION);

    if(!in_array(strtolower($extension), $extensions)) {
      return array('status' => false, 'msg' => str_replace('{LIST_EXTENSIONS}', implode(', ', $extensions), $Lang->get('FORM__INVALID_EXTENSION')));
    }

    $infos = getimagesize($request->params['form']['image']['tmp_name']);

    if($infos[2] < 1 || $infos[2] > 14 || $infos[0] === NULL || $infos[1] === NULL) {
      return array('status' => false, 'msg' => $Lang->get('FORM__INVALID_IMG'));
    }

    if($max_size) {
      $size = filesize($request->params['form']['image']['tmp_name']);

      if($size > $max_size) {
        return array('status' => false, 'msg' => str_replace('{MAX_SIZE}', $max_size, $Lang->get('FORM__FILE_TOO_HEAVY')));
      }
    }

    if($width_max) {
      if($infos[0] > $width_max) {
        return array('status' => false, 'msg' => str_replace('{MAX_WIDTH}', $width_max, $Lang->get('FORM__INVALID_WIDTH')));
      }
    }

    if($height_max) {
      if($infos[1] > $height_max) {
        return array('status' => false, 'msg' => str_replace('{MAX_HEIGHT}', $height_max, $Lang->get('FORM__INVALID_HEIGHT')));
      }
    }

    return array('status' => true, 'infos' => array('extension' => $extension, 'width' => $infos[0], 'height' => $infos[1]));

  }

  public function uploadImage($request, $name) {
    $folders = explode('/', $name);
    $folders = end($folders);
    if(!is_dir($folders)) {
      if(!mkdir($folders, 0755, true)) {
        return false;
      }
    }
    return move_uploaded_file($request->params['form']['image']['tmp_name'], $name);
  }

}
