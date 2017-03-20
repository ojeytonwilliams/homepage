<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// Displays Initial, Peak and Total Memory usage
function bpsPro_memory_resource_usage() {
	
	if ( is_admin() && current_user_can('manage_options') ) {

	$memory_usage_peak = memory_get_peak_usage();
	$mbytes_peak = number_format( $memory_usage_peak / ( 1024 * 1024 ), 2 );
	$kbytes_peak = number_format( $memory_usage_peak / ( 1024 ) );	
	
	$memory_usage = memory_get_usage();
	$mbytes = number_format( $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes = number_format( $memory_usage / ( 1024 ) );
	
	$mbytes_total = number_format( $memory_usage_peak / ( 1024 * 1024 ) - $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes_total = number_format( $memory_usage_peak / ( 1024 ) - $memory_usage / ( 1024 ) );	
	
	$usage = '<strong>'.__('Peak Memory Usage: ', 'bulletproof-security').'</strong>'. $mbytes_peak . __('MB|', 'bulletproof-security').$kbytes_peak.__('KB', 'bulletproof-security').'<br><strong>'.__('Initial Memory in Use: ', 'bulletproof-security').'</strong>'. $mbytes . __('MB|', 'bulletproof-security').$kbytes.__('KB', 'bulletproof-security').'<br><strong>'.__('Total Memory Used: ', 'bulletproof-security').'</strong>'. $mbytes_total . __('MB|', 'bulletproof-security').$kbytes_total.__('KB', 'bulletproof-security').'<br>';

	return $usage;
	}
}

// Logs Initial, Peak and Total Memory usage
function bpsPro_memory_resource_usage_logging() {
	
	$memory_usage_peak = memory_get_peak_usage();
	$mbytes_peak = number_format( $memory_usage_peak / ( 1024 * 1024 ), 2 );
	$kbytes_peak = number_format( $memory_usage_peak / ( 1024 ) );	
	
	$memory_usage = memory_get_usage();
	$mbytes = number_format( $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes = number_format( $memory_usage / ( 1024 ) );
	
	$mbytes_total = number_format( $memory_usage_peak / ( 1024 * 1024 ) - $memory_usage / ( 1024 * 1024 ), 2 );
	$kbytes_total = number_format( $memory_usage_peak / ( 1024 ) - $memory_usage / ( 1024 ) );	
	
	$usage = __('Peak Memory Usage: ', 'bulletproof-security'). $mbytes_peak . __('MB|', 'bulletproof-security').$kbytes_peak.__('KB', 'bulletproof-security')."\r\n".__('Initial Memory in Use: ', 'bulletproof-security'). $mbytes . __('MB|', 'bulletproof-security').$kbytes.__('KB', 'bulletproof-security')."\r\n".__('Total Memory Used: ', 'bulletproof-security'). $mbytes_total . __('MB|', 'bulletproof-security').$kbytes_total.__('KB', 'bulletproof-security');

	return $usage;
}

// BPS Master htaccess File Editing - file checks and get contents for editor
function bps_get_secure_htaccess() {
$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';

	if ( file_exists($secure_htaccess_file) ) {
		$bpsString = file_get_contents($secure_htaccess_file);
		echo $bpsString;
	} else {
		$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
		_e('The secure.htaccess file either does not exist or is not named correctly. Check the /', 'bulletproof-security').$bps_plugin_dir.__('/bulletproof-security/admin/htaccess/ folder to make sure the secure.htaccess file exists and is named secure.htaccess.', 'bulletproof-security');
	}
}

function bps_get_default_htaccess() {
$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';

	if ( file_exists($default_htaccess_file) ) {
		$bpsString = file_get_contents($default_htaccess_file);
		echo $bpsString;
	} else {
		$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
		_e('The default.htaccess file either does not exist or is not named correctly. Check the /', 'bulletproof-security').$bps_plugin_dir.__('/bulletproof-security/admin/htaccess/ folder to make sure the default.htaccess file exists and is named default.htaccess.', 'bulletproof-security');
	}
}

function bps_get_wpadmin_htaccess() {
$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';

	if ( file_exists($wpadmin_htaccess_file) ) {
		$bpsString = file_get_contents($wpadmin_htaccess_file);
		echo $bpsString;
	} else {
		$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
		_e('The wpadmin-secure.htaccess file either does not exist or is not named correctly. Check the /', 'bulletproof-security').$bps_plugin_dir.__('/bulletproof-security/admin/htaccess/ folder to make sure the wpadmin-secure.htaccess file exists and is named wpadmin-secure.htaccess.', 'bulletproof-security');
	}
}

// The current active root htaccess file - file check
function bps_get_root_htaccess() {
$root_htaccess_file = ABSPATH . '.htaccess';
	
	if ( file_exists($root_htaccess_file) ) {
		$bpsString = file_get_contents($root_htaccess_file);
		echo $bpsString;
	} else {
		_e('An htaccess file was not found in your website root folder.', 'bulletproof-security');
	}
}

// The current active wp-admin htaccess file - file check
function bps_get_current_wpadmin_htaccess_file() {
$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
	
	if ( file_exists($current_wpadmin_htaccess_file) ) {
		$bpsString = file_get_contents($current_wpadmin_htaccess_file);
		echo $bpsString;
	} else {
		_e('An htaccess file was not found in your wp-admin folder.', 'bulletproof-security');
	}
}

// File write checks for editor
function bps_secure_htaccess_file_check() {
$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
	
	if ( ! is_writable($secure_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the secure.htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_default_htaccess_file_check() {
$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
	
	if ( ! is_writable($default_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the default.htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_wpadmin_htaccess_file_check() {
$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
	
	if ( ! is_writable($wpadmin_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the wpadmin-secure.htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_root_htaccess_file_check() {
$root_htaccess_file = ABSPATH . '.htaccess';
	
	if ( ! is_writable($root_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the Root htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// File write checks for editor
function bps_current_wpadmin_htaccess_file_check() {
$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
	
	if ( ! is_writable($current_wpadmin_htaccess_file) ) {
		$text = '<font color="#fb0101"><strong>'.__('Cannot write to the wp-admin htaccess file. Cause: file Permission or file Ownership problem.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	}
}

// Get Domain Root without prefix
function bpsGetDomainRoot() {

	if ( is_admin() && current_user_can('manage_options') ) {
	if ( isset( $_SERVER['SERVER_NAME'] ) ) {

		$ServerName = str_replace( 'www.', "", esc_html( $_SERVER['SERVER_NAME'] ) );
		return $ServerName;		
	
	} else {
		$ServerName = str_replace( 'www.', "", esc_html( $_SERVER['HTTP_HOST'] ) );
		return $ServerName;	
	}
	}
}

// File and Folder Permission Checking
function bps_check_perms($path, $perm) {
clearstatcache();
$current_perms = @substr(sprintf('%o', fileperms($path)), -4);
$stat = @stat($path);

	echo '<table style="width:100%;background-color:#fff;">';
	echo '<tr>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:40%;">' . $path . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . $perm . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . $current_perms . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . $stat['uid'] . '</td>';
    echo '<td style="color:#000;background-color:#fff;padding:2px;width:15%;">' . @fileowner( $path ) . '</td>';
    echo '</tr>';
	echo '</table>';
}
	
// Get WordPress Root Installation Folder 
function bps_wp_get_root_folder() {

	if ( is_admin() && current_user_can('manage_options') ) {
		$site_root = parse_url(get_option('siteurl'));
	if ( isset( $site_root['path'] ) )
		$site_root = trailingslashit($site_root['path']);
	else
		$site_root = '/';
	return $site_root;
	}
}

// Display Root or Subfolder Installation Type
function bps_wp_get_root_folder_display_type() {
$site_root = parse_url(get_option('siteurl'));
	if ( isset( $site_root['path'] ) )
		$site_root = trailingslashit($site_root['path']);
	else
		$site_root = '/';
	if ( preg_match('/[a-zA-Z0-9]/', $site_root) ) {
		echo __('Subfolder Installation', 'bulletproof-security');
	} else {
		echo __('Root Folder Installation', 'bulletproof-security');
	}
}

// System Info page - Check for GWIOD
function bps_gwiod_site_type_check() {
$WordPress_Address_url = get_option('home');
$Site_Address_url = get_option('siteurl');
	
	if ( $WordPress_Address_url == $Site_Address_url ) {
		echo __('Standard WP Site Type', 'bulletproof-security');
	} else {
		echo __('GWIOD WP Site Type', 'bulletproof-security').'<br>';
		echo __('WordPress Address (URL): ', 'bulletproof-security').$WordPress_Address_url.'<br>';
		echo __('Site Address (URL): ', 'bulletproof-security').$Site_Address_url;
	}	
}

// System Info page - Check for BuddyPress
function bps_buddypress_site_type_check() {

	if ( function_exists('bp_is_active') ) {
		echo __('BuddyPress is installed|enabled', 'bulletproof-security');
	} else {
		echo __('BuddyPress is not installed|enabled', 'bulletproof-security');
	}
}

// System Info page - Check for bbPress
function bps_bbpress_site_type_check() {

	if ( function_exists('is_bbpress') ) {
		echo __('bbPress is installed|enabled', 'bulletproof-security');
	} else {
		echo __('bbPress is not installed|enabled', 'bulletproof-security');
	}
}

// System Info page - Check for Multisite/Subdirectory/Subdomain
function bps_multisite_check() {  
	
	if ( ! is_multisite() ) { 
		$text = __('Network|Multisite is not installed|enabled', 'bulletproof-security');
		echo $text;	
	
	} else {
		
		if ( ! is_subdomain_install() ) {
			$text = __('Subdirectory Site Type', 'bulletproof-security');
			echo $text;
		} else {
			$text = __('Subdomain Site Type', 'bulletproof-security');
			echo $text;			
		}
	}
}

// Get SQL Mode from WPDB
function bps_get_sql_mode() {
global $wpdb;
$sql_mode_var = 'sql_mode';
$mysqlinfo = $wpdb->get_results( $wpdb->prepare( "SHOW VARIABLES LIKE %s", $sql_mode_var ) );	
	
	if ( is_array( $mysqlinfo ) ) { 
		$sql_mode = $mysqlinfo[0]->Value;
		if ( empty( $sql_mode ) ) { 
			$sql_mode = __('Not Set', 'bulletproof-security');
		} else {
			$sql_mode = __('Off', 'bulletproof-security');
		}
	}
}

// Show DB errors should already be set to false in /includes/wp-db.php
// Extra function insurance show_errors = false
function bps_wpdb_errors_off() {
global $wpdb;
$wpdb->show_errors = false;
	
	if ( $wpdb->show_errors != false ) {
		$text = '<font color="#fb0101"><strong>'.__('DB Show Errors: On. DB errors will be displayed', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	} else {
		$text = '<strong>'.__('DB Show Errors: ', 'bulletproof-security').'</strong>'.__('Off', 'bulletproof-security').'<br>';
		echo $text;
	}	
}

// Maintenance Mode On Dashboard Alert
function bpsPro_mmode_dashboard_alert() {

if ( current_user_can('manage_options') ) {

	$MMoptions = get_option('bulletproof_security_options_maint_mode');

	if ( ! is_multisite() ) {
		
	if ( ! get_option('bulletproof_security_options_maint_mode') || $MMoptions['bps_maint_on_off'] == 'Off' ) {
	return;
	}	
	
		$indexPHP = ABSPATH . 'index.php';
		
		if ( file_exists($indexPHP) ) {
			$check_string_index = @file_get_contents($indexPHP);			
		}

		$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';		
		
		if ( file_exists($wpadminHtaccess) ) {
			$check_string_wpadmin = @file_get_contents($wpadminHtaccess);			
		}

		if ( $MMoptions['bps_maint_on_off'] == 'On' && $MMoptions['bps_maint_dashboard_reminder'] == '1' ) {	
	
			if ( strpos( $check_string_index, "BEGIN BPS MAINTENANCE MODE IP" ) && ! strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			} elseif ( ! strpos( $check_string_index, "BEGIN BPS MAINTENANCE MODE IP" ) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style=""background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);><font color="blue">'.__('Reminder: Backend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;	
			} elseif ( strpos( $check_string_index, "BEGIN BPS MAINTENANCE MODE IP" ) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend & Backend Maintenance Modes are Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			}
		}
	}
	
	if ( is_multisite() ) {
		global $current_blog, $blog_id;

		$root_folder_maintenance_values = ABSPATH . 'bps-maintenance-values.php';
		if ( file_exists($root_folder_maintenance_values) ) {		
			$check_string_values = @file_get_contents($root_folder_maintenance_values);			
		}
		
		$indexPHP = ABSPATH . 'index.php';
		if ( file_exists($indexPHP) ) {
			$check_string_index = @file_get_contents($indexPHP);
		}
		
		$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
		if ( file_exists($wpadminHtaccess) ) {		
			$check_string_wpadmin = @file_get_contents($wpadminHtaccess);
		}

		if ( $blog_id == 1 && $MMoptions['bps_maint_dashboard_reminder'] == '1' ) {

			if ( strpos( $check_string_values, '$all_sites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for The Primary Site and All Subsites.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}
		
			if ( strpos( $check_string_values, '$all_subsites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for All Subsites, but Not The Primary Site.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}	
	
		if ( $MMoptions['bps_maint_on_off'] == 'On' ) {

			if ( strpos( $check_string_index, '$primary_site_status = \'On\';' ) && ! strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			} elseif ( !strpos($check_string_index, '$primary_site_status = \'On\';') && strpos($check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP") ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Backend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;	
			} elseif ( strpos($check_string_index, '$primary_site_status = \'On\';') && strpos($check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP") ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend & Backend Maintenance Modes are Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			}
		}
		}
	
		if ( $blog_id != 1 ) {
		
			if ( is_subdomain_install() ) {
		
				$subsite_remove_slashes = str_replace( '.', "-", $current_blog->domain );	
	
			} else {
	
				$subsite_remove_slashes = str_replace( '/', "", $current_blog->path );
			}			
			
			$subsite_maintenance_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/bps-maintenance-'.$subsite_remove_slashes.'.php';		

			if ( strpos( $check_string_values, '$all_sites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for The Primary Site and All Subsites.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}
		
			if ( strpos( $check_string_values, '$all_subsites = \'1\';' ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On for All Subsites, but Not The Primary Site.', 'bulletproof-security').'</font></div>';
				echo $text;	
			}		
		
		if ( $MMoptions['bps_maint_on_off'] == 'On' && $MMoptions['bps_maint_dashboard_reminder'] == '1' ) {

			if ( file_exists($subsite_maintenance_file) && ! strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			} elseif ( ! file_exists($subsite_maintenance_file) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Backend Maintenance Mode is Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;	
			} elseif ( file_exists($subsite_maintenance_file) && strpos( $check_string_wpadmin, "BEGIN BPS MAINTENANCE MODE IP" ) ) {
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('Reminder: Frontend & Backend Maintenance Modes are Turned On.', 'bulletproof-security').'</font></div>';
				echo $text;				
			}		
		}
		}
	} // end is multisite
}
}

add_action('admin_notices', 'bpsPro_mmode_dashboard_alert');

// Login Security Disable Password Reset notice: Displays a message that backend password reset has been disabled
function bpsPro_login_security_password_reset_disabled_notice() {

	if ( current_user_can( 'update_core' ) ) {
	
		global $pagenow;

		if ( 'profile.php' == $pagenow || 'user-edit.php' == $pagenow || 'user-new.php' == $pagenow ) {
			$BPSoptions = get_option('bulletproof_security_options_login_security');		
		
			if ( $BPSoptions['bps_login_security_OnOff'] == 'On' && $BPSoptions['bps_login_security_pw_reset'] == 'disable' ) {
		
				$text = '<div class="update-nag" style="background-color:#dfecf2;border:1px solid #999;font-size:1em;font-weight:bold;padding:2px 5px;margin-top:2px;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><font color="blue">'.__('BPS Login Security Disable Password Reset Frontend & Backend is turned On.', 'bulletproof-security').'</font><br>'.__('Backend Password Reset has been disabled. To enable Backend Password Reset click ', 'bulletproof-security').'<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/login/login.php' ).'">'.esc_attr__('here', 'bulletproof-security').'</a></div>';
				echo $text;
			}
		}
	}
}

add_action('admin_notices', 'bpsPro_login_security_password_reset_disabled_notice');
add_action('network_admin_notices', 'bpsPro_login_security_password_reset_disabled_notice');

// One time manual htaccess code update added in BPS Pro .51.2
// NOTE: Instead of automating this, this needs to be done manually by users
// "Always On" flush_rewrite_rules code correction: Unfortunately this needs to be an "Always On" check in order for it to be 100% effective
// .52.9: added additional check for the BULLETPROOF string in the root htaccess file, otherwise on a new install the WP standard rewrite code will be deleted.
// .53: unlock the root htaccess file, autofix and lock the root htaccess file again.
function bpsPro_htaccess_manual_update_notice() {
	
	if ( current_user_can('manage_options') ) {
		
		$filename = ABSPATH . '.htaccess';
		
		if ( file_exists($filename) ) {
		
			$check_string = @file_get_contents($filename);
			$pattern = '/#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress/';

			if ( strpos( $check_string, "BULLETPROOF" ) && preg_match( $pattern, $check_string, $flush_matches ) ) {
				
				$root_perms = @substr(sprintf('%o', fileperms($filename)), -4);
				$sapi_type = php_sapi_name();
				$autolock = get_option('bulletproof_security_options_autolock');
	
				if 	( @$root_perms == '0404') {
					$lock = '0404';			
				}

				if ( @substr( $sapi_type, 0, 6 ) != 'apache' || @$root_perms != '0666' || @$root_perms != '0777' ) { // Windows IIS, XAMPP, etc
					@chmod($filename, 0644);
				}				
				
				$stringReplace = preg_replace('/#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress/', "", $check_string);			
			
				if ( file_put_contents($filename, $stringReplace) ) {
					
					if ( $autolock['bps_root_htaccess_autolock'] == 'On' || @$lock == '0404' ) {	
						@chmod($filename, 0404);
					}					
				}		
			}				
		
			global $pagenow;

			// manual steps if version of BPS root htaccess file is very old
			if ( 'plugins.php' == $pagenow || @preg_match( '/page=bulletproof-security\/admin\/core\/core\.php/', esc_html( $_SERVER['REQUEST_URI'], $matches ) ) ) {

				$pos = strpos( $check_string, 'IMPORTANT!!! DO NOT DELETE!!! - B E G I N Wordpress' );
			
				if ( $pos === false ) {
    		
					return;
			
				} else {
    		
					echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
					$text = '<strong><font color="blue">'.__('BPS Notice: One-time Update Steps Required', 'bulletproof-security').'</font><br>'.__('Significant changes were made to the root and wp-admin htaccess files that require doing the one-time Update Steps below.', 'bulletproof-security').'<br>'.__('All future BPS upgrades will not require these one-time Update Steps to be performed.', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/root-and-wp-admin-htaccess-file-significant-changes/" target="_blank" title="Link opens in a new Browser window" style="text-decoration:underline;">'.__('Click Here', 'bulletproof-security').'</a>'.__(' If you would like to know what changes were made to the root and wp-admin htaccess files.', 'bulletproof-security').'<br>'.__('This Notice will go away automatically after doing all of the steps below.', 'bulletproof-security').'<br><br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'" style="text-decoration:underline;">'.esc_attr__('Click Here', 'bulletproof-security').'</a>'.__(' to go to the BPS Security Modes page.', 'bulletproof-security').'<br>'.__('1. Click the Root Folder BulletProof Mode Activate button.', 'bulletproof-security').'<br>'.__('2. Click the wp-admin Folder BulletProof Mode Activate button.', 'bulletproof-security').'</strong>';
					echo $text;
					echo '</p></div>';	
				}
			}
		}
	}
}

add_action('admin_notices', 'bpsPro_htaccess_manual_update_notice');

function bpsPro_presave_ui_theme_skin_options() {
	
	$ui_theme_skin_options = 'bulletproof_security_options_theme_skin';	
	$bps_ui_theme_skin = array( 'bps_ui_theme_skin' => 'blue' );
			
	if ( ! get_option( $ui_theme_skin_options ) ) {			
		
		foreach( $bps_ui_theme_skin as $key => $value ) {
			update_option('bulletproof_security_options_theme_skin', $bps_ui_theme_skin);
		}
	}	
}

// .52.9: POST Request Attack Protection code correction|addition
// .53: Condition added to allow commenting out wp-admin URI whitelist rule
function bpsPro_post_request_protection_check() {

	$pattern1 = '/BPS\sPOST\sRequest\sAttack\sProtection/';
	$pattern2 = '/#\sNEVER\sCOMMENT\sOUT\sTHIS\sLINE\sOF\sCODE\sBELOW\sFOR\sANY\sREASON(\s*){1}RewriteCond\s%\{REQUEST_URI\}\s\!\^\.\*\/wp-admin\/\s\[NC\]/';
	$pattern3 = '/#\sNEVER\sCOMMENT\sOUT\sTHIS\sLINE\sOF\sCODE\sBELOW\sFOR\sANY\sREASON(\s*){1}#{1,}(\s|){1,}RewriteCond\s%\{REQUEST_URI\}\s\!\^\.\*\/wp-admin\/\s\[NC\]/';
	
	$CC_options = get_option('bulletproof_security_options_customcode');
	
	if ( preg_match( $pattern3, htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ), $matches ) ) {
		return;
	}

	if ( preg_match( $pattern1, htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ), $matches ) && ! preg_match( $pattern2, htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ), $matches ) ) {
		
		$bps_customcode_three = preg_replace('/RewriteCond\s%\{REQUEST_METHOD\}\sPOST\s\[NC\]/s', "RewriteCond %{REQUEST_METHOD} POST [NC]\n# NEVER COMMENT OUT THIS LINE OF CODE BELOW FOR ANY REASON\nRewriteCond %{REQUEST_URI} !^.*/wp-admin/ [NC]\n# Whitelist the WordPress Theme Customizer\nRewriteCond %{HTTP_REFERER} !^.*/wp-admin/customize.php", htmlspecialchars_decode( $CC_options['bps_customcode_three'], ENT_QUOTES ) );

	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_options['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_options['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_options['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_options['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_options['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_options['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_options['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_options['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_options['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_options['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_options['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> $CC_options['bps_customcode_bpsqse'], 
		'bps_customcode_deny_files' 		=> $CC_options['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $bps_customcode_three 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> $CC_options['bps_customcode_one'], 
		'bps_customcode_server_signature' 	=> $CC_options['bps_customcode_server_signature'], 
		'bps_customcode_directory_index' 	=> $CC_options['bps_customcode_directory_index'], 
		'bps_customcode_server_protocol' 	=> $CC_options['bps_customcode_server_protocol'], 
		'bps_customcode_error_logging' 		=> $CC_options['bps_customcode_error_logging'], 
		'bps_customcode_deny_dot_folders' 	=> $CC_options['bps_customcode_deny_dot_folders'], 
		'bps_customcode_admin_includes' 	=> $CC_options['bps_customcode_admin_includes'], 
		'bps_customcode_wp_rewrite_start' 	=> $CC_options['bps_customcode_wp_rewrite_start'], 
		'bps_customcode_request_methods' 	=> $CC_options['bps_customcode_request_methods'], 
		'bps_customcode_two' 				=> $CC_options['bps_customcode_two'], 
		'bps_customcode_timthumb_misc' 		=> $CC_options['bps_customcode_timthumb_misc'], 
		'bps_customcode_bpsqse' 			=> $CC_options['bps_customcode_bpsqse'], 
		'bps_customcode_wp_rewrite_end' 	=> $CC_options['bps_customcode_wp_rewrite_end'], 
		'bps_customcode_deny_files' 		=> $CC_options['bps_customcode_deny_files'], 
		'bps_customcode_three' 				=> $bps_customcode_three 
		);					
	}

		foreach( $Root_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $Root_CC_Options);
		}
	}
}

// Check if WordPress Debugging & Debug Logging is turned on and display a message
// Note: cannot check defined('WP_DEBUG_DISPLAY') && true == WP_DEBUG_DISPLAY because it is turned On and is true by default.
function bpsPro_wp_debug_check() {

	if ( is_admin() && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['QUERY_STRING']), $matches) ) {
		
		if ( defined('WP_DEBUG') && true == WP_DEBUG || defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
		}	

		if ( defined('WP_DEBUG') && true == WP_DEBUG ) {
	
			$text = '<strong><font color="blue">'.__('WordPress Debugging is turned On in your wp-config.php file', 'bulletproof-security').'</font><br>'.__('You are currently using ', 'bulletproof-security').'define(\'WP_DEBUG\', true)'.__(' in your wp-config.php file. To turn WP Debugging Off, change true to false in your wp-config.php file.', 'bulletproof-security').'</strong><br>';
			echo $text;
		}

		if ( defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
	
			$text = '<strong><font color="blue">'.__('WordPress Debug Logging is turned On in your wp-config.php file', 'bulletproof-security').'</font><br>'.__('You are currently using ', 'bulletproof-security').'define(\'WP_DEBUG_LOG\', true)'.__(' in your wp-config.php file to log errors to the WordPress debug.log file. To turn WP Debug Logging Off, change true to false in your wp-config.php file.', 'bulletproof-security').'</strong><br>';
			echo $text;
			 
			}

		if ( defined('WP_DEBUG') && true == WP_DEBUG || defined('WP_DEBUG_LOG') && true == WP_DEBUG_LOG ) {
			echo '</p></div>';
		}
	}
}

add_action('admin_notices', 'bpsPro_wp_debug_check');

// .53.6: Reset php handler dismiss notice if Wordfence WAF disaster code is detected.
// Note: Has the extra added benefit of checking if the code was put in the correct CC text box.
function bpsPro_php_handler_dismiss_notice_reset() {
global $current_user;
$user_id = $current_user->ID;
$file = ABSPATH . '.htaccess';		
	
	if ( file_exists($file) ) {		

		$file_contents = @file_get_contents($file);
		$CustomCodeoptions = get_option('bulletproof_security_options_customcode');
		preg_match( '/Wordfence WAF/', $CustomCodeoptions['bps_customcode_one'], $DBmatches );

		if ( stripos( $file_contents, "Wordfence WAF" ) && ! $DBmatches[0] ) {
			
			delete_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice');
		}
	}
}

// BPS upgrade: adds/updates/saves any new DB options, does cleanup & everything else.
// This function is executed in this function: bpsPro_new_feature_autoupdate() which is executed ONLY during BPS upgrades.
// .53.1: This function has been completely changed: literally checks if a DB option exists and has a value using ternary operations.
// If a DB option does not exist then update it with a default value else resave existing DB option values.
// NOTE: All DB options will not be added here. Only critical new DB options. For every other situation an uninstall/reinstall & Setup Wizard rerun is required.
function bpsPro_new_version_db_options_files_autoupdate() {
	
	if ( current_user_can('manage_options') ) {
		global $bps_version, $bps_last_version, $wp_version, $wpdb, $aitpro_bullet, $pagenow;
	
		// .54.3: BugFix: pre-save bps_isl_custom_roles as an array
		// .54.2: Added Custom User Roles. bps_isl_uri_exclusions was not added/updated in previous BPS version.
		// .53.8: Add/Update new ISL DB options. conditional on whether someone is using ISL or not.
		$isl_options = get_option('bulletproof_security_options_idle_session');
		
		if ( $isl_options['bps_isl'] == 'On' || $isl_options['bps_isl'] == 'Off' ) {

		if ( ! $isl_options['bps_isl_custom_roles'] ) {
			$isl_options18 = array( 'bps', '' );
		
		} else {

			foreach ( $isl_options as $key => $value ) {
		
				if ( $key == 'bps_isl_custom_roles' ) {
					
					if ( ! is_array($value) ) {
						$isl_options18 = array( 'bps', '' );
					} else { 
						$isl_options18 = $isl_options['bps_isl_custom_roles'];
					}
				}
			}	
		}

		$isl_db_options = array(
		'bps_isl' 							=> $isl_options['bps_isl'], 
		'bps_isl_timeout' 					=> $isl_options['bps_isl_timeout'], 
		'bps_isl_logout_url' 				=> $isl_options['bps_isl_logout_url'], 
		'bps_isl_login_url' 				=> $isl_options['bps_isl_login_url'], 
		'bps_isl_custom_message' 			=> $isl_options['bps_isl_custom_message'], 
		'bps_isl_custom_css_1' 				=> $isl_options['bps_isl_custom_css_1'], 
		'bps_isl_custom_css_2' 				=> $isl_options['bps_isl_custom_css_2'], 
		'bps_isl_custom_css_3' 				=> $isl_options['bps_isl_custom_css_3'], 
		'bps_isl_custom_css_4' 				=> $isl_options['bps_isl_custom_css_4'], 
		'bps_isl_user_account_exceptions'	=> $isl_options['bps_isl_user_account_exceptions'],  
		'bps_isl_administrator' 			=> $isl_options['bps_isl_administrator'], 
		'bps_isl_editor' 					=> $isl_options['bps_isl_editor'], 
		'bps_isl_author' 					=> $isl_options['bps_isl_author'], 
		'bps_isl_contributor' 				=> $isl_options['bps_isl_contributor'], 
		'bps_isl_subscriber' 				=> $isl_options['bps_isl_subscriber'], 
		'bps_isl_tinymce' 					=> $isl_options['bps_isl_tinymce'], 
		'bps_isl_uri_exclusions' 			=> $isl_options['bps_isl_uri_exclusions'], 
		'bps_isl_custom_roles' 				=> $isl_options18 
		);

		foreach( $isl_db_options as $key => $value ) {
			update_option('bulletproof_security_options_idle_session', $isl_db_options);
		}
		}

		// .54.3: BugFix: pre-save ACE db options.
		$ace_options = get_option('bulletproof_security_options_auth_cookie');  
	
		if ( $ace_options['bps_ace'] == 'On' || $ace_options['bps_ace'] == 'Off' ) {
			
		if ( ! $ace_options['bps_ace_custom_roles'] ) {
			$ace_options11 = array( 'bps', '' );
		
		} else {
			
			foreach ( $ace_options as $key => $value ) {
		
				if ( $key == 'bps_ace_custom_roles' ) {
					
					if ( ! is_array($value) ) {
						$ace_options11 = array( 'bps', '' );
					} else { 
						$ace_options11 = $ace_options['bps_ace_custom_roles'];
					}
				}
			}	
		}
		
		$ace_db_options = array(
		'bps_ace' 							=> $ace_options['bps_ace'], 
		'bps_ace_expiration' 				=> $ace_options['bps_ace_expiration'], 
		'bps_ace_rememberme_expiration' 	=> $ace_options['bps_ace_rememberme_expiration'], 
		'bps_ace_user_account_exceptions' 	=> $ace_options['bps_ace_user_account_exceptions'], 
		'bps_ace_administrator' 			=> $ace_options['bps_ace_administrator'], 
		'bps_ace_editor' 					=> $ace_options['bps_ace_editor'], 
		'bps_ace_author' 					=> $ace_options['bps_ace_author'], 
		'bps_ace_contributor' 				=> $ace_options['bps_ace_contributor'], 
		'bps_ace_subscriber' 				=> $ace_options['bps_ace_subscriber'], 
		'bps_ace_rememberme_disable'		=> $ace_options['bps_ace_rememberme_disable'],  
		'bps_ace_custom_roles' 				=> $ace_options11 
		);

		foreach( $ace_db_options as $key => $value ) {
			update_option('bulletproof_security_options_auth_cookie', $ace_db_options);
		}
		}

		// .54.3: New Enable LSM for WooCommerce option added
		// .51.8: New Login Security Attempts Remaining option added
		$lsm = get_option('bulletproof_security_options_login_security');	
	
		if ( $woo_return_var == 1 ) {
			if ( ! $lsm['bps_enable_lsm_woocommerce'] ) {
				$bps_enable_lsm_woocommerce = '1';
			} else {
				$bps_enable_lsm_woocommerce = $lsm['bps_enable_lsm_woocommerce'];
			}
		} else {
			$bps_enable_lsm_woocommerce = $lsm['bps_enable_lsm_woocommerce'];
		}

		$lsm1 = ! $lsm['bps_max_logins'] ? '3' : $lsm['bps_max_logins'];
		$lsm2 = ! $lsm['bps_lockout_duration'] ? '60' : $lsm['bps_lockout_duration'];
		$lsm3 = ! $lsm['bps_manual_lockout_duration'] ? '60' : $lsm['bps_manual_lockout_duration'];
		$lsm4 = ! $lsm['bps_max_db_rows_display'] ? '' : $lsm['bps_max_db_rows_display'];
		$lsm5 = ! $lsm['bps_login_security_OnOff'] ? 'On' : $lsm['bps_login_security_OnOff'];
		$lsm6 = ! $lsm['bps_login_security_logging'] ? 'logLockouts' : $lsm['bps_login_security_logging'];
		$lsm7 = ! $lsm['bps_login_security_errors'] ? 'wpErrors' : $lsm['bps_login_security_errors'];
		$lsm8 = ! $lsm['bps_login_security_remaining'] ? 'On' : $lsm['bps_login_security_remaining'];
		$lsm9 = ! $lsm['bps_login_security_pw_reset'] ? 'enable' : $lsm['bps_login_security_pw_reset'];
		$lsm10 = ! $lsm['bps_login_security_sort'] ? 'ascending' : $lsm['bps_login_security_sort'];

		$lsm_options = array(
		'bps_max_logins' 				=> $lsm1, 
		'bps_lockout_duration' 			=> $lsm2, 
		'bps_manual_lockout_duration' 	=> $lsm3, 
		'bps_max_db_rows_display' 		=> $lsm4, 
		'bps_login_security_OnOff' 		=> $lsm5, 
		'bps_login_security_logging' 	=> $lsm6, 
		'bps_login_security_errors' 	=> $lsm7, 
		'bps_login_security_remaining' 	=> $lsm8, 
		'bps_login_security_pw_reset' 	=> $lsm9, 
		'bps_login_security_sort' 		=> $lsm10, 
		'bps_enable_lsm_woocommerce' 	=> $bps_enable_lsm_woocommerce 
		);

		foreach( $lsm_options as $key => $value ) {
			update_option('bulletproof_security_options_login_security', $lsm_options);
		}

		// .53.9: Custom default.htacces File
		// If a /bps-backup/master-backups/default.htaccess exists then copy it to /htaccess/default.htaccess
		$custom_default_htaccess = WP_CONTENT_DIR . '/bps-backup/master-backups/default.htaccess';
		$DefaultHtaccess = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
		if ( file_exists($custom_default_htaccess) ) {
			copy($custom_default_htaccess, $DefaultHtaccess);
		}

		// .53.8: Add/Update new Hidden|Empty Plugin Folders|Files Cron DB options.
		$hpf_cron = get_option('bulletproof_security_options_hpf_cron');

		$hpf_cron1 = ! $hpf_cron['bps_hidden_plugins_cron'] ? 'On' : $hpf_cron['bps_hidden_plugins_cron'];
		$hpf_cron2 = ! $hpf_cron['bps_hidden_plugins_cron_frequency'] ? '15' : $hpf_cron['bps_hidden_plugins_cron_frequency'];
		$hpf_cron3 = ! $hpf_cron['bps_hidden_plugins_cron_email'] ? '' : $hpf_cron['bps_hidden_plugins_cron_email'];
		$hpf_cron4 = ! $hpf_cron['bps_hidden_plugins_cron_alert'] ? '' : $hpf_cron['bps_hidden_plugins_cron_alert'];	
		
		$hpf_cron_options = array(
		'bps_hidden_plugins_cron' 			=> $hpf_cron1, 
		'bps_hidden_plugins_cron_frequency' => $hpf_cron2, 
		'bps_hidden_plugins_cron_email' 	=> $hpf_cron3, 
		'bps_hidden_plugins_cron_alert' 	=> $hpf_cron4 
		);

		foreach( $hpf_cron_options as $key => $value ) {
			update_option('bulletproof_security_options_hpf_cron', $hpf_cron_options);
		}			
		
		// .53.8: Add/Update new Hidden|Empty Plugin Folders|Files Ignore Hidden Plugin Folders & Files DB options.
		$hpf_check = get_option('bulletproof_security_options_hidden_plugins');
		$hpf_check1 = ! $hpf_check['bps_hidden_plugins_check'] ? '' : $hpf_check['bps_hidden_plugins_check'];
		
		$hpf_check_options = array( 'bps_hidden_plugins_check' => $hpf_check1 );

		foreach( $hpf_check_options as $key => $value ) {
			update_option('bulletproof_security_options_hidden_plugins', $hpf_check_options);
		}

		// .53.6: Wordfence WAF mess - Reset php handler dismiss notice.
		bpsPro_php_handler_dismiss_notice_reset();

		// .53.5: Old obsolete function deleted and code moved here.
		// Email Alerting & Log file zip, email and deleting DB options.
		$email_log = get_option('bulletproof_security_options_email');
		$admin_email = get_option('admin_email');

		$email_log1 = ! $email_log['bps_send_email_to'] ? $admin_email : $email_log['bps_send_email_to'];
		$email_log2 = ! $email_log['bps_send_email_from'] ? $admin_email : $email_log['bps_send_email_from'];
		$email_log3 = ! $email_log['bps_send_email_cc'] ? '' : $email_log['bps_send_email_cc'];
		$email_log4 = ! $email_log['bps_send_email_bcc'] ? '' : $email_log['bps_send_email_bcc'];
		$email_log5 = ! $email_log['bps_login_security_email'] ? 'lockoutOnly' : $email_log['bps_login_security_email'];
		$email_log6 = ! $email_log['bps_security_log_size'] ? '500KB' : $email_log['bps_security_log_size'];
		$email_log7 = ! $email_log['bps_security_log_emailL'] ? 'email' : $email_log['bps_security_log_emailL'];
		$email_log8 = ! $email_log['bps_dbb_log_email'] ? 'email' : $email_log['bps_dbb_log_email'];
		$email_log9 = ! $email_log['bps_dbb_log_size'] ? '500KB' : $email_log['bps_dbb_log_size'];

		$email_log_options = array(
		'bps_send_email_to' 			=> $email_log1, 
		'bps_send_email_from' 			=> $email_log2, 
		'bps_send_email_cc' 			=> $email_log3, 
		'bps_send_email_bcc' 			=> $email_log4, 
		'bps_login_security_email' 		=> $email_log5, 
		'bps_security_log_size' 		=> $email_log6, 
		'bps_security_log_emailL' 		=> $email_log7, 
		'bps_dbb_log_email' 			=> $email_log8, 
		'bps_dbb_log_size' 				=> $email_log9 
		);

		foreach( $email_log_options as $key => $value ) {
			update_option('bulletproof_security_options_email', $email_log_options);
		}

		// .53: Condition added to allow commenting out wp-admin URI whitelist rule
		// .52.9: POST Request Attack Protection code correction|addition
		bpsPro_post_request_protection_check();

		// .52.7: Set Security Log Limit POST Request Body Data option to checked/limited by default
		$bps_seclog_post_limit_Options = 'bulletproof_security_options_sec_log_post_limit';			

		$seclog_post_limit_Options = array( 'bps_security_log_post_limit' => '1' );
			
		if ( ! get_option( $bps_seclog_post_limit_Options ) ) {			
		
			foreach( $seclog_post_limit_Options as $key => $value ) {
				update_option('bulletproof_security_options_sec_log_post_limit', $seclog_post_limit_Options);
			}
		}

		// BPS .52.6: Pre-save UI Theme Skin with Blue Theme if DB option does not exist
		bpsPro_presave_ui_theme_skin_options();

		// .52.3: If Custom Code db options do not exist yet, create blank values
		$ccr = get_option('bulletproof_security_options_customcode');
	
		$ccr1 = ! $ccr['bps_customcode_one'] ? '' : $ccr['bps_customcode_one'];
		$ccr2 = ! $ccr['bps_customcode_server_signature'] ? '' : $ccr['bps_customcode_server_signature'];
		$ccr3 = ! $ccr['bps_customcode_directory_index'] ? '' : $ccr['bps_customcode_directory_index'];
		$ccr4 = ! $ccr['bps_customcode_server_protocol'] ? '' : $ccr['bps_customcode_server_protocol'];
		$ccr5 = ! $ccr['bps_customcode_error_logging'] ? '' : $ccr['bps_customcode_error_logging'];
		$ccr6 = ! $ccr['bps_customcode_deny_dot_folders'] ? '' : $ccr['bps_customcode_deny_dot_folders'];
		$ccr7 = ! $ccr['bps_customcode_admin_includes'] ? '' : $ccr['bps_customcode_admin_includes'];
		$ccr8 = ! $ccr['bps_customcode_wp_rewrite_start'] ? '' : $ccr['bps_customcode_wp_rewrite_start'];
		$ccr9 = ! $ccr['bps_customcode_request_methods'] ? '' : $ccr['bps_customcode_request_methods'];
		$ccr10 = ! $ccr['bps_customcode_two'] ? '' : $ccr['bps_customcode_two'];
		$ccr11 = ! $ccr['bps_customcode_timthumb_misc'] ? '' : $ccr['bps_customcode_timthumb_misc'];
		$ccr12 = ! $ccr['bps_customcode_bpsqse'] ? '' : $ccr['bps_customcode_bpsqse'];
		$ccr12m = @! $ccr['bps_customcode_wp_rewrite_end'] ? '' : $ccr['bps_customcode_wp_rewrite_end'];
		$ccr13 = ! $ccr['bps_customcode_deny_files'] ? '' : $ccr['bps_customcode_deny_files'];
		$ccr14 = ! $ccr['bps_customcode_three'] ? '' : $ccr['bps_customcode_three'];

		if ( ! is_multisite() ) {

			$ccr_options = array(
			'bps_customcode_one' 				=> $ccr1, 
			'bps_customcode_server_signature' 	=> $ccr2, 
			'bps_customcode_directory_index' 	=> $ccr3, 
			'bps_customcode_server_protocol' 	=> $ccr4, 
			'bps_customcode_error_logging' 		=> $ccr5, 
			'bps_customcode_deny_dot_folders' 	=> $ccr6, 
			'bps_customcode_admin_includes' 	=> $ccr7, 
			'bps_customcode_wp_rewrite_start' 	=> $ccr8, 
			'bps_customcode_request_methods' 	=> $ccr9, 
			'bps_customcode_two' 				=> $ccr10, 
			'bps_customcode_timthumb_misc' 		=> $ccr11, 
			'bps_customcode_bpsqse' 			=> $ccr12, 
			'bps_customcode_deny_files' 		=> $ccr13, 
			'bps_customcode_three' 				=> $ccr14
			);
				
		} else {
					
			$ccr_options = array(
			'bps_customcode_one' 				=> $ccr1, 
			'bps_customcode_server_signature' 	=> $ccr2, 
			'bps_customcode_directory_index' 	=> $ccr3, 
			'bps_customcode_server_protocol' 	=> $ccr4, 
			'bps_customcode_error_logging' 		=> $ccr5, 
			'bps_customcode_deny_dot_folders' 	=> $ccr6, 
			'bps_customcode_admin_includes' 	=> $ccr7, 
			'bps_customcode_wp_rewrite_start' 	=> $ccr8, 
			'bps_customcode_request_methods' 	=> $ccr9, 
			'bps_customcode_two' 				=> $ccr10, 
			'bps_customcode_timthumb_misc' 		=> $ccr11, 
			'bps_customcode_bpsqse' 			=> $ccr12, 
			'bps_customcode_wp_rewrite_end' 	=> $ccr12m, 
			'bps_customcode_deny_files' 		=> $ccr13, 
			'bps_customcode_three' 				=> $ccr14
			);					
		}

		foreach( $ccr_options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $ccr_options);
		}

		$ccw = get_option('bulletproof_security_options_customcode_WPA');
	
		$ccw1 = ! $ccw['bps_customcode_deny_files_wpa'] ? '' : $ccw['bps_customcode_deny_files_wpa'];
		$ccw2 = ! $ccw['bps_customcode_one_wpa'] ? '' : $ccw['bps_customcode_one_wpa'];
		$ccw3 = ! $ccw['bps_customcode_two_wpa'] ? '' : $ccw['bps_customcode_two_wpa'];
		$ccw4 = ! $ccw['bps_customcode_bpsqse_wpa'] ? '' : $ccw['bps_customcode_bpsqse_wpa'];
	
		$ccw_options = array(
		'bps_customcode_deny_files_wpa' => $ccw1, 
		'bps_customcode_one_wpa' 		=> $ccw2, 
		'bps_customcode_two_wpa' 		=> $ccw3, 
		'bps_customcode_bpsqse_wpa' 	=> $ccw4
		);
			
		foreach( $ccw_options as $key => $value ) {
			update_option('bulletproof_security_options_customcode_WPA', $ccw_options);
		}

		$bps_option_name_dbb = 'bulletproof_security_options_DBB_log';
		$bps_new_value_dbb = bpsPro_DBB_LogLastMod_wp_secs();
		$BPS_Options_dbb = array( 'bps_dbb_log_date_mod' => $bps_new_value_dbb );

		if ( ! get_option( $bps_option_name_dbb ) ) {	
		
			foreach( $BPS_Options_dbb as $key => $value ) {
				update_option('bulletproof_security_options_DBB_log', $BPS_Options_dbb);
			}
		}

		// Save the Setup Wizard DB option only if it does not already exist
		$bps_setup_wizard = 'bulletproof_security_options_wizard_free';
		$BPS_Wizard = array( 'bps_wizard_free' => 'upgrade' );	
	
		if ( ! get_option( $bps_setup_wizard ) ) {	
		
			foreach( $BPS_Wizard as $key => $value ) {
				update_option('bulletproof_security_options_wizard_free', $BPS_Wizard);
			}
		}

		// Misc cleanup, etc.
		// delete the old Maintenance Mode DB option - added in BPS .49.9
		if ( get_option('bulletproof_security_options_maint') ) {	
			delete_option('bulletproof_security_options_maint');
		}
		// Delete all the old plugin api junk content in this transient
		delete_transient( 'bulletproof-security_info' );
	}
}

?>