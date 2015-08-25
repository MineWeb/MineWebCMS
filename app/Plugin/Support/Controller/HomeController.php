<?php
class HomeController extends AppController {

	public $components = array('Connect', 'Configuration');

    public function index() {
        $this->set('title_for_layout',"Support");
    	$this->layout = $this->Configuration->get_layout();
        $this->loadModel('Ticket');
    	$tickets = $this->Ticket->find('all', array('order' => array('id' => 'desc')));
    	$this->set(compact('tickets'));
        $this->loadModel('ReplyTicket');
        $reply_tickets = $this->ReplyTicket->find('all');
        $this->set(compact('reply_tickets'));
    	$nbr_tickets = $this->Ticket->find('count'); $this->set(compact('nbr_tickets'));
    	$nbr_tickets_resolved = $this->Ticket->find('count', array('conditions' => array('state' => 1))); $this->set(compact('nbr_tickets_resolved'));
    	$nbr_tickets_unresolved = $this->Ticket->find('count', array('conditions' => array('state' => 0))); $this->set(compact('nbr_tickets_unresolved'));
    }

    public function ajax_delete() {
    	$this->layout = null;
    	$this->loadModel('Ticket');
        $pseudo = $this->Ticket->find('all', array('conditions' => array('id' => $this->request->data['id'])));
        $pseudo = $pseudo['0']['Ticket']['author'];
        if($this->Connect->connect() AND $this->Connect->if_admin() OR $this->Connect->connect() AND $this->Connect->get_pseudo() == $pseudo AND $this->Permissions->can('DELETE_HIS_TICKET') OR $this->Permissions->can('DELETE_ALL_TICKETS')) {
    		$this->loadModel('Ticket');
    		if($this->request->is('post')) {
    			$this->Ticket->delete($this->request->data['id']);
                $this->loadModel('ReplyTicket');
                $this->ReplyTicket->deleteAll(array('ticket_id' => $this->request->data['id']));
    			echo 'true';
    		} else {
    			echo 'NOT_POST';
    		}
    	} else {
    		echo 'NOT_ADMIN_OR_CREATOR';
    	}
    }

    public function ajax_reply_delete() {
        $this->layout = null;
        if($this->Connect->connect() AND $this->Connect->if_admin()) {
            $this->loadModel('ReplyTicket');
            if($this->request->is('post')) {
                $this->ReplyTicket->delete($this->request->data['id']);
                echo 'true';
            } else {
                echo 'NOT_POST';
            }
        } else {
            echo 'NOT_ADMIN';
        }
    }

    public function ajax_resolved() {
    	$this->layout = null;
    		if($this->request->is('post')) {
    			$this->loadModel('Ticket');
		    	$pseudo = $this->Ticket->find('all', array('conditions' => array('id' => $this->request->data['id'])));
		    	$pseudo = $pseudo['0']['Ticket']['author'];
		    	if($this->Connect->connect() AND $this->Connect->if_admin() OR $this->Connect->connect() AND $this->Connect->get_pseudo() == $pseudo AND $this->Permissions->can('RESOLVE_HIS_TICKET') OR $this->Permissions->can('RESOLVE_ALL_TICKETS')) {
					$this->Ticket->read(null, $this->request->data['id']);
					$this->Ticket->set(array('state' => 1));
					$this->Ticket->save();
					echo 'true';
		    	} else {
		    		echo 'NOT_PERMISSION';
		    	}
    	} else {
			echo 'NOT_POST';
		}
    }

    public function ajax_reply() {
        $this->layout = null;
          
            if($this->request->is('post')) {
                if(!empty($this->request->data['message']) && !empty($this->request->data['id'])) {
                    $this->loadModel('Ticket');
                    $pseudo = $this->Ticket->find('all', array('conditions' => array('id' => $this->request->data['id'])));
                    $pseudo = $pseudo['0']['Ticket']['author'];
                    if($this->Connect->connect() AND $this->Connect->if_admin() OR $this->Connect->connect() AND $this->Connect->get_pseudo() == $pseudo AND $this->Permissions->can('REPLY_TO_HIS_TICKETS') OR $this->Permissions->can('REPLY_TO_ALL_TICKETS')) {
                        $this->loadModel('ReplyTicket');
                        $this->ReplyTicket->create();
                        $this->ReplyTicket->set(array('ticket_id' => $this->request->data['id'], 'reply' => $this->request->data['message'], 'author' => $this->Connect->get_pseudo()));
                        $this->ReplyTicket->save();
                        echo 'true';
                    } else {
                        echo 'NOT_PERMISSION';
                    }
                } else {
                    echo '1';
                }
        } else {
            echo 'NOT_POST';
        }
    }

    public function ajax_post() {
        $this->layout = null;
          
        if($this->request->is('post')) {
            if(!empty($this->request->data['title']) AND !empty($this->request->data['content'])) {
                if($this->Connect->connect() AND $this->Permissions->can('POST_TICKET')) {
                    $this->loadModel('Ticket');
                    $this->request->data['author'] = $this->Connect->get_pseudo();
                    $this->request->data['private'] = $this->request->data['ticket_private'];
                    $this->request->data['title'] == before_display($this->request->data['title']);
                    $this->request->data['content'] == before_display($this->request->data['content']);
                    $this->Ticket->read(null, null);
                    $this->Ticket->set($this->request->data);
                    $this->Ticket->save();
                    $id = $this->Ticket->find('all', array('conditions' => array('title' => $this->request->data['title'], 'content' => $this->request->data['content'], 'author' => $this->request->data['author'])));
                    $id = $id['0']['Ticket']['id'];
                    echo $id;
                } else {
                    echo 'NOT_PERMISSION';
                }
            } else {
                echo $this->Lang->get('COMPLETE_ALL_FIELDS');
            }
        } else {
            echo 'NOT_POST';
        }
    }
}