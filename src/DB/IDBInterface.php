<?php

namespace Synaptic4U\DB;

/**
 * Interface IDBInterface
 *
 * Provides a contract for database operations including executing queries,
 * retrieving the last inserted ID, getting the number of affected rows,
 * and checking the status of the database connection.
 *
 * @package Synaptic4U\DB
 */
interface IDBInterface
{
    public function query($params, $sql);

    public function getLastId(): int;

    public function getrowCount(): int;

    public function getStatus();
}
