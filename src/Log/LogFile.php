<?php

namespace Synaptic4U\Log;

use Exception;


/**
 * Class LogFile
 *
 * Handles logging messages to a specified file. The log entry includes a timestamp,
 * user ID, and the message content. If the message is an array, it is formatted
 * as a JSON string. The log file is stored in a 'logs' directory relative to the
 * project's root.
 *
 * Methods:
 * - __construct($msg, $file, $userid): Initializes the log message, file, and user ID, then writes the log entry.
 * - writeLog(): Opens the log file in append mode, writes the constructed log message, and then closes the file.
 * - buildPath(): Constructs the file path for the log file by navigating three directories up from the current file's location and appending the 'logs' directory and the file name with a '.txt' extension.
 * - buildMessage(): Builds a formatted log message string that includes the current timestamp, user ID, and the log message. If the log message is an array, it is iterated over and each key-value pair is appended to the message. Nested arrays are JSON encoded for readability.
 *
 * @package Synaptic4U\Log
 */
class LogFile
{

    private $msg;
    private $file;
    private $userid;

    /**
     * Constructs a new instance of the log file handler.
     *
     * @param string $msg The message to be logged.
     * @param string $file The file where the log will be written.
     * @param int $userid The ID of the user associated with the log entry.
     *
     * Initializes the log message, file, and user ID, then writes the log entry.
     * If an exception occurs, it suggests checking the Apache2 error log.
     */
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

    /**
     * Writes a log entry to a file.
     *
     * Opens the log file in append mode, writes the constructed log message,
     * and then closes the file.
     */
    protected function writeLog()
    {
        $log = fopen($this->buildPath(), 'a');

        fwrite($log, $this->buildMessage());

        fclose($log);
    }

    /**
     * Constructs the file path for the log file.
     *
     * This method generates the path to the log file by navigating
     * three directories up from the current file's location and appending
     * the 'logs' directory and the file name with a '.txt' extension.
     *
     * @return string The constructed file path for the log file.
     */
    protected function buildPath()
    {
        // $root = realpath($_SERVER['DOCUMENT_ROOT']);
        // $app = '/../app/src/logs/';

        // return $root.$app.$this->file.'.txt';

        $filepath = dirname(__FILE__, 3) . '/logs/' . $this->file . '.txt';
        return $filepath;
    }

    /**
     * Builds a formatted log message string.
     *
     * The message includes the current timestamp, user ID, and the log message.
     * If the log message is an array, it is iterated over and each key-value pair
     * is appended to the message. Nested arrays are JSON encoded for readability.
     *
     * @return string The formatted log message.
     */
    protected function buildMessage()
    {
        $date = date('Y-m-d H:i:s');

        $message = "\n" . $date . "\n";

        $message .= 'userid: ' . $this->userid . "\n";

        if (1 == is_array($this->msg)) {
            foreach ($this->msg as $key => $value) {
                $value = (1 == is_array($value)) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
                $message .= $key . ': ' . $value . "\n";
            }
        } else {
            $message .= $this->msg . "\n";
        }

        return $message;
    }
}
