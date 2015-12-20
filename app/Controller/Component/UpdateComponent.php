<?php
@ignore_user_abort(true);
@set_time_limit(0);

class UpdateComponent extends Object {

	public $components = array('Session', 'Configuration', 'Lang');

	private $update = array('status' => false, 'version' => NULL, 'type' => NULL, 'visible' => false);
	private $cmsVersion = NULL;

	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
	function beforeRedirect() {}
	function initialize(&$controller) {
		$controller->set('Update', new UpdateComponent());

		$this->check();

		if(!file_exists(ROOT.'/config/update') OR strtotime('+5 hours', filemtime(ROOT.'/config/update')) < time()) {
			if($this->update->status) {
				if($this->update->type == "forced") {
					$this->update($this->update->version);
				}
			}
		}
	}
	function startup(&$controller) {}

	public function available() {
		if(!file_exists(ROOT.'/config/update') OR strtotime('+5 hours', filemtime(ROOT.'/config/update')) < time()) {
			App::import('Component', 'LangComponent');
	    $this->Lang = new LangComponent();

			if($this->update->status) {
				if($this->update->visible AND $this->update->type == "choice") { // choice -> l'utilisateur choisis ou pas, forced -> la màj est faite automatiquement
					file_put_contents(ROOT.'/config/update', '<div class="alert alert-info">'.$this->Lang->get('UPDATE_AVAILABLE').' '.$this->Lang->get('YOUR_VERSION').' : '.$this->cmsVersion.', '.$this->Lang->get('UPDATE_VERSION').' : '.$this->update->version.' <a href="'.Router::url(array('controller' => 'update', 'action' => 'index', 'admin' => true)).'" style="margin-top: -6px;" class="btn btn-info pull-right">'.$this->Lang->get('UPDATE').'</a></div>');
					return '<div class="alert alert-info">'.$this->Lang->get('UPDATE_AVAILABLE').' '.$this->Lang->get('YOUR_VERSION').' : '.$this->cmsVersion.', '.$this->Lang->get('UPDATE_VERSION').' : '.$this->update->version.' <a href="'.Router::url(array('controller' => 'update', 'action' => 'index', 'admin' => true)).'" style="margin-top: -6px;" class="btn btn-info pull-right">'.$this->Lang->get('UPDATE').'</a></div>';
				} else {
					file_put_contents(ROOT.'/config/update', 1);
					return false;
				}
			} else {
				file_put_contents(ROOT.'/config/update', 1);
				return false;
			}
		} else {
			$content = file_get_contents(ROOT.'/config/update');
			if($content != 1) {
				return $content;
			}
		}
	}

	private function check() {
		$lastVersion = @file_get_contents('http://mineweb.org/api/v1/get_update');
		$lastVersion = json_decode($lastVersion, true);

		App::import('Component', 'ConfigurationComponent');
		$this->Configuration = new ConfigurationComponent();
		$cmsVersion = $this->Configuration->get('version');

		$this->cmsVersion = $cmsVersion;

		if(version_compare($lastVersion['last_version'], $cmsVersion, '<')) {
			$this->update['status'] = true;
			$this->update['version'] = $lastVersion['last_version'];
			$this->update['type'] = $lastVersion['type'];
			$this->update['visible'] = $lastVersion['visible'];
		}
	}

	public function get_update_files($version) {

		// récupérer les fichiers mis à jour sur mineweb.org (le zip dans un dossier temp)

		$url = 'http://mineweb.org/api/v1/update/';
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

		if(!curl_errno($curl)) {
	        $return_json = json_decode($return, true);
          	if(!$return_json) {
	            $zip = $return;
    	      } elseif($return_json['status'] == "error") {
        	    return false;
          	}
		} else {
			return false;
		}

		curl_close($curl);

		if (!is_dir(ROOT.'/temp/')) mkdir(ROOT.'/temp/');
		$write = fopen(ROOT.'/temp/'.$version.'.zip', 'w+');
		if(!fwrite($write, $zip)) {
			return false;
		}
		fclose($write);
		return true;
	}

	public function stop_update() {
		return file_put_contents(ROOT.'/stop_update', '1');
	}

