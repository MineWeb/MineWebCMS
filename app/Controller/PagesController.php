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

		$this->layout = $this->Configuration->getKey('layout');

		$passwd = explode('?' ,$_SERVER['REQUEST_URI']); // on récupére l'url
		if(isset($passwd[1])) { // si il y a un truc en plus
			$passwd = explode('_', $passwd[1]);
			if(isset($passwd[0]) AND $passwd[0] == "resetpasswd") { // si c'est pour reset le password
				if(!empty($passwd[1])) {
					$this->loadModel('Lostpassword');
					$search = $this->Lostpassword->find('first', array('conditions' => array('key' => $passwd[1]))); // on cherche la key de reset password
					if(!empty($search)) { // si elle existe
						if(strtotime(date('Y-m-d H:i:s', strtotime($search['Lostpassword']['created'])).' +1 hour') >= time()) { // si le lien ne date pas de plus d'1 heure
							$resetpsswd['email'] = $search['Lostpassword']['email'];
							$resetpsswd['key'] = $search['Lostpassword']['key'];
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
		$title_for_layout = $this->Lang->get('GLOBAL__HOME');
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
		$search_news = $this->News->find('all', array('recursive' => 1, 'limit' => '6', 'order' => 'id desc', 'conditions' => array('published' => 1))); // on cherche les 3 dernières news (les plus veille)

		// je cherche toutes les news que l'utilisateur connecté a aimé
		foreach ($search_news as $key => $model) {
			if($this->isConnected) {
				foreach ($model['Like'] as $k => $value) {
					foreach ($value as $column => $v) {
						if($this->User->getKey('id') == $v) {
							$search_news[$key]['News']['liked'] = true;
						}
					}
				}
			}
			if(!isset($search_news[$key]['News']['liked'])) {
				$search_news[$key]['News']['liked'] = false;
			}

			$search_news[$key]['News']['count_comments'] = count($search_news[$key]['Comment']);
			$search_news[$key]['News']['count_likes'] = count($search_news[$key]['Like']);
		}

		$can_like = ($this->Permissions->can('LIKE_NEWS')) ? true : false;

		$this->set(compact('search_news', 'can_like')); // on envoie les données à la vue

		//récupération des slides
		$this->loadModel('Slider');
		$search_slider = $this->Slider->find('all');
		$this->set(compact('search_slider'));

		// Fin
		$this->render('home');
	}

	public function robots() {
		$this->autoRender = false;
		echo file_get_contents(ROOT.DS.'robots.txt');
	}

	public function index($slug = false) {
		if($slug != false) {
			$this->loadModel('Page');
			$search = $this->Page->find('all', array('conditions' => array('slug' => $slug)));
			if(!empty($search)) {
				$this->layout = $this->Configuration->getKey('layout');
				$page = $search[0]['Page'];

				// Parser variables

				$page['author'] = $this->User->getFromUser('pseudo', $page['user_id']);


				$page['content'] = str_replace('{username}', $this->User->getKey('pseudo'), $page['content']);

				// Parser les conditions

				$count = mb_substr_count($page['content'], '{%') / 2; // on regarde combien de fois il y a une condition (divise par 2 car {% endif %})

				$i = 0;
				while ($i < $count) { // on fais une boucle pour les conditions
					$i++;

					$start = explode('{% if(', $page['content']); // on récupère le contenu de la condition
					$content = explode(') %}', $start[1]);
					$end = explode('{% endif %}', $content[1]); // et ce qu'on doit afficher pour condition

					$content_if = $content[0];

					ob_start();
					if($this->isConnected) {
						$connected = 1;
					} else {
						$connected = 0;
					}
					if($this->Server->online()) {
						$server_online = 1;
					} else {
						$server_online = 0;
					}
					$content_if = str_replace('{isConnected}', $connected, $content_if);
					$content_if = str_replace('{isServerOnline}', $server_online, $content_if);

					if(explode(' == ', $content_if)) {

						$content_if = explode(' == ', $content_if);

						if($content_if[0] == $content_if[1]) { // si la condition s'effectue
							echo $end[0];
						}

					} else {

						if($content_if) { // si la condition s'effectue
							echo $end[0];
						}

					}
					$if_result = ob_get_clean();
					$page['content'] = str_replace('{% if('.$content[0].') %}'.$end[0].'{% endif %}', $if_result, $page['content']);
				}
				//

				$this->set(compact('page'));
				$this->set('title_for_layout', $page['title']);
			} else {
				throw new NotFoundException();
			}
		} else {
			throw new NotFoundException();
		}
	}

	public function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {

			$this->set('title_for_layout',$this->Lang->get('PAGE__LIST'));
			$this->layout = 'admin';
			$this->loadModel('Page');
			$pages = $this->Page->find('all');
			foreach ($pages as $pageid => $page) {
				$pages[$pageid]['Page']['author'] = $this->User->getFromUser('pseudo', $page['Page']['user_id']);
			}
			$this->set(compact('pages'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {

			$this->set('title_for_layout',$this->Lang->get('PAGE__ADD'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_ajax() {
		$this->autoRender = false;
		$this->response->type('json');
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			if($this->request->is('post')) {
				if(!empty($this->request->data['title']) AND !empty($this->request->data['slug']) AND !empty($this->request->data['content'])) {
					$this->loadModel('Page');
					$this->loadModel('User');
					$this->Page->read(null, null);
					$this->Page->set(array(
						'title' => $this->request->data['title'],
						'content' => $this->request->data['content'],
						'user_id' => $this->User->getKey('id'),
						'slug' => $this->request->data['slug'],
						'updated' => date('Y-m-d H:i:s'),
					));
					$this->Page->save();
					$this->History->set('ADD_PAGE', 'page');
					$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('PAGE__ADD_SUCCESS'))));
					$this->Session->setFlash($this->Lang->get('PAGE__ADD_SUCCESS'), 'default.success');
				} else {
					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
				}
			} else {
				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_delete($id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			if($id != false) {

				$this->loadModel('Page');
				if($this->Page->delete($id)) {
					$this->History->set('DELETE_PAGE', 'page');
					$this->Session->setFlash($this->Lang->get('PAGE__DELETE_SUCCESS'), 'default.success');
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			if($id != false) {

				$this->set('title_for_layout',$this->Lang->get('PAGE__EDIT'));
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
		$this->autoRender = false;
		$this->response->type('json');
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
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
					$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('PAGE__EDIT_SUCCESS'))));
					$this->Session->setFlash($this->Lang->get('PAGE__EDIT_SUCCESS'), 'default.success');
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
}
