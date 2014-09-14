<?php if( !defined( 'CORELOADED' ) ) die();

// Returns sanitized user IP address.
function get_IP() {
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if(!empty($_SERVER['HTTP_VIA'])) {
        $ip = $_SERVER['HTTP_VIA'];
    } else if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $ip = preg_replace('/[^0-9a-fA-F:., ]/', '', $ip);
    $ip = filter_var($ip, FILTER_VALIDATE_IP);
    return($ip);
}

// Returns sanitized user agent string.
function get_user_agent() {
    if ( !isset( $_SERVER['HTTP_USER_AGENT'] ) )
        return "";
    $ua = strip_tags( html_entity_decode( $_SERVER['HTTP_USER_AGENT'] ));
    $ua = preg_replace('![^0-9a-zA-Z\':., /{}\(\)\[\]\+@&\!\?;_\-=~\*\#]!', '', $ua );
    return substr( $ua, 0, 254 );
}

// returns the address of the page the user claims to have come from
function get_referrer() {
    $referrer = "";
    if (isset( $_SERVER['HTTP_REFERER']) &&
        filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)
       ) {
        $referrer = $_SERVER['HTTP_REFERER'];
    } elseif($ua && strpos($ua,'FBForIPhone') !== false) {
        // DIRTY HACK TO MAKE FACEBOOK IPHONE APP REPORT PROPERLY
        $referrer = "http://m.facebook.com/l.php";
    }
    
    return ($referrer);
}

// a good-faith effort at getting the important parts of the referrer address for statistical aggregation
// example: http://us6.yp21.mail.yahoo.com/message/?id=3215747 becomes mail.yahoo.com
function get_referrer_host($referrer) {
    if(!$referrer)
        return "";
    $refParts     = parse_url($referrer);
    $refHost      = $refParts['host'];
    if(!$refHost)
        return "";
    // convert common social media shorteners
    $shortDomains = array('t.co'    =>'twitter.com', 'fb.me'  =>'facebook.com', 'lnkd.in'=>'linkedin.com',
                          'youtu.be'=>'youtube.com', 'redd.it'=>'reddit.com');
    if(isset($shortDomains[$refHost])) {
        $refHost = $shortDomains[$refHost];
    } else {
        $hostChanged = false;
        $refHostParts = explode('.', $refHost);
        if(count($refHostParts) > 2) {
            // kill mobile and www subdomains... they dilute stats
            $commonSubdomains = array('www', 'mobile', 'm', 'touch', 'mbasic');
            if(in_array($refHostParts[0], $commonSubdomains)) {
                array_shift($refHostParts);
                $hostChanged = true;
            }
            if(count($refHostParts) == 3) {
                // kill a-z/numbers from email servers... they dilute stats
                // eg: webmail37a.yahoo.com becomes webmail.yahoo.com
                $maybe_mail_str = $refHostParts[0];
                if(strpos($maybe_mail_str, 'mail') !== false) {
                    $refHostParts[0] = preg_replace('/^(mailbox|mail|webmail|email)(.+)?/', '$1', $maybe_mail_str);
                    $hostChanged = true;
                }
            } else {
                // kill subdomains that segment webmail servers... they dilute stats
                // eg: us.webmail.yahoo.com becomes webmail.yahoo.com
                $mail_pos = count($refHostParts)-3;
                $maybe_mail_str = $refHostParts[$mail_pos];
                $commonMailSubs = array('mail', 'webmail', 'mailbox', 'email');
                if(in_array($refHostParts[$mail_pos], $commonMailSubs)) {
                    $refHostParts = array_slice($refHostParts, $mail_pos);
                    $hostChanged = true;
                }
            }
            if($hostChanged)
                $refHost = implode('.', $refHostParts);
        }
    }
    return($refHost);
}

// make sure page is always loaded fresh, never cached
function force_no_cache() {
    if(!headers_sent()) {
        $gmtDate = gmdate('D, d M Y H:i:s').' GMT';
        header('Expires: Thu, 23 Mar 1972 07:00:00 GMT');
        header('Last-Modified: '.$gmtDate);
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}

// redirect unsecured connections to the ssl secure equivalent
function force_ssl() {
    if(!is_ssl()) {
        $url = get_url();
        $url = preg_replace( '/^http:\/\//','https://', $url );
        header_safe_redirect($url);
        die();
    }
}

// starts secure session
function start_secure_session() {
    $session_length = 1440;
    session_name("AUTHSESSION");
    session_set_cookie_params ($session_length, '/', '.'.SHORTDOMAIN, true, true);
    session_start();
    session_regenerate_id(true);
}

// returns the current url, including query string
function get_url() {
    $protocol  = (is_ssl())?"https://":"http://";
    return($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}

// returns the current url, sans query string
function get_current_address() {
    $urlParts = explode('?', get_url());
    $address = array_shift($urlParts);
    return($address);
}

function get_server_address() {
    $protocol = (isset($_SERVER['HTTPS']) && (strcasecmp('off', $_SERVER['HTTPS']) !== 0))?'https':'http';
    $address  = getHostByName(php_uname('n'));
    $port     = $_SERVER['SERVER_PORT'];
    $path     = $_SERVER['REQUEST_URI'];
    $totrim   = substr(strrchr($path,'/'), 1);
    $uri      = substr($path, 0, -1*strlen($totrim));
    echo $protocol.'://'.$address.':'.$port.$uri;
}

// tests whether the connection is secure or not
function is_ssl() {
    if( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ) {
        $proto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']);
        if ( $proto == 'https' )
            return(true);
    } elseif ( isset($_SERVER['HTTPS']) ) {
        if ( strtolower($_SERVER['HTTPS']) == 'on' )
            return true;
        if ( $_SERVER['HTTPS'] === '1' )
            return true;
    } elseif ( isset($_SERVER['SERVER_PORT']) && ( $_SERVER['SERVER_PORT'] == 443 ) ) {
        return true;
    }
    return false;
}

// returns the contents of the root of the domain
function get_dir_contents($dir) {
    $contents = array();
    if ($handle = opendir($dir)) {
        $blacklist = array('.', '..'); // don't bother grabbing the parent options
        while (false !== ($file = readdir($handle))) {
            if (!in_array($file, $blacklist)) {
                $contents[] = $file;
            }
        }
        closedir($handle);
    }
    return($contents);
}

// redirect user's browser to $address, whether or not headers have already been sent
function header_safe_redirect($address) {
    if(!headers_sent()) {
        header("Location: ".$address);
        die();
    } else {
        start_output_rendering();
        echo "<html><head><script type=\"text/javascript\">window.location.href='".$address."';</script></head><body>";
        echo "<center><h1>You are being redirected</h1>";
        echo "<p>Please wait while we direct you to <a href=\"".$address."\">".$address."</a></p>";
        echo "<p>If this page doesn't change within a few seconds, please click the link above to manually redirect.</p>";
        echo "</center></body></html>";
        end_output_rendering();
    }
}

// capture all output before sending to browser
function start_output_rendering() {
    if(!debugging_enabled())
        ob_start('ob_gzhandler');
}

// send captured output to browser and end processing
function end_output_rendering() {
    if(!debugging_enabled())
        ob_end_flush();
    die();
}

?>