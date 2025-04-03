<?php

namespace Synaptic4U\Log;

/**
 * Interface for logging operations.
 *
 * Defines the constructor method for initializing a log entry with a message,
 * file, user ID, and log type.
 *
 * @param string $msg The log message.
 * @param string $file The file where the log is recorded.
 * @param int $userid The ID of the user associated with the log.
 * @param string $type The type of log entry.
 */
interface LogInterface
{
    public function __construct($msg, $file, $userid, $type);
}