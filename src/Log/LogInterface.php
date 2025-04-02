<?php

namespace Synaptic4U\Log;

interface LogInterface
{
    public function __construct($msg, $file, $userid, $type);
}