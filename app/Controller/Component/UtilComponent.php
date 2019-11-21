<?php
App::uses('CakeObject', 'Core');

class UtilComponent extends CakeObject
{

    private $controller;

    private $to;
    private $from;
    private $subject;
    private $message;
    private $typeSend = 'default';

    private $smtpOptions = array();

    function shutdown($controller)
    {
    }

    function beforeRender($controller)
    {
    }

    function beforeRedirect()
    {
    }

    function initialize($controller)
    {
        $this->controller = $controller;

        if ($this->controller->Configuration === NULL) {
            $this->controller->Configuration = ClassRegistry::init('Configuration');
        }
    }

    function startup($controller)
    {
    }

    // Get ip (support cloudfare)

    public function getIP()
    {
        return isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? htmlentities($_SERVER["HTTP_CF_CONNECTING_IP"]) : htmlentities($_SERVER["REMOTE_ADDR"]);
    }

    // Encoder un mot de passe

    public function password($password, $username)
    {
        $event = new CakeEvent('beforeEncodePassword', $this, array('password' => $password, 'username' => $username));
        $this->controller->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return $event->result;
        }

        $hash = $this->controller->Configuration->getKey('passwords_hash');
        $salt = $this->controller->Configuration->getKey('passwords_salt');

        if (!isset($hash) || empty($hash)) {
            $hash = 'sha256';
        }
        if (!isset($salt) || empty($salt)) {
            $salt = false;
        }

