<?php
class APIComponent extends Object {

	public $components = array('Session', 'Configuration', 'Lang');

	public $mineguard_active;
	public $skin_active;
	public $cape_active;
  
	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
	function beforeRedirect() {}
	function initialize(&$controller) {
		$controller->set('API', new APIComponent());
		App::import('Component', 'LangComponent');
    	$this->Lang = new LangComponent();
    	App::import('Component', 'ConfigurationComponent');
    	$this->Configuration = new ConfigurationComponent();
    	App::import('Component', 'ConnectComponent');
    	$this->Connect = new ConnectComponent();

    	$this->User = ClassRegistry::init('User');

    	$this->mineguard_active = $this->Configuration->get('mineguard');
    	if($this->mineguard_active == "true") {
    		$this->mineguard_active = true;
    	} else {
    		$this->mineguard_active = false;
    	}

    	$this->ApiConfiguration = ClassRegistry::init('ApiConfiguration');

    	$skin_search = $this->ApiConfiguration->find('first');
    	$this->skin_active = $skin_search['ApiConfiguration']['skins'];
    	if($this->skin_active == "1") {
    		$this->skin_active = true;
    	} else {
    		$this->skin_active = false;
    	}

    	$cape_search = $this->ApiConfiguration->find('first');
    	$this->cape_active = $cape_search['ApiConfiguration']['capes'];
    	if($this->cape_active == "1") {
    		$this->cape_active = true;
    	} else {
    		$this->cape_active = false;
    	}
	}
	function startup(&$controller) {}

	public function set($key, $value) {
      $this->Configuration = ClassRegistry::init('ApiConfiguration');
      $this->Configuration->read(null, 1);
      $this->Configuration->set(array($key => $value));
      if($this->Configuration->save()) {
        return true;
      } else {
        return false;
      }
    }

		/* CAPE ET SKINS */ 

	public function can_skin() {
		if($this->skin_active) {
			$skin_search = $this->ApiConfiguration->find('first');
    		if($skin_search['ApiConfiguration']['skin_free'] == 1) {
    			return true;
    		} else {
    			$skin = $this->Connect->get('skin');
    			if($skin == 1) {
    				return true;
    			} else {
    				return false;
    			}
    		}
    	} else {
    		return false;
    	}
	}

	public function can_cape() {
		if($this->cape_active) {
			$cape_search = $this->ApiConfiguration->find('first');
    		if($cape_search['ApiConfiguration']['skin_free'] == 1) {
    			return true;
    		} else {
    			$cape = $this->Connect->get('cape');
    			if($cape == 1) {
    				return true;
    			} else {
    				return false;
    			}
    		}
    	} else {
    		return false;
    	}
	}

	public function get_skin($name, $where) {
 
		$filename =  $where.$name.'.png';
		 
		header('Content-Type: image/png');
		$rendered = imagecreatetruecolor(240, 480);
		$source = imagecreatefrompng($filename);
		$b = 120;
		$s = 8;
		$pink = imagecolorallocate($rendered, 255, 0, 255);
		imagefilledrectangle($rendered, 0, 0, 240, 480, $pink);
		imagecolortransparent($rendered, $pink);
		$size_x = imagesx($source);
		$size_y = imagesy($source);
		$temp = imagecreatetruecolor($size_x, $size_y);
		$x = imagecopyresampled($temp, $source, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);
		$fsource = $temp;
		imagecopyresampled($rendered, $source, $b / 2, 0, $s, $s, $b, $b, $s, $s);
		imagecopyresampled($rendered, $source, $b / 2, 0, $s * 5, $s, $b, $b, $s, $s);
		imagecopyresampled($rendered, $source, $b / 2, $b, $s * 2.5, $s * 2.5, $b, $b * 1.5, $s, $s * 1.5);
		imagecopyresampled($rendered, $source, $b * 1.5, $b, $s * 5.5, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);
		imagecopyresampled($rendered, $fsource, 0, $b, $s * 2, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);
		imagecopyresampled($rendered, $source, 60, $b * 2.5, $s / 2, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);
		imagecopyresampled($rendered, $fsource, $b * 1, $b * 2.5, $s * 7, $s * 2.5, $b / 2, $b * 1.5, $s / 2, $s * 1.5);
		imagepng($rendered);
	}

