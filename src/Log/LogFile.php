<?php

namespace Synaptic4U\Log;

use Exception;

class LogFile
{
    private $msg;
    private $file;
    private $userid;

    public function __construct($msg, $file, $userid)
    {
        try {
            $this->msg = $msg;

            $this->file = $file;

            $this->userid = $userid;

            $this->writeLog();
        } catch (Exception $e) {
            //  This is the log file, so...? Go look in Apache2 error log!
        }
    }

    protected function writeLog()
    {
        $log = fopen($this->buildPath(), 'a');

        fwrite($log, $this->buildMessage());

        fclose($log);
    }

    protected function buildPath()
    {
        // $root = realpath($_SERVER['DOCUMENT_ROOT']);
        // $app = '/../app/src/logs/';

        // return $root.$app.$this->file.'.txt';

        $filepath = dirname(__FILE__, 3).'/logs/'.$this->file.'.txt';
        return $filepath;
    }

    protected function buildMessage()
    {
        $date = date('Y-m-d H:i:s');

        $message = "\n".$date."\n";

        $message .= 'userid: '.$this->userid."\n";

        if (1 == is_array($this->msg)) {
            foreach ($this->msg as $key => $value) {
                $value = (1 == is_array($value)) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
                $message .= $key.': '.$value."\n";
            }
        } else {
            $message .= $this->msg."\n";
        }

        return $message;
    }
}
