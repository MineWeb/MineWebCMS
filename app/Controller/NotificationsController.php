<?php
class NotificationsController extends AppController {

  public function beforeFilter() {
    parent::beforeFilter();

    if($this->params['action'] != "admin_index") {
      $this->response->type('json');
      $this->autoRender = false;
      $this->response->body(json_encode(array()));
    }
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

  public function admin_index() {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
      $this->layout = 'admin';
    } else {
      throw new ForbiddenException();
    }
  }

  public function admin_getAll() {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
      $this->DataTable = $this->Components->load('DataTable');
      $this->modelClass = 'Notification';
      $this->DataTable->initialize($this);
      $this->paginate = array(
      'fields' => array('Notification.id','Notification.user_id','Notification.from','Notification.content','Notification.created'),
      );
      $this->DataTable->mDataProp = true;

      $response = $this->DataTable->getResponse();

      $data = array();
      foreach ($response['aaData'] as $notification) {

        if($notification['Notification']['from'] == null) {
          $from = '<small class="text-muted">'.$this->Lang->get('NOTIFICATION__NO_FROM').'</small>';
        } else {
          $from = $this->User->getFromUser('pseudo', $notification['Notification']['from']);
        }

        $pseudo = $this->User->getFromUser('pseudo', $notification['Notification']['user_id']);

        $data[]['Notification'] = array(
          'pseudo' => $pseudo,
          'from' => $from,
          'content' => $notification['Notification']['content'],
          'created' => $notification['Notification']['created'],
          'actions' => '<a class="btn btn-danger" href="'.Router::url(array('action' => 'clearFromUser', $notification['Notification']['id'], $notification['Notification']['user_id'])).'">'.$this->Lang->get('GLOBAL__DELETE').'</a>'
        );

      }
      $response['aaData'] = $data;

      $this->response->body(json_encode($response));
    } else {
      throw new ForbiddenException();
    }
  }

  public function admin_setTo() {
    if($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {

      if($this->request->is('ajax')) {

        if(!empty($this->request->data['content']) && !empty($this->request->data['user_id']) && ($this->request->data['user_id'] == 'all' || !empty($this->request->data['user_pseudo']))) {

          $from = ($this->request->data['from']) ? $this->User->getKey('id') : null;

          if($this->request->data['user_id'] == 'all') {
            $users = $this->User->find('all');

            $notifications = array();

            foreach ($users as $user) {
              $notifications[] = array(
                'content' => $this->request->data['content'],
                'user_id' => $user['User']['id'],
                'from' => $from
              );
            }

            $this->Notification->saveMany($notifications);

          } else {
            $user_id = $this->User->getFromUser('id', $this->request->data['user_pseudo']);

            if(empty($user_id)) {
              $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__EDIT_ERROR_UNKNOWN'))));
              return;
            }

            $this->Notification->setToUser($this->request->data['content'], $user_id, $from);
          }

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
