<?php
App::uses('CakeObject', 'Core');
class APIComponent extends CakeObject
{

  public $components = array('Session', 'Configuration', 'Lang');

  private $controller;

  public $skin_active;
  public $cape_active;

  function shutdown($controller) {}
  function beforeRender($controller) {}
  function beforeRedirect() {}
  function startup($controller) {}

  function initialize($controller)
  {
    $this->controller = $controller;

    $controller->set('API', $this);

    $this->Lang = $this->controller->Lang;
    $this->Configuration = $this->controller->Configuration;

    $this->User = ClassRegistry::init('User');

    $this->ApiConfiguration = ClassRegistry::init('ApiConfiguration');
    $this->config = $this->ApiConfiguration->find('first')['ApiConfiguration'];

    $this->skin_active = $this->config['skins'] == '1';
    $this->cape_active = $this->config['capes'] == '1';
  }

  public function set($key, $value)
  {
    $this->ApiConfiguration->read(null, 1);
    $this->ApiConfiguration->setKey(array($key => $value));
    return ($this->ApiConfiguration->save());
  }

  public function can_skin()
  {
    if (!$this->skin_active) return false;
    if ($this->config['skin_free'] == 1) return true;
    return $this->User->getKey('skin') == 1;
  }

  public function can_cape()
  {
    if (!$this->skin_active) return false;
    if ($this->config['cape_free'] == 1) return true;
    return $this->User->getKey('cape') == 1;
  }

  private function _getSkinFromUsername($username) {
    // We need to get UUID
    $user = @json_decode(@file_get_contents("https://api.mojang.com/users/profiles/minecraft/$username"), true);
    if (!$user) return false;
    $uuid = $user['id'];
    // Get profile with skin as base64
    $profile = @json_decode(@file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/$uuid"), true);
    if (!$profile) return false;
    // Get texture item
    $properties = $profile['properties'];
    $textures = null;
    foreach ($properties as $property)
      if ($property['name'] === 'textures')
        $textures = $property;
    if (!$textures) return false;
    // Decode value
    $texturesObject = @json_decode(@base64_decode($textures['value']), true);
    if (!$texturesObject) return false;
    $url = $texturesObject['textures']['SKIN']['url'];
    return file_get_contents($url);
  }

  private function _getSkinImage($username) {
    if ($this->skin_active) {
        $filename = str_replace('{PLAYER}', $username, $this->config['skin_filename']);
        $content = @file_get_contents(WWW_ROOT . $filename . '.png');
    } else {
        $content = base64_decode(Cache::read('skin_'.$username, 'skin'));

        if (empty($content)) {
            $content = $this->_getSkinFromUsername($username);
            Cache::remember('skin_'.$username, function() use ($content){
                return base64_encode($content);
            }, 'skin');
        }
    }
    
    if ($content) return @imagecreatefromstring($content);
    // Return steve skin
    return imagecreatefromstring(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAEAAAAAgCAMAAACVQ462AAAABGdBTUEAALGPC/xhBQAAAwBQTFRF' .
    'AAAAHxALIxcJJBgIJBgKJhgLJhoKJxsLJhoMKBsKKBsLKBoNKBwLKRwMKh0NKx4NKx4OLR0OLB4O' .
    'Lx8PLB4RLyANLSAQLyIRMiMQMyQRNCUSOigUPyoVKCgoPz8/JiFbMChyAFtbAGBgAGhoAH9/Qh0K' .
    'QSEMRSIOQioSUigmUTElYkMvbUMqb0UsakAwdUcvdEgvek4za2trOjGJUj2JRjqlVknMAJmZAJ6e' .
    'AKioAK+vAMzMikw9gFM0hFIxhlM0gVM5g1U7h1U7h1g6ilk7iFo5j14+kF5Dll9All9BmmNEnGNF' .
    'nGNGmmRKnGdIn2hJnGlMnWpPlm9bnHJcompHrHZaqn1ms3titXtnrYBttIRttolsvohst4Jyu4ly' .
    'vYtyvY5yvY50xpaA////AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' .
    'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPSUN6AAAAQB0Uk5T////////////////////////' .
    '////////////////////////////////////////////////////////////////////////////' .
    '////////////////////////////////////////////////////////////////////////////' .
    '////////////////////////////////////////////////////////////////////////////' .
    '////////////////////////////////////////////////////////////////////////////' .
    '////////////AFP3ByUAAAAYdEVYdFNvZnR3YXJlAFBhaW50Lk5FVCB2My4zNqnn4iUAAAKjSURB' .
    'VEhLpZSLVtNAEIYLpSlLSUITLCBaGhNBQRM01M2mSCoXNUURIkZFxQvv/wz6724Wij2HCM7J6UyS' .
    '/b+dmZ208rsww6jiqo4FhannZb5yDqjaNgDVwE/8JAmCMqF6fwGwbU0CKjD/+oAq9jcM27gxAFpN' .
    'QxU3Bwi9Ajy8fgmGZuvaGAcIuwFA12CGce1jJESr6/Ot1i3Tnq5qptFqzet1jRA1F2XHWQFAs3Rz' .
    'wTTNhQd3rOkFU7c0DijmohRg1TR9ZmpCN7/8+PX954fb+sTUjK7VLKOYi1IAaTQtUrfm8pP88/vT' .
    'w8M5q06sZoOouSgHEDI5vrO/eHK28el04yxf3N8ZnyQooZiLfwA0arNb6d6bj998/+vx8710a7bW' .
    '4E2Uc1EKsEhz7WiQBK9eL29urrzsB8ngaK1JLDUXpYAkGSQH6e7640fL91dWXjxZ33138PZggA+S' .
    'z0WQlAL4gmewuzC1uCenqXevMPWc9XrMX/VXh6Hicx4ByHEeAfRg/wtgSMAvz+CKEkYAnc5SpwuD' .
    '4z70PM+hUf+4348ixF7EGItjxmQcCx/Dzv/SOkuXAF3PdT3GIujjGLELNYwxhF7M4oi//wsgdlYZ' .
    'dMXCmEUUSsSu0OOBACMoBTiu62BdRPEjYxozXFyIpK7IAE0IYa7jOBRqGlOK0BFq3Kdpup3DthFw' .
    'P9QDlBCGKEECoHEBEDLAXHAQMQnI8jwFYRQw3AMOQAJoOADoAVcDAh0HZAKQZUMZdC43kdeqAPwU' .
    'BEsC+M4cIEq5KEEBCl90mR8CVR3nxwCdBBS9OAe020UGnXb7KcxzPY9SXoEEIBZtgE7UDgBKyLMh' .
    'gBS2YdzjMJb4XHRDAPiQhSGjNOxKQIZTgC8BiMECgarxprjjO0OXiV4MAf4A/x0nbcyiS5EAAAAA' .
    'SUVORK5CYII='));
  }

