<?php
@ignore_user_abort(true);
@set_time_limit(0);

/**
* Composant qui gère les plugins
* Leur affichage sur la navbar et leur configuration
**/

class EyPluginComponent extends Object {
  
  function shutdown(&$controller) {
  }

  function beforeRender(&$controller) {
  }
  
  function beforeRedirect() { 
  }

/**
*
* Fonction lancé au lancement du composant
* -> Check si il n'y a pas un plugin à installé
* -> Check si il n'y a pas un plugin à supprimé
*
**/

  function initialize(&$controller) {
    $this->plugins = ClassRegistry::init('plugins'); // le model plugins 

    $plugins_list = $this->getPluginsInFolder(); // on récupére tout les plugins dans le dossier

    if($plugins_list != false) {

      foreach ($plugins_list as $k => $v) { // on les passes touts 1 à 1 

        $search = $this->plugins->find('all', array('conditions' => array('name' => $v))); // je cherche tout les plugins du dossier dans la bdd

        if(empty($search)) { // si un est pas activé dans la bdd

          $config = file_get_contents(ROOT.'/app/Plugin/'.$v.'/config.json');
          $config = json_decode($config, true); // on récupère la config

          $p_tables = file_get_contents(ROOT.'/app/Plugin/'.$v.'/sql_name.txt');
          $p_tables = explode(',', $p_tables); // on récupère les tables qui vont être installés

          $this->plugins->create();
          $this->plugins->set(array('plugin_id' => $config['plugin_id'], 'name' => $v, 'author' => $config['author'], 'version' => $config['version'], 'tables' => serialize($p_tables)));
          $this->plugins->save(); // on save le plugin dans la base de données
          
          $this->addTables($v); // on ajoute les tables nécessaires

          if(file_exists(ROOT.'/app/Plugin/'.$v.'/Controller/Component/MainComponent.php')) { // On fais le onEnable si il existe
            App::uses('MainComponent', 'Plugin/'.$v.'/Controller/Component');
            $this->Main = new MainComponent();
            $this->Main->onEnable();
          } 

          // une fois que on a mis les tables, on s'occupe des permissions
          $this->addPermissions($config); // on ajoute les perms par défaut

        }
      }

      $search = $this->plugins->find('all'); // on les cherche tous

      foreach ($search as $key => $value) { // on rend ca plus clair pour le array_diff
        $array[] = $value['plugins']['name'];
      }

      $bdd = $array;
      $diff = array_diff($bdd, $plugins_list); // on cherche le plugin qui est présent dans la bdd et non pas dans le dossier

      if(!empty($diff)) { // si il y a une différence entre les deux listes

        foreach ($diff as $key => $value) { // on les parcours

          $search = $this->plugins->find('all', array('conditions' => array('name' => $value))); // on cherche le nom

          $tables_plugins = unserialize($search['0']['plugins']['tables']); // pour récupérer les tables installées
          App::import('Model', 'ConnectionManager');
          $con = new ConnectionManager;
          $cn = $con->getDataSource('default');
          foreach ($tables_plugins as $k => $v) {
            if(!empty($v)) {
              $cn->query("DROP TABLE ".$v); // on les supprimes
            }
          }

          $this->plugins->delete($search['0']['plugins']['id']); // je le supprime de la bdd

          @clearDir(ROOT.'/app/Plugin/'.$search['0']['plugins']['name']);
          @clearFolder(ROOT.'/app/tmp/cache/models/');
          @clearFolder(ROOT.'/app/tmp/cache/persistent/'); // et on clear le cache pour éviter les soucis
        }

      }

    }
  }

  function startup(&$controller) {
    $plugins = $this->getPluginsInFolder();
    if($plugins && !empty($plugins)) {
      Configure::write('eyplugins.plugins.list', $plugins);
    } else {
      Configure::write('eyplugins.plugins.list', false);
    }
  }

  private function addTables($plugin_name) {
    $tables_plugins = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/sql.txt');
    if(!empty($tables_plugins)) {
      $tables_plugins = explode('|', $tables_plugins);
      App::import('Model', 'ConnectionManager');
      $con = new ConnectionManager;
      $cn = $con->getDataSource('default');
      foreach ($tables_plugins as $do) {
        if(!empty($do)) {
          $cn->query($do);
        }
      }
    }
  }

