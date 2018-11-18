<?php

class UpdateController extends AppController
{
  public $components = array('Session', 'Update');

  public function admin_index() {
    if (!$this->User->isAdmin()) throw new BadRequestException();
    $this->set('title_for_layout', $this->Lang->get('GLOBAL__UPDATE'));
    $this->layout = 'admin';
  }

  public function admin_clear_cache() {
    if (!$this->User->isAdmin()) throw new BadRequestException();
    $this->autoRender = false;

    App::uses('Folder', 'Utility');
    $folder = new Folder(ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache');
    if (!empty($folder->path)) {
      $folder->delete();
    }

    $this->redirect(array('action' => 'index'));
  }

  public function admin_update($componentUpdated = '0') {
    if (!$this->User->isAdmin()) throw new BadRequestException();
    $this->response->type('json');
    $this->autoRender = false;

    $componentUpdated = ($componentUpdated) ? true : false;
    if (!$this->Update->updateCMS($componentUpdated))
      return $this->response->body(json_encode(array('statut' => 'error', 'msg' => $this->Lang->get('UPDATE__FAILED'))));
    if ($componentUpdated == '0')
      return $this->response->body(json_encode(array('statut' => 'continue', 'msg' => '')));
    return $this->response->body(json_encode(array('statut' => 'success', 'msg' => $this->Lang->get('UPDATE__SUCCESS'))));
  }

  public function admin_check() {
    unlink(ROOT . '/config/update');
    $this->redirect(array('action' => 'index'));
  }
}
