<?php if( !defined( 'CORELOADED' ) ) die();

$debugging_mode = false; // global variables are bad

// Activate debugging
// For live site's security, disable DEBUG_MODE 
function initialize_debugging() {
    global $debugging_mode;
    if(DEBUG_MODE) $debugging_mode = true;
}

// Return status of debugging command
function debugging_enabled() {
    global $debugging_mode;
    return($debugging_mode);
}

// Print $data to the user's browser's javascript console log, if DEBUG_MODE is enabled
// CAUTION: DOES NOT COMPLY TO WEB STANDARDS.
// COULD CAUSE ODD PAGE BEHAVIOR WHILE IN DEBUG MODE
function debug_to_console($data) {
    if(debugging_enabled()) {
        $lninfo = debug_get_line_info();
        $outputArray = ($data !== false)?explode("\n", print_r($data, true)):array("FALSE");
        
        foreach($outputArray as $line) {
            echo "<script>console.log( \"PHP Debug (".$lninfo."): ".addslashes($line)."\" );</script>\n";
        }
    }
}

function debug_get_line_info() {
    $backtrace = debug_backtrace();
    $linenum   = $backtrace[1]['line'];
    $filename  = $backtrace[1]['file'];
    $baseDir   = dirname(__CORE__);
    if (substr($filename, 0, strlen($baseDir)) == $baseDir) {
        $filename = substr($filename, strlen($baseDir));
    }
    return($filename." Ln:".$linenum);
}
?>