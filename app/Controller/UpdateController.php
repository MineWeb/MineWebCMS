<?php

class UpdateController extends AppController {

	public $components = array('Session', 'Update');

	public function admin_index() { // ajout d'un commentaire pour git
		if($this->isConnected AND $this->User->isAdmin()) {
			$this->set('title_for_layout',$this->Lang->get('UPDATE'));
			$this->layout = 'admin';

        if(@$dir = opendir(ROOT.'/app/tmp/logs/update/')) {
          while(($file = readdir($dir)) !== false) {
            	if($file != ".." && $file != "." && $file != '.DS_Store' && $file != '__MACOSX' && $file != 'lang') {
            		$files[$file] = filemtime(ROOT.'/app/tmp/logs/update/'.$file);
            	}
        	}

        	arsort($files);
        	$files = array_keys($files);
        	$logs = array_shift($files);
        	$logs = file_get_contents(ROOT.'/app/tmp/logs/update/'.$logs);
        	$logs = json_decode($logs, true);
      } else {
      	$logs = array();
      }
			$this->set(compact('logs'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_update() {
		if($this->isConnected AND $this->User->isAdmin()) {
			$this->autoRender = false;
			if($this->Update->update($this->Update->get_version())) {
				echo $this->Lang->get('UPDATE_SUCCESS').'|true';
			} else {
				echo $this->Lang->get('UPDATE_FAILED').'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_check() {
		unlink(ROOT.'/config/update');
		$this->redirect(array('action' => 'index'));
	}
}
