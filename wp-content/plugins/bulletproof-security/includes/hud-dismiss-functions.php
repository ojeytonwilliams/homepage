<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// HUD Alerts in WP Dashboard
function bps_HUD_WP_Dashboard() {
	
	if ( current_user_can('manage_options') ) { 
		$plugin_var_w3tc = 'w3-total-cache/w3-total-cache.php';
		$plugin_var_wpsc = 'wp-super-cache/wp-cache.php';
		echo bps_check_php_version_error();
		echo bps_check_safemode();
		echo bps_check_permalinks_error();
		echo bps_check_iis_supports_permalinks();
		echo bps_hud_check_bpsbackup();
		echo bpsPro_bonus_custom_code_dismiss_notices();
		echo bps_hud_PhpiniHandlerCheck();
		echo bps_hud_check_sucuri();
		echo bps_hud_check_wordpress_firewall2();
		echo bps_hud_broken_link_checker();
		echo bps_hud_check_jetpack();
		echo bps_hud_check_woocommerce();
		echo bpsPro_hud_woocommerce_enable_lsm_jtc();
		echo bps_hud_BPSQSE_old_code_check();
		echo @bps_w3tc_htaccess_check($plugin_var_w3tc);
		echo @bps_wpsc_htaccess_check($plugin_var_wpsc);
		echo bpsPro_BBM_htaccess_check();
		echo bpsPro_hud_speed_boost_cache_code();
		echo bps_hud_check_autoupdate();
		//echo bps_hud_check_public_username();
	}
}
add_action('admin_notices', 'bps_HUD_WP_Dashboard');

// Heads Up Display - Check PHP version - top error message new activations/installations
function bps_check_php_version_error() {
	
	if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) ) {
		return;
	}
	
	if ( version_compare( PHP_VERSION, '5.0.0', '<' ) ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS requires at least PHP5 to function correctly. Your PHP version is: ', 'bulletproof-security').PHP_VERSION.'</font><br><a href="https://www.ait-pro.com/aitpro-blog/1166/bulletproof-security-plugin-support/bulletproof-security-plugin-guide-bps-version-45#bulletproof-security-issues-problems" target="_blank">'.__('BPS Guide - PHP5 Solution', 'bulletproof-security').'</a><br>'.__('The BPS Guide will open in a new browser window. You will not be directed away from your WordPress Dashboard.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display w/ Dismiss - Check if PHP Safe Mode is On - 1 is On - 0 is Off
function bps_check_safemode() {
	
	if ( ini_get('safe_mode') == 1 ) {
		
		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		
		
		if ( ! get_user_meta($user_id, 'bps_ignore_safemode_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that Safe Mode is set to On in your php.ini file.', 'bulletproof-security').'</font><br>'.__('If you see errors that BPS was unable to automatically create the backup folders this is probably the reason why.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_safemode_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_safemode_nag_ignore');

function bps_safemode_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_safemode_nag_ignore']) && '0' == $_GET['bps_safemode_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_safemode_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if Permalinks are enabled - top error message new activations/installations
function bps_check_permalinks_error() {

	if ( current_user_can('manage_options') && get_option('permalink_structure') == '' ) {

		global $current_user;
		$user_id = $current_user->ID;
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}	
	
		if ( ! get_user_meta($user_id, 'bps_ignore_Permalinks_notice') ) { 
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Custom Permalinks are NOT being used.', 'bulletproof-security').'</font><br>'.__('It is recommended that you use Custom Permalinks: ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2304/wordpress-tips-tricks-fixes/permalinks-wordpress-custom-permalinks-wordpress-best-wordpress-permalinks-structure/" target="_blank" title="Link opens in a new Browser window">'.__('How to setup Custom Permalinks', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_Permalinks_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;		
		}
	}
}

add_action('admin_init', 'bps_Permalinks_nag_ignore');

function bps_Permalinks_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_Permalinks_nag_ignore']) && '0' == $_GET['bps_Permalinks_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_Permalinks_notice', 'true', true);
	}
}

// Heads Up Display w/Dismiss - Check if Windows IIS server and if IIS7 supports permalink rewriting
function bps_check_iis_supports_permalinks() {
global $wp_rewrite, $is_IIS, $is_iis7, $current_user;
$user_id = $current_user->ID;	

	if ( current_user_can('manage_options') && $is_IIS && ! iis7_supports_permalinks() ) {
	if ( ! get_user_meta($user_id, 'bps_ignore_iis_notice')) {

	if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
		$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
	} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
		$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
	} else {
		$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
	}

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS has detected that your Server is a Windows IIS Server that does not support htaccess rewriting.', 'bulletproof-security').'</font><br>'.__('Do NOT activate BulletProof Modes unless you know what you are doing.', 'bulletproof-security').'<br>'.__('Your Server Type is: ', 'bulletproof-security').esc_html( $_SERVER['SERVER_SOFTWARE'] ).'<br><a href="http://codex.wordpress.org/Using_Permalinks" target="_blank" title="This link will open in a new browser window.">'.__('WordPress Codex - Using Permalinks - see IIS section', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_iis_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';		
		echo $text;
	}
	}
}

add_action('admin_init', 'bps_iis_nag_ignore');

function bps_iis_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_iis_nag_ignore'] ) && '0' == $_GET['bps_iis_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_iis_notice', 'true', true);
	}
}

