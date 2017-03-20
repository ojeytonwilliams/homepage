<?php ob_start(); ?>
<?php session_cache_limiter('nocache'); ?>
<?php session_start(); ?>
<?php error_reporting(0); ?>
<?php session_destroy(); ?>
<?php
# BEGIN HEADERS
header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden', true, 403);
header('Status: 403 Forbidden');
header('Content-type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate' ); 
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache' );
# END HEADERS
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>403 Forbidden</title>
<style type="text/css">
<!--
body { 
	/* If you want to add a background image uncomment the CSS properties below */
	/* background-image:url(http://www.example.com/path-to-some-image-file/example-image-file.jpg); /*
	/* background-repeat:repeat; */
	background-color:#CCCCCC;
	line-height: normal;
}

#bpsMessage {
	text-align:center; 
	background-color: #F7F8F9; 
	border:5px solid #000000; 
	padding:10px;
}

p {
    font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size:18px;
	font-weight:bold;
}
-->
</style>
</head>

<body>
<div id="bpsMessage"> 
	<p><?php $bps_hostname = str_replace( 'www.', '', htmlspecialchars( $_SERVER['SERVER_NAME'], ENT_QUOTES ) );
	echo $bps_hostname; ?> 403 Forbidden Error Page</p>
	<p>If you arrived here due to a search or clicking on a link click your Browser's back button to return to the previous page. Thank you.</p>
    <p>IP Address: <?php echo htmlspecialchars( $_SERVER['REMOTE_ADDR'], ENT_QUOTES ); ?></p>
</div>

<?php
if ( file_exists( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' ) ) {
	require_once('../../../wp-load.php');
}

// NOTE: fwrite is faster in benchmark tests than file_put_contents for successive writes
$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
$hostname = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
$timeNow = time();
$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	
	$post_limit = get_option('bulletproof_security_options_sec_log_post_limit'); 

	if ( $post_limit['bps_security_log_post_limit'] == '1' ) {
		$request_body = file_get_contents( 'php://input', NULL, NULL, 0, 500 );
	} else {
		$request_body = file_get_contents( 'php://input', NULL, NULL, 0, 250000 ); // roughly 250KB Max Limit
	}

	if ( ! get_option( 'gmt_offset' ) ) {
		$timestamp = date("F j, Y g:i a", time() );
	} else {
		$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	}

	// BPS .52.5: Do not log test 403 errors for /mod-test/ Apache Module testing
	if ( ! preg_match('/wp-content\/plugins\/bulletproof-security\/admin\/mod-test/', $_SERVER['REQUEST_URI'] ) ) {

	// .52.7: Request Body condition added
	if ( ! empty($request_body) ) {

	if ( preg_match_all('/(.*)\/plugins\/(.*)\.js|(.*)\/plugins\/(.*)\.json|(.*)\/plugins\/(.*)\.php|(.*)\/plugins\/(.*)\.swf/', $_SERVER['REQUEST_URI'], $matches ) ) {
		$event = 'PSBR-HPRA';
		$solution = 'https://forum.ait-pro.com/forums/topic/security-log-event-codes/';
	}
	elseif ( preg_match('/(.*)\/wp-admin\/(.*)\.php/', $_SERVER['REQUEST_URI'], $matches ) || @preg_match('/(.*)\/wp-admin\/(.*)\.php/', $_SERVER['HTTP_REFERER'], $matches ) ) {
		$event = 'WPADMIN-SBR';
		$solution = 'https://forum.ait-pro.com/forums/topic/security-log-event-codes/';	
	
	} else {
		$event = 'BFHS - Blocked/Forbidden Hacker or Spammer';
		$solution = 'N/A - Hacker/Spammer Blocked/Forbidden';
	}

@$log_contents = "\r\n" . '[403 POST Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR']."\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: '.$_SERVER['SERVER_PROTOCOL']."\r\n" . 'HTTP_CLIENT_IP: '.$_SERVER['HTTP_CLIENT_IP']."\r\n" . 'HTTP_FORWARDED: '.$_SERVER['HTTP_FORWARDED']."\r\n" . 'HTTP_X_FORWARDED_FOR: '.$_SERVER['HTTP_X_FORWARDED_FOR']."\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: '.$_SERVER['HTTP_X_CLUSTER_CLIENT_IP']."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$_SERVER['HTTP_REFERER']."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$_SERVER['QUERY_STRING']."\r\n" . 'HTTP_USER_AGENT: '.$_SERVER['HTTP_USER_AGENT'] . "\r\n" . 'REQUEST BODY: ' . $request_body . "\r\n";

	if ( is_writable( $bpsProLog ) ) {

	if ( ! $handle = fopen( $bpsProLog, 'a' ) ) {
         exit;
    }

    if ( fwrite( $handle, $log_contents) === FALSE ) {
        exit;
    }

    fclose($handle);
	}
	}

	if ( empty($request_body) ) {
# BEGIN USERAGENT FILTER
if ( @!preg_match('/BPSUserAgentPlaceHolder/', $_SERVER['HTTP_USER_AGENT']) ) {
# END USERAGENT FILTER

	if ( preg_match_all('/(.*)\/plugins\/(.*)\.js|(.*)\/plugins\/(.*)\.php|(.*)\/plugins\/(.*)\.swf/', $_SERVER['REQUEST_URI'], $matches ) ) {
		$event = 'PSBR-HPRA';
		$solution = 'https://forum.ait-pro.com/forums/topic/security-log-event-codes/';
	}
	elseif ( preg_match('/(.*)\/wp-admin\/(.*)\.php/', $_SERVER['REQUEST_URI'], $matches ) || @preg_match('/(.*)\/wp-admin\/(.*)\.php/', $_SERVER['HTTP_REFERER'], $matches ) ) {
		$event = 'WPADMIN-SBR';
		$solution = 'https://forum.ait-pro.com/forums/topic/security-log-event-codes/';	
	
	} else {
		$event = 'BFHS - Blocked/Forbidden Hacker or Spammer';
		$solution = 'N/A - Hacker/Spammer Blocked/Forbidden';
	}

@$log_contents = "\r\n" . '[403 GET Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR']."\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: '.$_SERVER['SERVER_PROTOCOL']."\r\n" . 'HTTP_CLIENT_IP: '.$_SERVER['HTTP_CLIENT_IP']."\r\n" . 'HTTP_FORWARDED: '.$_SERVER['HTTP_FORWARDED']."\r\n" . 'HTTP_X_FORWARDED_FOR: '.$_SERVER['HTTP_X_FORWARDED_FOR']."\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: '.$_SERVER['HTTP_X_CLUSTER_CLIENT_IP']."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$_SERVER['HTTP_REFERER']."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$_SERVER['QUERY_STRING']."\r\n" . 'HTTP_USER_AGENT: '.$_SERVER['HTTP_USER_AGENT']."\r\n";

	if ( is_writable( $bpsProLog ) ) {

	if ( ! $handle = fopen( $bpsProLog, 'a' ) ) {
         exit;
    }

    if ( fwrite( $handle, $log_contents) === FALSE ) {
        exit;
    }

    fclose($handle);
	}
	}
	}
	}
?>
</body>
</html>
<?php ob_end_flush(); ?>