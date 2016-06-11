<?php
class NotificationsController extends AppController {

  public function beforeFilter() {
    parent::beforeFilter();

    $this->response->type('json');
    $this->autoRender = false;
    $this->response->body(json_encode(array()));
  }

  public function getAll() {
    if($this->isConnected) {
      $notifications = $this->Notification->getFromUser($this->User->getKey('id'));
      $this->response->body(json_encode($notifications));
    }
  }

  public function clear($id = 0) {
    if($this->isConnected) {
      $notifications = $this->Notification->clearFromUser($id, $this->User->getKey('id'));
      $this->response->body(json_encode(array('status' => $notifications)));
    }
  }
  public function clearAll() {
    if($this->isConnected) {
      $notifications = $this->Notification->clearAllFromUser($this->User->getKey('id'));
      $this->response->body(json_encode(array('status' => $notifications)));
    }
  }

  public function admin_setToUser($user_id = 0) {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {

      if($this->User->exist($user_id) && $this->request->is('ajax')) {

        if(!empty($this->request->data['content'])) {

          $from = (!isset($this->request->data['from']) || $this->request->data['from'] == 'all') ? null : $this->User->getKey('id');

          $this->Notification->setToUser($this->request->data['content'], $user_id, $from);

          $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('NOTIFICATION__SUCCESS_SET'))));

        } else {
          $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
        }

      } else {
        throw new NotFoundException();
      }

    } else {
      throw new ForbiddenException();
    }
  }
  public function admin_setToAll() {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {

      if($this->request->is('ajax')) {

        if(!empty($this->request->data['content'])) {

          $from = (!isset($this->request->data['from']) || $this->request->data['from'] == 'all') ? null : $this->User->getKey('id');

          $this->Notification->setToUser($this->request->data['content'], null, $from);

          $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('NOTIFICATION__SUCCESS_SET'))));

        } else {
          $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
        }

      } else {
        throw new NotFoundException();
      }

    } else {
      throw new ForbiddenException();
    }
  }

  public function admin_clearFromUser($id, $user_id) {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
      $notifications = $this->Notification->clearFromUser($id, $user_id);
      $this->response->body(json_encode(array('status' => $notifications)));
    } else {
      throw new ForbiddenException();
    }
  }
  public function admin_clearAllFromUser($user_id) {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
      $notifications = $this->Notification->clearAllFromUser($user_id);
      $this->response->body(json_encode(array('status' => $notifications)));
    } else {
      throw new ForbiddenException();
    }
  }

  public function admin_clearFromAllUsers($id) {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
      $notifications = $this->Notification->clearFromAllUsers($id);
      $this->response->body(json_encode(array('status' => $notifications)));
    } else {
      throw new ForbiddenException();
    }
  }
  public function admin_clearAllFromAllUsers() {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
      $notifications = $this->Notification->clearAllFromAllUsers();
      $this->response->body(json_encode(array('status' => $notifications)));
    } else {
      throw new ForbiddenException();
    }
  }

}
