<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists ('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

function bulletproof_security_admin_init() {
global $wpdb, $wp_version, $blog_id;

	if ( is_multisite() && $blog_id != 1 ) {

	$Ltable_name = $wpdb->prefix . "bpspro_login_security";

	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $Ltable_name ) ) != $Ltable_name ) {
	
	$sql = "CREATE TABLE $Ltable_name (
  id bigint(20) NOT NULL auto_increment,
  status varchar(60) NOT NULL default '',
  user_id varchar(60) NOT NULL default '',
  username varchar(60) NOT NULL default '',
  public_name varchar(250) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  role varchar(15) NOT NULL default '',
  human_time datetime NOT NULL default '0000-00-00 00:00:00',
  login_time varchar(10) NOT NULL default '',
  lockout_time varchar(10) NOT NULL default '',
  failed_logins varchar(2) NOT NULL default '',
  ip_address varchar(45) NOT NULL default '',
  hostname varchar(60) NOT NULL default '',
  request_uri varchar(255) NOT NULL default '',
  UNIQUE KEY id (id)
    );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	}	

	} else {

	$Stable_name = $wpdb->prefix . "bpspro_seclog_ignore";
	$Ltable_name = $wpdb->prefix . "bpspro_login_security";
	$DBBtable_name = $wpdb->prefix . "bpspro_db_backup";

	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $Stable_name ) ) != $Stable_name ) {	
	
	$sql = "CREATE TABLE $Stable_name (
  id bigint(20) NOT NULL auto_increment,
  time datetime NOT NULL default '0000-00-00 00:00:00',
  user_agent_bot text NOT NULL,
  UNIQUE KEY id (id)
    );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	}

	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $Ltable_name ) ) != $Ltable_name ) {	
	
	$sql = "CREATE TABLE $Ltable_name (
  id bigint(20) NOT NULL auto_increment,
  status varchar(60) NOT NULL default '',
  user_id varchar(60) NOT NULL default '',
  username varchar(60) NOT NULL default '',
  public_name varchar(250) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  role varchar(15) NOT NULL default '',
  human_time datetime NOT NULL default '0000-00-00 00:00:00',
  login_time varchar(10) NOT NULL default '',
  lockout_time varchar(10) NOT NULL default '',
  failed_logins varchar(2) NOT NULL default '',
  ip_address varchar(45) NOT NULL default '',
  hostname varchar(60) NOT NULL default '',
  request_uri varchar(255) NOT NULL default '',
  UNIQUE KEY id (id)
    );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	}

	// last job, next job is updated by the cron - job size is the total size of all tables selected in that job
	if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $DBBtable_name ) ) != $DBBtable_name ) {	
	
	$sql = "CREATE TABLE $DBBtable_name (
  bps_id bigint(20) NOT NULL auto_increment,
  bps_table_name text NOT NULL,
  bps_desc text NOT NULL,
  bps_job_type varchar(9) NOT NULL default '',
  bps_frequency varchar(7) NOT NULL default '',
  bps_last_job varchar(30) NOT NULL default '',
  bps_next_job varchar(30) NOT NULL default '',
  bps_next_job_unix varchar(10) NOT NULL default '',  
  bps_email_zip varchar(10) NOT NULL default '',
  bps_job_created datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY bps_id (bps_id)
    );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	}
	}
	
// Whitelist BPS DB options: Total: 31
register_setting('bulletproof_security_options', 'bulletproof_security_options', 'bulletproof_security_options_validate');
register_setting('bulletproof_security_options_SLF', 'bulletproof_security_options_SLF', 'bulletproof_security_options_validate_SLF');
register_setting('bulletproof_security_options_debug', 'bulletproof_security_options_debug', 'bulletproof_security_options_validate_debug');
register_setting('bulletproof_security_options_DBB_log', 'bulletproof_security_options_DBB_log', 'bulletproof_security_options_validate_DBB_log');
register_setting('bulletproof_security_options_autolock', 'bulletproof_security_options_autolock', 'bulletproof_security_options_validate_autolock');
register_setting('bulletproof_security_options_db_backup', 'bulletproof_security_options_db_backup', 'bulletproof_security_options_validate_db_backup');
register_setting('bulletproof_security_options_wpt_nodes', 'bulletproof_security_options_wpt_nodes', 'bulletproof_security_options_validate_wpt_nodes');
register_setting('bulletproof_security_options_customcode', 'bulletproof_security_options_customcode', 'bulletproof_security_options_validate_customcode');
register_setting('bulletproof_security_options_autoupdate', 'bulletproof_security_options_autoupdate', 'bulletproof_security_options_validate_autoupdate');
register_setting('bulletproof_security_options_wizard_free', 'bulletproof_security_options_wizard_free', 'bulletproof_security_options_validate_wizard_free');	
register_setting('bulletproof_security_options_pop_uninstall', 'bulletproof_security_options_pop_uninstall', 'bulletproof_security_options_validate_pop_uninstall');
register_setting('bulletproof_security_options_customcode_WPA', 'bulletproof_security_options_customcode_WPA', 'bulletproof_security_options_validate_customcode_WPA');
register_setting('bulletproof_security_options_apache_modules', 'bulletproof_security_options_apache_modules', 'bulletproof_security_options_validate_apache_modules');
register_setting('bulletproof_security_options_hidden_plugins', 'bulletproof_security_options_hidden_plugins', 'bulletproof_security_options_validate_hidden_plugins');
register_setting('bulletproof_security_options_setup_wizard_woo', 'bulletproof_security_options_setup_wizard_woo', 'bulletproof_security_options_validate_setup_wizard_woo');
register_setting('bulletproof_security_options_sec_log_post_limit', 'bulletproof_security_options_sec_log_post_limit', 'bulletproof_security_options_validate_sec_log_post_limit');
register_setting('bulletproof_security_options_status_display', 'bulletproof_security_options_status_display', 'bulletproof_security_options_validate_status_display');
register_setting('bulletproof_security_options_login_security', 'bulletproof_security_options_login_security', 'bulletproof_security_options_validate_login_security');
register_setting('bulletproof_security_options_htaccess_files', 'bulletproof_security_options_htaccess_files', 'bulletproof_security_options_validate_htaccess_files');
register_setting('bulletproof_security_options_idle_session', 'bulletproof_security_options_idle_session', 'bulletproof_security_options_validate_idle_session');
register_setting('bulletproof_security_options_htaccess_res', 'bulletproof_security_options_htaccess_res', 'bulletproof_security_options_validate_htaccess_res');
register_setting('bulletproof_security_options_auth_cookie', 'bulletproof_security_options_auth_cookie', 'bulletproof_security_options_validate_auth_cookie');	
register_setting('bulletproof_security_options_maint_mode', 'bulletproof_security_options_maint_mode', 'bulletproof_security_options_validate_maint_mode');
register_setting('bulletproof_security_options_theme_skin', 'bulletproof_security_options_theme_skin', 'bulletproof_security_options_validate_theme_skin');
register_setting('bulletproof_security_options_scrolltop', 'bulletproof_security_options_scrolltop', 'bulletproof_security_options_validate_scrolltop');
register_setting('bulletproof_security_options_hpf_cron', 'bulletproof_security_options_hpf_cron', 'bulletproof_security_options_validate_hpf_cron');
register_setting('bulletproof_security_options_spinner', 'bulletproof_security_options_spinner', 'bulletproof_security_options_validate_spinner');
register_setting('bulletproof_security_options_mynotes', 'bulletproof_security_options_mynotes', 'bulletproof_security_options_validate_mynotes');
register_setting('bulletproof_security_options_zip_fix', 'bulletproof_security_options_zip_fix', 'bulletproof_security_options_validate_zip_fix');
register_setting('bulletproof_security_options_email', 'bulletproof_security_options_email', 'bulletproof_security_options_validate_email');			
register_setting('bulletproof_security_options_GDMW', 'bulletproof_security_options_GDMW', 'bulletproof_security_options_validate_GDMW');
	
	// Create BPS Backup Folder
	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup' ) ) {
		@mkdir( WP_CONTENT_DIR . '/bps-backup', 0755, true );
		@chmod( WP_CONTENT_DIR . '/bps-backup/', 0755 );
	}
	
	// Create master backups folder
	if ( ! is_dir( WP_CONTENT_DIR . '/bps-backup/master-backups' ) ) {
		@mkdir( WP_CONTENT_DIR . '/bps-backup/master-backups', 0755, true );
		@chmod( WP_CONTENT_DIR . '/bps-backup/master-backups/', 0755 );
	}

	// Create logs folder
	if( ! is_dir( WP_CONTENT_DIR . '/bps-backup/logs' ) ) {
		@mkdir( WP_CONTENT_DIR . '/bps-backup/logs', 0755, true );
		@chmod( WP_CONTENT_DIR . '/bps-backup/logs/', 0755 );
	}

	// Create backups folder with randomly generated folder name & save the backups folder name to the DB
	bpsPro_create_db_backup_folder();

	// Create the Security/HTTP error log in /logs
	$bpsProLog = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/http_error_log.txt';
	$bpsProLogARQ = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
	
	if ( ! file_exists($bpsProLogARQ) ) {
		@copy($bpsProLog, $bpsProLogARQ);
	}	

	// Create the DB Backup log in /logs
	$bpsProDBBLog = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/db_backup_log.txt';
	$bpsProDBBLogARQ = WP_CONTENT_DIR . '/bps-backup/logs/db_backup_log.txt';
	
	if ( ! file_exists($bpsProDBBLogARQ) ) {
		@copy($bpsProDBBLog, $bpsProDBBLogARQ);
	}
}

