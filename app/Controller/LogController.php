<?php
App::uses('File', 'Utility');

class LogController extends AppController {
    function admin_error()
    {
        if (!$this->isConnected || !$this->Permissions->can("PERMISSIONS__VIEW_WEBSITE_LOGS"))   
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get("LOG__VIEW_ERROR"));
        $this->layout = 'admin';

        $errorFile = new File(LOGS . "error.log");
        
        if ($errorFile->exists()) {
            $errorFile->open();

            $errorContent = $errorFile->read();
            $errorContent = explode("\n", $errorContent);
            $errors = [];

            $errorNbr = 0;
            foreach($errorContent as $line) {
                if ($line == "") {
                    $errorNbr++;
                    continue;
                }

                $errors[$errorNbr][] = $line;
            }

            $this->set("errorContent", $errors);

            $errorFile->close();
        }
    }

    function admin_debug()
    {
        if (!$this->isConnected || !$this->Permissions->can("PERMISSIONS__VIEW_WEBSITE_LOGS"))   
            throw new ForbiddenException();
            
        $this->set('title_for_layout', $this->Lang->get("LOG__VIEW_DEBUG"));
        $this->layout = 'admin';

        $debugFile = new File(LOGS . "debug.log");
        
        if ($debugFile->exists()) {
            $debugFile->open();

            $debugContent = $debugFile->read();
            $debugContent = explode("\n", $debugContent);
            $debugs = [];

            $debugNbr = 0;
            foreach($debugContent as $line) {
                if ($line == "") {
                    $debugNbr++;
                    continue;
                }

                $debugs[$debugNbr][] = $line;
            }

            $this->set("debugContent", $debugs);

            $debugFile->close();
        }
    }
}
