<?php

namespace Synaptic4U\DB;

/**
 * Class DB
 *
 * A database handler class that provides methods to execute queries,
 * retrieve the last inserted ID, get the number of rows affected by
 * the last query, and check the status of the database connection.
 *
 * @package Synaptic4U\DB
 */
class DB
{
    private $db;

    /**
     * Constructor for initializing the database interface.
     *
     * @param IDBInterface $db An instance of the database interface.
     */
    public function __construct(IDBInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Executes a database query using the provided parameters and SQL statement.
     *
     * @param mixed $params Parameters to bind to the SQL query.
     * @param string $sql The SQL query to execute.
     * @return mixed The result of the database query.
     */
    public function query($params, $sql)
    {
        return $this->db->query($params, $sql);
    }

    /**
     * Retrieves the last inserted ID from the database.
     *
     * @return int The last inserted ID.
     */
    public function getLastId(): int
    {
        return $this->db->getLastId();
    }

    /**
     * Retrieves the number of rows from the database.
     *
     * @return int The total number of rows.
     */
    public function getrowCount(): int
    {
        return $this->db->getRowCount();
    }

    /**
     * Retrieves the current status from the database.
     *
     * @return mixed The status obtained from the database.
     */
    public function getStatus()
    {
        return $this->db->getStatus();
    }
}
