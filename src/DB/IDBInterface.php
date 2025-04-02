<?php

namespace Synaptic4U\DB;

interface IDBInterface
{
    public function query($params, $sql);

    public function getLastId(): int;

    public function getrowCount(): int;

    public function getStatus();
}
