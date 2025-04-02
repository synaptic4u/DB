<?php

namespace Synaptic4U\DB\MYSQL;

use PDO;
use Exception;
use Synaptic4U\Log\Log;
use Synaptic4U\Log\Error;
use Synaptic4U\Log\Activity;
use Synaptic4U\DB\IDBInterface;

class MYSQL implements IDBInterface
{
    private $lastinsertid = -1;
    private $rowcount = -1;
    private $conn;
    private $status;
    private $pdo;

    public function __construct()
    {
        try {
            $filepath = dirname(__FILE__, 4).'/db_config.json';

            //  Returns associative array.
            $this->conn = json_decode(file_get_contents($filepath), true);
            
            $this->log([
                'Location' => __METHOD__.'()',
                'conn' => $this->conn,
            ]);

            $dsn = 'mysql:host='.$this->conn['host'].';dbname='.$this->conn['dbname'];

            //  Create PDO instance.
            $this->pdo = new PDO($dsn, $this->conn['user'], $this->conn['pass']);
        } catch (Exception $e) {
            $this->error([
                'Location' => __METHOD__.'()',
                'error' => $e->__toString(),
            ]);

            $result = null;
        }
    }

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
                'Location' => __METHOD__.'()',
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

    public function getLastId(): int
    {
        return $this->lastinsertid;
    }

    public function getrowCount(): int
    {
        return $this->rowcount;
    }

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
