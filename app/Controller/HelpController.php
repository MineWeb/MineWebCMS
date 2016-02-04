<?php

class HelpController extends AppController {

  public function admin_index() {
    if($this->isConnected AND $this->User->isAdmin()) {
      $this->layout = 'admin';
      $this->set('title_for_layout', $this->Lang->get('HELP__TITLE'));
    } else {
      throw new ForbiddenException();
    }
  }

  public function admin_getQuestionsAndAnswers() {
    if($this->isConnected AND $this->User->isAdmin()) {
      $this->autoRender = false;
      $this->response->type('json');

      if($this->request->is('ajax')) {

        //$lang = 'fr_FR';
        //$get = @file_get_contents('http://mineweb.org/api/v1/getFAQ/'.$lang);
        if(/*$get && json_decode($get)*/1==1) {

          //echo $data;
          $data = array(
            array('id' => 1, 'question' => 'Suis-je intelligent ?', 'answer' => 'Non, tu es totalement débile jeune garçon.'),
            array('id' => 2, 'question' => 'Suis-je intelligent (2) ?', 'answer' => 'Oui, vraiment très intelligent jeune garçon.'),
            array('id' => 3, 'question' => 'Suis-je intelligent (3) ?', 'answer' => 'Peut-être, je ne sais pas trop jeune garçon.')
          );
          echo json_encode($data);

        }

      } else {
        echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
      }

    } else {
      throw new ForbiddenException();
    }
  }

  public function admin_submitTicket() {
    if($this->isConnected AND $this->User->isAdmin()) {
        $this->autoRender = false;

        if($this->request->is('ajax')) {

          if(!empty($this->request->data['title']) && !empty($this->request->data['content'])) {

            if($this->sendTicketToAPI($this->request->data)) {
              echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('HELP__TICKET_ADD_SUCCESS')));
            } else {
              echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__INTERNAL_ERROR')));
            }

          } else {
            echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
          }

        } else {
          echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
        }

    } else {
      throw new ForbiddenException();
    }
  }

}
