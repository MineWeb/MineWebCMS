<?php

class ThemeController extends AppController
{

    function admin_index()
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get('THEME__LIST'));
        $this->layout = 'admin';

        $this->set('themesAvailable', $this->Theme->getThemesOnAPI(true, true));
        $this->set('themesInstalled', $this->Theme->getThemesInstalled());
    }

    function admin_enable($slug = false)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();

        $this->Configuration->setKey('theme', $slug);
        $this->History->set('SET_THEME', 'theme');
        $this->Session->setFlash($this->Lang->get('THEME__ENABLED_SUCCESS'), 'default.success');
        $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
    }

    function admin_delete($slug = false)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();

        if ($this->Configuration->getKey('theme') == $slug) { // active theme
            $this->Session->setFlash($this->Lang->get('THEME__CANT_DELETE_IF_ACTIVE'), 'default.error');
            $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
        }

        clearDir(ROOT . '/app/View/Themed/' . $slug);
        $this->History->set('DELETE_THEME', 'theme');
        $this->Session->setFlash($this->Lang->get('THEME__DELETE_SUCCESS'), 'default.success');
        $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
    }

    function admin_install($slug = false)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        // install
        $error = $this->Theme->install($slug);

        if ($error !== true) {
            $this->Session->setFlash($this->Lang->get($error), 'default.error');
            return $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
        }

        $this->History->set('INSTALL_THEME', 'theme');
        $this->Session->setFlash($this->Lang->get('THEME__INSTALL_SUCCESS'), 'default.success');
        $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
    }

    function admin_update($slug)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        // install
        $error = $this->Theme->install($slug, true);
        if ($error !== true) {
            $this->Session->setFlash($this->Lang->get($error), 'default.error');
            return $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
        }

        $this->History->set('UPDATE_THEME', 'theme');
        $this->Session->setFlash($this->Lang->get('THEME__UPDATE_SUCCESS'), 'default.success');
        $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
    }

    function admin_custom($slug = false)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        // config
        $this->set('title_for_layout', $this->Lang->get('THEME__CUSTOMIZATION'));
        $this->layout = 'admin';
        list($theme_name, $config) = $this->Theme->getCustomData($slug);
        $this->set(compact('config', 'theme_name'));

        if ($this->request->is('post')) {
            if ($this->Theme->processCustomData($slug, $this->request)) // success save
                $this->Session->setFlash($this->Lang->get('THEME__CUSTOMIZATION_SUCCESS'), 'default.success');
            return $this->redirect(array('controller' => 'theme', 'action' => 'custom', 'admin' => true, $slug));
        }

        if ($slug != "default") // custom theme
            $this->render(DS . 'Themed' . DS . $slug . DS . 'Config' . DS . 'view');
    }

    public function admin_custom_files($slug)
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        $this->layout = 'admin';
        // utils
        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');
        // config
        if ($slug == "default")
            $CSSfolder = ROOT . DS . 'app' . DS . 'webroot' . DS . 'css';
        else
            $CSSfolder = ROOT . DS . 'app' . DS . 'View' . DS . 'Themed' . DS . $slug . DS . 'webroot' . DS . 'css';
        $dir = new Folder($CSSfolder);
        // each files
        $files = $dir->findRecursive('.*\.css');
        foreach ($files as $path) {
            $file = new File($path);
            $basename = substr($path, strlen($CSSfolder));

            $css_files[] = array(
                'basename' => $basename,
                'name' => $file->name
            );
        }

        $this->set(compact('slug', 'css_files'));
    }

    public function admin_get_custom_file($slug)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        // config
        $file = func_get_args();
        unset($file[0]);
        $file = implode(DS, $file);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if ($slug == "default")
            $CSSfolder = ROOT . DS . 'app' . DS . 'webroot' . DS . 'css';
        else
            $CSSfolder = ROOT . DS . 'app' . DS . 'View' . DS . 'Themed' . DS . $slug . DS . 'webroot' . DS . 'css';

        if (!file_exists($CSSfolder . DS . $file) || $ext != 'css')
            throw new NotFoundException();

        $get = @file_get_contents($CSSfolder . DS . $file);
        $this->response->body($get);
    }

    public function admin_save_custom_file($slug)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_THEMES'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        // config
        $file = $this->request->data['file'];
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $content = $this->request->data['content'];
        if ($slug == "default")
            $CSSfolder = ROOT . DS . 'app' . DS . 'webroot' . DS . 'css';
        else
            $CSSfolder = ROOT . DS . 'app' . DS . 'View' . DS . 'Themed' . DS . $slug . DS . 'webroot' . DS . 'css';

        if (!file_exists($CSSfolder . DS . $file) || $ext != 'css')
            throw new NotFoundException();

        @file_put_contents($CSSfolder . DS . $file, $content);
        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('THEME__CUSTOM_FILES_FILE_CONTENT_SAVE_SUCCESS'))));
    }

}
