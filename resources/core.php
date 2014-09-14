<?php
define("CORELOADED", true);
define('__CORE__'  , str_replace("\\","/", dirname(__FILE__)));
require_once(__CORE__.'/'."variables.php");
require_once(__CORE__.'/'."settings.php");
date_default_timezone_set(TIMEZONE);
$dir = __CORE__.'/'."functions".'/';
if ($handle = opendir($dir)) {
    $blacklist = array('.', '..'); // don't bother grabbing the parent options
    while (false !== ($file = readdir($handle))) {
        if (!in_array($file, $blacklist)) {
            require_once($dir.$file);
        }
    }
    closedir($handle);
} else {
    die();
}
initialize_debugging();
debug_to_console("CORE LOADED");
?>