// BPS Menu
function bulletproof_security_admin_menu() {
global $blog_id;
	
	if ( current_user_can('manage_options') ) {
	
	// Network/Multisite display partial BPS menus
	if ( is_multisite() && $blog_id != 1 ) {

	add_menu_page(__('BulletProof Security Settings', 'bulletproof-security'), __('BPS Security', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/login/login.php', '', plugins_url('bulletproof-security/admin/images/bps-icon-small.png'));
	add_submenu_page('bulletproof-security/admin/login/login.php', __('Login Security', 'bulletproof-security'), __('Login Security', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/login/login.php' );
	
	// Do not display the Maintenance Mode menu for GDMW hosted sites
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] != 'disabled' || $GDMW_options['bps_gdmw_hosting'] != 'yes' ) {		
	add_submenu_page('bulletproof-security/admin/login/login.php', __('Maintenance Mode', 'bulletproof-security'), __('Maintenance Mode', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/maintenance/maintenance.php' );
	}
	
	add_submenu_page('bulletproof-security/admin/login/login.php', __('System Info', 'bulletproof-security'), __('System Info', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/system-info/system-info.php' );
	add_submenu_page('bulletproof-security/admin/login/login.php', __('UI|UX Settings', 'bulletproof-security'), __('UI|UX Settings', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/theme-skin/theme-skin.php' );
	
	} else {

	add_menu_page(__('BulletProof Security ~ htaccess Core', 'bulletproof-security'), __('BPS Security', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/core/core.php', '', plugins_url('bulletproof-security/admin/images/bps-icon-small.png'));
	add_submenu_page('bulletproof-security/admin/core/core.php', __('BulletProof Security ~ htaccess Core', 'bulletproof-security'), __('htaccess Core', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/core/core.php' );
	add_submenu_page('bulletproof-security/admin/core/core.php', __('Login Security ~ ISL ~ ACE', 'bulletproof-security'), __('Login Security', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/login/login.php' );
	add_submenu_page('bulletproof-security/admin/core/core.php', __('Login Security ~ ISL ~ ACE', 'bulletproof-security'), __('Idle Session Logout<br>Cookie Expiration', 'bulletproof-security'), 'manage_options', 'admin.php?page=bulletproof-security/admin/login/login.php#bps-tabs-2' );
	add_submenu_page('bulletproof-security/admin/core/core.php', __('DB Backup & Security', 'bulletproof-security'), __('DB Backup', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/db-backup-security/db-backup-security.php' );
	add_submenu_page('bulletproof-security/admin/core/core.php', __('Security Log', 'bulletproof-security'), __('Security Log', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/security-log/security-log.php' );
	
	// Do not display the Maintenance Mode menu for GDMW hosted sites
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] != 'disabled' || $GDMW_options['bps_gdmw_hosting'] != 'yes' ) {	
	add_submenu_page('bulletproof-security/admin/core/core.php', __('Maintenance Mode', 'bulletproof-security'), __('Maintenance Mode', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/maintenance/maintenance.php' );
	}
	
	add_submenu_page('bulletproof-security/admin/core/core.php', __('System Info', 'bulletproof-security'), __('System Info', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/system-info/system-info.php' );
	add_submenu_page('bulletproof-security/admin/core/core.php', __('UI|UX Settings', 'bulletproof-security'), __('UI|UX Settings', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/theme-skin/theme-skin.php' );
	add_submenu_page('bulletproof-security/admin/core/core.php', __('Setup Wizard', 'bulletproof-security'), __('Setup Wizard', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/wizard/wizard.php' );	
	
	// Do not display a submenu|link: jQuery UI Dialog Pop up Form Uninstaller Options for BPS free
	add_submenu_page( null, __('BPS Plugin Uninstall Options', 'bulletproof-security'), __('BPS Plugin Uninstall Options', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/includes/uninstall.php' );
	
	}
	}
}

// Network|Multisite Network Admin Dashboard Menu for jQuery UI Dialog Pop up Form Uninstaller Options
function bulletproof_security_network_admin_menu() {
	add_submenu_page( null, __('BPS Plugin Uninstall Options', 'bulletproof-security'), __('BPS Plugin Uninstall Options', 'bulletproof-security'), 'manage_options', 'bulletproof-security/admin/includes/uninstall.php' );
}

$bpsPro_SLF_options = get_option('bulletproof_security_options_SLF');

if ( $bpsPro_SLF_options['bps_slf_filter'] == 'On' ) {

	if ( is_admin() && preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {

	add_filter( 'style_loader_tag', 'bpsPro_style_loader_filter' );
	add_filter( 'script_loader_tag', 'bpsPro_script_loader_filter' );

	}
}

// Prevents other plugin and theme Styles from loading in BPS plugin pages
// .53.8: Added Debug option
function bpsPro_style_loader_filter($tag){
	
	if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches) ) {

		$topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

		$Debug_options = get_option('bulletproof_security_options_debug');
		$matches = '';

		if ( ! strpos( $tag, 'bulletproof-security' ) && ! strpos( $tag, 'wp-admin' ) && ! strpos( $tag, 'wp-includes' ) )

			$tag = preg_replace( '/\.css/', ".css-script-nulled", $tag );
			
			if ( $Debug_options['bps_debug'] == 'On' ) {
			
				if ( preg_match( '/\/(plugins|themes)\/.*\.css-script-nulled/', $tag, $matches ) ) {
			
					echo $topDiv;
					echo '<font color="blue"><strong>'.__('BPS UI|UX Debug: SLF css Scripts Nulled', 'bulletproof-security').'</strong></font><br>';
					print_r($matches[0]);
					echo '</p></div>';
				}

				if ( ! preg_match( '/\/(plugins|themes)\/.*\.css-script-nulled/', $tag, $matches ) && preg_match( '/\/(plugins|themes)\/.*\.css/', $tag, $matches ) ) {						
					echo $topDiv;
					echo '<font color="blue"><strong>'.__('BPS UI|UX Debug: SLF css Scripts Not Nulled|Allowed', 'bulletproof-security').'</strong></font><br>';
					print_r($matches[0]);
					echo '</p></div>';
				}
			}	
		return $tag;
	}
}

// Prevents other plugin and theme Scripts from loading in BPS plugin pages
// .53.8: Added Debug option
function bpsPro_script_loader_filter($tag){

	if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches) ) {
		
		$topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
		
		$Debug_options = get_option('bulletproof_security_options_debug');
		$matches = '';

		if ( ! strpos( $tag, 'bulletproof-security' ) && ! strpos( $tag, 'wp-admin' ) && ! strpos( $tag, 'wp-includes' ) )

			$tag = preg_replace( '/\.js/', ".js-script-nulled", $tag );
			
			if ( $Debug_options['bps_debug'] == 'On' ) {
			
				if ( preg_match( '/\/(plugins|themes)\/.*\.js-script-nulled/', $tag, $matches ) ) {
					
					echo $topDiv;
					echo '<font color="blue"><strong>'.__('BPS UI|UX Debug: SLF js Scripts Nulled', 'bulletproof-security').'</strong></font><br>';
					print_r($matches[0]);
					echo '</p></div>';
				}

				if ( ! preg_match( '/\/(plugins|themes)\/.*\.js-script-nulled/', $tag, $matches ) && preg_match( '/\/(plugins|themes)\/.*\.js/', $tag, $matches ) ) {	
					echo $topDiv;
					echo '<font color="blue"><strong>'.__('BPS UI|UX Debug: SLF js Scripts Not Nulled|Allowed', 'bulletproof-security').'</strong></font><br>';
					print_r($matches[0]);
					echo '</p></div>';
				}
			}	
		return $tag;
	}
}

add_action( 'admin_enqueue_scripts', 'bpsPro_register_enqueue_scripts_styles' );

// Register scripts and styles, Enqueue scripts and styles, Dequeue any plugin or theme scripts and styles loading in BPS plugin pages
// .53.8: BugFix: script handles & dependencies code was fubar. Added: ver Query Strings * load scripts in footer * Debug option
function bpsPro_register_enqueue_scripts_styles() {
global $wp_scripts, $wp_styles, $bulletproof_security, $wp_version, $bps_version;

	// Register & Load BPS scripts and styles on BPS plugin pages ONLY
	if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {

		$UIoptions = get_option('bulletproof_security_options_theme_skin');
		$Debug_options = get_option('bulletproof_security_options_debug');
		
		// Register BPS Scripts
		wp_register_script('bps-tabs', plugins_url( '/bulletproof-security/admin/js/bps-ui-tabs.js' ), array( 'jquery', 'jquery-ui-tabs' ), $bps_version, true );
		wp_register_script('bps-dialog', plugins_url( '/bulletproof-security/admin/js/bps-ui-dialog.js' ), array( 'jquery', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-explode' ), $bps_version, true );	
		wp_register_script('bps-accordion', plugins_url( '/bulletproof-security/admin/js/bps-ui-accordion.js' ), array( 'jquery', 'jquery-ui-accordion' ), $bps_version, true );
	
		// Register BPS Styles
		if ( version_compare( $wp_version, '3.8', '>=' ) ) {
		
			switch ( $UIoptions['bps_ui_theme_skin'] ) {
    			case 'blue':
					wp_register_style('bps-css-38', plugins_url('/bulletproof-security/admin/css/bps-blue-ui-theme.css'), array(), $bps_version, 'all' );
				break;
    			case 'grey':
					wp_register_style('bps-css-38', plugins_url('/bulletproof-security/admin/css/bps-grey-ui-theme.css'), array(), $bps_version, 'all' );
				break;
    			case 'black':
					wp_register_style('bps-css-38', plugins_url('/bulletproof-security/admin/css/bps-black-ui-theme.css'), array(), $bps_version, 'all' );
				break;
			default: 		
					wp_register_style('bps-css-38', plugins_url('/bulletproof-security/admin/css/bps-blue-ui-theme.css'), array(), $bps_version, 'all' );		
			}
		
		} else {
		
			wp_register_style('bps-css', plugins_url('/bulletproof-security/admin/css/bps-blue-ui-theme-old-wp-versions.css'), array(), $bps_version, 'all' );
		}

		// Enqueue BPS scripts & script dependencies
		wp_enqueue_script( 'bps-tabs' );
		wp_enqueue_script( 'bps-dialog' );
		wp_enqueue_script( 'bps-accordion' );

		// Enqueue BPS stylesheets
		if ( version_compare( $wp_version, '3.8', '>=' ) ) {
		
			switch ( $UIoptions['bps_ui_theme_skin'] ) {
    			case 'blue':
					wp_enqueue_style('bps-css-38' );
				break;
    			case 'grey':
					wp_enqueue_style('bps-css-38' );
				break;
    			case 'black':
					wp_enqueue_style('bps-css-38' );
				break;
			default: 		
					wp_enqueue_style('bps-css-38' );	
			}
		
		} else {
		
			wp_enqueue_style('bps-css' );
		}

		// Dequeue any other plugin or theme scripts that should not be loading on BPS plugin pages
		$script_handles = array( 'bps-tabs', 'bps-dialog', 'bps-accordion', 'admin-bar', 'jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-dialog', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position', 'jquery-ui-accordion', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-explode', 'common', 'utils', 'svg-painter', 'wp-auth-check', 'heartbeat', 'debug-bar' );

		$style_handles = array( 'bps-css', 'bps-css-38', 'admin-bar', 'colors', 'ie', 'wp-auth-check', 'debug-bar' );
		
		if ( $Debug_options['bps_debug'] == 'On' ) {
			echo '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
			echo '<font color="blue"><strong>'.__('BPS UI|UX Debug: Scripts|Styles Dequeued', 'bulletproof-security').'</strong></font><br>';
		}

		$NSCD = 0;

			foreach( $wp_scripts->queue as $handle ) {
				
				if ( ! in_array( $handle, $script_handles ) ) {
					wp_dequeue_script( $handle );
				
					if ( $Debug_options['bps_debug'] == 'On' ) {
						$NSCD = 1;
						echo '<strong>'.__('Script Dequeued: ', 'bulletproof-security') . '</strong>' . $handle . '<br>';
					}
				}
			}
		
			if ( $Debug_options['bps_debug'] == 'On' && 0 == $NSCD ) {
				echo '<strong>'.__('No plugin or theme Scripts were Dequeued (prevented) from loading in BPS plugin pages', 'bulletproof-security') . '</strong><br>';
			}
			
			$NSTD = 0;

			foreach( $wp_styles->queue as $handle ) {
        	
				if ( ! in_array( $handle, $style_handles ) ) {
					wp_dequeue_style( $handle );
				
					if ( $Debug_options['bps_debug'] == 'On' ) {
						$NSTD = 1;
						echo '<strong>'.__('Style Dequeued: ', 'bulletproof-security') . '</strong>' . $handle . '<br>';
					}					
				}
			}	
			
			if ( $Debug_options['bps_debug'] == 'On' && 0 == $NSTD ) {			
				echo '<strong>'.__('No plugin or theme Styles were Dequeued (prevented) from loading in BPS plugin pages', 'bulletproof-security') . '</strong><br>';
			}
		
		if ( $Debug_options['bps_debug'] == 'On' ) {
			echo '</p></div>';
		}	
	}
}

add_action( 'wp_before_admin_bar_render', 'bpsPro_remove_non_wp_nodes_from_toolbar' );

// Removes any/all additional WP Toolbar nodes / menu items added by other plugins and themes
// in BPS plugin pages ONLY. Does NOT remove any of the default WP Toolbar nodes.
// Note: This file is loaded in the WP Dashboard. This function is ONLY processed in BPS plugin pages.
// .53.8: Added Debug option
function bpsPro_remove_non_wp_nodes_from_toolbar() {
	
	if ( preg_match( '/page=bulletproof-security/', esc_html($_SERVER['REQUEST_URI']), $matches ) ) {
	
		$UIWPToptions = get_option('bulletproof_security_options_wpt_nodes');
	
		if ( $UIWPToptions['bps_wpt_nodes'] != 'allnodes' ) {
			
			global $wp_admin_bar;
			$all_toolbar_nodes = $wp_admin_bar->get_nodes();
			$Debug_options = get_option('bulletproof_security_options_debug');
			$WPTB = 0;

			$topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';

			if ( $all_toolbar_nodes ) {
		
				if ( ! is_multisite() ) {
				
					$wp_default_nodes = array( 'user-actions', 'user-info', 'edit-profile', 'logout', 'menu-toggle', 'my-account', 'wp-logo', 'about', 'wporg', 'documentation', 'support-forums', 'feedback', 'site-name', 'view-site', 'updates', 'comments', 'new-content', 'new-post', 'new-media', 'new-page', 'new-user', 'top-secondary', 'wp-logo-external' );
				
					if ( $Debug_options['bps_debug'] == 'On' ) {
						echo $topDiv;
						echo '<font color="blue"><strong>'.__('BPS UI|UX Debug: WP Toolbar nodes|menu items Removed', 'bulletproof-security').'</strong></font><br>';
					}

					foreach ( $all_toolbar_nodes as $node ) {
						// For Testing: echo '<br>'; print_r($node->id); 
					
						if ( ! in_array( $node->id, $wp_default_nodes ) ) {
							$wp_admin_bar->remove_node( $node->id );
								
							if ( $Debug_options['bps_debug'] == 'On' ) {
								
								$WPTB = 1;
								echo '<strong>'.__('WP Toolbar node|menu item Removed: ', 'bulletproof-security') . '</strong>';
								print_r($node->id);
								echo '<br>';
							}
						}
					}				
					
					if ( $Debug_options['bps_debug'] == 'On' && 0 == $WPTB ) {
						echo '<strong>'.__('No WP Toolbar nodes|menu items were Removed in BPS plugin pages', 'bulletproof-security') . '</strong><br>';
					}
			
					if ( $Debug_options['bps_debug'] == 'On' ) {
						echo '</p></div>';
					}

				} else {
				
					$wp_default_nodes = array( 'user-actions', 'user-info', 'edit-profile', 'logout', 'menu-toggle', 'my-account', 'wp-logo', 'about', 'wporg', 'documentation', 'support-forums', 'feedback', 'site-name', 'view-site', 'updates', 'comments', 'new-content', 'new-post', 'new-media', 'new-page', 'new-user', 'top-secondary', 'wp-logo-external', 'my-sites', 'my-sites-super-admin', 'network-admin', 'network-admin-d', 'network-admin-s', 'network-admin-u', 'network-admin-t', 'network-admin-p', 'my-sites-list', 'edit-site' );
				
					if ( $Debug_options['bps_debug'] == 'On' ) {
						echo $topDiv;
						echo '<font color="blue"><strong>'.__('BPS UI|UX Debug: WP Toolbar nodes|menu items Removed', 'bulletproof-security').'</strong></font><br>';
					}

					foreach ( $all_toolbar_nodes as $node ) {
						// For Testing: echo '<br>'; print_r($node->id); 
					
						if ( ! in_array( $node->id, $wp_default_nodes ) && ! preg_match( '/blog-[0-9]/', $node->id, $matches ) ) {
							$wp_admin_bar->remove_node( $node->id );	
						
							if ( $Debug_options['bps_debug'] == 'On' ) {
								
								$WPTB = 1;
								echo '<strong>'.__('WP Toolbar node|menu item Removed: ', 'bulletproof-security') . '</strong>';
								print_r($node->id);
								echo '<br>';
							}						
						}
					}
					
					if ( $Debug_options['bps_debug'] == 'On' && 0 == $WPTB ) {
						echo '<strong>'.__('No WP Toolbar nodes|menu items were Removed in BPS plugin pages', 'bulletproof-security') . '</strong><br>';
					}
			
					if ( $Debug_options['bps_debug'] == 'On' ) {
						echo '</p></div>';
					}				
				}
			}
		}
	}
}

// Create Backup folder with randomly generated folder name and update DB with folder name
function bpsPro_create_db_backup_folder() {
$options = get_option('bulletproof_security_options_db_backup');

	if ( $options['bps_db_backup_folder'] && $options['bps_db_backup_folder'] != '' || @$_POST['Submit-DBB-Reset'] == true ) {
		return;	
	}
	
	$source = WP_CONTENT_DIR . '/bps-backup';

	if ( is_dir($source) ) {
		
		$iterator = new DirectoryIterator($source);
			
		foreach ( $iterator as $folder ) {
			if ( $folder->isDir() && ! $folder->isDot() && preg_match( '/backups_[a-zA-Z0-9]/', $folder ) ) {
				return;
			}
		}
				
		$str = '1234567890abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVWXYZU3xt8Eb9Qw422hG0yv1LCT2Pzub7';
		$folder_obs = substr( str_shuffle($str), 0, 15 );
		@mkdir( WP_CONTENT_DIR . '/bps-backup/backups_' . $folder_obs, 0755, true );
		@chmod( WP_CONTENT_DIR . '/bps-backup/backups_' . $folder_obs . '/', 0755 );
				
		$dbb_options = 'bulletproof_security_options_db_backup';
		$bps_db_backup_folder = addslashes( WP_CONTENT_DIR . '/bps-backup/backups_' . $folder_obs );
		$bps_db_backup_download_link = ( WP_CONTENT_DIR . '/bps-backup/backups_' . $folder_obs );
		$bps_db_backup_download_link = content_url( '/bps-backup/backups_' ) . $folder_obs . '/';
		
		$DBB_Options = array(
		'bps_db_backup' 						=> 'On', 
		'bps_db_backup_description' 			=> '', 
		'bps_db_backup_folder' 					=> $bps_db_backup_folder, 
		'bps_db_backup_download_link' 			=> $bps_db_backup_download_link, 
		'bps_db_backup_job_type' 				=> '', 
		'bps_db_backup_frequency' 				=> '', 		 
		'bps_db_backup_start_time_hour' 		=> '', 
		'bps_db_backup_start_time_weekday' 		=> '', 
		'bps_db_backup_start_time_month_date' 	=> '', 
		'bps_db_backup_email_zip' 				=> '', 
		'bps_db_backup_delete' 					=> '', 
		'bps_db_backup_status_display' 			=> 'No DB Backups' 
		);	
	
		if ( ! get_option( $dbb_options ) ) {	
		
			foreach( $DBB_Options as $key => $value ) {
				update_option('bulletproof_security_options_db_backup', $DBB_Options);
			}
			
		} else {

			foreach( $DBB_Options as $key => $value ) {
				update_option('bulletproof_security_options_db_backup', $DBB_Options);
			}	
		}			
	}
}

function bulletproof_security_install() {
global $bulletproof_security, $bps_version;
$previous_install = get_option('bulletproof_security_options');
	
	if ( $previous_install ) {
	if ( version_compare($previous_install['version'], $bps_version, '<') )
		delete_transient( 'bulletproof-security_info' );
	}
}

// On BPS Plugin Deactivation: remove/unschedule all scheduled Cron jobs: 3 total
function bulletproof_security_deactivation() {
	wp_clear_scheduled_hook('bpsPro_DBB_check');	
	wp_clear_scheduled_hook('bpsPro_email_log_files');	
	wp_clear_scheduled_hook('bpsPro_HPF_check');	
}

// Delete the /bps-backup/ files and folder
// Note: SKIP_DOTS or isDot is unnecessary for this specific usage
function bpsPro_pop_uninstall_bps_backup_folder($source) {
	
	if ( ! is_array( spl_classes() ) ) {
		exit();
	}

	$source = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'bps-backup';
	
	if ( is_dir($source) ) {
		
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::CHILD_FIRST);
		
		foreach ( $iterator as $file ) {
			
			if ( $file->isDir() ) {
				rmdir( $file->getRealPath() );

			} else {			
		
				if ( $file->isFile() ) {
					unlink( $file->getRealPath() );
				}
			}
		}
	rmdir($source);	
	}
}

// Uninstallation: Conditional Uninstall based on bps_pop_uninstall value: 2 == Complete BPS Plugin Uninstall or 1 == BPS Pro Upgrade Uninstall
function bulletproof_security_uninstall() {
$POPoptions = get_option('bulletproof_security_options_pop_uninstall');

require_once( ABSPATH . 'wp-admin/includes/plugin.php');

	if ( $POPoptions['bps_pop_uninstall'] == 2 ) {
		
	global $wpdb, $current_user;	

	bpsPro_pop_uninstall_bps_backup_folder($source);

	$user_id = $current_user->ID;
	$Stable_name = $wpdb->prefix . "bpspro_seclog_ignore";
	$Ltable_name = $wpdb->prefix . "bpspro_login_security";
	$DBBtable_name = $wpdb->prefix . "bpspro_db_backup";
	$RootHtaccess = ABSPATH . '.htaccess';
	$RootHtaccessBackup = WP_CONTENT_DIR . '/bps-backup/master-backups/root.htaccess';
	$wpadminHtaccess = ABSPATH . 'wp-admin/.htaccess';
	$wpadminHtaccessBackup = WP_CONTENT_DIR . '/bps-backup/master-backups/wpadmin.htaccess';

	if ( file_exists($RootHtaccess) ) {
		copy($RootHtaccess, $RootHtaccessBackup);
	}
	if ( file_exists($wpadminHtaccess) ) {
		copy($wpadminHtaccess, $wpadminHtaccessBackup);
	}

	delete_transient( 'bulletproof-security_info' );
	
	delete_option('bulletproof_security_options');
	delete_option('bulletproof_security_options_customcode');
	delete_option('bulletproof_security_options_customcode_WPA');
	delete_option('bulletproof_security_options_maint');
	delete_option('bulletproof_security_options_maint_mode');
	delete_option('bulletproof_security_options_mynotes');
	delete_option('bulletproof_security_options_email');
	delete_option('bulletproof_security_options_autolock');
	delete_option('bulletproof_security_options_login_security');
	delete_option('bulletproof_security_options_theme_skin');
	delete_option('bulletproof_security_options_db_backup');
	delete_option('bulletproof_security_options_DBB_log');
	delete_option('bulletproof_security_options_htaccess_res');
	delete_option('bulletproof_security_options_net_correction');
	delete_option('bulletproof_security_options_spinner');
	delete_option('bulletproof_security_options_wpt_nodes');
	delete_option('bulletproof_security_options_status_display'); 
	delete_option('bulletproof_security_options_pop_uninstall'); 
	delete_option('bulletproof_security_options_GDMW');
	delete_option('bulletproof_security_options_wizard_free');
	delete_option('bulletproof_security_options_idle_session'); 	
	delete_option('bulletproof_security_options_auth_cookie'); 
	delete_option('bulletproof_security_options_SLF');
	delete_option('bulletproof_security_options_scrolltop');
	delete_option('bulletproof_security_options_apache_modules');
	delete_option('bulletproof_security_options_sec_log_post_limit'); 
	delete_option('bulletproof_security_options_debug'); 
	delete_option('bulletproof_security_options_hidden_plugins');
	delete_option('bulletproof_security_options_hpf_cron');
	delete_option('bulletproof_security_options_zip_fix');
	delete_option('bulletproof_security_options_autoupdate');
	delete_option('bulletproof_security_options_setup_wizard_woo');
	// will be adding this new upgrade notice option later
	// delete_option('bulletproof_security_options_upgrade_notice');	
	
	$wpdb->query("DROP TABLE IF EXISTS $Stable_name");
	$wpdb->query("DROP TABLE IF EXISTS $Ltable_name");
	$wpdb->query("DROP TABLE IF EXISTS $DBBtable_name");
	
	delete_user_meta($user_id, 'bps_ignore_iis_notice');
	delete_user_meta($user_id, 'bps_ignore_sucuri_notice');
	delete_user_meta($user_id, 'bps_ignore_BLC_notice');
	delete_user_meta($user_id, 'bps_ignore_PhpiniHandler_notice');
	delete_user_meta($user_id, 'bps_ignore_Permalinks_notice');
	delete_user_meta($user_id, 'bps_brute_force_login_protection_notice');
	delete_user_meta($user_id, 'bps_speed_boost_cache_notice');
	delete_user_meta($user_id, 'bps_xmlrpc_ddos_notice');
	delete_user_meta($user_id, 'bps_author_enumeration_notice');
	delete_user_meta($user_id, 'bps_ignore_wpfirewall2_notice');
	delete_user_meta($user_id, 'bps_hud_NetworkActivationAlert_notice');
	delete_user_meta($user_id, 'bps_referer_spam_notice');
	delete_user_meta($user_id, 'bps_sniff_driveby_notice');
	delete_user_meta($user_id, 'bps_iframe_clickjack_notice');
	delete_user_meta($user_id, 'bps_bonus_code_dismiss_all_notice');
	delete_user_meta($user_id, 'bps_post_request_attack_notice');	
	delete_user_meta($user_id, 'bps_ignore_jetpack_notice');	
	delete_user_meta($user_id, 'bps_ignore_woocommerce_notice');

		@unlink($wpadminHtaccess);	
	
		if ( @unlink($RootHtaccess) || ! file_exists($RootHtaccess) ) {
			flush_rewrite_rules();
		}	
	
		$autoupdate_muplugins_file = WP_CONTENT_DIR . '/mu-plugins/bps-plugin-autoupdate.php';
	
		if ( file_exists($autoupdate_muplugins_file) ) {
			unlink($autoupdate_muplugins_file);
		}	
	
	} else {

		delete_option( 'bulletproof_security_options' );
		delete_option('bulletproof_security_options_wizard_free');
		delete_transient( 'bulletproof-security_info' );
	}
}

// was being used, no longer being used for anything
function bulletproof_security_options_validate($input) {  
	$options = get_option('bulletproof_security_options');  
	$options['bps_blank'] = wp_filter_nohtml_kses($input['bps_blank']);
			
	return $options;  
}

// Maintenance Mode
function bulletproof_security_options_validate_maint_mode($input) {  
	$options = get_option('bulletproof_security_options_maint_mode');  
	$options['bps_maint_on_off'] = wp_filter_nohtml_kses($input['bps_maint_on_off']);
	$options['bps_maint_countdown_timer'] = wp_filter_nohtml_kses($input['bps_maint_countdown_timer']);
	$options['bps_maint_countdown_timer_color'] = wp_filter_nohtml_kses($input['bps_maint_countdown_timer_color']);
	$options['bps_maint_time'] = wp_filter_nohtml_kses($input['bps_maint_time']);
	$options['bps_maint_retry_after'] = wp_filter_nohtml_kses($input['bps_maint_retry_after']);
	$options['bps_maint_frontend'] = wp_filter_nohtml_kses($input['bps_maint_frontend']);
	$options['bps_maint_backend'] = wp_filter_nohtml_kses($input['bps_maint_backend']);
	$options['bps_maint_ip_allowed'] = wp_filter_nohtml_kses($input['bps_maint_ip_allowed']);
	$options['bps_maint_text'] = esc_html($input['bps_maint_text']);
	$options['bps_maint_background_images'] = wp_filter_nohtml_kses($input['bps_maint_background_images']);
	$options['bps_maint_center_images'] = wp_filter_nohtml_kses($input['bps_maint_center_images']);
	$options['bps_maint_background_color'] = wp_filter_nohtml_kses($input['bps_maint_background_color']);
	$options['bps_maint_show_visitor_ip'] = wp_filter_nohtml_kses($input['bps_maint_show_visitor_ip']);
	$options['bps_maint_show_login_link'] = wp_filter_nohtml_kses($input['bps_maint_show_login_link']);
	$options['bps_maint_dashboard_reminder'] = wp_filter_nohtml_kses($input['bps_maint_dashboard_reminder']);	
	$options['bps_maint_log_visitors'] = wp_filter_nohtml_kses($input['bps_maint_log_visitors']);
	$options['bps_maint_countdown_email'] = wp_filter_nohtml_kses($input['bps_maint_countdown_email']);
	$options['bps_maint_email_to'] = trim(wp_filter_nohtml_kses($input['bps_maint_email_to']));
	$options['bps_maint_email_from'] = trim(wp_filter_nohtml_kses($input['bps_maint_email_from']));
	$options['bps_maint_email_cc'] = trim(wp_filter_nohtml_kses($input['bps_maint_email_cc']));
	$options['bps_maint_email_bcc'] = trim(wp_filter_nohtml_kses($input['bps_maint_email_bcc']));	
	$options['bps_maint_mu_entire_site'] = wp_filter_nohtml_kses($input['bps_maint_mu_entire_site']);
	$options['bps_maint_mu_subsites_only'] = wp_filter_nohtml_kses($input['bps_maint_mu_subsites_only']);
	
	return $options;  
}

// Root .htaccess file AutoLock 
function bulletproof_security_options_validate_autolock($input) {  
	$options = get_option('bulletproof_security_options_autolock');  
	$options['bps_root_htaccess_autolock'] = wp_filter_nohtml_kses($input['bps_root_htaccess_autolock']);
		
	return $options;  
}

// BPS Custom Code - Root .htaccess
function bulletproof_security_options_validate_customcode($input) {  
	$options = get_option('bulletproof_security_options_customcode');  
	// TOP PHP/PHP.INI HANDLER/CACHE CODE
	$options['bps_customcode_one'] = esc_html($input['bps_customcode_one']);
	$options['bps_customcode_server_signature'] = esc_html($input['bps_customcode_server_signature']);
	$options['bps_customcode_directory_index'] = esc_html($input['bps_customcode_directory_index']);
	// BRUTE FORCE LOGIN PAGE PROTECTION
	$options['bps_customcode_server_protocol'] = esc_html($input['bps_customcode_server_protocol']);	
	$options['bps_customcode_error_logging'] = esc_html($input['bps_customcode_error_logging']);
	$options['bps_customcode_deny_dot_folders'] = esc_html($input['bps_customcode_deny_dot_folders']);	
	$options['bps_customcode_admin_includes'] = esc_html($input['bps_customcode_admin_includes']);
	$options['bps_customcode_wp_rewrite_start'] = esc_html($input['bps_customcode_wp_rewrite_start']);
	$options['bps_customcode_request_methods'] = esc_html($input['bps_customcode_request_methods']);
	// PLUGIN/THEME SKIP/BYPASS RULES
	$options['bps_customcode_two'] = esc_html($input['bps_customcode_two']);
	$options['bps_customcode_timthumb_misc'] = esc_html($input['bps_customcode_timthumb_misc']);
	$options['bps_customcode_bpsqse'] = esc_html($input['bps_customcode_bpsqse']);
	if ( is_multisite() ) {
	$options['bps_customcode_wp_rewrite_end'] = esc_html($input['bps_customcode_wp_rewrite_end']);
	}
	$options['bps_customcode_deny_files'] = esc_html($input['bps_customcode_deny_files']);
	// BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE
	$options['bps_customcode_three'] = esc_html($input['bps_customcode_three']);

	return $options;  
}


// BPS Custom Code - WP-admin .htaccess
function bulletproof_security_options_validate_customcode_WPA($input) {  
	$options = get_option('bulletproof_security_options_customcode_WPA');  
	$options['bps_customcode_deny_files_wpa'] = esc_html($input['bps_customcode_deny_files_wpa']);
	$options['bps_customcode_one_wpa'] = esc_html($input['bps_customcode_one_wpa']);
	$options['bps_customcode_two_wpa'] = esc_html($input['bps_customcode_two_wpa']);
	$options['bps_customcode_bpsqse_wpa'] = esc_html($input['bps_customcode_bpsqse_wpa']);		
	
	return $options;  
}

// BPS "My Notes" settings 
function bulletproof_security_options_validate_mynotes($input) {  
	$options = get_option('bulletproof_security_options_mynotes');  
	$options['bps_my_notes'] = esc_html($input['bps_my_notes']);
		
	return $options;  
}

// Login Security & Monitoring
function bulletproof_security_options_validate_login_security($input) {  
	$BPSoptions = get_option('bulletproof_security_options_login_security');  
	$BPSoptions['bps_max_logins'] = trim(wp_filter_nohtml_kses($input['bps_max_logins']));
	$BPSoptions['bps_lockout_duration'] = trim(wp_filter_nohtml_kses($input['bps_lockout_duration']));
	$BPSoptions['bps_manual_lockout_duration'] = trim(wp_filter_nohtml_kses($input['bps_manual_lockout_duration']));
	$BPSoptions['bps_max_db_rows_display'] = trim(wp_filter_nohtml_kses($input['bps_max_db_rows_display']));
	$BPSoptions['bps_login_security_OnOff'] = wp_filter_nohtml_kses($input['bps_login_security_OnOff']);
	$BPSoptions['bps_login_security_logging'] = wp_filter_nohtml_kses($input['bps_login_security_logging']);
	$BPSoptions['bps_login_security_errors'] = wp_filter_nohtml_kses($input['bps_login_security_errors']);
	$BPSoptions['bps_login_security_remaining'] = wp_filter_nohtml_kses($input['bps_login_security_remaining']);
	$BPSoptions['bps_login_security_pw_reset'] = wp_filter_nohtml_kses($input['bps_login_security_pw_reset']);
	$BPSoptions['bps_login_security_sort'] = wp_filter_nohtml_kses($input['bps_login_security_sort']);
	$BPSoptions['bps_enable_lsm_woocommerce'] = wp_filter_nohtml_kses($input['bps_enable_lsm_woocommerce']);

	return $BPSoptions;  
}

// Idle Session Logout (ISL): Do not automatically set ISL up. This should be left up to users to choose whether to use this or not.
function bulletproof_security_options_validate_idle_session($input) {  
	$options = get_option('bulletproof_security_options_idle_session');  
	$options['bps_isl'] = wp_filter_nohtml_kses($input['bps_isl']);
	$options['bps_isl_timeout'] = trim(wp_filter_nohtml_kses($input['bps_isl_timeout']));
	$options['bps_isl_logout_url'] = trim(wp_filter_nohtml_kses($input['bps_isl_logout_url']));	
	$options['bps_isl_login_url'] = trim(wp_filter_nohtml_kses($input['bps_isl_login_url']));
	@$options['bps_isl_custom_message'] = wp_filter_nohtml_kses($input['bps_isl_custom_message']);
	$options['bps_isl_custom_css_1'] = wp_filter_nohtml_kses($input['bps_isl_custom_css_1']);
	$options['bps_isl_custom_css_2'] = wp_filter_nohtml_kses($input['bps_isl_custom_css_2']);
	$options['bps_isl_custom_css_3'] = wp_filter_nohtml_kses($input['bps_isl_custom_css_3']);
	$options['bps_isl_custom_css_4'] = wp_filter_nohtml_kses($input['bps_isl_custom_css_4']);
	@$options['bps_isl_user_account_exceptions'] = wp_filter_nohtml_kses($input['bps_isl_user_account_exceptions']);
	@$options['bps_isl_administrator'] = wp_filter_nohtml_kses($input['bps_isl_administrator']);
	@$options['bps_isl_editor'] = wp_filter_nohtml_kses($input['bps_isl_editor']);
	@$options['bps_isl_author'] = wp_filter_nohtml_kses($input['bps_isl_author']);
	@$options['bps_isl_contributor'] = wp_filter_nohtml_kses($input['bps_isl_contributor']);
	@$options['bps_isl_subscriber'] = wp_filter_nohtml_kses($input['bps_isl_subscriber']);
	@$options['bps_isl_tinymce'] = wp_filter_nohtml_kses($input['bps_isl_tinymce']);
	@$options['bps_isl_uri_exclusions'] = wp_filter_nohtml_kses($input['bps_isl_uri_exclusions']);
	// Note: You cannot use: wp_filter_nohtml_kses for multidimensional arrays - it will strip out the inner array code.
	@$options['bps_isl_custom_roles'] = $input['bps_isl_custom_roles'];
	
	return $options;  
}

// Authentication Cookie Expiration (ACE): Do not automatically set ACE up. This should be left up to users to choose whether to use this or not.
function bulletproof_security_options_validate_auth_cookie($input) {  
	$options = get_option('bulletproof_security_options_auth_cookie');  
	$options['bps_ace'] = wp_filter_nohtml_kses($input['bps_ace']);
	$options['bps_ace_expiration'] = trim(wp_filter_nohtml_kses($input['bps_ace_expiration']));
	$options['bps_ace_rememberme_expiration'] = trim(wp_filter_nohtml_kses($input['bps_ace_rememberme_expiration']));
	@$options['bps_ace_user_account_exceptions'] = wp_filter_nohtml_kses($input['bps_ace_user_account_exceptions']);
	@$options['bps_ace_administrator'] = wp_filter_nohtml_kses($input['bps_ace_administrator']);
	@$options['bps_ace_editor'] = wp_filter_nohtml_kses($input['bps_ace_editor']);
	@$options['bps_ace_author'] = wp_filter_nohtml_kses($input['bps_ace_author']);
	@$options['bps_ace_contributor'] = wp_filter_nohtml_kses($input['bps_ace_contributor']);
	@$options['bps_ace_subscriber'] = wp_filter_nohtml_kses($input['bps_ace_subscriber']);
	@$options['bps_ace_rememberme_disable'] = wp_filter_nohtml_kses($input['bps_ace_rememberme_disable']);
	// Note: You cannot use: wp_filter_nohtml_kses for multidimensional arrays - it will strip out the inner array code.
	@$options['bps_ace_custom_roles'] = $input['bps_ace_custom_roles'];
	
	return $options;  
}

// BPS Free Email Alerts
function bulletproof_security_options_validate_email($input) {  
	$options = get_option('bulletproof_security_options_email');  
	$options['bps_send_email_to'] = trim(wp_filter_nohtml_kses($input['bps_send_email_to']));
	$options['bps_send_email_from'] = trim(wp_filter_nohtml_kses($input['bps_send_email_from']));
	$options['bps_send_email_cc'] = trim(wp_filter_nohtml_kses($input['bps_send_email_cc']));
	$options['bps_send_email_bcc'] = trim(wp_filter_nohtml_kses($input['bps_send_email_bcc']));
	$options['bps_login_security_email'] = wp_filter_nohtml_kses($input['bps_login_security_email']);
	//$options['bps_upgrade_email'] = wp_filter_nohtml_kses($input['bps_upgrade_email']);		
	$options['bps_security_log_size'] = wp_filter_nohtml_kses($input['bps_security_log_size']);
	$options['bps_security_log_emailL'] = wp_filter_nohtml_kses($input['bps_security_log_emailL']);	
	$options['bps_dbb_log_email'] = wp_filter_nohtml_kses($input['bps_dbb_log_email']);	
	$options['bps_dbb_log_size'] = wp_filter_nohtml_kses($input['bps_dbb_log_size']);
	
	return $options;  
}

// UI Theme Skin 
function bulletproof_security_options_validate_theme_skin($input) {  
	$options = get_option('bulletproof_security_options_theme_skin');  
	$options['bps_ui_theme_skin'] = wp_filter_nohtml_kses($input['bps_ui_theme_skin']);
			
	return $options;  
}

// DB Backup
function bulletproof_security_options_validate_db_backup($input) {  
	$options = get_option('bulletproof_security_options_db_backup');  
	$options['bps_db_backup'] = wp_filter_nohtml_kses($input['bps_db_backup']);
	$options['bps_db_backup_description'] = trim(wp_filter_nohtml_kses($input['bps_db_backup_description']));		
	$options['bps_db_backup_folder'] = trim(wp_filter_nohtml_kses($input['bps_db_backup_folder']));
	$options['bps_db_backup_download_link'] = trim(wp_filter_nohtml_kses($input['bps_db_backup_download_link']));				
	$options['bps_db_backup_job_type'] = wp_filter_nohtml_kses($input['bps_db_backup_job_type']);	
	$options['bps_db_backup_frequency'] = wp_filter_nohtml_kses($input['bps_db_backup_frequency']);	
	$options['bps_db_backup_start_time_hour'] = wp_filter_nohtml_kses($input['bps_db_backup_start_time_hour']);
	$options['bps_db_backup_start_time_weekday'] = wp_filter_nohtml_kses($input['bps_db_backup_start_time_weekday']);
	$options['bps_db_backup_start_time_month_date'] = wp_filter_nohtml_kses($input['bps_db_backup_start_time_month_date']);
	$options['bps_db_backup_email_zip'] = wp_filter_nohtml_kses($input['bps_db_backup_email_zip']);		
	$options['bps_db_backup_delete'] = wp_filter_nohtml_kses($input['bps_db_backup_delete']);		
	$options['bps_db_backup_status_display'] = wp_filter_nohtml_kses($input['bps_db_backup_status_display']); // hidden form option
	
	return $options;  
}

// DB Backup Log Last Modified Time DB
function bulletproof_security_options_validate_DBB_log($input) {  
	$options = get_option('bulletproof_security_options_DBB_log');  
	$options['bps_dbb_log_date_mod'] = wp_filter_nohtml_kses($input['bps_dbb_log_date_mod']);
		
	return $options;  
}

// Hosting that does not allow wp-admin .htaccess files - Go Daddy Managed WordPress hosting
function bulletproof_security_options_validate_htaccess_res($input) {  
	$options = get_option('bulletproof_security_options_htaccess_res');  
	$options['bps_wpadmin_restriction'] = wp_filter_nohtml_kses($input['bps_wpadmin_restriction']);
		
	return $options;  
}

// Go Daddy Managed WordPress hosting
function bulletproof_security_options_validate_GDMW($input) {  
	$options = get_option('bulletproof_security_options_GDMW');  
	$options['bps_gdmw_hosting'] = wp_filter_nohtml_kses($input['bps_gdmw_hosting']);
	
	return $options;  
}

// Loading/Processing Spinner On/Off
function bulletproof_security_options_validate_spinner($input) {  
	$options = get_option('bulletproof_security_options_spinner');  
	$options['bps_spinner'] = wp_filter_nohtml_kses($input['bps_spinner']);
	
	return $options;  
}

// jQuery ScrollTop Animation On/Off
function bulletproof_security_options_validate_scrolltop($input) {  
	$options = get_option('bulletproof_security_options_scrolltop');  
	$options['bps_scrolltop'] = wp_filter_nohtml_kses($input['bps_scrolltop']);
	
	return $options;  
}

// WP Toolbar remove or allow all nodes
function bulletproof_security_options_validate_wpt_nodes($input) {  
	$options = get_option('bulletproof_security_options_wpt_nodes');  
	$options['bps_wpt_nodes'] = wp_filter_nohtml_kses($input['bps_wpt_nodes']);
	
	return $options;  
}

// Inpage Status display - displays on BPS plugin pages only
function bulletproof_security_options_validate_status_display($input) {  
	$options = get_option('bulletproof_security_options_status_display');  
	$options['bps_status_display'] = wp_filter_nohtml_kses($input['bps_status_display']);
	
	return $options;  
}

// jQuery UI Dialog Uninstall Form Options
function bulletproof_security_options_validate_pop_uninstall($input) {  
	$options = get_option('bulletproof_security_options_pop_uninstall');  
	$options['bps_pop_uninstall'] = wp_filter_nohtml_kses($input['bps_pop_uninstall']);
	
	return $options;  
}

// Setup Wizard 
function bulletproof_security_options_validate_wizard_free($input) {  
	$options = get_option('bulletproof_security_options_wizard_free');  
	$options['bps_wizard_free'] = wp_filter_nohtml_kses($input['bps_wizard_free']);
	
	return $options;  
}

// Setup Wizard New BPS Installations: Used in WooCommerce Dismiss Notice.
// New installations of BPS should not display the WooCommerce Enable LSM option Dismiss Notice.
function bulletproof_security_options_validate_setup_wizard_woo($input) {  
	$options = get_option('bulletproof_security_options_setup_wizard_woo');  
	$options['bps_wizard_woo'] = wp_filter_nohtml_kses($input['bps_wizard_woo']);
	
	return $options;  
}

// Style/Script Loader Filter (SLF)
function bulletproof_security_options_validate_SLF($input) {  
	$options = get_option('bulletproof_security_options_SLF');  
	$options['bps_slf_filter'] = wp_filter_nohtml_kses($input['bps_slf_filter']);

	return $options;  
}

// Apache Modules IfModule condition: create IfModule conditions or just Order, Deny, Allow htaccess code
function bulletproof_security_options_validate_apache_modules($input) {  
	$options = get_option('bulletproof_security_options_apache_modules');  
	$options['bps_apache_mod_ifmodule'] = wp_filter_nohtml_kses($input['bps_apache_mod_ifmodule']);
	$options['bps_apache_mod_time'] = wp_filter_nohtml_kses($input['bps_apache_mod_time']);

	return $options;  
}

// Security Log Limit POST Request Body Data
function bulletproof_security_options_validate_sec_log_post_limit($input) {  
	$options = get_option('bulletproof_security_options_sec_log_post_limit');  
	$options['bps_security_log_post_limit'] = wp_filter_nohtml_kses($input['bps_security_log_post_limit']);
		
	return $options;  
}

// Setup Wizard: Enable|Disable htaccess Files
// Based on Apache Module test results or manual setting: Servers that do no have either mod_access_compat and mod_authz_core or mod_rewrite Loaded
function bulletproof_security_options_validate_htaccess_files($input) {  
	$options = get_option('bulletproof_security_options_htaccess_files');  
	$options['bps_htaccess_files'] = wp_filter_nohtml_kses($input['bps_htaccess_files']);
		
	return $options;  
}

// UI|UX Debug: Displays scripts and styles dequeued|SLF scripts and styles nulled|WP Toolbar nodes|menu items that were Removed
function bulletproof_security_options_validate_debug($input) {  
	$options = get_option('bulletproof_security_options_debug');  
	$options['bps_debug'] = wp_filter_nohtml_kses($input['bps_debug']);
		
	return $options;  
}

// UI|UX AutoUpdate the BPS Plugin
function bulletproof_security_options_validate_autoupdate($input) {  
	$options = get_option('bulletproof_security_options_autoupdate');  
	$options['bps_autoupdate'] = wp_filter_nohtml_kses($input['bps_autoupdate']);
		
	return $options;  
}

// Hidden|Empty Plugin Folders|Files Cron: core.php, wizard.php
function bulletproof_security_options_validate_hpf_cron($input) {  
	$options = get_option('bulletproof_security_options_hpf_cron');  
	$options['bps_hidden_plugins_cron'] = wp_filter_nohtml_kses($input['bps_hidden_plugins_cron']);
	$options['bps_hidden_plugins_cron_frequency'] = wp_filter_nohtml_kses($input['bps_hidden_plugins_cron_frequency']);
	$options['bps_hidden_plugins_cron_email'] = wp_filter_nohtml_kses($input['bps_hidden_plugins_cron_email']);		
	$options['bps_hidden_plugins_cron_alert'] = wp_filter_nohtml_kses($input['bps_hidden_plugins_cron_alert']);		
	
	return $options;  
}

// Hidden|Empty Plugin Folders|Files: Check /plugins/ folder Hidden or Empty Plugin Folders & non-standard WP Files Check
// Textarea box to check against ignored folders and/or files.
function bulletproof_security_options_validate_hidden_plugins($input) {  
	$options = get_option('bulletproof_security_options_hidden_plugins');  
	$options['bps_hidden_plugins_check'] = wp_filter_nohtml_kses($input['bps_hidden_plugins_check']);
	
	return $options;  
}

// Setup Wizard Options: Zip File Download Fix (Incapsula, Proxy, Other Cause)
function bulletproof_security_options_validate_zip_fix($input) {  
	$options = get_option('bulletproof_security_options_zip_fix');  
	$options['bps_zip_download_fix'] = wp_filter_nohtml_kses($input['bps_zip_download_fix']);
	
	return $options;  
}

?>