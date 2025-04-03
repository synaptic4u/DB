<?php

namespace Synaptic4U\Log;

use Exception;
use Synaptic4U\DB\DB;
use Synaptic4U\DB\MYSQL;

/**
 * Class LogDB
 *
 * Handles logging operations to a database. Initializes a database connection
 * and writes log entries with specified message, file, and user ID.
 *
 * @package Synaptic4U\Log
 *
 * @property DB $db Database connection instance.
 * @property mixed $msg Log message details.
 * @property string $file File associated with the log entry.
 * @property mixed $userid User ID associated with the log entry.
 *
 * @method void __construct(mixed $msg, string $file, mixed $userid) Initializes the LogDB instance and writes a log entry.
 * @method void writeLog() Writes a log entry to the database.
 * @method array buildMessage() Constructs the log message details.
 */
class LogDB
{
    private $db;
    private $msg;
    private $file;
    private $userid;

    /**
     * Constructs a new log entry and writes it to the database.
     *
     * Initializes a database connection using the organization's DB and MYSQL classes,
     * assigns the provided message, file, and user ID to the instance, and writes the log.
     *
     * @param string $msg The log message.
     * @param string $file The file associated with the log entry.
     * @param int $userid The ID of the user associated with the log entry.
     * @throws Exception If an error occurs during the logging process.
     */
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

    /**
     * Writes a log entry to the database.
     *
     * This method constructs a log message and inserts it into the 'logs' table
     * with various parameters such as user ID, file location, and log details.
     * The log data is encoded in JSON format before insertion.
     *
     * @throws Exception If the database query fails.
     */
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

    /**
     * Builds a structured log message from the current message array.
     *
     * The method processes the first element of the message array to determine
     * the location and constructs a log entry with a timestamp. It iterates
     * through the message array to populate specific keys ('params', 'data',
     * 'post', 'calls', 'result', 'reply') in the result array. If the message
     * array contains additional keys, they are appended to the log entry.
     *
     * @return array An associative array containing the structured log message
     *               with keys: 'location', 'log', 'params', 'data', 'post',
     *               'calls', 'result', and 'reply'.
     */
    protected function buildMessage()
    {
        $msg = array_shift($this->msg);

        $result = [
            'location' => (strchr($msg, 'Synaptic4U\\')) ? str_replace('Synaptic4U\\', '', $msg) : $msg,
            'log' => "\n" . strftime('%Y / %m / %d : %H %M %S', time()) . "\n",
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
                        $result['log'] .= $key . ': ' . $value . "\n";
                        break;
                }
            }
        } else {
            $result['log'] .= $this->msg . "\n";
        }

        return $result;
    }
}