        return Security::hash($password, $hash, $salt);
    }

    // Pour gérer les temps d'attente ou autre

    public function secondsToTime($inputSeconds)
    {

        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // return the final array
        $obj = array(
            'd' => (int)$days,
            'h' => (int)$hours,
            'm' => (int)$minutes,
            's' => (int)$seconds,
        );
        return $obj;
    }

    public function generateStringFromTime($waitTime)
    {
        $waitTime = $this->secondsToTime($waitTime);
        $time = [];
        if ($waitTime['d'] > 0)
            $time[] = $waitTime['d'] . ' ' . $this->controller->Lang->get('GLOBAL__DATE_R_DAYS');
        if ($waitTime['h'] > 0)
            $time[] = $waitTime['h'] . ' ' . $this->controller->Lang->get('GLOBAL__DATE_R_HOURS');
        if ($waitTime['m'] > 0)
            $time[] = $waitTime['m'] . ' ' . $this->controller->Lang->get('GLOBAL__DATE_R_MINUTES');
        if ($waitTime['s'] > 0)
            $time[] = $waitTime['s'] . ' ' . $this->controller->Lang->get('GLOBAL__DATE_R_SECONDS');
        return implode(', ', $time);
    }

    /*
      MAIL
    */

    public function prepareMail($to, $subject, $message)
    {

        $configuration = $this->controller->Configuration;

        $this->to = $to;

        $this->subject = $subject . ' | ' . $configuration->getKey('name');

        $this->message = $message;

        $this->from = array($configuration->getKey('email') => $configuration->getKey('name'));

        $this->typeSend = (!$configuration->getKey('email_send_type') || $configuration->getKey('email_send_type') != 2) ? 'default' : 'smtp';

        if ($this->typeSend == "smtp") {

            $this->smtpOptions['host'] = $configuration->getKey('smtpHost'); // smtp.sendgrid.net - ssl://smtp.gmail.com
            $this->smtpOptions['port'] = $configuration->getKey('smtpPort'); // 587 - 465
            $this->smtpOptions['username'] = $configuration->getKey('smtpUsername'); // Eywek
            $this->smtpOptions['password'] = $configuration->getKey('smtpPassword'); // motdepasse
            $this->smtpOptions['timeout'] = '30';
            //$this->smtpOptions['client'] = ''; // mineweb.org

        }

        return $this;
    }

    public function sendMail()
    {

        App::uses('CakeEmail', 'Network/Email');
        $this->Email = new CakeEmail();

        if ($this->typeSend == "smtp") {
            $this->Email->transport('Smtp');

            $this->Email->config($this->smtpOptions);
        }

        $this->Email->from($this->from);
        $this->Email->to($this->to);
        $this->Email->subject($this->subject);
        $this->Email->template('default');
        $this->Email->emailFormat('html');
        $this->Email->theme($this->controller->Configuration->getKey('theme'));

        $event = new CakeEvent('beforeSendMail', $this, array('emailConfig' => $this->Email, 'message' => $this->message));
        $this->controller->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return $event->result;
        }

        return $this->Email->send($this->message);

    }

    public function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    public function isValidImage($request, $extensions = array('png'), $width_max = false, $height_max = false, $max_size = false)
    {

        $Lang = $this->controller->Lang;

        if (empty($request->params['form']['image']['name'])) {
            return array('status' => false, 'msg' => $Lang->get('FORM__EMPTY_IMG'));
        }

        if (!$request->params['form']['image']['size'] || !$request->params['form']['image']['tmp_name']) {
            return array('status' => false, 'msg' => $Lang->get('FORM__NOT_UPLOADED'));
        }

        $extension = pathinfo($request->params['form']['image']['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($extension), $extensions)) {
            return array('status' => false, 'msg' => str_replace('{LIST_EXTENSIONS}', implode(', ', $extensions), $Lang->get('FORM__INVALID_EXTENSION')));
        }

        $infos = getimagesize($request->params['form']['image']['tmp_name']);

        if ($infos[2] < 1 || $infos[2] > 14 || $infos[0] === NULL || $infos[1] === NULL) {
            return array('status' => false, 'msg' => $Lang->get('FORM__INVALID_IMG'));
        }

        if ($max_size) {
            $size = filesize($request->params['form']['image']['tmp_name']);

            if ($size > $max_size) {
                return array('status' => false, 'msg' => str_replace('{MAX_SIZE}', $max_size, $Lang->get('FORM__FILE_TOO_HEAVY')));
            }
        }

        if ($width_max) {
            if ($infos[0] > $width_max) {
                return array('status' => false, 'msg' => str_replace('{MAX_WIDTH}', $width_max, $Lang->get('FORM__INVALID_WIDTH')));
            }
        }

        if ($height_max) {
            if ($infos[1] > $height_max) {
                return array('status' => false, 'msg' => str_replace('{MAX_HEIGHT}', $height_max, $Lang->get('FORM__INVALID_HEIGHT')));
            }
        }

        return array('status' => true, 'infos' => array('extension' => $extension, 'width' => $infos[0], 'height' => $infos[1]));

    }

    public function uploadImage($request, $name)
    {
        $event = new CakeEvent('beforeUploadImage', $this, array('request' => $request, 'name' => $name));
        $this->controller->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return $event->result;
        }

        $path = pathinfo($name);
        $path = $path['dirname'];
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                return false;
            }
        }
        return move_uploaded_file($request->params['form']['image']['tmp_name'], $name);
    }

    public function isValidReCaptcha($code, $ip = null, $secret)
    {
        if (empty($code)) {
            return false; // Si aucun code n'est entré, on s'arrete ici
        }
        $params = [
            'secret' => $secret, // Clé secrète a obtenir sur https://www.google.com/recaptcha/admin
            'response' => $code
        ];
        if ($ip) {
            $params['remoteip'] = $ip;
        }
        $url = "https://www.google.com/recaptcha/api/siteverify?" . http_build_query($params);
        if (function_exists('curl_version')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            $response = curl_exec($curl);
        } else {
            $response = file_get_contents($url);
        }

        if (empty($response) || is_null($response)) {
            return false;
        }

        $json = json_decode($response);
        return $json->success;
    }

    // Retourne un item aléatoire selon sa probabilité

    public function random($list, $probabilityTotal)
    {
        $pct = 1000;
        $rand = mt_rand(0, $pct);
        $items = array();

        foreach ($list as $key => $value) {
            $items[$key] = $value / $probabilityTotal;
        }

        $i = 0;
        asort($items);

        foreach ($items as $name => $value) {
            $item = $name;
            if ($rand <= $i += ($value * $pct)) {
                $item = $name;
                break;
            }
        }
        return $item;
    }


}
