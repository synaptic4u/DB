<?php

namespace Synaptic4U\Log;

/**
 * Initializes a new Log instance and creates a log entry.
 *
 * @param string $msg The message to be logged.
 * @param string $file The log file name, default is 'activity'.
 * @param int $userid The user ID associated with the log, default is 3.
 */
class Log
{
    public function __construct($msg, $file = 'activity', $userid = 3)
    {
        new LogFile($msg, $file, $userid);
        
        if (!('database' === (string) $file)) {

            
            new LogDB($msg, $file, $userid);
        }
    }
}