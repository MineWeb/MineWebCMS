<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
require ROOT.'/config/function.php';



/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	var $components = array('Session', 'Connect', 'Configuration', 'EyPlugin', 'History', 'Statistics', 'Navbar', 'Server', 'Permissions', 'Lang', 'Update');
	var $helpers = array('Session');

	var $view = 'Theme';

	public function beforeFilter() {

		/* Charger les components des plugins si ils s'appellent "EventsConpoment.php" */

		$plugins = $this->EyPlugin->get_list();
		foreach ($plugins as $key => $value) {
			$useEvents = $this->EyPlugin->get('useEvents', $value['plugins']['name']);
			if($useEvents) {
				$this->Components->load($value['plugins']['name'].'.Events');
			}
		}

		/* ---- */

		if($this->Connect->connect()) {
			if($this->Connect->get('rank') == 5 AND $this->params['controller'] != "maintenance") {
				$this->redirect(array('controller' => 'maintenance', 'action' => 'index/banned'));
			}
		}

		if($this->params['prefix'] == "admin") {
			$plugins_need_admin = $this->EyPlugin->get_list();
			foreach ($plugins_need_admin as $key => $value) {
				if($this->EyPlugin->get('admin', $value['plugins']['name'])) {
					$plugins_admin[] = array('name' => $value['plugins']['name'], 'slug' => $this->EyPlugin->get('slug', $value['plugins']['name'])); 
				}
			}
			if(!empty($plugins_admin)) {
				$plugins_need_admin = $plugins_admin;
			} else {
				$plugins_need_admin = null;
			}
			$this->set(compact('plugins_need_admin'));
		}

		if($this->params['controller'] == "user" OR $this->params['controller'] == "maintenance" OR $this->Configuration->get('maintenance') == '0' OR $this->Connect->connect() AND $this->Connect->if_admin()) {
		} else {
			$this->redirect(array('controller' => 'maintenance', 'action' => 'index'));
		}
		Configure::write('theme', $this->Configuration->get('theme'));
		$this->__setTheme();
	}

	function beforeRender() {
		$this->getEventManager()->dispatch(new CakeEvent('onLoadPage', $this, $this->request->data));
	}

	function __setTheme() {
        $this->theme = Configure::read('theme');
    }
}
