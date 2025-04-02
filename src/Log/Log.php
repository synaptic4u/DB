<?php

namespace Synaptic4U\Log;

class Log
{
    public function __construct($msg, $file = 'activity', $userid = 3)
    {
        new LogFile($msg, $file, $userid);
        // new LogDB($msg, $file, $userid);
    }
}