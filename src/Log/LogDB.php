<?php

namespace Synaptic4U\Log;

use Exception;
use Synaptic4U\DB\DB;
use Synaptic4U\DB\MYSQL;

class LogDB
{
    private $db;
    private $msg;
    private $file;
    private $userid;

    public function __construct($msg, $file, $userid)
    {
        try {
            // In DB __construct set to one just for logs database.
            $this->db = new DB(new MYSQL());

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
        $sql = 'insert into logs(userid, type, location, log, params, data, post, calls, result, reply)values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);';

        $log = $this->buildMessage();

        $this->db->query([
            $this->userid,
            $this->file,
            $log['location'],
            json_encode($log['log']),
            $log['params'],
            $log['data'],
            $log['post'],
            $log['calls'],
            $log['result'],
            $log['reply'],
        ], $sql);
    }

    protected function buildMessage()
    {
        $msg = array_shift($this->msg);
        
        $result = [
            'location' => (strchr($msg, 'Synaptic4U\\')) ? str_replace('Synaptic4U\\', '', $msg) : $msg,
            'log' => "\n".strftime('%Y / %m / %d : %H %M %S', time())."\n",
            'params' => null,
            'data' => null,
            'post' => null,
            'calls' => null,
            'result' => null,
            'reply' => null,
        ];

        if (1 == is_array($this->msg)) {
            foreach ($this->msg as $key => $value) {
                $value = (1 == is_array($value)) ? json_encode($value) : $value;
                
                switch ($key) {
                    case 'params':
                        $result['params'] = $value;
                        break;
                    case 'data':
                        $result['data'] = $value;
                        break;
                    case 'post':
                        $result['post'] = $value;
                        break;
                    case 'calls':
                        $result['calls'] = $value;
                        break;
                    case 'result':
                        $result['result'] = $value;
                        break;
                    case 'reply':
                        $result['reply'] = $value;
                        break;
                    default:
                        $result['log'] .= $key.': '.$value."\n";
                        break;
                }
            }
        } else {
            $result['log'] .= $this->msg."\n";
        }

        return $result;
    }
}