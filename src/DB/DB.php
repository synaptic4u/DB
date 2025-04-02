<?php

namespace Synaptic4U\DB;

class DB
{
    private $db;

    public function __construct(IDBInterface $db)
    {
        $this->db = $db;
    }

    public function query($params, $sql)
    {
        return $this->db->query($params, $sql);
    }

    public function getLastId(): int
    {
        return $this->db->getLastId();
    }

    public function getrowCount(): int
    {
        return $this->db->getRowCount();
    }

    public function getStatus()
    {
        return $this->db->getStatus();
    }
}
