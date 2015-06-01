<?php
class UpdateComponent extends Object {

	public $components = array('Session', 'Configuration', 'Lang');
  
	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
	function beforeRedirect() {}
	function initialize(&$controller) {
		$controller->set('Update', new UpdateComponent());

		if($this->available() != false) {
			if($this->get_type() == "forced") {
				$this->update($this->get_version());
			}
		}
	}
	function startup(&$controller) {}

	public function available() {
		App::import('Component', 'LangComponent');
    	$this->Lang = new LangComponent();
    	App::import('Component', 'ConfigurationComponent');
    	$this->Configuration = new ConfigurationComponent();
		$last_version = $this->get_version();
		$cms_version = $this->Configuration->get('version');
		if($cms_version != $last_version) {
			$type = $this->get_type();
			$visible = $this->get_visible();
			if($visible AND $type == "choice") { // choice -> l'utilisateur choisis ou pas, forced -> la màj est faite automatiquement
				return '<div class="alert alert-info">'.$this->Lang->get('UPDATE_AVAILABLE').' '.$this->Lang->get('YOUR_VERSION').' : '.$cms_version.', '.$this->Lang->get('UPDATE_VERSION').' : '.$last_version.' <a href="'.Router::url(array('controller' => 'update', 'action' => 'index', 'admin' => true)).'" style="margin-top: -6px;" class="btn btn-info pull-right">'.$this->Lang->get('UPDATE').'</a></div>';
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function get_version() {
		// va check la dernière version sur mineweb.org
		$get = @file_get_contents('http://mineweb.org/api/update.json');
		$get = json_decode($get, true);
		return $get['last_version'];
	}

	public function get_update_files($version) {
		// récupérer les fichiers mis à jour sur mineweb.org (le zip dans un dossier temp)
		$zip = file_get_contents('http://mineweb.org/api/update_files/'.$version.'.zip');
		if (!is_dir(ROOT.'/temp/')) mkdir(ROOT.'/temp/');
		$write = fopen(ROOT.'/temp/'.$version.'.zip', 'w+');
		if(!fwrite($write, $zip)) { 
			return false;
		}
		fclose($write);
		return true;
	}

	public function get_type() {
		// récupére le type de la dernière màj (forcé ou pas)
		$get = @file_get_contents('http://mineweb.org/api/update.json');
		$get = json_decode($get, true);
		return $get['type'];
	}

	public function get_visible() {
		// récupére si la màj est signaler à l'utilisateur ou pas
		$get = @file_get_contents('http://mineweb.org/api/update.json');
		$get = json_decode($get, true);
		return $get['visible'];
	}

	public function update($version) {
		// update le site avec le zip dans ROOT/temp/
		if($this->get_update_files($version)) {
			$zipHandle = zip_open(ROOT.'/temp/'.$version.'.zip');
			$rand = substr(md5(rand()), 0, 5);
			while ($aF = zip_read($zipHandle)) {
				$thisFileName = zip_entry_name($aF);
				$thisFileDir = dirname($thisFileName);
				if(substr($thisFileName,-1,1) == '/') continue;
				if(!is_dir (ROOT.'/'.$thisFileDir)) {
					if(strstr($thisFileDir, '__MACOSX') === false) {
						mkdir (ROOT.'/'.$thisFileDir);
						$this->set_log('CREATE_FOLDER', 'success', $thisFileDir, $rand);
					}
				}
				if (!is_dir(ROOT.'/'.$thisFileName)) {
					$contents = zip_entry_read($aF, zip_entry_filesize($aF));
					$contents = str_replace("\r\n", "\n", $contents);
					$updateThis = '';
					
					if($thisFileName == 'modify.php') {
						$upgradeExec = fopen('modify.php','w');
						fwrite($upgradeExec, $contents);
						fclose($upgradeExec);
						include 'modify.php';
						unlink('modify.php');
						$this->set_log('EXECUTE', 'success', $thisFileName, $rand);
					} elseif($thisFileName != ".DS_Store") {
						if(file_exists(ROOT.'/'.$thisFileName)) {
							$exist = true;
						} else {
							$exist = false;
						}
						if(strstr($thisFileName, '__MACOSX') === false AND strstr($thisFileName, '.DS_Store') === false) {
							if($updateThis = fopen(ROOT.'/'.$thisFileName, 'w')) {
								$aF_time = filemtime(zip_entry_read($aF, zip_entry_filesize($aF)));
								$last_time = filemtime(ROOT.'/'.$thisFileName);
								if($aF_time != $last_time OR $exist === false) {
									fwrite($updateThis, $contents);
									fclose($updateThis);
									unset($contents);
									if($exist == true) {
										$this->set_log('UPDATE_FILE', 'success', $thisFileName, $rand);
									} else {
										$this->set_log('CREATE_FILE', 'success', $thisFileName, $rand);
									}
								}
							} else {
								$this->set_log('CREATE_FILE', 'error', $thisFileName, $rand);
							}
						}
					}
				}
			}
			if($this->end_log($rand)) {
				return true;
			} else {
				return false;
			}
		} else {
			$this->set_log('GET_FILES', 'error', 'CANT_WRITE_IN_TEMP', $rand);
		}
	}

	public function plugin($url, $plugin_name, $plugin_id) {
		// récupérer les fichiers mis à jour sur mineweb.org (le zip dans un dossier temp)
		$zip = file_get_contents($url);
		if (!is_dir(ROOT.'/temp/')) mkdir(ROOT.'/temp/');
		$write = fopen(ROOT.'/temp/'.$plugin_name.'-'.$plugin_id.'.zip', 'w+');
		if(!fwrite($write, $zip)) { 
			return false;
		}
		fclose($write);

		// on ouvre le zip et on met les fichiers
		$zipHandle = zip_open(ROOT.'/temp/'.$plugin_name.'-'.$plugin_id.'.zip');
		$rand = substr(md5(rand()), 0, 5);
		while ($aF = zip_read($zipHandle)) {
			$thisFileName = zip_entry_name($aF);
			$thisFileDir = dirname($thisFileName);
			if(substr($thisFileName,-1,1) == '/') continue;
			if(!is_dir (ROOT.'/app/Plugin/'.$thisFileDir)) {
				if(strstr($thisFileDir, '__MACOSX') === false) {
					mkdir (ROOT.'/app/Plugin/'.$thisFileDir);
				}
			}
			if (!is_dir(ROOT.'/app/Plugin/'.$thisFileName)) {
				$contents = zip_entry_read($aF, zip_entry_filesize($aF));
				$contents = str_replace("\r\n", "\n", $contents);
				$updateThis = '';
				
				if($thisFileName == $plugin_name.'_update.php') {
					$upgradeExec = fopen(ROOT.'/temp/'.$plugin_name.'_update.php','w');
					fwrite($upgradeExec, $contents);
					fclose($upgradeExec);
				} elseif($thisFileName != ".DS_Store") {
					if(file_exists(ROOT.'/app/Plugin/'.$thisFileName)) {
						$exist = true;
					} else {
						$exist = false;
					}
					if(strstr($thisFileName, '__MACOSX') === false AND strstr($thisFileName, '.DS_Store') === false) {
						if($updateThis = fopen(ROOT.'/app/Plugin/'.$thisFileName, 'w')) {
							$aF_time = filemtime(zip_entry_read($aF, zip_entry_filesize($aF)));
							$last_time = filemtime(ROOT.'/app/Plugin/'.$thisFileName);
							if($aF_time != $last_time OR $exist === false) {
								fwrite($updateThis, $contents);
								fclose($updateThis);
								unset($contents);
							}
						} else {
							return false;
						}
					}
				}
			}
		}
		return true;
	}

	public function set_log($action, $state = "success", $args, $rand) {
		// set les logs de la mise à jour 
		$filename = ROOT.'/app/tmp/logs/update/'.$this->get_version().'-'.$rand.'.log';
		if(!file_exists($filename)) {
			mkdir(ROOT.'/app/tmp/logs/update');
			$write = fopen($filename, "x+");
			$header = json_encode(array('head' => array('date' => date('d/m/Y H:i:s'), 'version' => $this->get_version())), JSON_PRETTY_PRINT);
			fwrite($write, $header);
			fclose($write);
		}
		$before = file_get_contents($filename);
		$before = json_decode($before, true);
		$write = fopen($filename, 'w+');
		$string = $before;
		$i = count($string['update']) + 1;
		$string['update'][$i][$action]['statut'] = $state;
		$string['update'][$i][$action]['arg'] = $args;
		$string = json_encode($string, JSON_PRETTY_PRINT);
		fwrite($write, $string);
		fclose($write);
	}

	public function end_log($rand) {
		$filename = ROOT.'/app/tmp/logs/update/'.$this->get_version().'-'.$rand.'.log';
		$content = file_get_contents($filename);
		$content = json_decode($content, true);
		if(in_array('error', $content)) {
			$this->Configuration->set('version', $this->get_version());
			return true;
		} else {
			return false;
		}
	}

}