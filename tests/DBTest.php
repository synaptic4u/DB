<?php
/**
 * Connects to a database and retrieves the row count for each table.
 *
 * This script checks for the presence of the autoload file and includes it if available.
 * It defines a function `getCount` that connects to the database using the DB and MYSQL
 * classes, retrieves all table names, and counts the rows in each table. The function
 * returns an associative array with table names as keys and row counts as values.
 * The script also measures the execution time of the `getCount` function and outputs
 * the results in a formatted manner.
 */

/**
 * Checks for the existence of the Composer autoload file two or four directories up
 * from the current file's location. If it exists, includes it to enable
 * autoloading of classes and dependencies.
 */
if (file_exists(dirname(__FILE__,4) . '/autoload.php')) {
    require_once dirname(__FILE__,4) . '/autoload.php';
    // var_dump(dirname(__FILE__, 4).'/autoload.php');
}else if (file_exists(dirname(__FILE__, 2) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
    // var_dump(dirname(__FILE__, 2).'/vendor/autoload.php');
}

use Synaptic4U\DB\DB;
use Synaptic4U\DB\MYSQL;

/**
 * Retrieves the count of rows for each table in the database.
 *
 * This function connects to the database using the DB and MYSQL classes,
 * retrieves a list of all tables, and then queries each table to count
 * the number of rows. It returns an associative array where the keys
 * are table names and the values are the corresponding row counts.
 *
 * @return array An associative array with table names as keys and row counts as values.
 */
function getCount()
{

    $db = new DB(new MYSQL);

    $table_list = [];
    $table_report = [];

    $sql = 'show tables where 1=?;';
    $result = $db->query([1], $sql);

    foreach ($result as $res) {
        $table_list[] = $res[0];
    }

    foreach ($table_list as $table) {
        $sql = 'select count(*) as num from ' . $table . ' where 1=?;';
        $result = $db->query([1], $sql);
        $table_report[$table] = $result[0]['num'];
    }

    return $table_report;
}

/**
 * Connects to a database and counts all rows in all tables.
 *
 * This script measures the time taken to perform the operation and
 * outputs the statistics including start time, finish time, and duration.
 * It then prints the row count for each table in the database.
 *
 * The results are displayed in a human-readable format using JSON encoding.
 */
print_r('Connects to a DB and counts all rows in all tables.' . PHP_EOL . PHP_EOL);

$start = microtime(true);

$report = getCount();

$finish = microtime(true);

$app_timer = [
    'Date & Time:               ' => date('Y-m-d H:i:s', time()),
    'Start:                     ' => $start,
    'Finish:                    ' => $finish,
    'Duration min:sec:          ' => (($finish - $start) > 60) ? (floor(($finish - $start) / 60)) . ':' . (($finish - $start) % 60) : '0:' . (($finish - $start) % 60),
    'Duration sec.microseconds: ' => $finish - $start,
];

print_r('Test Stats: ' . json_encode($app_timer, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL);

$result = array_merge([
    'Table names' => 'Rows per table',
], $report);

print_r('DB Row count for each table: ' . json_encode($report, JSON_PRETTY_PRINT) . PHP_EOL);