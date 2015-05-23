<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	//public $uses = array();
	public $components = array('Session');
	public $helpers = array('Session');

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */

	public function display() {

		//debug($this->Server->commands('say BLABLA[{+}]broardcast mdr[{+}]save-all'));

		$this->layout = $this->Configuration->get_layout();

		$passwd = explode('?' ,$_SERVER['REQUEST_URI']); // on récupére l'url
		if(isset($passwd[1])) { // si il y a un truc en plus
			$passwd = explode('_', $passwd[1]);
			if(isset($passwd[0]) AND $passwd[0] == "resetpasswd") { // si c'est pour reset le password
				if(!empty($passwd[1])) {
					$this->loadModel('Lostpassword');
					$search = $this->Lostpassword->find('all', array('conditions' => array('key' => $passwd[1]))); // on cherche la key de reset password
					if(!empty($search)) { // si elle existe
						if(strtotime(date('Y-m-d H:i:s', strtotime($search[0]['Lostpassword']['created'])).' +1 hour') >= time()) { // si le lien ne date pas de plus d'1 heure
							$resetpsswd['email'] = $search[0]['Lostpassword']['email'];
							$this->loadModel('User');
							$search = $this->User->find('all', array('conditions' => array('email' => $resetpsswd['email'])));
							$resetpsswd['pseudo'] = $search[0]['User']['pseudo'];
							$this->set(compact('resetpsswd'));
						}
					}
				}
			}
		}

		// on delete tout les liens de reset de password au dessus de 1 heure
		$this->loadModel('Lostpassword');
		$search_passwd = $this->Lostpassword->find('all');
		foreach ($search_passwd as $key => $value) {
			if(strtotime(date('Y-m-d H:i:s', strtotime($value['Lostpassword']['created'])).' +1 hour') < time()) {
				$this->Lostpassword->delete($value['Lostpassword']['id']);
			}
		}

		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$title_for_layout = $this->Lang->get('HOME');
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}

		// Page principal 

		// récupérage des news
		$this->loadModel('News'); // on charge le model
		$search_news = $this->News->find('all', array('limit' => '6', 'order' => 'id desc')); // on cherche les 3 dernières news (les plus veille)
		$this->set(compact('search_news')); // on envoie les données à la vue

		// je cherche toutes les news que l'utilisateur connecté a aimé
		if($this->Connect->connect()) {
			$this->loadModel('Like');
			$likes = $this->Like->find('all', array('conditions' => array('author' => $this->Connect->get_pseudo())));
			if(!empty($likes)) {
				foreach ($likes as $key => $value) {
					$likes_list[] = $value['Like']['news_id'];
				}
				$likes = $likes_list;
			} else {
				$likes = array('929302');
			}
			$this->set(compact('likes'));
		}

		//récupération des slides
		$this->loadModel('Slider');
		$search_slider = $this->Slider->find('all');
		$this->set(compact('search_slider'));

		// Fin
		$this->render('home');
	}

	public function debug($o = false, $args = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if(!$o) {
				$this->layout = null;
				$return['first_administrator'] = $this->Configuration->get_first_admin();
				$return['created'] = $this->Configuration->get_created_date();
				$return['url'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
				$return['version'] = $this->Configuration->get('version');
				$return['layout'] = $this->Configuration->get_layout();
				$return['theme'] = $this->Configuration->get('theme');
				$return['plugins'] = $this->EyPlugin->get_plugins();
				$return['today'] = date('d/m/Y H:i:s');
				$return['server_state'] = $this->Configuration->get('server_state');
				echo '<h2><b>This page is only for debug if you have a problem. This string below must be give to Eywek for debug.</b></h2>';
				echo '<center><div style="width:50%;word-wrap: break-word;">'.base64_encode(json_encode($return)).'</div></center>';
			} else {
				$this->layout = null;
				echo '<pre>';
				var_dump(json_decode(base64_decode($args), true));
				echo '</pre>';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function index($slug = false) {
		if($slug != false) {
			$this->loadModel('Page');
			$search = $this->Page->find('all', array('conditions' => array('slug' => $slug)));
			if(!empty($search)) {
				$this->layout = $this->Configuration->get('layout');
				$page = $search[0]['Page'];
				$this->set(compact('page'));
				$this->set('title_for_layout', $page['title']);
			} else {
				$this->redirect('/');
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout',$this->Lang->get('PAGES_LIST'));
			$this->layout = 'admin';
			$this->loadModel('Page');
			$pages = $this->Page->find('all');
			$this->set(compact('pages'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout',$this->Lang->get('ADD_PAGE'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_ajax() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($this->request->is('post')) {
				if(!empty($this->request->data['title']) AND !empty($this->request->data['slug']) AND !empty($this->request->data['content'])) {
					$this->loadModel('Page');
					$this->Page->read(null, null);
					$this->Page->set(array(
						'title' => $this->request->data['title'],
						'content' => $this->request->data['content'],
						'author' => $this->Connect->get_pseudo(),
						'slug' => $this->request->data['slug'],
						'updated' => date('Y-m-d H:i:s'),
					));
					$this->Page->save();
					$this->History->set('ADD_PAGE', 'page');
					echo $this->Lang->get('SUCCESS_PAGE_ADD').'|true';
					$this->Session->setFlash($this->Lang->get('SUCCESS_PAGE_ADD'), 'default.success');
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_delete($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($id != false) {
				 
				$this->loadModel('Page');
				if($this->Page->delete($id)) {
					$this->History->set('DELETE_PAGE', 'page');
					$this->Session->setFlash($this->Lang->get('PAGE_DELETE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'pages', 'action' => 'index', 'admin' => true));
				} else {
					$this->redirect(array('controller' => 'pages', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'pages', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($id != false) {
				 
				$this->set('title_for_layout',$this->Lang->get('EDIT_PAGE'));
				$this->layout = 'admin';
				$this->loadModel('Page');
				$page = $this->Page->find('all', array('conditions' => array('id' => $id)));
				if(!empty($page)) {
					$page = $page[0]['Page'];
					$this->set(compact('page'));
				} else {
					$this->redirect('/');
				}
			} else {
				$this->redirect('/');
			}
		} else {
			$this->redirect('/');
		}	
	}

	public function admin_edit_ajax() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($this->request->is('post')) {
				if(!empty($this->request->data['id']) AND !empty($this->request->data['title']) AND !empty($this->request->data['slug']) AND !empty($this->request->data['content'])) {
					$this->loadModel('Page');
					$this->Page->read(null, $this->request->data['id']);
					$this->Page->set(array(
						'title' => $this->request->data['title'],
						'content' => $this->request->data['content'],
						'slug' => $this->request->data['slug'],
						'updated' => date('Y-m-d H:i:s'),
					));
					$this->Page->save();
					$this->History->set('EDIT_PAGE', 'page');
					echo $this->Lang->get('SUCCESS_PAGE_EDIT').'|true';
					$this->Session->setFlash($this->Lang->get('SUCCESS_PAGE_EDIT'), 'default.success');
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}
}
























