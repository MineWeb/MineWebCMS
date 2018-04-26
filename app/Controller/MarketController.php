<?php

class MarketController extends AppController
{

    function admin_index()
    {
		if ($this->isConnected AND $this->User->isAdmin()) {
        $this->set('title_for_layout', $this->Lang->get('GLOBAL__MARKET'));
        $this->layout = 'admin';
		} else {
            $this->redirect('/');
        }
    }
}