  private function getPluginsInFolder() {
    $dir = ROOT.'/app/Plugin';
    $plugins = scandir($dir);
    $plugins = array_delete_value($plugins, '.');
    $plugins = array_delete_value($plugins, '..');
    $plugins = array_delete_value($plugins, '.DS_Store');
    if(!empty($plugins)) {
      foreach ($plugins as $key => $value) {
        $list_plugins[] = $value;
      }
    } else {
      return false;
    }
    if(!empty($plugins)) {
      return $list_plugins;
    }
    return false;
  }

  private function addPermissions($config) {
    $this->Permission = ClassRegistry::init('Permission');
    if(isset($config['permissions']['default']) AND !empty($config['permissions']['default'])) {
      foreach ($config['permissions']['default'] as $key => $value) {
        $where = $this->Permission->find('all', array('conditions' => array('rank' => $key))); // je cherche les perms du rank 
        if(!empty($where)) {
          $addperm = unserialize($where['0']['Permission']['permissions']);
          foreach ($addperm as $k2 => $v2) {
            foreach ($value as $kp => $perm) {
              $addperm[] = $perm; // on ajoute les perms
            }
          }
          $this->Permission->read(null, $where['0']['Permission']['id']);
          $this->Permission->set(array('permissions' => serialize($addperm)));
          $this->Permission->save();
        }
      }
    }
  }

  function delete($id) {
    $this->plugins = ClassRegistry::init('plugins');
    $search = $this->plugins->find('all', array('conditions' => array('id' => $id)));
    $config = file_get_contents(ROOT.'/app/Plugin/'.$search[0]['plugins']['name'].'/config.json');
    $config = json_decode($config, true);
    $tables_plugins = unserialize($search['0']['plugins']['tables']);
    App::import('Model', 'ConnectionManager');
    $con = new ConnectionManager;
    $cn = $con->getDataSource('default');
    foreach ($tables_plugins as $k => $v) {
      if(!empty($v)) {
        $cn->query("DROP TABLE ".$v);
      }
    }
    // une fois que on a mis les tables, on s'occupe des permissions
    $this->Permission = ClassRegistry::init('Permission');
    if(isset($config['permissions']['default']) AND !empty($config['permissions']['default'])) {
      foreach ($config['permissions']['default'] as $key => $value) {
        $where = $this->Permission->find('all', array('conditions' => array('rank' => $key))); // je cherche les perms du rank 
        if(!empty($where)) {
          $addperm = unserialize($where['0']['Permission']['permissions']);
          foreach ($addperm as $k2 => $v2) {
            foreach ($value as $kp => $perm) {
              array_delete_value($addperm, $perm); // on supprime les perms
            }
          }
          $this->Permission->read(null, $where['0']['Permission']['id']);
          $this->Permission->set(array('permissions' => serialize($addperm)));
          $this->Permission->save();
        }
      }
    }
    if($this->plugins->delete($search['0']['plugins']['id'])) {
      if(file_exists(ROOT.'/app/Plugin/'.$search['0']['plugins']['name'].'/Controller/Component/MainComponent.php')) {
        App::uses('MainComponent', 'Plugin/'.$search['0']['plugins']['name'].'/Controller/Component');
        $this->Main = new MainComponent();
        $this->Main->onDisable();
      }
      clearDir(ROOT.'/app/Plugin/'.$search['0']['plugins']['name']);
      clearFolder(ROOT.'/app/tmp/cache/models/');
      clearFolder(ROOT.'/app/tmp/cache/persistent/');
      return true;
    } else {
      return false;
    }
  }

  function enable($id) {
    $this->plugins = ClassRegistry::init('plugins');
    $this->plugins->read(null, $id);
    $this->plugins->set(array('state' => 1));
    if($this->plugins->save()) {
      $search = $this->plugins->find('all', array('conditions' => array('id' => $id)));
      if(file_exists(ROOT.'/app/Plugin/'.$search['0']['plugins']['name'].'/Controller/Component/MainComponent.php')) {
        App::uses('MainComponent', 'Plugin/'.$search['0']['plugins']['name'].'/Controller/Component');
        $this->Main = new MainComponent();
        $this->Main->onEnable();
      }
      return true;
    } else {
      return false;
    }
  }

