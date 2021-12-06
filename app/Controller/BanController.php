<?php

class BanController extends AppController
{
    function index() {
        if (!$this->isConnected || $this->User->isBanned() == false)
            $this->redirect("/");

        $this->set('title_for_layout', $this->Lang->get("BAN__BAN"));
        $this->set('reason', $this->User->isBanned());
    }

    function ip() {
        if (!$this->IPisBan())
            $this->redirect("/");

        $this->set('title_for_layout', $this->Lang->get("BAN__BAN"));
        $this->set('reason', $this->isBanned);
    }

    function admin_index()
    {
        if (!$this->isConnected || !$this->Permissions->can("MANAGE_BAN"))
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get("BAN__HOME"));
        $this->layout = 'admin';
        $this->loadModel("user");
        $this->loadModel("Ban");
        $banned_users = $this->Ban->find("all");

        $users = $this->User->find("all");

        $this->set(compact("banned_users", "users"));
    }

    function admin_add()
    {
        if (!$this->isConnected || !$this->Permissions->can("MANAGE_BAN"))
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get("BAN__HOME"));
        $this->layout = 'admin';
        $this->set('type', $this->Configuration->getKey('member_page_type'));

        if ($this->request->is("post")) {
            $this->autoRender = false;
            $this->response->type('json');

            if (empty($this->request->data("reason")))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));

            $this->loadModel("User");
            foreach ($this->request->data as $key => $v) {
                if ($v != "on" || $key == "name" || strpos($key, "-ip"))
                    continue;

                $this->Ban->create();
                $this->Ban->set([
                    "user_id" => $key,
                    "reason" => $this->request->data("reason")
                ]);

                if ($this->request->data($key . "-ip") == "on")
                    $this->Ban->set([
                        "ip" => $this->User->find("first", ["conditions" => ['id' => $key]])['User']['ip']
                    ]);

                $this->Ban->save();
            }

            $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('BAN__SUCCESS')]));
        }
    }

    function admin_unban($id = false)
    {
        if (!$this->isConnected || !$this->Permissions->can("MANAGE_BAN"))
            throw new ForbiddenException();

        $this->loadModel('Ban');
        $this->Ban->delete($id);
        $this->Session->setFlash($this->Lang->get('BAN__UNBAN_SUCCESS'), 'default.success');
        $this->redirect(['controller' => 'ban', 'action' => 'index', 'admin' => true]);
    }

    public function admin_get_users_not_ban()
    {
        if ($this->isConnected and $this->Permissions->can('MANAGE_BAN')) {
            $this->autoRender = false;
            $this->response->type('json');
            if ($this->request->is('ajax')) {
                $available_ranks = [
                    0 => ['label' => 'success', 'name' => $this->Lang->get('USER__RANK_MEMBER')],
                    2 => ['label' => 'warning', 'name' => $this->Lang->get('USER__RANK_MODERATOR')],
                    3 => ['label' => 'danger', 'name' => $this->Lang->get('USER__RANK_ADMINISTRATOR')],
                    4 => ['label' => 'danger', 'name' => $this->Lang->get('USER__RANK_ADMINISTRATOR')]
                ];
                $this->loadModel('Rank');
                $custom_ranks = $this->Rank->find('all');
                foreach ($custom_ranks as $key => $value) {
                    $available_ranks[$value['Rank']['rank_id']] = [
                        'label' => 'info',
                        'name' => $value['Rank']['name']
                    ];
                }
                $this->DataTable = $this->Components->load('DataTable');
                $this->modelClass = 'User';
                $this->DataTable->initialize($this);
                $this->paginate = [
                    'fields' => ['User.id', 'User.pseudo', 'User.rank', 'User.ip'],
                ];
                $this->DataTable->mDataProp = true;
                $response = $this->DataTable->getResponse();
                $users = $response['aaData'];
                $data = [];
                foreach ($users as $key => $value) {
                    $checkIsBan = $this->Ban->find('first', ["conditions" => ['user_id' => $value['User']['id']]]);

                    if ($checkIsBan != null)
                        continue;

                    if ($this->Permissions->have($value['User']['rank'], "BYPASS_BAN"))
                        continue;

                    $username = $value['User']['pseudo'];
                    $rank_label = (isset($available_ranks[$value['User']['rank']])) ? $available_ranks[$value['User']['rank']]['label'] : $available_ranks[0]['label'];
                    $rank_name = (isset($available_ranks[$value['User']['rank']])) ? $available_ranks[$value['User']['rank']]['name'] : $available_ranks[0]['name'];
                    $rank = '<span class="label label-' . $rank_label . '">' . $rank_name . '</span>';
                    $checkbox = "<input type='checkbox' name=" . $value['User']['id'] . ">";
                    $banIpCheckbox = "<input type='checkbox' name=" . $value['User']['id'] . "-ip>";
                    $data[] = [
                        'User' => [
                            'pseudo' => $username,
                            'ban' => $checkbox,
                            'banIp' => $banIpCheckbox,
                            'rank' => $rank,
                            'ip' => $value['User']['ip']
                        ]
                    ];
                }
                $response['aaData'] = $data;
                $this->response->body(json_encode($response));
            }
        }
    }

    function admin_liveSearch($query = false)
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->isConnected and $this->Permissions->can('MANAGE_BAN')) {
            $this->loadModel("User");
            if ($query != false) {
                $result = $this->User->find('all', ['conditions' => ['pseudo LIKE' => $query . '%']]);
                foreach ($result as $key => $value) {
                    $checkIsBan = $this->Ban->find('first', ["conditions" => ['user_id' => $value['User']['id']]]);

                    if ($checkIsBan != null)
                        continue;

                    if ($this->Permissions->have($value['User']['rank'], "BYPASS_BAN"))
                        continue;

                    $users[] = ['pseudo' => $value['User']['pseudo'], 'id' => $value['User']['id']];
                }
                $response = (empty($result)) ? ['status' => false] : ['status' => true, 'data' => $users];
                $this->response->body($response);
            } else {
                $this->response->body(json_encode(['status' => false]));
            }
        } else {
            $this->response->body(json_encode(['status' => false]));
        }
    }
}
