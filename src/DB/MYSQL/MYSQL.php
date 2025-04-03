<?php

namespace Synaptic4U\DB\MYSQL;

use PDO;
use Exception;
use Synaptic4U\Log\Log;
use Synaptic4U\Log\Error;
use Synaptic4U\Log\Activity;
use Synaptic4U\DB\IDBInterface;

/**
 * Class MYSQL
 *
 * This class implements the IDBInterface for MySQL database operations.
 * It handles database connections, query execution, and logging of activities and errors.
 * The class uses PDO for database interactions and logs messages using the Log class.
 *
 * Methods:
 * - __construct(): Initializes the database connection using configuration from a JSON file.
 * - query(array $params, string $sql): Executes a SQL query with the provided parameters, manages transactions, and logs errors.
 * - getLastId(): Returns the ID of the last inserted row.
 * - getrowCount(): Returns the number of rows affected by the last query.
 * - getStatus(): Returns the status of the last executed query.
 * - error(array $msg): Logs error messages.
 * - log(array $msg): Logs activity messages.
 *
 * @package Synaptic4U\DB\MYSQL
 */
class MYSQL implements IDBInterface
{
    private $lastinsertid = -1;
    private $rowcount = -1;
    private $conn;
    private $status;
    private $pdo;

    /**
     * Initializes a new instance of the class.
     *
     * This constructor attempts to load database configuration from a JSON file
     * and establish a PDO connection using the retrieved credentials. It logs
     * the connection details and handles any exceptions by logging the error.
     *
     * @throws Exception If there is an error reading the configuration file or
     *                   establishing the database connection.
     */
    public function __construct()
    {
        try {
            $filepath = dirname(__FILE__, 4) . '/db_config.json';

            //  Returns associative array.
            $this->conn = json_decode(file_get_contents($filepath), true);

            $this->log([
                'Location' => __METHOD__ . '()',
                'conn' => $this->conn,
            ]);

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
     * Executes a SQL query using prepared statements and returns the result set.
     *
     * Initiates a transaction, prepares and executes the given SQL statement with
     * the provided parameters. Commits the transaction if parameters are present.
     * Captures and logs any exceptions that occur during execution.
     *
     * @param array $params An array of parameters to bind to the SQL statement.
     * @param string $sql The SQL query to be executed.
     * @return array|null The result set as an array, or null if an error occurs.
     */
    public function query($params, $sql)
    {
        try {
            $result = [];
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);

            $this->status = ($stmt->execute($params)) ? 'true' : 'false';

            $this->lastinsertid = $this->pdo->lastInsertId();

            if (sizeof($params) > 0) {
                $this->pdo->commit();
            }

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
     * Retrieves the current row count.
     *
     * @return int The number of rows.
     */
    public function getrowCount(): int
    {
        return $this->rowcount;
    }

    /**
     * Retrieves the current status.
     *
     * @return mixed The current status value.
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