  function disable($id) {
    $this->plugins = ClassRegistry::init('plugins');
    $this->plugins->read(null, $id);
    $this->plugins->set(array('state' => 0));
    if($this->plugins->save()) {
      $search = $this->plugins->find('all', array('conditions' => array('id' => $id)));
      if(file_exists(ROOT.'/app/Plugin/'.$search['0']['plugins']['name'].'/Controller/Component/MainComponent.php')) {
        App::uses('MainComponent', 'Plugin/'.$search['0']['plugins']['name'].'/Controller/Component');
        $this->Main = new MainComponent();
        $this->Main->onDisable();
      }
      return true;
    } else {
      return false;
    }
  }

  function get_plugins($all = false) {
    $this->plugins = ClassRegistry::init('plugins');
    if(!$all) {
      $plugins = $this->plugins->find('all', array('conditions' => array('state' => '1'))); // pour avoir seulement les plugins activés & installés
    } else {
      $plugins = $this->plugins->find('all');
    }
    if(!empty($plugins)) {
      foreach ($plugins as $key => $value) {
        $plugins_list[] = $value['plugins']['name']; 
      }
      $plugins = $plugins_list;
    } else {
      $plugins = array();
    }
    return $plugins;
  }

  function get_list() {
    $this->plugins = ClassRegistry::init('plugins');
    return $this->plugins->find('all');
  }

  function get($key, $plugin_name) {
    $config = @file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/config.json');
    if($config) {
      $config = json_decode($config, true);
      return $config[$key];
    } else {
      return false;
    }
  }

