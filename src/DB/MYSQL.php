<?php

namespace Synaptic4U\DB;

use Exception;
use PDO;
use Synaptic4U\Log\Log;

/**
 * Class MYSQL
 *
 * This class implements the IDBInterface for managing MySQL database connections and operations.
 * It handles the creation of a PDO instance for database interaction, executes SQL queries,
 * and provides methods to retrieve the last inserted ID, row count, and query execution status.
 * Error and activity logging are also supported.
 *
 * Methods:
 * - __construct(): Initializes the database connection using configuration from a JSON file.
 * - query($params, $sql): Executes a SQL query with parameters, manages transactions, and returns the result set.
 * - getLastId(): Returns the ID of the last inserted row.
 * - getrowCount(): Returns the number of rows affected by the last query.
 * - getStatus(): Returns the execution status of the last query.
 * - error($msg): Logs error messages.
 * - log($msg): Logs activity messages.
 *
 * @package Synaptic4U\DB
 */
class MYSQL implements IDBInterface
{
    private $lastinsertid = -1;
    private $rowcount = -1;
    private $conn;
    private $status;
    private $pdo;

    /**
     * Initializes a new instance of the class, establishing a connection to a MySQL database.
     *
     * This constructor attempts to read database configuration from a JSON file located
     * three directories up from the current file. It then creates a PDO instance using
     * the retrieved configuration details. If an error occurs during this process, it
     * logs the error details.
     *
     * @throws Exception If there is an error reading the configuration file or establishing
     *                   the database connection.
     */
    public function __construct()
    {
        try {
            $filepath = dirname(__FILE__, 3) . '/config_db.json';

            //  Returns associative array.
            $this->conn = json_decode(file_get_contents($filepath), true);

            $dsn = 'mysql:host=' . $this->conn['host'] . ';dbname=' . $this->conn['dbname'];

            //  Create PDO instance.
            $this->pdo = new PDO($dsn, $this->conn['user'], $this->conn['pass']);
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__ . '()',
                'error' => $e->__toString(),
            ]);

            $result = null;
        }
    }

    /**
     * Executes a SQL query with the provided parameters and returns the result set.
     *
     * Initiates a transaction, prepares and executes the SQL statement, and fetches
     * all results. Commits the transaction if successful, otherwise rolls back and
     * logs the error details. Returns the fetched results or null if an error occurs.
     *
     * @param array $params Parameters to bind to the SQL statement.
     * @param string $sql The SQL query to execute.
     * @return array|null The result set as an array, or null if an error occurs.
     */
    public function query($params, $sql)
    {
        try {
            $stmt = null;
            $result = [];
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);

            $this->status = ($stmt->execute($params)) ? 'true' : 'false';

            $this->lastinsertid = $this->pdo->lastInsertId();

            $this->pdo->commit();

            $this->rowcount = $stmt->rowCount();

            $result = $stmt->fetchAll();

            $stmt = null;
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__ . '()',
                'pdo->errorInfo' => $this->pdo->errorInfo(),
                'error' => $e->__toString(),
                'stmt' => $stmt,
                'sql' => $sql,
                'params' => $params,
            ]);

            $result = null;
            $stmt = null;
            $this->pdo = null;
        } finally {
            return $result;
        }
    }

    /**
     * Retrieves the last inserted ID.
     *
     * @return int The ID of the last inserted record.
     */
    public function getLastId(): int
    {
        return $this->lastinsertid;
    }

    /**
     * Retrieves the number of rows affected by the last operation.
     *
     * @return int The count of rows affected.
     */
    public function getrowCount(): int
    {
        return $this->rowcount;
    }

    /**
     * Retrieves the current status of the operation.
     *
     * @return mixed The status of the operation.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    private function error($msg)
    {
        new Log($msg, 'error');
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    private function log($msg)
    {
        new Log($msg);
    }
}