	public function update($version) {
		// update le site avec le zip dans ROOT/temp/
		$rand = substr(md5(rand()), 0, 5);
		if($this->get_update_files($version)) {
			$zipTmp = new ZipArchive;
			$res = $zipTmp->open(ROOT.'/temp/'.$version.'.zip');
			$zipHandle = zip_open(ROOT.'/temp/'.$version.'.zip');
			while ($aF = zip_read($zipHandle)) {
				if(file_exists(ROOT.'/stop_update')) {
					unlink(ROOT.'/stop_update');
					@unlink(ROOT.'/temp/'.$version.'.zip');
					@unlink(ROOT.'/config/update');
					break;
				}
				$thisFileName = zip_entry_name($aF);
				$thisFileDir = dirname($thisFileName);
				if(substr($thisFileName,-1,1) == '/') continue;
				if(!is_dir (ROOT.'/'.$thisFileDir)) {
					if(strstr($thisFileDir, '__MACOSX') === false) {
						if(mkdir (ROOT.'/'.$thisFileDir, 0755, true)) {
							$this->set_log('CREATE_FOLDER', 'success', $thisFileDir, $rand);
						} else {
							$this->set_log('CREATE_FOLDER', 'error', $thisFileDir, $rand);
						}
					}
				}
				if (!is_dir(ROOT.'/'.$thisFileName)) {
					$contents = zip_entry_read($aF, zip_entry_filesize($aF));
					$extension = explode('.', $thisFileName);
					end($extension);
					$extension = $extension[key($extension)];
					if(!in_array($extension, array('png', 'jpg', 'jpeg', 'gif', 'eot', 'svg', 'ttf', 'otf', 'woff'))) {
						$contents = str_replace("\r\n", "\n", $contents);
					}
					$updateThis = '';

					if($thisFileName == 'modify.php') {
						$upgradeExec = fopen('modify.php','w');
						fwrite($upgradeExec, $contents);
						fclose($upgradeExec);
						include 'modify.php';
						unlink('modify.php');
						$this->set_log('EXECUTE', 'success', $thisFileName, $rand);
					} elseif($thisFileName != ".DS_Store" AND $thisFileName != "app/Config/database.php" AND $thisFileName != "config/secure") {
						if(file_exists(ROOT.'/'.$thisFileName)) {
							$exist = true;
						} else {
							$exist = false;
						}
						if(strstr($thisFileName, '__MACOSX') === false AND strstr($thisFileName, '.DS_Store') === false) {
							$aF_time = $zipTmp->statname($thisFileName)["mtime"];
							$last_time = @filemtime(ROOT.'/'.$thisFileName);
							$filezip_size = zip_entry_filesize($aF);
							$file_size = @filesize(ROOT.'/'.$thisFileName);
							if($filezip_size != $file_size OR $exist === false) {
								if($updateThis = fopen(ROOT.'/'.$thisFileName, 'w')) {
									fwrite($updateThis, $contents);
									fclose($updateThis);
									unset($contents);
									if($exist == true) {
										$this->set_log('UPDATE_FILE', 'success', $thisFileName, $rand);
									} else {
										$this->set_log('CREATE_FILE', 'success', $thisFileName, $rand);
									}
								} else {
									$this->set_log('CREATE_FILE', 'error', $thisFileName, $rand);
								}
							}
						}
					}
				}
			}
			if($this->end_log($rand)) {
				@unlink(ROOT.'/temp/'.$version.'.zip');
				@unlink(ROOT.'/config/update');
				return true;
			} else {
				return false;
			}
		} else {
			$this->set_log('GET_FILES', 'error', 'CANT_WRITE_IN_TEMP', $rand);
		}
	}

