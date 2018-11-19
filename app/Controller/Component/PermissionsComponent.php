<?php
App::uses('CakeObject', 'Core');

class PermissionsComponent extends CakeObject
{

    public $permissions = array(
        'COMMENT_NEWS',
        'LIKE_NEWS',
        'DELETE_HIS_COMMENT',
        'DELETE_COMMENT',
        'EDIT_HIS_EMAIL',
        'ACCESS_DASHBOARD',
        'SEND_SERVER_COMMAND_FROM_DASHBOARD',
        'MANAGE_NEWS',
        'MANAGE_SLIDER',
        'MANAGE_PAGE',
        'MANAGE_NAV',
        'BYPASS_MAINTENANCE',
        'MANAGE_MAINTENANCE',
        'MANAGE_CONFIGURATION',
        'USE_ADMIN_HELP',
        'MANAGE_PERMISSIONS',
        'MANAGE_PLUGINS',
        'MANAGE_API',
        'MANAGE_SERVERS',
        'MANAGE_NOTIFICATIONS',
        'VIEW_STATISTICS',
        'MANAGE_THEMES',
        'MANAGE_USERS',
        'VIEW_WEBSITE_HISTORY'
    );

    public $components = array('Session');

    private $userModel;

    private $controller;

    public $ranks = [];

    function shutdown($controller) {}

    function beforeRender($controller) {}

    function beforeRedirect() {}

    function initialize($controller)
    {
        $this->controller = $controller;
        $this->userModel = ClassRegistry::init('User');
        $this->permModel = ClassRegistry::init('Permission');
        $this->rankModel = ClassRegistry::init('Rank');
    }

    function startup($controller)
    {
        $controller->set('Permissions', $this);
    }

    public function can($perm)
    {
        if (!$this->userModel->isConnected())
            return false;
        if ($this->userModel->isAdmin())
            return true;
        return $this->have($this->userModel->getKey('rank'), $perm);
    }

    public function getRankPermissions($rank)
    {
        if (isset($this->ranks[$rank]))
            return $this->ranks[$rank];
        $search = $this->permModel->find('first', ['conditions' => ['rank' => $rank]]);
        if (empty($search) || !is_array(($search = unserialize($search['Permission']['permissions']))))
            return $this->ranks[$rank] = [];
        return $this->ranks[$rank] = $search;
    }

    public function have($rank, $perm)
    {
        if ($rank == 3 || $rank == 4)
            return true;
        return in_array($perm, $this->getRankPermissions($rank));
    }

    public function get_all()
    {
        $permissionsList = $this->permissions;
        $this->EyPlugin = $this->controller->EyPlugin;

        foreach ($this->EyPlugin->getPluginsActive() as $id => $plugin) {
            foreach ($plugin->permissions->available as $permission) {
                array_push($permissionsList, $permission);
            }
        }

        $customRanks = $this->rankModel->find('all');
        $permissions = [];
        foreach ($permissionsList as $permission) {
            $permissions[$permission] = [
                0 => $this->have(0, $permission),
                2 => $this->have(2, $permission),
            ];
            foreach ($customRanks as $rank) {
                $rank = $rank['Rank']['rank_id'];
                $permissions[$permission][$rank] = $this->have($rank, $permission);
            }
        }
        return $permissions;
    }

}
