<?php 

class LangController extends AppController {

	public $components = array('Session', 'History', 'Connect');

	function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('LANG'));
			$this->layout = 'admin';

			if($this->request->is('post')) {
				$this->request->data['COPYRIGHT'] = "Tous droit réservés - 2015 <span class=\"pull-right\">Propulsé par <a href=\"http://mineweb.org\">mineweb.org</a></span>";
				$this->request->data['FOOTER_ADMIN'] = "<p>Développé par <a href=\"http://eywek.fr\">Eywek</a> & Designé par <a href=\"http://thisismac.fr\">Mac'</a>.</p><p>Site proposé par <a href=\"http://mineweb.org\">MineWeb</a> - Tous droit réservés.</p><p>Ce CMS doit être utilisé uniquement par une personne ayant acheté la licence.</p><ul><li><a href=\"http://mineweb.org\">MineWeb</a></li><li class=\"muted\">·</li><li><a href=\"http://eywek.fr\">Eywek</a></li><li class=\"muted\">·</li><li><a href=\"http://thisismac.fr\">Mac'</a></li></ul>";
				$this->Lang->setall($this->request->data);

				$this->History->set('EDIT_LANG', 'lang');
				 
				$this->Session->setFlash($this->Lang->get('EDIT_LANG_SUCCESS'), 'default.success');
			}

		} else {
			$this->redirect('/');
		}
	}

}