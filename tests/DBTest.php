<?php

use Synaptic4U\DB\DB;
use Synaptic4U\DB\MYSQL;

/**
 * TEST
 * Progress Report.
 * Called from the command line.
 * Calculates the variance with a 10 second time delay to find which table is being written too.
 * Produces a summary of all the tables in the database, showing the number of rows in each table.
 */
if (file_exists(dirname(__FILE__, 2).'/vendor/autoload.php')) {
    require_once dirname(__FILE__, 2).'/vendor/autoload.php';
    // var_dump(dirname(__FILE__, 1).'/vendor/autoload.php');
}


/**
 * Gets a list of all the tables.
 * Cycles through the list querying the row count for each table.
 *
 * @return $table_report object - each row in object has row count
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
        $sql = 'select count(*) as num from '.$table.' where 1=?;';
        $result = $db->query([1], $sql);
        $table_report[$table] = $result[0]['num'];
    }

    return $table_report;
}

    print_r('Connects to a DB and counts all rows in all tables.'.PHP_EOL.PHP_EOL);


    $start = microtime(true);

    $report = getCount();

    $finish = microtime(true);

    $app_timer = [
        'Date & Time:               ' => date('Y-m-d H:i:s', time()),
        'Start:                     ' => $start,
        'Finish:                    ' => $finish,
        'Duration min:sec:          ' => (($finish - $start) > 60) ? (floor(($finish - $start) / 60)).':'.(($finish - $start) % 60) : '0:'.(($finish - $start) % 60),
        'Duration sec.microseconds: ' => $finish - $start,
    ];

    print_r('Test Stats: '.json_encode($app_timer, JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL);

    $result = array_merge([
        'Table names' => 'Rows per table',
    ], $report);

    print_r('DB Row count for each table: '.json_encode($report, JSON_PRETTY_PRINT).PHP_EOL);