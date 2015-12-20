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

}
