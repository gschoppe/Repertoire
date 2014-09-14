<?php if( !defined( 'CORELOADED' ) ) die();

// returns Selected POST variable in an HTML-safe manner
// NOTE: Always wrap in DOUBLE quotes
function print_POST($name) {
    if(!isset($_POST[$name])) return "";
    $var  = $_POST[$name];
    $safe = htmlspecialchars($var);
    return($safe);
}

// returns Selected GET variable in an HTML-safe manner
// NOTE: Always wrap in DOUBLE quotes
function print_GET($name) {
    if(!isset($_GET[$name])) return "";
    $var  = $_GET[$name];
    $safe = htmlspecialchars($var);
    return($safe);
}

// returns Selected DB variable in an HTML-safe manner
// NOTE: Always wrap in DOUBLE quotes
function print_DB($var) {
    if($var===0 || $var==='0') return "0";
    if(!$var) return "";
    $safe = htmlspecialchars($var);
    return($safe);
}

// returns an HTML-safe version of the first defined, non-falsey argument
function print_OR(&$highp, &$lowp, $default=null) {
    if(isset($highp)&&($highp||$highp===0)) return htmlspecialchars($highp);
    if(isset($lowp )&&($lowp ||$lowp ===0)) return htmlspecialchars($lowp);
    return htmlspecialchars($default);
}

// outputs abbreviated number with short scale postfixes (K for Thousand, M for million)
function format_abbreviate_number($val, $digits=4) {
    $val      = filter_var ($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $postfixes = array('', 'K', 'M', 'Bn', 'Tr');
    if(!is_numeric($val)){ return false;}
    $i = 0;
    while($val >= 1000 && $i < count($postfixes)) {
        $val = $val/1000;
        $i++;
    }
    $post = $postfixes[$i];
    $string = ''.$val.$post;
    $i=$digits;
    while(strlen($string) > $digits && $i>=0) {
        $string = ''.round($val,$i).$post;
        $i--;
    }
    
    return($string);
}

// Takes a file size in bytes
// returns string representation in proper unit, to two decimal points
function format_file_size($bytes = 0, $blockFormat = false, $tippingPoint = 768) {
    $postFixList = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    if($tippingPoint > 1000)
        $tippingPoint = 1000;
    $temp = $bytes;
    $i = 0;
    while ($temp >= $tippingPoint && $i < count($postFixList)-1) { // display in bytes
        $temp = $temp / 1024;
        $i++;
    }
    $val     = round($temp,2); // format to max of two decimal places
    $postfix = $postFixList[$i];
    if($blockFormat) {
        $val = number_format((float)$val, 2, '.', ',');
        $val = pad_str_to_length($val, 6);
        while(strlen($postfix) < 2)
            $postfix .=' ';
    }
    return("".$temp.$postfix);
}

// add leading characters to a string
function pad_str_to_length($str, $numChars=0, $padChar=' ') {
    //handle integers
    if($str === 0) {
        $str = "0";
    } else {
        $str = "".$str;
    }
    while(strlen($str)<$numChars) {
        $str = "".$padChar.$str;
    }
    return $str;
}

?>