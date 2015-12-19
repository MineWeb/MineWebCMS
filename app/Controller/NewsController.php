<?php

class NewsController extends AppController {

	public $components = array('Session');

	function blog() {
		$this->loadModel('News');
		$search_news = $this->News->find('all', array('order' => array('id DESC'), 'conditions' => array('published' => 1)));

		// Je met tout les commentaires à chaque news
		$this->loadModel('Comment');
		$comments = $this->Comment->find('all');

		foreach ($comments as $key => $value) {

			foreach ($search_news as $k => $v) {

				if($value['Comment']['news_id'] == $v['News']['id']) {

					$search_news[$k]['News']['comment'][] = $value['Comment'];

					break;
				}

			}

		}

		// Je met tout les likes à chaque news
		$this->loadModel('Like');
		$comments = $this->Like->find('all');

		foreach ($comments as $key => $value) {

			foreach ($search_news as $k => $v) {

				if($value['Like']['news_id'] == $v['News']['id']) {

					$search_news[$k]['News']['likes'][] = $value['Like'];

					break;
				}

			}

		}

		// je cherche toutes les news que l'utilisateur connecté a aimé
		if($this->isConnected) {
			$user_likes = $this->Like->find('all', array('conditions' => array('author' => $this->User->getKey('pseudo'))));
			if(!empty($user_likes)) {
				$i = 0;
				foreach ($user_likes as $key => $value) {
					$i++;
					foreach ($search_news as $k => $v) {
						if($value['Like']['news_id'] == $v['News']['id']) {
								$search_news[$k]['News']['liked'] = true;
						} elseif(count($user_likes) == $i && !isset($search_news[$k]['News']['liked'])) {
							$search_news[$k]['News']['liked'] = false; // si c'est le dernier like et que y'a toujours pas de like sur cette news on dis false
						}
					}
				}
			} else {
				foreach ($search_news as $k => $v) {
					$search_news[$k]['News']['liked'] = false;
				}
			}
		} else {
			foreach ($search_news as $k => $v) {
				$search_news[$k]['News']['liked'] = false;
			}
		}

		$can_like = ($this->Permissions->can('LIKE_NEWS')) ? true : false;

		$this->set(compact('search_news', 'can_like'));
	}

	function index($slug) {
		$this->layout= $this->Configuration->get_layout();

		if(isset($slug)) { // si le slug est présent
			$search_news = $this->News->find('all', array('conditions' => array('slug' => $slug)));
			if($search_news) { // si le slug existe
				// récupération du titre la news ...
				$id = $search_news['0']['News']['id'];
				$title = $search_news['0']['News']['title'];
				$content = $search_news['0']['News']['content'];
				$author = $search_news['0']['News']['author'];
				$created = $search_news['0']['News']['created'];
				$updated = $search_news['0']['News']['updated'];
				$comments = $search_news['0']['News']['comments'];
				$like = $search_news['0']['News']['like'];
				$img = $search_news['0']['News']['img'];

				$this->set('title_for_layout',$title);

				// on cherche les commentaires
				$this->loadModel('Comment');
				$search_comments = $this->Comment->find('all', array('conditions' => array('news_id' => $id), 'order' => 'id desc')); $this->set(compact('search_comments'));

				if($this->isConnected) {
					$this->loadModel('Like');
					$likes = $this->Like->find('all', array('conditions' => array('author' => $this->Connect->get_pseudo())));
					if(!empty($likes)) {
						foreach ($likes as $key => $value) {
							$likes_list[] = $value['Like']['news_id'];
						}
						$likes = $likes_list;
					} else {
						$likes = array('-1');
					}
				}

				// on chercher les 4 dernières news pour la sidebar
				$search_news = $this->News->find('all', array('limit' => '4', 'order' => 'id desc', 'conditions' => array('published' => 1))); // on cherche les 3 dernières news (les plus veille)
				$this->set(compact('search_news', 'likes', 'title', 'content', 'author', 'created', 'updated', 'id', 'comments', 'like', 'likes', 'img')); // on envoie les données à la vue
			} else {
				throw new NotFoundException();
			}
		} else {
			throw new NotFoundException();
		}
	}