	public function plugin($zip, $plugin_name, $plugin_id) {
		// récupérer les fichiers mis à jour sur mineweb.org (le zip dans un dossier temp)

		if (!is_dir(ROOT.'/temp/')) mkdir(ROOT.'/temp/');
		$write = fopen(ROOT.'/temp/'.$plugin_name.'-'.$plugin_id.'.zip', 'w+');
		if(!fwrite($write, $zip)) {
			return false;
		}
		fclose($write);

		// on ouvre le zip et on met les fichiers
		$zipTmp = new ZipArchive;
		$res = $zipTmp->open(ROOT.'/temp/'.$plugin_name.'-'.$plugin_id.'.zip');
		$zipHandle = zip_open(ROOT.'/temp/'.$plugin_name.'-'.$plugin_id.'.zip');
		$rand = substr(md5(rand()), 0, 5);
		while ($aF = zip_read($zipHandle)) {
			$thisFileName = zip_entry_name($aF);
			$thisFileDir = dirname($thisFileName);
			if(substr($thisFileName,-1,1) == '/') continue;
			if(!is_dir (ROOT.'/app/Plugin/'.$thisFileDir)) {
				if(strstr($thisFileDir, '__MACOSX') === false) {
					if(!mkdir (ROOT.'/app/Plugin/'.$thisFileDir, 0755, true)) {
						return false;
					}
				}
			}
			if (!is_dir(ROOT.'/app/Plugin/'.$thisFileName)) {
				$contents = zip_entry_read($aF, zip_entry_filesize($aF));
				$extension = explode('.', $thisFileName);
				end($extension);
				$extension = $extension[key($extension)];
				if(!in_array($extension, array('png', 'jpg', 'jpeg', 'gif', 'eot', 'svg', 'ttf', 'otf', 'woff'))) {
					$contents = str_replace("\r\n", "\n", $contents);
				}
				$updateThis = '';

				if($thisFileName == $plugin_name.'/'.$plugin_name.'_update.php') {
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
						$aF_time = $zipTmp->statname($thisFileName)["mtime"];
						$last_time = @filemtime(ROOT.'/app/Plugin/'.$thisFileName);
						$filezip_size = zip_entry_filesize($aF);
						$file_size = @filesize(ROOT.'/app/Plugin/'.$thisFileName);
						if($filezip_size != $file_size OR $exist === false OR $thisFileName == $plugin_name."/config.json") {
							if($updateThis = fopen(ROOT.'/app/Plugin/'.$thisFileName, 'w')) {
								fwrite($updateThis, $contents);
								fclose($updateThis);
								unset($contents);
							} else {
								return false;
							}
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
			if(!is_dir(ROOT.'/app/tmp/logs/update')) {
				mkdir(ROOT.'/app/tmp/logs/update');
			}
			$write = fopen($filename, "x+");
			$header = json_encode(array('head' => array('date' => date('d/m/Y H:i:s'), 'version' => $this->get_version())), JSON_PRETTY_PRINT);
			fwrite($write, $header);
			fclose($write);
		}
		$before = file_get_contents($filename);
		$before = json_decode($before, true);
		$write = fopen($filename, 'w+');
		$string = $before;
		$i = @count($string['update']) + 1;
		$string['update'][$i][$action]['statut'] = $state;
		$string['update'][$i][$action]['arg'] = $args;
		$string = json_encode($string, JSON_PRETTY_PRINT);
		fwrite($write, $string);
		fclose($write);
	}

	public function end_log($rand) {
		$filename = ROOT.'/app/tmp/logs/update/'.$this->get_version().'-'.$rand.'.log';
		if(!file_exists($filename)) {
			if(!is_dir(ROOT.'/app/tmp/logs/update')) {
				mkdir(ROOT.'/app/tmp/logs/update');
			}
			$write = fopen($filename, "x+");
			$header = json_encode(array('head' => array('date' => date('d/m/Y H:i:s'), 'version' => $this->get_version())), JSON_PRETTY_PRINT);
			fwrite($write, $header);
			fclose($write);
		}
		$content = file_get_contents($filename);
		$content = json_decode($content, true);
		$error = false;
		foreach ($content as $key => $value) {
			if($key == "update") {
				foreach ($value as $k => $v) {
					foreach ($v as $k2 => $v2) {
						if($v2['statut'] == "error") {
							$error = true;
							break;
						}
					}
				}
			}
		}
		if(!$error) {
			App::import('Component', 'Configuration');
			$this->Configuration = new ConfigurationComponent();
			$this->Configuration->set('version', $this->get_version());
			return true;
		} else {
			return false;
		}
	}

}
