<?php

class NotificationsController extends AppController
{

    public function beforeFilter()
    {
        parent::beforeFilter();

        if ($this->params['action'] != "admin_index") {
            $this->response->type('json');
            $this->autoRender = false;
            $this->response->body(json_encode(array()));
        }
    }

    public function getAll($type = 'user')
    {
        if ($this->isConnected) {
            $notifications = $this->Notification->getFromUser($this->User->getKey('id'), $type);
            $this->response->body(json_encode($notifications));
        }
    }

    public function clear($id = 0)
    {
        if ($this->isConnected) {
            $notifications = $this->Notification->clearFromUser($id, $this->User->getKey('id'));
            $this->response->body(json_encode(array('status' => $notifications)));
        }
    }

    public function clearAll()
    {
        if ($this->isConnected) {
            $notifications = $this->Notification->clearAllFromUser($this->User->getKey('id'));
            $this->response->body(json_encode(array('status' => $notifications)));
        }
    }

    public function markAsSeen($id = 0)
    {
        if ($this->isConnected) {
            $notifications = $this->Notification->markAsSeenFromUser($id, $this->User->getKey('id'));
            $this->response->body(json_encode(array('status' => $notifications)));
        }
    }

    public function markAllAsSeen()
    {
        if ($this->isConnected) {
            $notifications = $this->Notification->markAllAsSeenFromUser($this->User->getKey('id'));
            $this->response->body(json_encode(array('status' => $notifications)));
        }
    }

    public function admin_index()
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $this->layout = 'admin';
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_getAll()
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $this->DataTable = $this->Components->load('DataTable');
            $this->modelClass = 'Notification';
            $this->DataTable->initialize($this);
            $this->paginate = array(
                'fields' => array('Notification.id', 'User.pseudo', 'Notification.group', 'Notification.user_id', 'Notification.from', 'Notification.content', 'Notification.seen', 'Notification.type', 'Notification.created'),
                'recursive' => 1
            );
            $this->DataTable->mDataProp = true;

            $response = $this->DataTable->getResponse();

            $data = array();
            foreach ($response['aaData'] as $notification) {

                if ($notification['Notification']['from'] == null) {
                    $from = '<small class="text-muted">' . $this->Lang->get('NOTIFICATION__NO_FROM') . '</small>';
                } else {
                    $from = $this->User->getFromUser('pseudo', $notification['Notification']['from']);
                }

                $actions = '<div class="btn btn-group">';
                if ($notification['Notification']['seen']) {
                    $actions .= '<btn class="btn btn-default disabled active" disabled>' . $this->Lang->get('NOTIFICATION__SEEN') . '</btn>';
                } else {
                    $actions .= '<a class="btn btn-default mark-as-seen" data-seen="' . $this->Lang->get('NOTIFICATION__SEEN') . '" href="' . Router::url(array('action' => 'markAsSeenFromUser', $notification['Notification']['id'], $notification['Notification']['user_id'])) . '">' . $this->Lang->get('NOTIFICATION__MARK_AS_SEEN') . '</a>';
                }
                $actions .= '<a class="btn btn-danger delete-notification" href="' . Router::url(array('action' => 'clearFromUser', $notification['Notification']['id'], $notification['Notification']['user_id'])) . '">' . $this->Lang->get('GLOBAL__DELETE') . '</a>';
                $actions .= '</div>';

                if ($notification['Notification']['type'] == "admin") {
                    $type = '<span class="label label-danger">' . $this->Lang->get('NOTIFICATION__TYPE_ADMIN') . '</span>';
                } else {
                    $type = '<span class="label label-success">' . $this->Lang->get('NOTIFICATION__TYPE_USER') . '</span>';
                }

                $data[] = array(
                    'Notification' => array(
                        'group' => (!empty($notification['Notification']['group']) ? '#' . $notification['Notification']['group'] : '<small class="text-muted">' . $this->Lang->get('NOTIFICATION__NO_FROM') . '</small>'),
                        'from' => $from,
                        'content' => $notification['Notification']['content'],
                        'type' => $type,
                        'created' => $notification['Notification']['created'],
                        'actions' => $actions
                    ),
                    'User' => $notification['User']
                );
            }
            $response['aaData'] = $data;

            $this->response->body(json_encode($response));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_setTo()
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {

            if ($this->request->is('ajax')) {

                if (!empty($this->request->data['content']) && !empty($this->request->data['user_id']) && ($this->request->data['user_id'] == 'all' || !empty($this->request->data['user_pseudo']))) {

                    $from = ($this->request->data['from']) ? $this->User->getKey('id') : null;

                    if ($this->request->data['user_id'] == 'all') {

                        $this->Notification->setToAll($this->request->data['content'], $from);

                    } else {
                        $user_id = $this->User->getFromUser('id', $this->request->data['user_pseudo']);

                        if (empty($user_id)) {
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

    public function admin_clearFromUser($id, $user_id)
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->clearFromUser($id, $user_id);
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_clearAllFromUser($user_id)
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->clearAllFromUser($user_id);
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_clearFromAllUsers($id)
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->clearFromAllUsers($id);
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_clearAllFromAllUsers()
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->clearAllFromAllUsers();
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_markAsSeenFromUser($id, $user_id)
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->markAsSeenFromUser($id, $user_id);
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_markAllAsSeenFromUser($user_id)
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->markAllAsSeenFromUser($user_id);
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_markAsSeenFromAllUsers($id)
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->markAsSeenFromAllUsers($id);
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_markAllAsSeenFromAllUsers()
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_NOTIFICATIONS')) {
            $notifications = $this->Notification->markAllAsSeenFromAllUsers();
            $this->response->body(json_encode(array('status' => $notifications)));
        } else {
            throw new ForbiddenException();
        }
    }

    public function admin_clearAllFromGroup()
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_NOTIFICATIONS'))
            throw new ForbiddenException();
        if (!isset($this->request->data['group']) || empty($this->request->data['group']))
            return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
        $this->Notification->clearAllFromGroup($this->request->data['group']);
        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('NOTIFICATION__SUCCESS_REMOVE'))));
    }


}
