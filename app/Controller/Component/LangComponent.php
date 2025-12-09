<?php
App::uses('CakeObject', 'Core');

class LangComponent extends CakeObject
{
    public $components = ['Cookie'];
    public $langFolder;
    public $languages;
    public $lang;
    public $mode = 'config';
    private $controller;

    function __construct()
    {
        $this->langFolder = ROOT . DS . 'lang';

        if (!is_dir(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang')) {
            mkdir(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang', 0775, true);
        }
    }

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
        $this->controller->set('Lang', $this);

        $this->languages = $this->getLanguages();
        $this->lang = $this->getLang();
    }

    public function getLanguages()
    {
        $languages_available = [];

        $dh = opendir($this->langFolder);
        while (false !== ($filename = readdir($dh))) {
            $parts = explode('.', $filename);
            if (count($parts) < 2 || $parts[1] !== "json") {
                continue;
            }

            $fileContent = file_get_contents($this->langFolder . DS . $filename);
            $fileContent = json_decode($fileContent, true);

            if (!empty($fileContent) && $fileContent !== false) {
                if (
                    isset($fileContent['INFORMATIONS']) &&
                    isset($fileContent['INFORMATIONS']['name']) &&
                    isset($fileContent['INFORMATIONS']['author']) &&
                    isset($fileContent['INFORMATIONS']['version']) &&
                    isset($fileContent['MESSAGES'])
                ) {
                    $key = $parts[0];
                    $languages_available[$key]['name'] = $fileContent['INFORMATIONS']['name'];
                    $languages_available[$key]['author'] = $fileContent['INFORMATIONS']['author'];
                    $languages_available[$key]['version'] = $fileContent['INFORMATIONS']['version'];
                    $languages_available[$key]['path'] = $key;
                    $languages_available[$key]['fullpath'] = $this->langFolder . DS . $filename;
                } else {
                    $this->log('Language file : ' . $filename . ' is not a valid lang format.');
                }
            } else {
                $this->log('Language file : ' . $filename . ' is not a valid JSON format.');
            }
        }

        return $languages_available;
    }

    public function getLang($mode = false, $language = null)
    {
        $mode = (!$mode) ? $this->mode : $mode;

        if ($mode == 'cookie') {
            if (isset($_COOKIE['language'])) {
                $language = $_COOKIE['language'];
            } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $headerLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                $language = substr($headerLang, 0, 2);
            }
        } else {
            $this->Configuration = $this->controller->Configuration;
            try {
                $language = $this->Configuration->getKey('lang');
            } catch (Exception $e) {
            }
        }

        if (!isset($language) || empty($language) || !isset($this->languages[$language])) {
            $language = 'fr_FR';
        }

        if (isset($this->languages[$language])) {
            $languageData = $this->languages[$language];
            $langJson = file_get_contents($this->langFolder . DS . $languageData['path'] . '.json');
            $decoded = json_decode($langJson, true);
            $languageData['messages'] = isset($decoded['MESSAGES']) ? $decoded['MESSAGES'] : [];
        } else {
            $languageData = [];
            $languageData['name'] = null;
            $languageData['author'] = null;
            $languageData['version'] = null;
            $languageData['path'] = null;
            $languageData['fullpath'] = $this->langFolder . DS;
            $languageData['messages'] = [];
        }

        $this->EyPlugin = $this->controller->EyPlugin;
        $this->Theme = $this->controller->Theme;
        try {
            $plugins = $this->EyPlugin->getPluginsActive();
            $themes = $this->Theme->getThemesInstalled(false);
        } catch (Exception $e) {
        }

        if (isset($plugins) && !empty($plugins)) {
            foreach ($plugins as $value) {
                $name = $value->slug;
                $language_file = $this->getLangFile($this->EyPlugin->pluginsFolder . DS . $name, $languageData['path']);
                if (isset($languageData['messages']) && is_array($languageData['messages']) && isset($language_file) && is_array($language_file)) {
                    if (isset($language_file['MESSAGES']) && is_array($language_file['MESSAGES'])) {
                        $mergeMessages = $language_file['MESSAGES'];
                    } else {
                        $mergeMessages = $language_file;
                    }
                    $languageData['messages'] = array_merge($languageData['messages'], $mergeMessages);
                }
            }
        }

        if (isset($themes) && !empty($themes)) {
            foreach ($themes as $value) {
                $name = $value->slug;
                $language_file = $this->getLangFile($this->Theme->themesFolder . DS . $name, $languageData['path']);
                if (isset($languageData['messages']) && is_array($languageData['messages']) && isset($language_file) && is_array($language_file)) {
                    if (isset($language_file['MESSAGES']) && is_array($language_file['MESSAGES'])) {
                        $mergeMessages = $language_file['MESSAGES'];
                    } else {
                        $mergeMessages = $language_file;
                    }
                    $languageData['messages'] = array_merge($languageData['messages'], $mergeMessages);
                }
            }
        }

        return $languageData;
    }