  public function get_skin($username)
  {
    $rendered = imagecreatetruecolor(240, 480);
    $source = $this->_getSkinImage($username);
    $b = 120;
    $s = 8;
    $pink = imagecolorallocate($rendered, 255, 0, 255);
    imagefilledrectangle($rendered, 0, 0, 240, 480, $pink);
    imagecolortransparent($rendered, $pink);
    $size_x = imagesx($source);
    $size_y = imagesy($source);
    $temp = imagecreatetruecolor($size_x, $size_y);
    $x = imagecopyresampled($temp, $source, 0, 0, ($size_x - 1), 0, $size_x, $size_y, 0 - $size_x, $size_y);
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

  public function get_head_skin($username, $size = 50)
  {
    $src = $this->_getSkinImage($username);
    $dest = imagecreatetruecolor(8, 8);
    imagecopy($dest, $src, 0, 0, 8, 8, 8, 8);
    $bg_color = imagecolorat($src, 0, 0);
    $no_helm = true;
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
    imagepng($final);
    imagedestroy($dest);
    imagedestroy($final);
  }

  /* API Launcher (connexion) */

  public function get($username, $password, array $args = null)
  {
    if (empty($username) || empty($password))
      return ['status' => false]; // password must be a password encrypted by sha256
    if (!is_array($args))
      return ['status' => false];

    $user = $this->User->find('first', array('conditions' => array('pseudo' => $username, 'password' => $password)));
    if (empty($user))
      return ['status' => false];
    $user = $user['User'];

    $result = ['status' => true];
    if (in_array('id', $args))
      $result['args']['id'] = $user['id'];
    if (in_array('email', $args))
      $result['args']['email'] = $user['email'];
    if (in_array('rank', $args))
      $result['args']['rank'] = $user['rank'];
    if (in_array('money', $args))
      $result['args']['money'] = $user['money'];
    if (in_array('ip', $args))
      $result['args']['ip'] = $user['ip'];
    if (in_array('vote', $args))
      $result['args']['vote'] = $user['vote'];
    if (in_array('created', $args))
      $result['args']['created'] = $user['created'];

    return $result;
  }
}