	function add_comment() {
		$this->autoRender = false;
		if($this->request->is('post')) {
			if($this->Permissions->can('COMMENT_NEWS')) {
				if(!empty($this->request->data['content']) && !empty($this->request->data['news_id']) && !empty($this->request->data['author'])) {
					$this->loadModel('Comment');
					$this->request->data['content'] = $this->request->data['content'];
					$this->Comment->save($this->request->data);
					$comments = $this->News->find('all', array('conditions' => array('id' => $this->request->data['news_id'])));
					$comments = $comments['0']['News']['comments'];
					$comments = $comments + 1;
					$this->News->read(null, $this->request->data['news_id']);
					$this->News->set(array('comments' => $comments));
					$this->News->save();
					echo json_encode(array('statut' => true, 'msg' => 'success'));
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NEED_CONNECT')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('PAGE_BAD_EXECUTED')));
		}
	}

	function like() {
		$this->autoRender = false;
		if($this->request->is('post')) {
			if($this->Permissions->can('LIKE_NEWS')) {
				$this->loadModel('Like');
				$already = $this->Like->find('all', array('conditions' => array('news_id' => $this->request->data['id'], 'author' => $this->User->getKey('pseudo'))));
				if(empty($already)) {
					$this->loadModel('News');
					$like = $this->News->find('all', array('conditions' => array('id' => $this->request->data['id'])));
					$like = $like['0']['News']['like'];
					$like = $like + 1;
					$this->News->read(null, $this->request->data['id']);
					$this->News->set(array('like' => $like));
					$this->News->save();

					$this->Like->read(null, null);
					$this->Like->set(array('news_id' => $this->request->data['id'], 'author' => $this->User->getKey('pseudo')));
					$this->Like->save();
				}
			}
		}
	}

	function dislike() {
		$this->autoRender = false;
		if($this->request->is('post')) {
			if($this->Permissions->can('LIKE_NEWS')) {
				$this->loadModel('Like');
				$already = $this->Like->find('all', array('conditions' => array('news_id' => $this->request->data['id'], 'author' => $this->User->getKey('pseudo'))));
				if(!empty($already)) {
					$this->loadModel('News');
					$like = $this->News->find('all', array('conditions' => array('id' => $this->request->data['id'])));
					$like = $like['0']['News']['like'];
					$like = $like - 1;
					$this->News->read(null, $this->request->data['id']);
					$this->News->set(array('like' => $like));
					$this->News->save();

					$this->Like->deleteAll(array('news_id' => $this->request->data['id'], 'author' => $this->User->getKey('pseudo')));
				}
			}
		}
	}


	function ajax_comment_delete() {
        $this->layout = null;
        $this->loadModel('Comment');
        $search = $this->Comment->find('all', array('conditions' => array('id' => $this->request->data['id'])));
        if($this->Permissions->can('DELETE_COMMENT') OR $this->Permissions->can('DELETE_HIS_COMMENT') AND $this->Connect->get_pseudo() == $search[0]['Comment']['author']) {
            if($this->request->is('post')) {
            	$this->loadModel('News');
            	$news = $this->News->find('first', array('conditions' => $search[0]['Comment']['news_id']));
            	$this->News->read(null, $search[0]['Comment']['news_id']);
            	$this->News->set(array('comments' => ($news['News']['comments'] - 1)));
            	$this->News->save();
                $this->Comment->delete($this->request->data['id']);
                echo 'true';
            } else {
                echo 'NOT_POST';
            }
        } else {
            echo 'NOT_ADMIN';
        }
    }

	function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NEWS')) {

			$this->set('title_for_layout',$this->Lang->get('NEWS_LIST'));
			$this->layout = 'admin';
			$this->loadModel('News');
			$view_news = $this->News->find('all');
			$this->set(compact('view_news'));
		} else {
			$this->redirect('/');
		}
	}

	function admin_delete($id = false) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NEWS')) {
			if($id != false) {

				$this->loadModel('News');
				if($this->News->delete($id)) {
					$this->loadModel('Like');
					$this->loadModel('Comment');
					$this->Like->deleteAll(array('news_id' => $id));
					$this->Comment->deleteAll(array('news_id' => $id));
					$this->History->set('DELETE_NEWS', 'news');
					$this->Session->setFlash($this->Lang->get('NEWS_DELETE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'news', 'action' => 'index', 'admin' => true));
				} else {
					$this->redirect(array('controller' => 'news', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'news', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_add() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NEWS')) {
			$this->layout = 'admin';

			$this->set('title_for_layout', $this->Lang->get('ADD_NEWS'));
		} else {
			$this->redirect('/');
		}
	}

	function admin_add_ajax() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NEWS')) {
			$this->layout = null;

			if($this->request->is('post')) {
				if(!empty($this->request->data['title']) AND !empty($this->request->data['content']) AND !empty($this->request->data['slug'])) {
					$this->loadModel('News');
					$this->News->read(null, null);
					$this->News->set(array(
						'title' => $this->request->data['title'],
						'content' => $this->request->data['content'],
						'author' => $this->Connect->get_pseudo(),
						'updated' => date('Y-m-d H:i:s'),
						'comments' => 0,
						'likes' => 0,
						'img' => 0,
						'slug' => $this->request->data['slug'],
						'published' => $this->request->data['published']
					));
					$this->News->save();
					$this->History->set('ADD_NEWS', 'news');
					echo $this->Lang->get('SUCCESS_NEWS_ADD').'|true';
					$this->Session->setFlash($this->Lang->get('SUCCESS_NEWS_ADD'), 'default.success');
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_edit($id = false) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NEWS')) {
			$this->layout = 'admin';

			if($id != false) {
				$this->loadModel('News');
				$search = $this->News->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$news = $search['0']['News'];
					$this->set(compact('news'));
				} else {
					$this->Session->setFlash($this->Lang->get('UKNOWN_ID'), 'default.error');
					$this->redirect(array('controller' => 'news', 'action' => 'admin_index', 'admin' => 'true'));
				}
			} else {
				$this->redirect(array('controller' => 'news', 'action' => 'admin_index', 'admin' => 'true'));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_edit_ajax() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NEWS')) {
			$this->layout = null;

			if($this->request->is('post')) {

				if(!empty($this->request->data['title']) AND !empty($this->request->data['content']) AND !empty($this->request->data['id']) AND !empty($this->request->data['slug'])) {
					$this->loadModel('News');
					$this->News->read(null, $this->request->data['id']);
					$this->News->set(array(
						'title' => $this->request->data['title'],
						'content' => $this->request->data['content'],
						'updated' => date('Y-m-d H:i:s'),
						'slug' => $this->request->data['slug'],
						'published' => $this->request->data['published']
					));
					$this->News->save();
					$this->History->set('EDIT_NEWS', 'news');
					echo $this->Lang->get('SUCCESS_NEWS_EDIT').'|true';
					$this->Session->setFlash($this->Lang->get('SUCCESS_NEWS_EDIT'), 'default.success');
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
		} else {
			$this->redirect('/');
		}

	}

}