// Heads Up Display - check if /bps-backup and /bps-backup/master-backups folders exist
function bps_hud_check_bpsbackup() {

	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );	

	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup folder manually via FTP. The folder permissions for the bps-backup folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
	
	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ) {
		$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WARNING! BPS was unable to automatically create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder.', 'bulletproof-security').'</font><br>'.__('You will need to create the /', 'bulletproof-security').$bps_wpcontent_dir.__('/bps-backup/master-backups folder manually via FTP. The folder permissions for the master-backups folder need to be set to 755 in order to successfully perform permanent online backups.', 'bulletproof-security').'<br>'.__('To remove this message permanently click ', 'bulletproof-security').'<a href="https://www.ait-pro.com/aitpro-blog/2566/bulletproof-security-plugin-support/bulletproof-security-error-messages" target="_blank">'.__('here.', 'bulletproof-security').'</a></div>';
		echo $text;
	}
}

// Heads Up Display - Bonus Custom Code with Dismiss Notices
function bpsPro_bonus_custom_code_dismiss_notices() {
global $current_user;
$user_id = $current_user->ID;	
	
	if ( current_user_can('manage_options') ) { 
		$text = '';
	
	// Setup Wizard DB option is saved by running the Setup Wizard, on BPS Upgrades & manual BPS setup
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');

	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		return;
	}

	if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
		$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
	} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
		$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
	} else {
		$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
	}
		
	// BPS Pro 11.2: New Dismiss Notice. Note: add new dismiss notice query strings in both "dismiss all" conditions for new Bonus code
	if ( get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') && ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br>';


		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		echo $text;
		echo '</div>';
	}		
	
	if ( ! get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice') ) {

	if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 		
		
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Bonus Custom Code:', 'bulletproof-security').'</font><br>'.__('Click the links below to get Bonus Custom Code or click the Dismiss Notice links or click this ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_bonus_code_dismiss_all_nag_ignore=0&bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss All Notices', 'bulletproof-security').'</a></span>'.__(' link. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br>';
		
	}

	if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') ) { 	
		
		$text .= '<div id="BC1" style="">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Additional Protection for the Login Page from Brute Force Login Attacks" target="_blank">'.__('Brute Force Login Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_brute_force_login_protection_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') ) { 	

		$text .= '<div id="BC2" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" title="Speed up your website performance with Browser Cache code" target="_blank">'.__('Speed Boost Cache Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_speed_boost_cache_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_author_enumeration_notice') ) { 

		$text .= '<div id="BC3" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-author-enumeration-bot-probe-protection-author-id-user-id/" title="Protects against hacker and spammer bots finding Author names & User names on your website" target="_blank">'.__('Author Enumeration BOT Probe Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_author_enumeration_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
		
	if ( ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') ) { 		

		$text .= '<div id="BC4" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/wordpress-xml-rpc-ddos-protection-protect-xmlrpc-php-block-xmlrpc-php-forbid-xmlrpc-php/" title="Protects against the XML Quadratic Blowup Attack, DDoS Attacks as well as other various XML-RPC exploits" target="_blank">'.__('XML-RPC DDoS Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_xmlrpc_ddos_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
	
	/*
	if ( ! get_user_meta($user_id, 'bps_referer_spam_notice') ) {

		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/block-referer-spammers-semalt-kambasoft-ranksonic-buttons-for-website/" title="Protects against Referer Spamming and Phishing" target="_blank">'.__('Referer Spam|Phishing Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_referer_spam_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}
	*/
	
	if ( ! get_user_meta($user_id, 'bps_post_request_attack_notice') ) {

		$text .= '<div id="BC5" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/post-request-protection-post-attack-protection-post-request-blocker/" title="Protects against POST Request Attacks" target="_blank">'.__('POST Request Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_post_request_attack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
		
	}

	if ( ! get_user_meta($user_id, 'bps_sniff_driveby_notice') ) {		
		
		$text .= '<div id="BC6" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/mime-sniffing-data-sniffing-content-sniffing-drive-by-download-attack-protection/" title="Protects against Mime Sniffing, Data Sniffing, Content Sniffing and Drive-by Download Attacks" target="_blank">'.__('Mime Sniffing|Drive-by Download Attack Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_sniff_driveby_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
	}

	if ( ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) {		
		
		$text .= '<div id="BC7" style="margin-top:2px;">'.__('Get ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/rssing-com-good-or-bad/" title="Protects against external websites displaying your website pages or Feeds in iFrames and Clickjacking Protection" target="_blank">'.__('External iFrame|Clickjacking Protection Code', 'bulletproof-security').'</a>'.__(' or ', 'bulletproof-security').'<span style=""><a href="'.$bps_base.'bps_iframe_clickjack_nag_ignore=0'.'" style="">'.__('Dismiss Notice', 'bulletproof-security').'</a></span></div>';
	}

		echo $text;
		
		if ( ! get_user_meta($user_id, 'bps_brute_force_login_protection_notice') || ! get_user_meta($user_id, 'bps_speed_boost_cache_notice') || ! get_user_meta($user_id, 'bps_author_enumeration_notice') || ! get_user_meta($user_id, 'bps_xmlrpc_ddos_notice') || ! get_user_meta($user_id, 'bps_post_request_attack_notice') || ! get_user_meta($user_id, 'bps_sniff_driveby_notice') || ! get_user_meta($user_id, 'bps_iframe_clickjack_notice') ) { 	
		echo '</div>';
		}
		}
	}
}

add_action('admin_init', 'bpsPro_bonus_custom_code_nag_ignores');

function bpsPro_bonus_custom_code_nag_ignores() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_bonus_code_dismiss_all_nag_ignore']) && '0' == $_GET['bps_bonus_code_dismiss_all_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice', 'true', true);
	}

	if ( isset($_GET['bps_brute_force_login_protection_nag_ignore']) && '0' == $_GET['bps_brute_force_login_protection_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_brute_force_login_protection_notice', 'true', true);
	}

	if ( isset($_GET['bps_speed_boost_cache_nag_ignore']) && '0' == $_GET['bps_speed_boost_cache_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_speed_boost_cache_notice', 'true', true);
	}

	if ( isset($_GET['bps_author_enumeration_nag_ignore']) && '0' == $_GET['bps_author_enumeration_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_author_enumeration_notice', 'true', true);
	}

	if ( isset($_GET['bps_xmlrpc_ddos_nag_ignore']) && '0' == $_GET['bps_xmlrpc_ddos_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_xmlrpc_ddos_notice', 'true', true);
	}

	/*
	if ( isset($_GET['bps_referer_spam_nag_ignore']) && '0' == $_GET['bps_referer_spam_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_referer_spam_notice', 'true', true);
	}
	*/
	
	if ( isset($_GET['bps_post_request_attack_nag_ignore']) && '0' == $_GET['bps_post_request_attack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_post_request_attack_notice', 'true', true);
	}

	if ( isset($_GET['bps_sniff_driveby_nag_ignore']) && '0' == $_GET['bps_sniff_driveby_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_sniff_driveby_notice', 'true', true);
	}

	if ( isset($_GET['bps_iframe_clickjack_nag_ignore']) && '0' == $_GET['bps_iframe_clickjack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_iframe_clickjack_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Check if php.ini handler code exists in root .htaccess file, but not in Custom Code
// .53.6: Additional conditional check added for Wordfence WAF Firewall mess.
function bps_hud_PhpiniHandlerCheck() {
global $current_user;
$user_id = $current_user->ID;
$file = ABSPATH . '.htaccess';	

	if ( esc_html($_SERVER['QUERY_STRING']) == 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {
	
		if ( file_exists($file) ) {		

			$file_contents = @file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $CustomCodeoptions['bps_customcode_one'], $DBmatches);

			if ( $matches[0] && ! $DBmatches[0] ) {
			
			preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );

			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}			
			
			if ( stripos( $file_contents, "Wordfence WAF" ) ) {

				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem before running the Setup Wizard.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;

			} else {
				
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
				echo $text;			
				echo '<pre style="margin:5px 0px 0px 5px;">';
				echo '# PHP/php.ini handler htaccess code<br>';				
				
				foreach ( $h_matches[0] as $Key => $Value ) {
					echo $Value;
				}
				echo '</pre>';
			}
			}
		}
	}

	if ( esc_html($_SERVER['QUERY_STRING']) != 'page=bulletproof-security/admin/wizard/wizard.php' && ! get_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice') ) {

		if ( file_exists($file) ) {		

			$file_contents = @file_get_contents($file);
			$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
			
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $file_contents, $matches);
			preg_match_all('/AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application/', $CustomCodeoptions['bps_customcode_one'], $DBmatches);
		
			if ( $matches[0] && ! $DBmatches[0] ) {
			
			preg_match_all('/(([#\s]{1,}|)(AddHandler|SetEnv PHPRC|suPHP_ConfigPath|Action application).*\s*){1,}/', $file_contents, $h_matches );

			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}		
			
				if ( stripos( $file_contents, "Wordfence WAF" ) ) {
					
					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: Wordfence PHP/php.ini handler htaccess code detected', 'bulletproof-security').'</font><br>'.__('Wordfence PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordfence-firewall-wp-contentwflogsconfig-php-file-quarantined/#wordfence-php-handler" target="_blank" title="Wordfence PHP Handler Fix">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for the steps to fix this Wordfence problem.', 'bulletproof-security').'<br><font color="#fb0101">'.__('CAUTION: ', 'bulletproof-security').'</font>'.__('Using the Wordfence WAF Firewall may cause serious/critical problems for your website and BPS.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;				
				
				} else {

					$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('HUD Check: PHP/php.ini handler htaccess code check', 'bulletproof-security').'</font><br>'.__('PHP/php.ini handler htaccess code was found in your root .htaccess file, but was NOT found in BPS Custom Code.', 'bulletproof-security').'<br>'.__('To automatically fix this click here: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'">'.esc_attr__('Setup Wizard Pre-Installation Checks', 'bulletproof-security').'</a><br>'.__('The Setup Wizard Pre-Installation Checks feature will automatically fix this just by visiting the Setup Wizard page.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_PhpiniHandler_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
					echo $text;			
					echo '<pre style="margin:5px 0px 0px 5px;">';
					echo '# PHP/php.ini handler htaccess code<br>';				
				
					foreach ( $h_matches[0] as $Key => $Value ) {
						echo $Value;
					}
					echo '</pre>';
				}
			}
		}
	}
}

add_action('admin_init', 'bps_PhpiniHandler_nag_ignore');

function bps_PhpiniHandler_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_PhpiniHandler_nag_ignore'] ) && '0' == $_GET['bps_PhpiniHandler_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Sucuri 1-click Hardening wp-content .htaccess file problem - breaks BPS and lots of other stuff
function bps_hud_check_sucuri() {
$filename = WP_CONTENT_DIR . '/.htaccess';
$plugin_var = 'sucuri-scanner/sucuri.php';
$return_var = in_array( $plugin_var, apply_filters('active_plugins', get_option('active_plugins') ) );

	if ( $return_var == 1 && ! file_exists($filename) ) { // 1 equals active
		return;	
	}
	
	if ( file_exists($filename) ) {
		$check_string = @file_get_contents($filename);
	}

	if ( $return_var == 1 && file_exists($filename) && strpos( $check_string, "deny from all" ) ) { // 1 equals active	
	
		global $current_user;
		$user_id = $current_user->ID;

		if ( ! get_user_meta($user_id, 'bps_ignore_sucuri_notice') ) {
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('Sucuri 1-click Hardening wp-content .htaccess file problem detected', 'bulletproof-security').'</font><br>'.__('Using the Sucuri 1-click Hardening wp-content .htaccess file will cause several BPS Pro features not to work correctly.', 'bulletproof-security').'<br>'.__('To fix this issue delete the Sucuri .htaccess file in your wp-content folder.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_sucuri_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_sucuri_nag_ignore');

function bps_sucuri_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_sucuri_nag_ignore'] ) && '0' == $_GET['bps_sucuri_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_sucuri_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - WordPress Firewall 2 plugin - breaks BPS and lots of other stuff
function bps_hud_check_wordpress_firewall2() {
$plugin_var = 'wordpress-firewall-2/wordpress-firewall-2.php';
$return_var = in_array( $plugin_var, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $return_var != 1 ) { // 1 equals active
		return;	
	}
	
	if ( $return_var == 1 ) { // 1 equals active	
	
		global $current_user;
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bps_ignore_wpfirewall2_notice') ) {
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('The WordPress Firewall 2 plugin is installed and activated', 'bulletproof-security').'</font><br>'.__('It is recommended that you delete the WordPress Firewall 2 plugin.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordpress-firewall-2-plugin-unable-to-save-custom-code/" target="_blank" title="Link opens in a new Browser window">'.__('Click Here', 'bulletproof-security').'</a>'.__(' for more information.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_wpfirewall2_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_wpfirewall2_nag_ignore');

function bps_wpfirewall2_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_wpfirewall2_nag_ignore'] ) && '0' == $_GET['bps_wpfirewall2_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_wpfirewall2_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Broken Link Checker plugin - HEAD Request Method filter check
function bps_hud_broken_link_checker() {
$filename = ABSPATH . '.htaccess';
$plugin_var = 'broken-link-checker/broken-link-checker.php';
$return_var = in_array( $plugin_var, apply_filters('active_plugins', get_option('active_plugins')));
$pattern2 = '/#{1,}(\s|){1,}RewriteCond\s\%\{REQUEST_METHOD\}\s\^\(HEAD\)\s\[NC\](.*\s*){1}(#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s(.*)\/bulletproof-security\/405\.php\s\[L\]|#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s\-\s\[R=405,L\])/';

	if ( file_exists($filename) ) {
		$check_string = @file_get_contents($filename);

    if ( $return_var == 1 && preg_match( $pattern2, $check_string, $matches ) ) { // 1 equals active
		return;
	}
	
	if ( $return_var == 1 ) {
		
		global $current_user;
		$user_id = $current_user->ID;

		if ( ! get_user_meta($user_id, 'bps_ignore_BLC_notice') ) {
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('Broken Link Checker plugin HEAD Request Method filter problem detected.', 'bulletproof-security').'</font><br>'.__('To fix this problem ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/broken-link-checker-plugin-403-error/" target="_blank" title="Link opens in a new Browser window">'.__('Click Here', 'bulletproof-security').'</a><br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_BLC_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
	}
}

add_action('admin_init', 'bps_BLC_nag_ignore');

function bps_BLC_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_BLC_nag_ignore'] ) && '0' == $_GET['bps_BLC_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_BLC_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - Jetpack plugin - displays forum links for Jetpack spefific HEAD Request code and Jetpack specific XML-RPC Bonus Custom Code.
function bps_hud_check_jetpack() {
$plugin_var = 'jetpack/jetpack.php';
$return_var = in_array( $plugin_var, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $return_var != 1 ) { // 1 equals active
		return;	
	}
	
	$filename = ABSPATH . '.htaccess';

	if ( $return_var == 1 && file_exists($filename) ) { // 1 equals active	
	
		global $current_user;
		$user_id = $current_user->ID;			
		
		if ( ! get_user_meta($user_id, 'bps_ignore_jetpack_notice') ) {
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
		$check_string = @file_get_contents($filename);
		$pattern1 = '/(\<FilesMatch\s\"\^\(xmlrpc\\\.php\)\"\>|\<FilesMatch\s\"\^\(xmlrpc\\\.php\|wp-trackback\\\.php\)\"\>)(.*\s*){1,8}Allow\sfrom\s192\.0\.64\.0\/18(.*\s*){1}Allow\sfrom\s209\.15\.0\.0\/16(.*\s*){1}Allow\sfrom\s66\.155\.0\.0\/17/';
		$pattern2 = '/#{1,}(\s|){1,}RewriteCond\s\%\{REQUEST_METHOD\}\s\^\(HEAD\)\s\[NC\](.*\s*){1}(#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s(.*)\/bulletproof-security\/405\.php\s\[L\]|#{1,}(\s|){1,}RewriteRule\s\^\(\.\*\)\$\s\-\s\[R=405,L\])/';
		$pattern3 = '/RewriteCond\s\%\{REQUEST_METHOD\}\s\^\(HEAD\)\s\[NC\](.*\s*){1}RewriteCond\s\%\{HTTP_USER_AGENT\}\s\!\^\(\.\*Jetpack\.\*\)\$/';

			// User has older Jetpack XML-RPC Bonus Custom Code in the Root htaccess file.
			if ( preg_match( $pattern1, $check_string, $matches ) ) {

				$text = '<div class="update-nag" style="max-width:96.5%;background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Jetpack XML-RPC Bonus Custom Code Notice', 'bulletproof-security').'</font><br>'.__('Older BPS XML-RPC Bonus Custom Code was found in your Root htaccess file. New XML-RPC Bonus Custom Code for specific usage with Jetpack has been created. Click the Click Here link below to get the new Jetpack XML-RPC Bonus Custom Code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/wordpress-xml-rpc-ddos-protection-protect-xmlrpc-php-block-xmlrpc-php-forbid-xmlrpc-php/" target="_blank" title="Jetpack XML-RPC Bonus Custom Code">'.__('Click Here', 'bulletproof-security').'</a>'.__(' To get and use the New Jetpack XML-RPC Bonus Custom Code, replace your existing XML-RPC Bonus Custom Code in BPS Custom Code with the newer Jetpack XML-RPC Bonus Custom Code.', 'bulletproof-security').'<br>'.__('To Dismiss these Jetpack Notices click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'</div>';
				echo $text;
			}
		
			// This HEAD Request checking condition needs to be an independent check.
			// Notes: pattern2 match = user is not using # signs in the REQUEST METHODS FILTERED code to allow all HEAD Requests.
			// pattern3 match = user is not using the new Jetpack whitelist by User Agent custom htaccess code for Jetpack.
			if ( ! preg_match( $pattern2, $check_string, $matches ) && ! preg_match( $pattern3, $check_string, $matches ) ) {		
		
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Jetpack Site Uptime Monitor Notice', 'bulletproof-security').'</font><br>'.__('New Jetpack Site Uptime Monitor code has been created for specific usage with Jetpack to allow HEAD Requests made by Jetpack. Click the Click Here link below to get the new Jetpack Site Uptime Monitor code.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/jetpack-site-uptime-monitor-403-error/#post-15400" target="_blank" title="Jetpack Site Uptime Monitor">'.__('Click Here', 'bulletproof-security').'</a>'.__(' To get and use the new Jetpack Site Uptime Monitor code, replace any existing REQUEST METHODS FILTERED code that you have added to BPS Custom Code with the newer Jetpack Site Uptime Monitor code.', 'bulletproof-security').'<br>'.__('To Dismiss these Jetpack Notices click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'</div>';
				echo $text;
		
			}
			
			if ( preg_match( $pattern1, $check_string, $matches ) || ! preg_match( $pattern2, $check_string, $matches ) && ! preg_match( $pattern3, $check_string, $matches ) ) {
				
				echo '<div style="width:100px;text-align:center;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_jetpack_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div>';
			
			}
		}
	}
}

add_action('admin_init', 'bps_jetpack_nag_ignore');

function bps_jetpack_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_jetpack_nag_ignore'] ) && '0' == $_GET['bps_jetpack_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_jetpack_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - WooCommerce plugin
function bps_hud_check_woocommerce() {
$plugin_var = 'woocommerce/woocommerce.php';
$return_var = in_array( $plugin_var, apply_filters('active_plugins', get_option('active_plugins')));
$filename = ABSPATH . '.htaccess';
$pattern = '/RewriteCond\s\%\{REQUEST_URI\}\s\^\.\*\/\(shop\|cart\|checkout\|wishlist\)\.\*\s\[NC\](.*\s*){1}RewriteRule\s\.\s\-\s\[S=\d+\]/';

	if ( file_exists($filename) ) {
		$check_string = @file_get_contents($filename);

    if ( $return_var == 1 && preg_match( $pattern, $check_string, $matches ) ) { // 1 equals active
		return;
	}
	
	if ( $return_var == 1 ) {

		global $current_user;
		$user_id = $current_user->ID;

		if ( ! get_user_meta($user_id, 'bps_ignore_woocommerce_notice') ) { 
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('WooCommerce Notice', 'bulletproof-security').'</font><br>'.__('New WooCommerce whitelisting code has been created to resolve problems where BPS is blocking something in WooCommerce.', 'bulletproof-security').'<br>'.__('If WooCommerce is working fine on your website then disregard this Notice and click the Dismiss Notice button below.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/woocommerce-read-me-first/" target="_blank" title="WooCommerce Whitelisting Code">'.__('Click Here', 'bulletproof-security').'</a>'.__(' To get the WooCommerce whitelisting code. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_woocommerce_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
	}
}

add_action('admin_init', 'bps_woocommerce_nag_ignore');

function bps_woocommerce_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_woocommerce_nag_ignore']) && '0' == $_GET['bps_woocommerce_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_woocommerce_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - WooCommerce LSM enable options
// Notes: This Notice needs to be displayed to everyone who already currently have WooCommerce installed until they Dismiss this Notice.
// The reason for that is the BPS upgrade will automatically enable LSM for the WooCommerce custom login page.
// If they install WooCommerce at a later time then this Notice is displayed.
// Exception: This Notice should not be displayed for new BPS installations before or after the Setup Wizard has been run.
function bpsPro_hud_woocommerce_enable_lsm_jtc() {
$plugin_var = 'woocommerce/woocommerce.php';
$return_var = in_array( $plugin_var, apply_filters('active_plugins', get_option('active_plugins')));
$lsm_options = get_option('bulletproof_security_options_login_security');
$sw_woo_options = get_option('bulletproof_security_options_setup_wizard_woo');

	if ( ! $lsm_options['bps_enable_lsm_woocommerce'] ) {
		return;
	}

	if ( $sw_woo_options['bps_wizard_woo'] == '1' ) {
		return;
	}

	if ( $return_var == 1 ) {

		global $current_user;
		$user_id = $current_user->ID;

		if ( ! get_user_meta($user_id, 'bps_ignore_woocommerce_lsm_jtc_notice') ) { 
			
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}			
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:600;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS WooCommerce Options Notice: Enable Login Security for WooCommerce', 'bulletproof-security').'</font><br>'.__('BPS Login Security & Monitoring (LSM) can be enabled/disabled for the WooCommerce custom login page by checking or unchecking the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'">'.__('Enable Login Security for WooCommerce', 'bulletproof-security').'</a>'.__(' checkbox option setting. The LSM WooCommerce option is automatically enabled during the BPS upgrade if you already had WooCommerce installed before upgrading BPS. If you just installed WooCommerce you can either run the Setup Wizard to enable the LSM WooCommerce option or you can enable this option manually by going to the BPS LSM plugin page if you want to enable LSM for WooCommerce.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_woo_jtc_lsm_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bps_woo_jtc_lsm_nag_ignore');

function bps_woo_jtc_lsm_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bps_woo_jtc_lsm_nag_ignore']) && '0' == $_GET['bps_woo_jtc_lsm_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_woocommerce_lsm_jtc_notice', 'true', true);
	}
}

// Check for older BPS Query String Exploits code saved to BPS Custom Code
function bps_hud_BPSQSE_old_code_check() {
$CustomCodeoptions = get_option('bulletproof_security_options_customcode');	

	if ( $CustomCodeoptions['bps_customcode_bpsqse'] == '' ) {
		return;
	}
	
	$subject = $CustomCodeoptions['bps_customcode_bpsqse'];	
	$pattern1 = '/RewriteCond\s%{QUERY_STRING}\s\(\\\.\/\|\\\.\.\/\|\\\.\.\.\/\)\+\(motd\|etc\|bin\)\s\[NC,OR\]/';
	$pattern2 = '/RewriteCond\s%\{THE_REQUEST\}\s(.*)\?(.*)\sHTTP\/\s\[NC,OR\]\s*RewriteCond\s%\{THE_REQUEST\}\s(.*)\*(.*)\sHTTP\/\s\[NC,OR\]/';
	$pattern3 = '/RewriteCond\s%\{THE_REQUEST\}\s.*\?\+\(%20\{1,\}.*\s*RewriteCond\s%\{THE_REQUEST\}\s.*\+\(.*\*\|%2a.*\s\[NC,OR\]/';

	if ( $CustomCodeoptions['bps_customcode_bpsqse'] != '' && preg_match($pattern1, $subject, $matches) || preg_match($pattern2, $subject, $matches) || preg_match($pattern3, $subject, $matches) ) {

		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Notice: BPS Query String Exploits Code Changes', 'bulletproof-security').'</font><br>'.__('Older BPS Query String Exploits code was found in BPS Custom Code. Several Query String Exploits rules were changed/added/modified in the root .htaccess file in BPS .49.6, .50.2 & .50.3.', 'bulletproof-security').'<br>'.__('Copy the new Query String Exploits section of code from your root .htaccess file and paste it into this BPS Custom Code text box: CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS and click the Save Root Custom Code button.', 'bulletproof-security').'<br>'.__('This Notice will go away once you have copied the new Query String Exploits code to BPS Custom Code and clicked the Save Root Custom Code button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

// Heads Up Display - Check if W3TC is active or not and check root htaccess file for W3TC htaccess code 
function bps_w3tc_htaccess_check($plugin_var_w3tc) {
	
	$plugin_var_w3tc = 'w3-total-cache/w3-total-cache.php';
    $return_var = in_array( $plugin_var_w3tc, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $return_var == 1 || is_plugin_active_for_network( 'w3-total-cache/w3-total-cache.php' )) { // checks if W3TC is active for Single site or Network
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

			$filename = ABSPATH . '.htaccess';
		
			if ( file_exists($filename) ) {		

			$string = file_get_contents($filename);	

			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				if ( ! strpos( $string, "W3TC" ) ) {
					$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('W3 Total Cache is activated, but W3TC htaccess code was NOT found in your root htaccess file.', 'bulletproof-security').'</font><br>'.__('W3TC needs to be redeployed by clicking either the W3TC auto-install or deploy buttons. Your Root htaccess file must be temporarily unlocked so that W3TC can write to your Root htaccess file. Click to ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=w3tc_general' ).'">'.esc_attr__('Redeploy W3TC.', 'bulletproof-security').'</a><br>'.__('You can copy W3TC .htaccess code from your Root .htaccess file to BPS Custom Code to save it permanently so that you will not have to do these steps in the future.', 'bulletproof-security').'<br>'.__('Copy W3TC .htaccess code to this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE, click the Save Root Custom Code button, go to the BPS Security Modes page and click the Root folder BulletProof Mode button.', 'bulletproof-security').'</div>';
					echo $text;
				}
			}
		}
	}
	elseif ( $return_var != 1 || ! is_plugin_active_for_network( 'w3-total-cache/w3-total-cache.php' )) { // checks if W3TC is active for Single site or Network
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}

		$filename = ABSPATH . '.htaccess';
		
		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);			
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				if ( strpos( $string, "W3TC" ) ) {
					$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('W3 Total Cache is deactivated and W3TC .htaccess code was found in your root htaccess file.', 'bulletproof-security').'</font><br>'.__('If this is just temporary then this warning message will go away when you reactivate W3TC. If you are planning on uninstalling W3TC the W3TC htaccess code will be automatically removed from your root htaccess file when you uninstall W3TC. Your Root htaccess file must be temporarily unlocked so that W3TC can remove the W3TC Root htaccess code. If you manually edit your root htaccess file then refresh your browser to perform a new htaccess file check.', 'bulletproof-security').'</div>';
					echo $text;
				} 
			}
		}
	}
}

// Heads Up Display - Check if WPSC is active or not and check root htaccess file for WPSC htaccess code 
function bps_wpsc_htaccess_check($plugin_var_wpsc) {
	
	$plugin_var_wpsc = 'wp-super-cache/wp-cache.php';
    $return_var = in_array( $plugin_var_wpsc, apply_filters('active_plugins', get_option('active_plugins')));

	if ( $return_var == 1 || is_plugin_active_for_network( 'wp-super-cache/wp-cache.php' ) ) { // checks if WPSC is active for Single site or Network
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);		
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				if ( ! strpos($string, "WPSuperCache" ) ) { 
					$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Super Cache is activated, but either you are not using WPSC mod_rewrite to serve cache files or the WPSC htaccess code was NOT found in your root htaccess file.', 'bulletproof-security').'</font><br>'.__('If you are not using WPSC mod_rewrite then copy this: # WPSuperCache to this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE, click the Save Root Custom Code button, go to the Security Modes page and click the Root folder BulletProof Mode button.', 'bulletproof-security').'<br>'.__('If you are using WPSC mod_rewrite and the WPSC htaccess code is not in your root htaccess file then unlock your Root htaccess file temporarily then click this ', 'bulletproof-security').'<a href="options-general.php?page=wpsupercache&tab=settings">'.__('Update WPSC link', 'bulletproof-security').'</a>'.__(' to go to the WPSC Settings page and click the Update Mod_Rewrite Rules button.', 'bulletproof-security').'<br>'.__('If you have deactivated Root Folder BulletProof Mode then disregard this Alert and DO NOT update your Mod_Rewrite Rules. Refresh your browser to perform a new htaccess file check.', 'bulletproof-security').'<br>'.__('You can copy WPSC .htaccess code from your Root .htaccess file to BPS Custom Code to save it permanently so that you will not have to do these steps in the future.', 'bulletproof-security').'<br>'.__('Copy WPSC .htaccess code to this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE, click the Save Root Custom Code button, go to the BPS Security Modes page and click the Root folder BulletProof Mode button.', 'bulletproof-security').'</div>';
					echo $text;
				}
			}
		}
	}
	elseif ( $return_var != 1 || ! is_plugin_active_for_network( 'wp-super-cache/wp-cache.php' )) { // checks if WPSC is NOT active for Single or Network
		
		if ( ! is_multisite() ) {
			$bpsSiteUrl = get_option('siteurl');
			$bpsHomeUrl = get_option('home');
		} else {
			$bpsSiteUrl = get_site_option('siteurl');
			$bpsHomeUrl = network_site_url();		
		}
		
		$filename = ABSPATH . '.htaccess';

		if ( file_exists($filename) ) {

			$string = file_get_contents($filename);				
		
			if ( $bpsSiteUrl == $bpsHomeUrl ) {
				if ( strpos($string, "WPSuperCache" ) ) {
					$text = '<div style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:0px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('WP Super Cache is deactivated and WPSC htaccess code - # BEGIN WPSuperCache # END WPSuperCache - was found in your root htaccess file.', 'bulletproof-security').'</font><br>'.__('If this is just temporary then this warning message will go away when you reactivate WPSC. You will need to set up and reconfigure WPSC again when you reactivate WPSC. Your Root htaccess file must be temporarily unlocked if you are planning on uninstalling WPSC. The WPSC htaccess code will be automatically removed from your root htaccess file when you uninstall WPSC. If you added a commented out line of code in anywhere in your root htaccess file - # WPSuperCache - then delete it and refresh your browser. If you added WPSC code to BPS Custom Code then delete it if you are removing WPSC permanently.', 'bulletproof-security').'</div>';
					echo $text;
				} 
			}
		}
	}
}

// Heads Up Display - Check if the /bps-backup/.htaccess file exists
function bpsPro_BBM_htaccess_check() {

	// New BPS installation - do not check or display error
	if ( ! get_option('bulletproof_security_options_wizard_free') ) { 
		return;
	}

	$options = get_option('bulletproof_security_options_monitor');
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );

	if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' && @$_POST['Submit-BBM-Activate'] != true ) {
		$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="#fb0101">'.__('BPS Alert! A BPS htaccess file was NOT found in the BPS Backup folder: ', 'bulletproof-security').'/'.$bps_wpcontent_dir.'/bps-backup/</font><br>'.__('Go to the ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'">'.esc_attr__('Security Modes page', 'bulletproof-security').'</a>'.__(' and click the BPS Backup Folder BulletProof Mode Activate button.', 'bulletproof-security').'</div>';
		echo $text;
	}
}

function bpsPro_hud_speed_boost_cache_code() {
global $current_user;
$user_id = $current_user->ID;	
	
	$CC_options = get_option('bulletproof_security_options_customcode');
	
	if ( $CC_options['bps_customcode_one'] == '') {
		return;
	}	
	
	$pattern1 = '/BEGIN\sWEBSITE\sSPEED\sBOOST/';
	$pattern2 = '/AddOutputFilterByType\sDEFLATE\stext\/plain\s*AddOutputFilterByType\sDEFLATE\stext\/html\s*AddOutputFilterByType\sDEFLATE\stext\/xml\s*AddOutputFilterByType\sDEFLATE\stext\/css\s*AddOutputFilterByType\sDEFLATE\sapplication\/xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/xhtml\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/rss\+xml\s*AddOutputFilterByType\sDEFLATE\sapplication\/javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-javascript\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-php\s*AddOutputFilterByType\sDEFLATE\sapplication\/x-httpd-fastphp\s*AddOutputFilterByType\sDEFLATE\simage\/svg\+xml/';

	if ( ! get_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice') ) { 
		
		if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
			$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
		} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
			$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
		} else {
			$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
		}		

		if ( preg_match( $pattern1, htmlspecialchars_decode( $CC_options['bps_customcode_one'], ENT_QUOTES ), $matches1 ) && preg_match( $pattern2, htmlspecialchars_decode( $CC_options['bps_customcode_one'], ENT_QUOTES ), $matches2 ) ) {

			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</font><br>'.__('Older BPS Speed Boost Cache Code was found saved in this BPS Custom Code text box: CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE', 'bulletproof-security').'.<br>'.__('Newer improved BPS Speed Boost Cache Code has been created, which should improve website load speed performance even more.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" target="_blank" title="BPS Speed Boost Cache Code">'.__('Get The New Improved BPS Speed Boost Cache Code', 'bulletproof-security').'</a>'.__('. To dismiss this Notice click the Dismiss Notice button below.', 'bulletproof-security').'<br>'.__('To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bpsPro_hud_speed_boost_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
		}
	}
}

add_action('admin_init', 'bpsPro_hud_speed_boost_nag_ignore');

function bpsPro_hud_speed_boost_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset($_GET['bpsPro_hud_speed_boost_nag_ignore']) && '0' == $_GET['bpsPro_hud_speed_boost_nag_ignore'] ) {
		add_user_meta($user_id, 'bpsPro_ignore_speed_boost_notice', 'true', true);
	}
}

// Heads Up Display w/ Dismiss - BPS Plugin AutoUpdate
// Notes: Only Display the AutoUpdate Dimiss Notice if the Bonus Custom Code Dismiss Notice is not being displayed (display after the BCC Dimiss Notice).
// There are 3 common scenarios: only the dismiss all notice link was clicked, some of the individual dismiss notices were clicked and 
// the dismiss all notice link was clicked and only all individual dimiss notice links were clicked, but not the dismiss all notice link.
// which leaves 2 possible conditions: either the dismiss all notice value == true or all other dismiss notice values == true.
function bps_hud_check_autoupdate() {
	
	if ( ! get_option('bulletproof_security_options_autoupdate') ) {
	
		global $current_user;
		$user_id = $current_user->ID;

		$bcc_dismiss_all = get_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice');
		$bcc1 = get_user_meta($user_id, 'bps_brute_force_login_protection_notice');
		$bcc2 = get_user_meta($user_id, 'bps_speed_boost_cache_notice');
		$bcc3 = get_user_meta($user_id, 'bps_author_enumeration_notice');
		$bcc4 = get_user_meta($user_id, 'bps_xmlrpc_ddos_notice');
		$bcc5 = get_user_meta($user_id, 'bps_post_request_attack_notice');
		$bcc6 = get_user_meta($user_id, 'bps_sniff_driveby_notice');
		$bcc7 = get_user_meta($user_id, 'bps_iframe_clickjack_notice');

		if ( true == $bcc_dismiss_all || true == $bcc1 && true == $bcc2 && true == $bcc3 && true == $bcc4 && true == $bcc5 && true == $bcc6 && true == $bcc7 ) {

			if ( ! get_user_meta($user_id, 'bps_ignore_autoupdate_notice') ) {
			
			if ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) != 'wp-admin' ) {
				$bps_base = basename(esc_html($_SERVER['REQUEST_URI'])) . '?';
			} elseif ( esc_html($_SERVER['QUERY_STRING']) == '' && basename(esc_html($_SERVER['REQUEST_URI'])) == 'wp-admin' ) {
				$bps_base = basename( str_replace( 'wp-admin', 'index.php?', esc_html($_SERVER['REQUEST_URI'])));
			} else {
				$bps_base = str_replace( admin_url(), '', esc_html($_SERVER['REQUEST_URI']) ) . '&';
			}		
			
			$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Plugin Automatic Update Notice', 'bulletproof-security').'</font><br>'.__('Would you like to have BPS plugin updates installed automatically? Click this link: ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/theme-skin/theme-skin.php#bps-plugin-autoupdate' ).'">'.esc_attr__('BPS Plugin AutoUpdate', 'bulletproof-security').'</a>'.__(' and choose the AutoUpdate On option setting.', 'bulletproof-security').'<br>'.__('To Dismiss this Notice click the Dismiss Notice button below. To Reset Dismiss Notices click the Reset|Recheck Dismiss Notices button on the Custom Code page.', 'bulletproof-security').'<br><div style="float:left;margin:3px 0px 3px 0px;padding:2px 6px 2px 6px;background-color:#e8e8e8;border:1px solid gray;"><a href="'.$bps_base.'bps_autoupdate_nag_ignore=0'.'" style="text-decoration:none;font-weight:bold;">'.__('Dismiss Notice', 'bulletproof-security').'</a></div></div>';
			echo $text;
			}
		}
	}
}

add_action('admin_init', 'bps_autoupdate_nag_ignore');

function bps_autoupdate_nag_ignore() {
global $current_user;
$user_id = $current_user->ID;
        
	if ( isset( $_GET['bps_autoupdate_nag_ignore'] ) && '0' == $_GET['bps_autoupdate_nag_ignore'] ) {
		add_user_meta($user_id, 'bps_ignore_autoupdate_notice', 'true', true);
	}
}

?>