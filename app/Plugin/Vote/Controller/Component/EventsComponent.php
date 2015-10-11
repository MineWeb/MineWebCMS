<?php
App::uses('CakeEventListener', 'Event');
App::uses('CakeEvent', 'Event');

class EventsComponent extends Component implements CakeEventListener {

  protected $controller;

  public function startup(Controller $controller) {
      parent::startup($controller);
      $controller->getEventManager()->attach($this);

      $this->controller = $controller;
  }

  public function implementedEvents() {
      return array(
          'requestPage' => 'checkRewardsWaiting',
      );
  }

  public function checkRewardsWaiting(CakeEvent $event) {
    if($event->subject()->request->params['controller'] == "user" && $event->subject()->request->params['action'] == "profile") {
    	App::import('Component', 'ConnectComponent');
    	$this->Connect = new ConnectComponent;
    	$rewards_waiting = ($this->Connect->get('rewards_waited') && $this->Connect->get('rewards_waited') > 0) ? $this->Connect->get('rewards_waited') : false;

    	ModuleComponent::$vars['rewards_waiting'] = $rewards_waiting;
    }
  }

}