    private function getLangFile($path, $lang)
    {
        $language_file = [];

        if (file_exists($path . DS . 'lang' . DS . $lang . '.json')) {
            $language_file = file_get_contents($path . DS . 'lang' . DS . $lang . '.json');
            $language_file = json_decode($language_file, true);
        } elseif (file_exists($path . DS . 'lang' . DS . 'fr_FR.json')) {
            $language_file = file_get_contents($path . DS . 'lang' . DS . 'fr_FR.json');
            $language_file = json_decode($language_file, true);
        }

        return $language_file;
    }

    function startup($controller)
    {
    }

    public function get($msg, $vars = [])
    {
        $language = $this->lang;

        if (!is_array($vars)) {
            $vars = [];
        }

        if (isset($language['messages'][$msg])) {
            return strtr($language['messages'][$msg], $vars);
        }

        return $msg;
    }

    public function set($msg, $value)
    {
        $language = $this->lang;

        $lang = file_get_contents($this->langFolder . DS . $language['path'] . '.json');
        $lang = json_decode($lang, true);

        $lang['MESSAGES'][$msg] = $value;

        $edit = json_encode($lang, JSON_PRETTY_PRINT);
        @file_put_contents($this->langFolder . DS . $language['path'] . '.json', $edit);
    }

    public function setAll($data)
    {
        $language = $this->getAll();
        $path = $this->lang['path'];

        foreach ($data as $key => $value) {
            foreach ($language as $type => $messages) {
                if (isset($messages[$key])) {
                    $language[$type][$key] = $value;
                }
            }
        }

        foreach ($language as $type => $messages) {
            if ($type == "CMS") {
                $JSON = [];
                $JSON['INFORMATIONS']['name'] = $this->lang['name'];
                $JSON['INFORMATIONS']['version'] = $this->lang['version'];
                $JSON['INFORMATIONS']['author'] = $this->lang['author'];

                foreach ($messages as $key => $value) {
                    if (isset($this->lang['messages'][$key]) && $this->lang['messages'][$key] != $value) {
                        if (file_exists(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang' . DS . $path . '.log.json')) {
                            $log = file_get_contents(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang' . DS . $path . '.log.json');
                            $log = json_decode($log, true);
                        } else {
                            $log = [];
                            $log['update'] = [];
                        }

                        $log['update'][$key] = date('Y-m-d H:i:s');

                        if (!is_dir(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang' . DS)) {
                            if (!mkdir(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang' . DS, 0755, true)) {
                                $this->log('Cannot create language log folder');
                            }
                        }
                        @file_put_contents(
                            ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang' . DS . $path . '.log.json',
                            json_encode($log, JSON_PRETTY_PRINT)
                        );
                    }
                }

                $JSON['MESSAGES'] = $messages;

                $fp = @fopen($this->langFolder . DS . $path . '.json', "w+");
                if ($fp !== false) {
                    fwrite($fp, json_encode($JSON, JSON_PRETTY_PRINT));
                    fclose($fp);
                }
            } else {
                if (file_exists($this->EyPlugin->pluginsFolder . DS . 'lang' . DS . $path . '.json')) {
                    $fp = fopen($this->EyPlugin->pluginsFolder . DS . 'lang' . DS . $path . '.json', "w+");
                    fwrite($fp, json_encode($messages, JSON_PRETTY_PRINT));
                    fclose($fp);
                }
            }
        }
    }

    public function getAll()
    {
        $language = $this->lang;

        $lang = file_get_contents($this->langFolder . DS . $language['path'] . '.json');
        $decoded = json_decode($lang, true);
        $messages = [];
        $messages['CMS'] = isset($decoded['MESSAGES']) ? $decoded['MESSAGES'] : [];

        $this->EyPlugin = $this->controller->EyPlugin;

        $plugins = $this->EyPlugin->getPluginsActive();

        if (!empty($plugins)) {
            foreach ($plugins as $value) {
                $name = $value->slug;
                $language_file = null;

                if (file_exists($this->EyPlugin->pluginsFolder . DS . $name . DS . 'lang' . DS . $language['path'] . '.json')) {
                    $language_file = file_get_contents($this->EyPlugin->pluginsFolder . DS . $name . DS . 'lang' . DS . $language['path'] . '.json');
                    $language_file = json_decode($language_file, true);
                } elseif (file_exists($this->EyPlugin->pluginsFolder . DS . $name . DS . 'lang' . DS . 'fr_FR.json')) {
                    $language_file = file_get_contents($this->EyPlugin->pluginsFolder . DS . $name . DS . 'lang' . DS . 'fr_FR.json');
                    $language_file = json_decode($language_file, true);
                }

                if ($language_file === null) {
                    $messages[$name] = [];
                } else {
                    if (isset($language_file['MESSAGES']) && is_array($language_file['MESSAGES'])) {
                        $messages[$name] = $language_file['MESSAGES'];
                    } else {
                        $messages[$name] = $language_file;
                    }
                }
            }
        }

        return $messages;
    }

    public function update($JSON, $file)
    {
        if (!file_exists($file)) {
            $fileRessource = fopen(ROOT . DS . $file, 'w');
            fwrite($fileRessource, $JSON);
            fclose($fileRessource);
            return;
        } else {
            $fileContent = file_get_contents($file);
            $fileContent = json_decode($fileContent, true);

            $newContent = $fileContent;
            $updatedContent = json_decode($JSON, true);

            if (isset($updatedContent['INFORMATIONS']['VERSION'])) {
                $newContent['INFORMATIONS']['VERSION'] = $updatedContent['INFORMATIONS']['VERSION'];
            }

            $array = explode('/', $file);
            $path = end($array);
            $path = explode('.', $path)[0];

            if (file_exists(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang' . DS . $path . '.log.json')) {
                $log = file_get_contents(ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS . 'lang' . DS . $path . '.log.json');
                $log = json_decode($log, true);
            } else {
                $log = [];
                $log['update'] = [];
            }

            if (isset($fileContent['MESSAGES']) && is_array($fileContent['MESSAGES'])) {
                foreach ($fileContent['MESSAGES'] as $key => $value) {
                    if (!isset($log['update'][$key]) && isset($updatedContent['MESSAGES'][$key])) {
                        $newContent['MESSAGES'][$key] = $updatedContent['MESSAGES'][$key];
                    }
                }
            }

            if (isset($updatedContent['MESSAGES']) && is_array($updatedContent['MESSAGES'])) {
                foreach ($updatedContent['MESSAGES'] as $key => $value) {
                    if (!isset($fileContent['MESSAGES'][$key])) {
                        $newContent['MESSAGES'][$key] = $value;
                    }
                }
            }

            $fileRessource = fopen(ROOT . DS . $file, 'w');
            fwrite($fileRessource, json_encode($newContent));
            fclose($fileRessource);

            return;
        }
    }

    function date($date)
    {
        $language = $this->lang;

        if (isset($language['messages']['GLOBAL__FORMAT_DATE'])) {
            $dateParts = explode(' ', $date);
            if (count($dateParts) < 2) {
                return $date;
            }

            $time = explode(':', $dateParts[1]);
            $dayParts = explode('-', $dateParts[0]);

            if (count($time) < 2 || count($dayParts) < 3) {
                return $date;
            }

            $return = str_replace('{%day}', $dayParts[2], $language['messages']['GLOBAL__FORMAT_DATE']);
            $return = str_replace('{%month}', $dayParts[1], $return);
            $return = str_replace('{%year}', $dayParts[0], $return);
            $return = str_replace('{%minutes}', $time[1], $return);

            $if = explode('|', $return);
            if (count($if) < 2) {
                return $date;
            }
            $if = explode('}', $if[1]);
            $formatHour = $if[0];

            if ($formatHour == 12) {
                $hourInt = (int)$time[0];
                if ($hourInt > 12) {
                    $hourInt = $hourInt - 12;
                    $pm_or_am = 'PM';
                } else {
                    $pm_or_am = 'AM';
                }
                $hour = str_pad((string)$hourInt, 2, '0', STR_PAD_LEFT);
                $return = str_replace('{%hour|12}', $hour, $return);
                $return = str_replace('{%PM_OR_AM}', $pm_or_am, $return);
            } elseif ($formatHour == 24) {
                $hour = $time[0];
                $return = str_replace('{%hour|24}', $hour, $return);
            } else {
                $return = 'ERROR';
            }
        } else {
            $return = $date;
        }

        return $return;
    }

    function email_reset($email, $pseudo, $key)
    {
        $msg = "USER__PASSWORD_RESET_EMAIL_CONTENT";
        $language = $this->lang;

        $path = isset($language['path']) && $language['path'] ? $language['path'] : 'fr_FR';

        if (file_exists(ROOT . DS . 'lang' . DS . $path . '.json')) {
            $language_file = file_get_contents(ROOT . DS . 'lang' . DS . $path . '.json');
        } else {
            $language_file = file_get_contents(ROOT . DS . 'lang' . DS . 'fr_FR.json');
        }

        $language_file = json_decode($language_file, true);

        if (isset($language_file['MESSAGES'][$msg])) {
            $template = $language_file['MESSAGES'][$msg];
            $template = str_replace('{EMAIL}', $email, $template);
            $template = str_replace('{PSEUDO}', $pseudo, $template);
            return str_replace('{LINK}', Router::url('/?resetpasswd_' . $key, true), $template);
        } else {
            return $msg;
        }
    }

    function history($action)
    {
        $message = $this->lang['messages']["HISTORY__ACTION_" . $action] ?? null;
        return !$message ? $action : $message;
    }
}
