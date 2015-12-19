<?php
App::uses('CakeEventListener', 'Event');

class VoteRewardsListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'requestPage' => 'checkRewardsWaiting',
        );
    }

    public function checkRewardsWaiting($event) {
        if($event->subject()->request->params['controller'] == "user" && $event->subject()->request->params['action'] == "profile") {

            // On récupére le modal
            $UserModel = ClassRegistry::init('User');

            $rewards_waiting = ($UserModel->getKey('rewards_waited') && $UserModel->getKey('rewards_waited') > 0) ? $UserModel->getKey('rewards_waited') : false;

            ModuleComponent::$vars['rewards_waiting'] = $rewards_waiting;
        }
    }
}
