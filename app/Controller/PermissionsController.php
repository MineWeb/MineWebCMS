<?php

class PermissionsController extends AppController
{

    public $components = ['Session', 'History'];

    function admin_index()
    {
        if (!$this->Permissions->can('MANAGE_PERMISSIONS'))
            throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('PERMISSIONS__LABEL'));
        $this->layout = 'admin';

        $this->loadModel('Rank');
        $all_ranks = [
            [
                "Rank" => [
                    'rank_id' => 0,
                    'name' => $this->Lang->get('GLOBAL__TYPE_NORMAL'),
                ],
            ],
            [
                "Rank" => [
                    'rank_id' => 2,
                    'name' => $this->Lang->get('USER__RANK_MODERATOR'),
                ],
            ],
        ];

        $all_ranks = array_merge($all_ranks, $this->Rank->find('all'));
        foreach ($all_ranks as $key => $rank) {
            if (!$this->Permissions->canManageRank($rank['Rank']['rank_id']))
                unset($all_ranks[$key]);
        }
        $this->set(compact('all_ranks'));

        $matrix = $this->Permissions->get_all();
        $available = array_filter(array_keys($matrix), [$this->Permissions, 'can']);
        if ($this->request->is('post')) {
            $this->loadModel('Permission');
            foreach ($all_ranks as $rankRow) {
                $rank = $rankRow['Rank']['rank_id'];
                $permissions = array_values(array_diff($this->Permissions->getRankPermissions($rank), $available));
                foreach ($available as $permission) {
                    if (isset($this->request->data[$permission . '-' . $rank]))
                        $permissions[] = $permission;
                }

                $this->Permission->create();
                $this->Permission->id = $this->Permission->field('id', ['rank' => $rank]);
                $this->Permission->save([
                    'permissions' => serialize($permissions),
                    'rank' => $rank
                ]);
            }

            $this->Session->setFlash($this->Lang->get('PERMISSIONS__SUCCESS_SAVE'), 'default.success');
            $this->Permissions->ranks = [];
            $this->Permissions->permModel->cacheQueries = false;
            $matrix = $this->Permissions->get_all();
        }

        $this->set('permissions', array_intersect_key($matrix, array_flip($available)));
    }

    function admin_add_rank()
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_PERMISSIONS')) {
            $this->autoRender = false;
            $this->response->type('json');
            if ($this->request->is('ajax')) {

                if (!empty($this->request->data['name'])) {

                    $this->loadModel('Rank');

                    // Le rank_id | L'id du rank utilisé dans le composant des permissions & dans la colonne rank des utilisateurs
                    // Le rank_id de base pour les rangs personnalisés commence à partir de 10
                    $rank_id = $this->Rank->find('first', ['limit' => '1', 'order' => 'rank_id desc']);
                    if (!empty($rank_id)) {
                        $rank_id = $rank_id['Rank']['rank_id'] + 1;
                    } else {
                        $rank_id = 10;
                    }

                    // on save
                    $this->Rank->create();
                    $this->Rank->set(['name' => $this->request->data['name'], 'rank_id' => $rank_id]);
                    $this->Rank->save();

                    $this->History->set('ADD_RANK', 'permissions');

                    $this->Session->setFlash($this->Lang->get('USER__RANK_ADD_SUCCESS'), 'default.success');
                    $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('USER__RANK_ADD_SUCCESS')]));

                } else {
                    $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));
                }

            } else {
                $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')]));
            }
        } else {
            $this->redirect('/');
        }
    }

    function admin_delete_rank($id = false)
    {
        if ($this->isConnected && $this->Permissions->can('MANAGE_PERMISSIONS')) {
            $this->autoRender = false;

            $this->loadModel('Rank');
            $search = $this->Rank->find('first', ['conditions' => ['rank_id' => $id]]);
            if (!empty($search)) {
                if (!$this->Permissions->canManageRank($search['Rank']['rank_id']))
                    throw new ForbiddenException();

                $this->Rank->delete($search['Rank']['id']);

                $this->loadModel('Permission');
                $search_perm = $this->Permission->find('first', ['conditions' => ['rank' => $id]]);
                if (!empty($search_perm)) {
                    $this->Permission->delete($search_perm['Permission']['id']);
                }

                $this->Session->setFlash($this->Lang->get('USER__RANK_DELETE_SUCCESS'), 'default.success');
                $this->redirect(['controller' => 'permissions', 'action' => 'index', 'admin' => true]);

            } else {
                $this->redirect(['controller' => 'permissions', 'action' => 'index', 'admin' => true]);
            }

        } else {
            $this->redirect('/');
        }
    }

}
