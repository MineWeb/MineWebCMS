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
		$search_news = $this->News->find('all', array('limit' => '6', 'order' => 'id desc', 'conditions' => array('published' => 1))); // on cherche les 3 dernières news (les plus veille)
		$this->set(compact('search_news')); // on envoie les données à la vue

		// je cherche toutes les news que l'utilisateur connecté a aimé
		if($this->isConnected) {
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

	public function robots() {
		$this->autoRender = false;
		echo file_get_contents(ROOT.DS.'robots.txt');
	}

	public function debug($key = false, $args = false) {
		$secure = file_get_contents(ROOT.'/config/secure');
		$secure = json_decode($secure, true);
		if($key == $secure['key']) {
			if(!$args) {
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

				$api_dev_key 			= '09ca1757ce4d128dc7601536a60f234d'; // your api_developer_key
				$api_paste_code 		= file_get_contents(ROOT.'/app/tmp/logs/error.log'); // your paste text
				$api_paste_private 		= '1'; // 0=public 1=unlisted 2=private
				$api_paste_name			= 'error.log'; // name or title of your paste
				$api_paste_expire_date 	= '10M';
				$api_paste_format 		= 'php';
				$api_user_key 			= ''; // if an invalid api_user_key or no key is used, the paste will be create as a guest
				$api_paste_name			= urlencode($api_paste_name);
				$api_paste_code			= urlencode($api_paste_code);

				$url 				= 'http://pastebin.com/api/api_post.php';
				$ch 				= curl_init($url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, 'api_option=paste&api_user_key='.$api_user_key.'&api_paste_private='.$api_paste_private.'&api_paste_name='.$api_paste_name.'&api_paste_expire_date='.$api_paste_expire_date.'&api_paste_format='.$api_paste_format.'&api_dev_key='.$api_dev_key.'&api_paste_code='.$api_paste_code.'');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				curl_setopt($ch, CURLOPT_NOBODY, 0);
				$response  			= curl_exec($ch);

				echo '<h2><b>This page is only for debug if you have a problem. This string below must be give to Eywek for debug.</b></h2>';
				echo '<center><div style="width:50%;word-wrap: break-word;">'.base64_encode(json_encode($return)).'</div></center>';
				echo '<center><b><a target="_blank" href="'.$response.'">Pastebin link</a></b></center>';
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

				// Parser variables

				$page['content'] = str_replace('{username}', $this->Connect->get_pseudo(), $page['content']);

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
				$this->redirect('/');
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			 
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			 
			$this->set('title_for_layout',$this->Lang->get('ADD_PAGE'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_ajax() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			 
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			 
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_PAGE')) {
			 
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
























