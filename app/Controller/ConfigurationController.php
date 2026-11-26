<?php

class ConfigurationController extends AppController
{

    public $components = ['Session', 'RequestHandler', 'Util'];

    public function admin_index()
    {
        if ($this->isConnected and $this->Permissions->can('MANAGE_CONFIGURATION')) {
            $this->layout = "admin";

            $isAdmin = $this->User->isAdmin();
            $generalFields = array_flip(['name', 'money_name_singular', 'money_name_plural', 'lang', 'member_page_type']);
            $this->set('isAdmin', $isAdmin);

            if ($this->request->is('post')) {
                $data = $this->request->data;
                if ($isAdmin) {
                    if (isset($data['xss']['end_layout_code']))
                        $data['end_layout_code'] = $data['xss']['end_layout_code'];
                    unset($data['id'], $data['version'], $data['Configuration'], $data['xss'], $data['_Token']);
                    if (isset($data['banner_server']))
                        $data['banner_server'] = serialize($data['banner_server']);

                    if (isset($data['passwords_hash'])) {
                        $previousHash = $this->Util->getPasswordHashType() ?: 'sha256';
                        if (($data['passwords_hash'] ?: 'sha256') === $previousHash) {
                            unset($data['passwords_hash']);
                        } else {
                            $oldType = $this->User->getDataSource()->value($previousHash, 'string');
                            $storedType = "CASE WHEN SUBSTR(password, 1, 4) IN ('$2a$', '$2x$', '$2y$') THEN 'blowfish' ELSE $oldType END";
                            if (!$this->User->updateAll(
                                ['password_hash' => $storedType],
                                ['OR' => [['password_hash' => null], ['password_hash' => '']]]
                            )) {
                                throw new InternalErrorException();
                            }
                        }
                    }
                } else {
                    $data = array_intersect_key($data, $generalFields);
                    if (!$data || array_filter($data, 'is_string') !== $data)
                        throw new BadRequestException();
                    if (isset($data['lang']) && !isset($this->Lang->languages[$data['lang']]))
                        throw new BadRequestException();
                    if (isset($data['member_page_type']) && !in_array($data['member_page_type'], ['0', '1'], true))
                        throw new BadRequestException();
                    $data = array_map('strip_tags', $data);
                }

                $this->Configuration->read(null, 1);
                $this->Configuration->save($data, true, array_keys($data));

                $this->History->set('EDIT_CONFIGURATION', 'configuration');

                $this->Configuration->cacheQueries = false; //On désactive le cache
                $this->Configuration->dataConfig = null;
                $this->Lang->lang = $this->Lang->getLang(); // on refresh les messages

                $this->Session->setFlash($this->Lang->get('CONFIG__EDIT_SUCCESS'), 'default.success');
            }

            $config = $this->Configuration->getAll();
            if (!$isAdmin)
                $config = array_intersect_key($config, $generalFields);

            $this->Configuration->cacheQueries = true; //On le réactive

            $config['lang'] = $this->Lang->getLang('config')['path'];

            foreach ($this->Lang->languages as $key => $value) {
                $config['languages_available'][$key] = $value['name'];
            }

            $this->set('config', $config);

            $this->set('shopIsInstalled', $this->EyPlugin->isInstalled('eywek.shop'));

        } else {
            $this->redirect('/');
        }
    }

    public function admin_editLang()
    {
        if ($this->isConnected and $this->User->isAdmin()) {

            $this->layout = 'admin';

            if ($this->request->is('post')) {

                if (stripos($this->request->data['GLOBAL__FOOTER'], '<a href="http://mineweb.org">mineweb.org</a>') === false) {
                    $this->Session->setFlash($this->Lang->get('CONFIG__ERROR_SAVE_LANG'), 'default.error');
                } else {

                    $this->Lang->setAll($this->request->data);

                    $this->History->set('EDIT_LANG', 'lang');

                    $this->Session->setFlash($this->Lang->get('CONFIG__EDIT_LANG_SUCCESS'), 'default.success');

                }
            }

            $this->Lang->lang = $this->Lang->getLang(); // on refresh les messages

            $this->set('messages', $this->Lang->lang['messages']);
            $this->set('title_for_layout', $this->Lang->get('CONFIG__LANG_LABEL'));

        } else {
            $this->redirect('/');
        }
    }

}
