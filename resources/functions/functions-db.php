<?php if(!defined('CORELOADED')) die();
require_once(__CORE__."/libraries/ezsql/ez_sql_core.php");
require_once(__CORE__."/libraries/ezsql/ez_sql_sqlite3.php");

// connect to the database, and return a handle
function db_connect() {
    $db =  new ezSQL_sqlite3(DB_PATH, DB_FILENAME);
    if ( $db->last_error ) {
        debug_to_console("DATABASE FAILED TO INITIALIZE");
        die();
    }
    return $db;
}

$db = db_connect();
?>