	public function get_head_skin($name, $size = 50, $where, $cache = true) {

		header("Content-type: image/png");
		if($cache){
		  if(!is_dir($cacheFolder)){
		    mkdir($cacheFolder);
		  }
		  $cachePath = ROOT.'/app/tmp/cache/skins/'.$name . '.png';
		  if(is_file($cachePath) && !isset($_GET['skip'])){
		    include($cachePath);
		    exit();
		  }
		}
		$src = imagecreatefrompng($where.$name.'.png');
		$dest   = imagecreatetruecolor(8, 8);
		imagecopy($dest, $src, 0, 0, 8, 8, 8, 8); 
		$bg_color = imagecolorat($src, 0, 0);
		$no_helm  = true;
		for ($i = 1; $i <= 8; $i++) {
		  for ($j = 1; $j <= 4; $j++) {
		    if (imagecolorat($src, 40 + $i, 7 + $j) != $bg_color) {
		      $no_helm = false;
		    }
		  }
		  if (!$no_helm)
		    break;
		}
		if (!$no_helm) {
		  imagecopy($dest, $src, 0, -1, 40, 7, 8, 4);
		}
		$final = imagecreatetruecolor($size, $size);
		imagecopyresized($final, $dest, 0, 0, 0, 0, $size, $size, 8, 8);
		if($cache){
		  imagepng($final, $cachePath);
		  include($cachePath);
		}
		else {
		  imagepng($final);
		}
		imagedestroy($im);
		imagedestroy($dest);
		imagedestroy($final);

	}

		/* API Launcher (connexion) */

	public function get($username, $password, array $args = null) {
		if(!empty($username) && !empty($password)) {
			$search_user = $this->User->find('all', array('conditions' => array('pseudo' => $username, 'password' => password($password))));
			if(!empty($search_user)) {
				if(is_array($args)) {
					$return = array('status' => true);
					if(in_array('id', $args)) {
						$return['args']['id'] = $search_user[0]['User']['id'];
					}
					if(in_array('email', $args)) {
						$return['args']['email'] = $search_user[0]['User']['email'];
					}
					if(in_array('rank', $args)) {
						$return['args']['rank'] = $search_user[0]['User']['rank'];
					}
					if(in_array('money', $args)) {
						$return['args']['money'] = $search_user[0]['User']['money'];
					}
					if(in_array('ip', $args)) {
						$return['args']['ip'] = $search_user[0]['User']['ip'];
					}
					if(in_array('vote', $args)) {
						$return['args']['vote'] = $search_user[0]['User']['vote'];
					}
					if(in_array('created', $args)) {
						$return['args']['created'] = $search_user[0]['User']['created'];
					}
					return $return;
				} else {
					return array('status' => true);
				}
			} else {
				return array('status' => false);
			}
		} else {
			return array('status' => false);
		}
	}

		/* API MineGuard */

	public function verifIp($username, $ip) {
		if($this->mineguard_active) {
			if(!empty($username) AND !empty($ip)) {
				if(filter_var($ip, FILTER_VALIDATE_IP)) {
					$search_user = $this->User->find('all', array('conditions' => array('pseudo' => $username)));
					if(!empty($search_user)) {
						$allowed_ip = unserialize($search_user[0]['User']['allowed_ip']);
						if(empty($allowed_ip)) {
							$allowed_ip[] = $search_user[0]['User']['ip'];
						}
						if(in_array($ip, $allowed_ip)) {
							return array('result' => 'SUCCESS');
						} else {
							return array('result' => 'NOT_ALLOWED');
						}
					} else {
						return array('result' => 'UNKNOWN_USER');
					}
				} else {
					return array('result' => 'INVALID_IP');
				}
			} else {
				return array('result' => 'FIELD_EMPTY');
			}
		} else {
			return array('result' => 'MINEGUARD_DISABLE');
		}
	}

	public function getIp($username) {
		if($this->mineguard_active) {
			if(!empty($username)) {
				$search_user = $this->User->find('all', array('conditions' => array('pseudo' => $username)));
				if(!empty($search_user)) {
					$allowed_ip = unserialize($search_user[0]['User']['allowed_ip']);
					if(empty($allowed_ip)) {
						$allowed_ip[] = $search_user[0]['User']['ip'];
					}
					return $allowed_ip;
				} else {
					return array();
				}
			} else {
				return array();
			}
		} else {
			return array();
		}
	}

	public function setIp($username, $ip) {
		if($this->mineguard_active) {
			if(!empty($username) AND !empty($ip)) {
	 			$ip_list = unserialize($this->Connect->get_to_user('allowed_ip', $username));
	 			$ip_list[] = $ip;
		 		$this->Connect->set_to_user('allowed_ip', serialize($ip_list), $username);
		 		return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function removeIp($username, $ip) {
		if($this->mineguard_active) {
			if(!empty($username) AND isset($ip)) {
	 			$ip_list = unserialize($this->Connect->get_to_user('allowed_ip', $username));
	 			if(!empty($ip_list)) {
		 			unset($ip_list[$ip]);
		 			$this->Connect->set_to_user('allowed_ip', serialize($ip_list), $username);
		 		}
		 		return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}