  function is_installed($name) {
    $plugins = $this->get_plugins();
    if($plugins != false) {
      if(in_array($name, $plugins)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  function get_a_plugin_by_slug($search) {
    $plugins = Configure::read('eyplugins.plugins.list');
    foreach ($plugins as $k => $v) {
      $config = file_get_contents(ROOT.'/app/Plugin/'.$v.'/config.json');
      $config = json_decode($config, true);
      if($config['slug'] == $search) {
        $url = $v;
      }
    }
    return $url;
  }

  function get_navbar() {
    $plugins = $this->get_plugins();
    foreach ($plugins as $k => $v) {
      $config = file_get_contents(ROOT.'/app/Plugin/'.$v.'/config.json');
      $config = json_decode($config, true);
      if($config['nav'] == true) {
        $plugin_in_nav[] = array($config['name'] => $config['slug']);
      }
    }
    if(!empty($plugin_in_nav)) {
      return $plugin_in_nav;
    } else {
      return false;
    }
  }

  function get_last_version($plugin_id) {
    $available_plugins = @file_get_contents('http://mineweb.org/api/getAllPlugins');
    if($available_plugins) {
      $available_plugins = json_decode($available_plugins, true);
      foreach ($available_plugins as $key => $value) {
        if($value['plugin_id'] == $plugin_id) {
          $version = $value['version'];
        }
      }
    } else {
      $version = array();
    }
    return $version;
  }

  function get_free_plugins() {
    $plugins = $this->get_plugins(true);
    $available_plugins = @file_get_contents('http://mineweb.org/api/getFreePlugins');
    if($available_plugins) {
      $available_plugins = json_decode($available_plugins, true);
      foreach ($available_plugins as $key => $value) {
        if(!in_array($value['name'], $plugins)) {
          $free_plugins[] = array('plugin_id' => $value['plugin_id'], 'name' => $value['name'], 'author' => $value['author'], 'version' => $value['version']);
        }
      }
    } else {
      $free_plugins = array();
    }

    // plugins payés
    $secure = file_get_contents(ROOT.'/config/secure');
    $secure = json_decode($secure, true);
    $purchased_plugins = @file_get_contents('http://mineweb.org/api/getPurchasedPlugins/'.$secure['id']);
    if($purchased_plugins) {
      $purchased_plugins = json_decode($purchased_plugins, true);
      if($purchased_plugins['status'] == "success") {
        foreach ($purchased_plugins['success'] as $key => $value) {
          if(!in_array($value['name'], $plugins)) {
            $free_plugins[] = array('plugin_id' => $value['plugin_id'], 'name' => $value['name'], 'author' => $value['author'], 'version' => $value['version']);
          }
        }
      }
    }

    if(!empty($free_plugins)) {
      return $free_plugins;
    } else {
      return false;
    }
  }


  function install($plugin_id = false, $plugin_name = false) {

    // get du zip sur mineweb.org
    $url = 'http://mineweb.org/api/get_plugin/'.$plugin_id;
    $secure = file_get_contents(ROOT.'/config/secure');
    $secure = json_decode($secure, true);
    $postfields = array(
      'id' => $secure['id'],
        'key' => $secure['key'],
        'domain' => Router::url('/', true)
    );

    $postfields = json_encode($postfields);
    $post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
WCqkx22behAGZq6rhwIDAQAB
-----END PUBLIC KEY-----');

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

    $return = curl_exec($curl);
    curl_close($curl);

    if(!preg_match('#Errors#i', $return)) {
           $return_json = json_decode($return, true);
          if(!$return_json) {
            $zip = $return;
          } elseif($return_json['status'] == "error") {
            return false;
          }
    } else {
      return false;
    }

    if(unzip($zip, ROOT.'/app/Plugin', 'install-zip', true)) {
      $this->plugins = ClassRegistry::init('plugins');
      $config = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/config.json');
      $config = json_decode($config, true);

      $p_tables = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/sql_name.txt');
      $p_tables = explode(',', $p_tables);

      $this->plugins->create();
      $this->plugins->set(array('plugin_id' => $plugin_id, 'name' => $plugin_name, 'author' => $config['author'], 'version' => $config['version'], 'tables' => serialize($p_tables)));
      $this->plugins->save();
      
      $this->addTables($plugin_name);

      if(file_exists(ROOT.'/app/Plugin/'.$plugin_name.'/Controller/Component/MainComponent.php')) {
        App::uses('MainComponent', 'Plugin/'.$plugin_name.'/Controller/Component');
        $this->Main = new MainComponent();
        $this->Main->onEnable();
      }

      // une fois que on a mis les tables, on s'occupe des permissions
      $this->addPermissions($config);

      clearDir(ROOT.'/app/Plugin/__MACOSX');
      clearFolder(ROOT.'/app/tmp/cache/models/');
      clearFolder(ROOT.'/app/tmp/cache/persistent/');
      return true;
    } else {
      return false;
    }
  }

  function update($plugin_id = false, $plugin_name = false) {

    // get du zip sur mineweb.org
    $url = 'http://mineweb.org/api/get_plugin/'.$plugin_id;
    $secure = file_get_contents(ROOT.'/config/secure');
    $secure = json_decode($secure, true);
    $postfields = array(
      'id' => $secure['id'],
      'key' => $secure['key'],
      'domain' => Router::url('/', true)
    );

    $postfields = json_encode($postfields);
    $post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
WCqkx22behAGZq6rhwIDAQAB
-----END PUBLIC KEY-----');

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

    $return = curl_exec($curl);
    curl_close($curl);

    if(!preg_match('#Errors#i', $return)) {
          $return_json = json_decode($return, true);
          if(!$return_json) {
            $zip = $return;
          } elseif($return_json['status'] == "error") {
            return false;
          }
    } else {
      return false;
    }

    App::import('Component', 'UpdateComponent');
    $this->Update = new UpdateComponent();
    if($this->Update->plugin($zip, $plugin_name, $plugin_id)) { // on update
    // si ca réussi
      // on récupère la nouvelle config
      $config = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/config.json');
      $config = json_decode($config, true);
      $version = $config['version'];
      $p_tables = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/sql_name.txt');
      $p_tables = explode(',', $p_tables);

      // on change la version/les tables dans la base de donnée
      $id = $this->plugins->find('all', array('conditions' => array('plugin_id' => $plugin_id)));
      $id = $id[0]['plugins']['id'];
      $this->plugins->read(null, $id);
      $this->plugins->set(array('version' => $version, 'tables' => serialize($p_tables)));
      $this->plugins->save();

      // on execute le fichier de modification si il existe (suppresion de fichier, modification sql)
      if(file_exists(ROOT.'/temp/'.$plugin_name.'_update.php')) {
        include(ROOT.'/temp/'.$plugin_name.'_update.php'); // on l'inclue
        unlink(ROOT.'/temp/'.$plugin_name.'_update.php'); // et on le supprime
      }

      // on supprime les fichiers inutiles
      clearDir(ROOT.'/app/Plugin/__MACOSX');
      clearDir(ROOT.'/temp/');
      clearFolder(ROOT.'/app/tmp/cache/models/');
      clearFolder(ROOT.'/app/tmp/cache/persistent/');
      return true; // et on dis qu'on a réussi
    } else {
      return false;
    }
  }

}