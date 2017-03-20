<?php
// Direct calls to this file are Forbidden when core files are not present
if ( ! function_exists('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

?>

<div id="bps-container" class="wrap" style="margin:45px 20px 5px 0px;">

<noscript><div id="message" class="updated" style="font-weight:600;font-size:13px;padding:5px;background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><span style="color:blue">BPS Warning: JavaScript is disabled in your Browser</span><br />BPS plugin pages will not display visually correct and all BPS JavaScript functionality will not work correctly.</div></noscript>

<?php 
$ScrollTop_options = get_option('bulletproof_security_options_scrolltop');

if ( $ScrollTop_options['bps_scrolltop'] != 'Off' ) {
	
	if ( esc_html($_SERVER['REQUEST_METHOD']) == 'POST' || isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true ) {

		bpsPro_Browser_UA_scroll_animation();
	}
}
?>

<?php
if ( function_exists('get_transient') ) {
require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

	if ( false === ( $bps_api = get_transient('bulletproof-security_info') ) ) {
		$bps_api = plugins_api( 'plugin_information', array( 'slug' => stripslashes( 'bulletproof-security' ) ) );
		
	if ( ! is_wp_error( $bps_api ) ) {
		$bps_expire = 60 * 30; // Cache downloads data for 30 minutes
		$bps_downloaded = array( 'downloaded' => $bps_api->downloaded );
		maybe_serialize( $bps_downloaded );
		set_transient( 'bulletproof-security_info', $bps_downloaded, $bps_expire );
	}
	}

		$bps_transient = get_transient( 'bulletproof-security_info' );
    	
		echo '<div class="bps-star-container">';
		echo '<div class="bps-star"><img src="'.plugins_url('/bulletproof-security/admin/images/star.png').'" /></div>';
		echo '<div class="bps-downloaded">';
		
		foreach ( $bps_transient as $key => $value ) {
			echo number_format_i18n( $value ) .' '. str_replace( 'downloaded', "Downloads", $key );
		}

		echo '<div class="bps-star-link"><a href="https://wordpress.org/support/view/plugin-reviews/bulletproof-security#postform" target="_blank" title="Add a Star Rating for the BPS plugin">'.__('Rate BPS', 'bulletproof-security').'</a><br><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Upgrade to BulletProof Security Pro">Upgrade to Pro</a></div>';
		echo '</div>';
		echo '</div>';
}

// Get Real IP address - USE EXTREME CAUTION!!!
function bpsPro_get_real_ip_address_cc() {
	
	if ( is_admin() && wp_script_is( 'bps-accordion', $list = 'queue' ) && current_user_can('manage_options') ) {
	
		if ( isset($_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = esc_html($_SERVER['HTTP_CLIENT_IP']);
			
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = esc_html($_SERVER['HTTP_X_FORWARDED_FOR']);
			
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = esc_html($_SERVER['REMOTE_ADDR']);
			
		}
	return $ip;
	}
}	

// Create a new Deny All .htaccess file on first page load with users current IP address to allow the cc-master.zip file to be downloaded
// Create a new Deny All .htaccess file if IP address is not current
function bpsPro_Core_CC_deny_all() {

	if ( is_admin() && wp_script_is( 'bps-accordion', $list = 'queue' ) && current_user_can('manage_options') ) {
		
		$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
		$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
		$Zip_download_Options = get_option('bulletproof_security_options_zip_fix');
		
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' || $Zip_download_Options['bps_zip_download_fix'] == 'On' ) {
			return;
		}

		if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) {	
	
			$denyall_content = "# BPS mod_authz_core IfModule BC\n<IfModule mod_authz_core.c>\nRequire ip ". bpsPro_get_real_ip_address_cc()."\n</IfModule>\n\n<IfModule !mod_authz_core.c>\n<IfModule mod_access_compat.c>\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_cc()."\n</FilesMatch>\n</IfModule>\n</IfModule>";
	
		} else {
		
			$denyall_content = "# BPS mod_access_compat\n<FilesMatch \"(.*)\$\">\nOrder Allow,Deny\nAllow from ". bpsPro_get_real_ip_address_cc()."\n</FilesMatch>";		
		}		
		
		$create_denyall_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/core/.htaccess';
		$check_string = @file_get_contents($create_denyall_htaccess_file);
		
		if ( ! file_exists($create_denyall_htaccess_file) ) { 

			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}			
		
		if ( file_exists($create_denyall_htaccess_file) && ! strpos( $check_string, bpsPro_get_real_ip_address_cc() ) ) { 
			$handle = fopen( $create_denyall_htaccess_file, 'w+b' );
    		fwrite( $handle, $denyall_content );
    		fclose( $handle );
		}
	}
}
bpsPro_Core_CC_deny_all();

?>  

<h2 class="bps-tab-title"><?php _e('BulletProof Security ~ htaccess Core', 'bulletproof-security'); ?></h2>

<div id="message" class="updated" style="border:1px solid #999;background-color:#000;">

<?php
// Apache IfModule htaccess file code check & creation: run on page load with 15 minute time restriction.
// System Info page: performs check in real-time without a 15 minute time restriction, but does not create htaccess files.
bpsPro_apache_mod_directive_check();

// default.htaccess, secure.htaccess, fwrite content for all WP site types
$bps_get_domain_root = bpsGetDomainRoot();
$bps_get_wp_root_default = bps_wp_get_root_folder();
// Replace ABSPATH = wp-content/plugins
$bps_plugin_dir = str_replace( ABSPATH, '', WP_PLUGIN_DIR );
// Replace ABSPATH = wp-content
$bps_wpcontent_dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
// Replace ABSPATH = wp-content/uploads
$wp_upload_dir = wp_upload_dir();
$bps_uploads_dir = str_replace( ABSPATH, '', $wp_upload_dir['basedir'] );
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

// General all purpose "Settings Saved." message for forms
if ( current_user_can('manage_options') && wp_script_is( 'bps-accordion', $list = 'queue' ) ) {
if ( isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true) {
	$text = '<p style="font-size:1em;font-weight:bold;padding:2px 0px 2px 5px;margin:0px -11px 0px -11px;background-color:#dfecf2;-webkit-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px 0px rgba(153,153,153,0.7);""><font color="green"><strong>'.__('Settings Saved', 'bulletproof-security').'</strong></font></p>';
	echo $text;
	}
}

require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-help-text.php' );

// WBM, HPF, MBM, BBM: activate and deactivate and all other form code
if ( isset( $_POST['Submit-WBM-Activate'] ) || isset( $_POST['Submit-WBM-Deactivate'] ) || isset( $_POST['Submit-Hidden-Plugins'] ) || isset( $_POST['Hidden-Plugins-Ignore-Submit'] ) || isset( $_POST['Submit-MBM-Activate'] ) || isset( $_POST['Submit-MBM-Deactivate'] ) || isset( $_POST['Submit-BBM-Activate'] ) || isset( $_POST['Submit-BBM-Deactivate'] ) || isset( $_POST['Submit-Backup-htaccess-Files'] ) || isset( $_POST['Submit-Restore-htaccess-Files'] ) || isset( $_POST['bpsResetDismissSubmit'] ) ) {
require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-forms.php' );	
}

// RBM: activate and deactivate form code
if ( isset( $_POST['Submit-RBM-Activate'] ) || isset( $_POST['Submit-RBM-Deactivate'] ) || isset( $_POST['Submit-RBM-Activate-Network'] ) || isset( $_POST['Submit-RBM-Deactivate-Network'] ) ) {
require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-htaccess-code.php' );
}

$bpsSpacePop = '-------------------------------------------------------------';

?>
</div>

<!-- jQuery UI Tabs Menu -->
<div id="bps-tabs" class="bps-menu">
    <div id="bpsHead"><img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-security-shield.gif'); ?>" /></div>
   
	<ul>
		<li><a href="#bps-tabs-1"><?php _e('Security Modes', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-6"><?php _e('htaccess File Editor', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-7"><?php _e('Custom Code', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-9"><?php _e('My Notes', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-10"><?php _e('Whats New', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-11"><?php _e('BPS Pro Features', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-tabs-12"><?php _e('Help &amp; FAQ', 'bulletproof-security'); ?></a></li>
	</ul>
            
<div id="bps-tabs-1" class="bps-tab-page">
<h2><?php _e('htaccess File Security Modes ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('RBM, WBM, HPF, MBM & BBM BulletProof Modes', 'bulletproof-security'); ?></span></h2>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<?php if ( ! current_user_can('manage_options') ) { _e('Permission Denied', 'bulletproof-security'); } else { ?>

    <h2 style="border-bottom:1px solid #999999;"><?php _e('Activate|Deactivate Security Modes', 'bulletproof-security'); ?></h2>
    
    <h3><?php _e('Root Folder BulletProof Mode (RBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal1" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>
    
   <div id="bps-modal-content1" title="<?php _e('Root Folder BulletProof Mode (RBM)', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/video-tutorials/" title="Setup Wizard & Other Video Tutorials" target="_blank"><?php _e('Setup Wizard & Other Video Tutorials', 'bulletproof-security'); ?></a></strong><br />
    <strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting" title="BPS Troubleshooting Steps" target="_blank"><?php _e('BPS Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br /><br />

	<?php echo $bps_general_help_info; echo $bps_rbm_content; ?>
    </p>
</div> 

<?php
// RBM Status: real-time status check
// 4 possible RBM Status indicators: Activated, Deactivated, Disabled or Root htaccess File Does Not Exist
function bpsPro_rbm_status() {
global $bps_version;
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = ABSPATH . '.htaccess';
	
	if ( file_exists($filename) ) {
		$check_string = @file_get_contents($filename);
	}
	
	if ( @$_POST['Submit-RBM-Activate'] != true && @$_POST['Submit-RBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Root htaccess File Does Not Exist', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( strpos( $check_string, "BULLETPROOF DEFAULT .HTACCESS" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;			
		}
	}

	if ( @$_POST['Submit-RBM-Activate'] == true || @$_POST['Submit-RBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Root htaccess File Does Not Exist', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( strpos( $check_string, "BULLETPROOF DEFAULT .HTACCESS" ) ) {
			$text = '<h3><strong>'.__('RBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;			
		}
	}
}
?>

<div id="RBM-Status"><?php bpsPro_rbm_status(); ?></div>

<div id="root-bulletproof-mode" style="padding-left:10px;border-bottom:1px solid #999999;">

<?php if ( ! is_multisite() ) { ?>

<form name="RBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_activate'); ?>

	<div id="RBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-RBM-Activate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="RBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_deactivate'); ?>

	<div id="RBM-buttons" style="">
    <input type="submit" name="Submit-RBM-Deactivate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<?php } else { ?>

<form name="RBM-Activate-Network" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_activate_network'); ?>

	<div id="RBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-RBM-Activate-Network" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="RBM-Deactivate-Network" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_rbm_deactivate_network'); ?>

	<div id="RBM-buttons" style="">
    <input type="submit" name="Submit-RBM-Deactivate-Network" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate Root Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<?php } ?>

</div>

<h3><?php _e('wp-admin Folder BulletProof Mode (WBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal2" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

   <div id="bps-modal-content2" title="<?php _e('Root Folder BulletProof Mode (RBM)', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/video-tutorials/" title="Setup Wizard & Other Video Tutorials" target="_blank"><?php _e('Setup Wizard & Other Video Tutorials', 'bulletproof-security'); ?></a></strong><br />
	<strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-free/#bps-free-general-troubleshooting" title="BPS Troubleshooting Steps" target="_blank"><?php _e('BPS Troubleshooting Steps', 'bulletproof-security'); ?></a></strong><br /><br />

	<?php echo $bps_general_help_info; echo $bps_wbm_content; ?>
    </p>
</div> 

<div id="PFWScan-Menu-Link"></div>

<?php
// WBM Status: real-time status check
// 3 possible WBM Status indicators: Activated, Deactivated or Disabled.
function bpsPro_wbm_status() {
global $bps_version;
	
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	
	$filename = ABSPATH . 'wp-admin/.htaccess';
	
	if ( file_exists($filename) ) {
		$check_string = @file_get_contents($filename);
	}
	
	if ( @$_POST['Submit-WBM-Activate'] != true && @$_POST['Submit-WBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' || $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) ) {	
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE-check" ) ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		}
	}

	if ( @$_POST['Submit-WBM-Activate'] == true || @$_POST['Submit-WBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' || $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) ) {	
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( strpos( $check_string, "BULLETPROOF $bps_version" ) && strpos( $check_string, "BPSQSE-check" ) ) {
			$text = '<h3><strong>'.__('WBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		}
	}
}
?>

<div id="WBM-Status"><?php bpsPro_wbm_status(); ?></div>

<div id="wpadmin-bulletproof-mode" style="padding-left:10px;border-bottom:1px solid #999999;">

<form name="WBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_wbm_activate'); ?>

	<div id="WBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-WBM-Activate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate wp-admin Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="WBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_wbm_deactivate'); ?>

	<div id="WBM-buttons" style="">
    <input type="submit" name="Submit-WBM-Deactivate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate wp-admin Folder BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>

<div id="UAEG-Menu-Link"></div>

<h3><?php _e('Hidden Plugin Folders|Files Cron (HPF)', 'bulletproof-security'); ?>  <button id="bps-open-modal5" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content5" title="<?php _e('Hidden Plugin Folders|Files Cron (HPF)', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text;
		echo $bps_general_help_info; 
		echo $bps_hpf_content;
	?>
    </p>
</div>

<?php
// HPF Status: real-time status check
// 2 possible HPF Status indicators: HPF Cron On, HPF Cron Off.
function bpsPro_hpf_status() {
	
	$hpf_options = get_option('bulletproof_security_options_hpf_cron');	
	
	if ( @$_POST['Submit-Hidden-Plugins'] != true && @$_POST['Hidden-Plugins-Ignore-Submit'] != true ) {
	
		if ( $hpf_options['bps_hidden_plugins_cron'] == 'On' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('HPF Cron On', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( $hpf_options['bps_hidden_plugins_cron'] == 'Off' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('HPF Cron Off', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		}
	}

	if ( @$_POST['Submit-Hidden-Plugins'] == true || @$_POST['Hidden-Plugins-Ignore-Submit'] == true ) {
		
		if ( $hpf_options['bps_hidden_plugins_cron'] == 'On' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('HPF Cron On', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( $hpf_options['bps_hidden_plugins_cron'] == 'Off' ) {
			$text = '<h3><strong>'.__('HPF Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('HPF Cron Off', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		}
	}
}
?>

<div id="HPF-Status"><?php bpsPro_hpf_status(); ?></div>

<div id="HPF1" style="padding-left:10px;">
<div id="HPF2" style="position:relative;top:10px;left:0px;float:left;margin:0px 15px 0px 0px;">
    
<?php
	// Form: Hidden|Empty Plugin Folders|Files Cron
	echo '<form name="HPFCron" action="'.admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ).'" method="post">';
	wp_nonce_field('bulletproof_security_hpf_cron');

	$hpf_options = get_option('bulletproof_security_options_hpf_cron');
	
	echo '<label for="bps-hpf">'.__('HPF Cron Check Frequency:', 'bulletproof-security').'</label><br>';
	echo '<select name="hpf_cron_frequency" class="form-340">';
	echo '<option value="1"'. selected('1', $hpf_options['bps_hidden_plugins_cron_frequency']).'>'.__('Run Check Every 1 Minute', 'bulletproof-security').'</option>';
	echo '<option value="5"'. selected('5', $hpf_options['bps_hidden_plugins_cron_frequency']).'>'.__('Run Check Every 5 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="10"'. selected('10', $hpf_options['bps_hidden_plugins_cron_frequency']).'>'.__('Run Check Every 10 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="15"'. selected('15', $hpf_options['bps_hidden_plugins_cron_frequency']).'>'.__('Run Check Every 15 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="30"'. selected('30', $hpf_options['bps_hidden_plugins_cron_frequency']).'>'.__('Run Check Every 30 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="60"'. selected('60', $hpf_options['bps_hidden_plugins_cron_frequency']).'>'.__('Run Check Every 60 Minutes', 'bulletproof-security').'</option>';
	echo '<option value="daily"'. selected('daily', $hpf_options['bps_hidden_plugins_cron_frequency']).'>'.__('Run Check Once Daily', 'bulletproof-security').'</option>';
	echo '</select><br><br>';

	echo '<label for="bps-hpf">'.__('HPF Cron On|Off:', 'bulletproof-security').'</label><br>';
	echo '<select name="hpf_on_off" class="form-340">';
	echo '<option value="On"'. selected('On', $hpf_options['bps_hidden_plugins_cron']).'>'.__('HPF Cron On', 'bulletproof-security').'</option>';
	echo '<option value="Off"'. selected('Off', $hpf_options['bps_hidden_plugins_cron']).'>'.__('HPF Cron Off', 'bulletproof-security').'</option>';
	echo '</select>';
	
	echo "<p><input type=\"submit\" name=\"Submit-Hidden-Plugins\" value=\"".__('Save HPF Cron Options', 'bulletproof-security')."\" class=\"button bps-button\" onclick=\"return confirm('".__('The default Cron Frequency is: Run Check Every 15 Minutes. This is a lightweight check that uses an insignificant amount of resources/memory so 4 checks per hour will not cause any performance issues whatsoever.\n\n-------------------------------------------------------------\n\nEven choosing Run Check Every 1 Minute would not cause any significant performance issues whatsoever.\n\n-------------------------------------------------------------\n\nClick OK to proceed or click Cancel', 'bulletproof-security')."')\" /></p></form>";

$scrolltoHiddenPlugins = isset($_REQUEST['scrolltoHiddenPlugins']) ? (int) $_REQUEST['scrolltoHiddenPlugins'] : 0; 
?>

</div>

<div id="HPF3" style="position:relative;top:0px;left:0px;float:left;margin:0px 0px 0px 0px;">

<form name="Hidden-Plugins" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
    <?php wp_nonce_field('bulletproof_security_hpf_cron_ignore'); ?>
	<?php $hpfi_options = get_option('bulletproof_security_options_hidden_plugins'); ?>

	<div id="HPF4" style="position:relative;top:0px;left:0px;margin:10px 0px 10px 0px;">
	<strong><label><?php _e('Ignore Hidden Plugin Folders & Files:', 'bulletproof-security'); ?></label></strong><br />
    <?php $text = '<div style="allow-from-small-text">'.__('Add Ignore rules using plugin folder names or file names.', 'bulletproof-security').'<br>'.__('Use a comma and a space between folder and/or file names.', 'bulletproof-security').'<br><strong>'.__('Example: plugin-folder-name, example-file-name.php', 'bulletproof-security').'</strong></div>'; echo $text; ?>
    <textarea class="PFW-Allow-From-Text-Area" name="bps_hidden_plugins_check" style="margin-top:5px;" tabindex="1"><?php echo esc_html( trim( $hpfi_options['bps_hidden_plugins_check'], ", \t\n\r") ); ?></textarea>
	<input type="hidden" name="scrolltoHiddenPlugins" id="scrolltoHiddenPlugins" value="<?php echo esc_html( $scrolltoHiddenPlugins ); ?>" />
	</div>

	<div id="HPF5" style="position:relative;top:0px;left:0px;margin:10px 0px 10px 0px;">
    <input type="submit" name="Hidden-Plugins-Ignore-Submit" class="button bps-button" value="<?php esc_attr_e('Save Plugin Folder|Files Ignore Rules', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('This option is for adding ignore rules for Hidden or Empty Plugin Folders Detected by BPS or Non-standard WP files detected by BPS in your /plugins/ folder.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('This is an independent option setting that does not require clicking any other buttons.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to proceed or click Cancel.', 'bulletproof-security'); echo $text; ?>')"/>
	</div>

</form>
</div>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#PFW-Hidden-Plugins').submit(function(){ $('#scrolltoHiddenPlugins').val( $('#bps_hidden_plugins_check').scrollTop() ); });
	$('#bps_hidden_plugins_check').scrollTop( $('#scrolltoHiddenPlugins').val() );
});
/* ]]> */
</script>
</div>

<div id="MC1" style="position:relative;top:0px;left:0px;float:left;margin:0px 0px 0px 0px;width:100%;border-top:1px solid #999999;">

<h3><?php _e('Master htaccess Folder BulletProof Mode (MBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal6" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content6" title="<?php _e('MBM BulletProof Modes', 'bulletproof-security'); ?>">
	<p>
	<?php 
	$text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text;
	echo $bps_general_help_info; 
	echo $bps_mbm_content; 
	?>
    </p>
</div>

<?php
// MBM Status: real-time status check
// 3 possible MBM Status indicators: Activated, Deactivated or Disabled.
function bpsPro_mbm_status() {
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/.htaccess';
	
	if ( @$_POST['Submit-MBM-Activate'] != true && @$_POST['Submit-MBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		}
	}

	if ( @$_POST['Submit-MBM-Activate'] == true || @$_POST['Submit-MBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('MBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		}
	}
}
?>

<div id="MBM-Status"><?php bpsPro_mbm_status(); ?></div>

<div id="mbm-bulletproof-mode" style="padding-left:10px;">

<form name="MBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mbm_activate'); ?>

	<div id="MBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-MBM-Activate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate MBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="MBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_mbm_deactivate'); ?>

	<div id="MBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-MBM-Deactivate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Deactivate MBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>
</div>

<div id="MC2" style="position:relative;top:0px;left:0px;float:left;margin:0px 0px 0px 0px;width:100%;border-top:1px solid #999999;">

<h3><?php _e('BPS Backup Folder BulletProof Mode (BBM)', 'bulletproof-security'); ?>  <button id="bps-open-modal7" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content7" title="<?php _e('BBM BulletProof Modes', 'bulletproof-security'); ?>">
	<p>
	<?php 
	$text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text;
	echo $bps_general_help_info; 
	echo $bps_bbm_content; 
	?>
    </p>
</div>

<?php
// BBM Status: real-time status check
// 3 possible BBM Status indicators: Activated, Deactivated or Disabled.
function bpsPro_bbm_status() {
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	$filename = WP_CONTENT_DIR . '/bps-backup/.htaccess';
	
	if ( @$_POST['Submit-BBM-Activate'] != true && @$_POST['Submit-BBM-Deactivate'] != true ) {
	
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		}
	}

	if ( @$_POST['Submit-BBM-Activate'] == true || @$_POST['Submit-BBM-Deactivate'] == true ) {
		
		if ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<font color="blue" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Disabled', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		} elseif ( ! file_exists($filename) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<font color="#fb0101" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Deactivated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;	
		} elseif ( file_exists($filename) ) {
			$text = '<h3><strong>'.__('BBM Status: ', 'bulletproof-security').'<font color="green" style="background:#fff;padding:0px 3px 0px 3px;">'.__('Activated', 'bulletproof-security').'</font></strong></h3>';
			echo $text;
		}
	}
}
?>

<div id="BBM-Status"><?php bpsPro_bbm_status(); ?></div>

<div id="bbm-bulletproof-mode" style="padding-left:10px;">

<form name="BBM-Activate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_bbm_activate'); ?>

	<div id="BBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-BBM-Activate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Activate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Activate BBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="BBM-Deactivate" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_bbm_deactivate'); ?>

	<div id="BBM-buttons" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-BBM-Deactivate" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Deactivate', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Caution: BPS Backup Folder BulletProof Mode (BBM) should only be deactivated for testing or troubleshooting. Be sure to activate BBM BulletProof Mode after you are done testing or troubleshooting.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Deactivate BBM BulletProof Mode or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>
</div>

<div id="MC3" style="position:relative;top:0px;left:0px;float:left;margin:0px 0px 0px 0px;width:100%;border-top:1px solid #999999;">

<h3><?php _e('Backup & Restore BPS htaccess Files', 'bulletproof-security'); ?> <button id="bps-open-modal8" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content8" title="<?php _e('Backup & Restore BPS htaccess Files', 'bulletproof-security'); ?>">
	<p>
	<?php 
	$text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
	echo $text;
	echo $bps_backup_restore_content; 
	?>
    </p>
</div>

<div id="backup-restore-mode" style="padding-left:10px;">

<form name="Backup-htaccess-Files" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_backup_active_htaccess_files'); ?>

	<div id="Backup-htaccess-Files" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-Backup-htaccess-Files" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Backup htaccess Files', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Backup BPS htaccess files or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

<form name="Restore-htaccess-Files" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_restore_active_htaccess_files'); ?>

	<div id="Restore-htaccess-Files" style="float:left;padding-right:20px;">
    <input type="submit" name="Submit-Restore-htaccess-Files" style="margin:10px 0px 10px 0px;" value="<?php esc_attr_e('Restore htaccess Files', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Restore BPS htaccess files or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	</div>
</form>

</div>
</div>

</td>
  </tr>
  <tr>
    <td class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>

<?php } ?>
</div>
            
<div id="bps-tabs-6" class="bps-tab-page">
<h2><?php _e('htaccess File Editor ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Check or edit BPS htaccess files/code manually/directly for testing.  Use BPS Custom Code to save htaccess code permanently.', 'bulletproof-security'); ?></span></h2>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell">    

<h3 style="margin:0px 0px 5px 5px;"><?php _e('htaccess File Editing', 'bulletproof-security'); ?>  <button id="bps-open-modal9" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="bps-modal-content9" title="<?php _e('htaccess File Editing', 'bulletproof-security'); ?>">  
	<p><?php echo $bps_hfe_content; ?></p>
</div>

<?php if ( ! current_user_can('manage_options') ) { _e('Permission Denied', 'bulletproof-security'); } else { ?>

<table width="100%" border="0">
  <tr>
    <td colspan="2">
    
    <div id="bps_file_editor" class="bps_file_editor_update">

<?php
echo bps_secure_htaccess_file_check();
echo bps_default_htaccess_file_check();
echo bps_wpadmin_htaccess_file_check();

// Perform File Open and Write test first by appending a blank space/nothing to files for write testing.
if ( current_user_can('manage_options') ) {
$secure_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/secure.htaccess';
$write_test = "";
$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	
	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: secure.htaccess Master file is disabled.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	
	} elseif ( ! file_exists($secure_htaccess_file) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
		$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: A secure.htaccess Master file was NOT found.', 'bulletproof-security').'</strong></font><br>';
		echo $text;	
		
	} else {
		
		if ( file_exists($secure_htaccess_file) ) {	
	
			if ( is_writable($secure_htaccess_file) ) {
			if ( ! $handle = fopen($secure_htaccess_file, 'a+b') ) {
				exit;
			}
    
			if ( fwrite($handle, $write_test) === FALSE ) {
				exit;
			}
		
				$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! The secure.htaccess Master file is writable.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
			fclose($handle);
			}
			
			if ( ! is_writable($secure_htaccess_file) ) {
				$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$secure_htaccess_file . '</strong></font><br>';
				echo $text;
			}	
		}
	}
}
	
	if ( isset( $_POST['submit1'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_1' );
		$newcontent1 = stripslashes($_POST['newcontent1']);
	
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: secure.htaccess Master file writing is disabled.', 'bulletproof-security').'</strong></font><br>';			
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		if ( ! is_writable($secure_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the secure.htaccess Master file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		}	
	
		if ( is_writable($secure_htaccess_file) ) {

    	if ( ! $handle = fopen($secure_htaccess_file, 'w+b') ) {
			exit;
    	}
    	
		if ( fwrite($handle, $newcontent1) === FALSE ) {
			exit;
		}

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('The secure.htaccess Master file has been updated.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
    		echo $bps_bottomDiv;
		
		fclose($handle);
		}
	}

if ( current_user_can('manage_options') ) {
$default_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/default.htaccess';
$write_test = "";
$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	
	if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: default.htaccess Master file is disabled.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	
	} elseif ( ! file_exists($default_htaccess_file) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
		$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: A default.htaccess Master file was NOT found.', 'bulletproof-security').'</strong></font><br>';
		echo $text;	
		
	} else {
		
		if ( file_exists($default_htaccess_file) ) {		
	
			if ( is_writable($default_htaccess_file) ) {
    		if ( ! $handle = fopen($default_htaccess_file, 'a+b') ) {
	    		exit;
    		}
    
			if ( fwrite($handle, $write_test) === FALSE ) {
	    		exit;
    		}
		
				$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! The default.htaccess Master file is writable.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
			fclose($handle);
			}
			
			if ( ! is_writable($default_htaccess_file) ) {
				$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$default_htaccess_file . '</strong></font><br>';
				echo $text;
			}	
		}
	}
}
	
	if ( isset( $_POST['submit2'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_2' );
		$newcontent2 = stripslashes($_POST['newcontent2']);
	
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: default.htaccess Master file writing is disabled.', 'bulletproof-security').'</strong></font><br>';			
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		if ( ! is_writable($default_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the default.htaccess Master file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		}	
	
		if ( is_writable($default_htaccess_file) ) {

    	if ( ! $handle = fopen($default_htaccess_file, 'w+b') ) {
			exit;
    	}
    	
		if ( fwrite($handle, $newcontent2) === FALSE ) {
			exit;
		}

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('The default.htaccess Master file has been updated.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
    		echo $bps_bottomDiv;
		
		fclose($handle);
		}
		
		$custom_default_htaccess = WP_CONTENT_DIR . '/bps-backup/master-backups/default.htaccess';

		// .53.9: Save the Custom default.htaccess file to /bps-backup/master-backups/default.htaccess
		if ( ! copy($default_htaccess_file, $custom_default_htaccess) ) {
			echo $bps_topDiv;
			$text = '<strong><font color="#fb0101">'.__('Failed to copy your Custom default.htaccess file: ', 'bulletproof-security').'</font>'.$default_htaccess_file.__(' to: ', 'bulletproof-security').$custom_default_htaccess.__(' Check that the /bps-backup/ and /master-backups/ folders exist and the folder permissions or Ownership for these folders.', 'bulletproof-security').'</strong><br>';
			echo $text;
			echo $bps_bottomDiv;
		} else {
			echo $bps_topDiv;
			$text = '<strong><font color="green">'.__('Your Custom default.htaccess Master file has been successfully saved to: ', 'bulletproof-security').'</font>'.$custom_default_htaccess.'</strong><br>';
			echo $text;
			echo $bps_bottomDiv;
		}
	}

if ( current_user_can('manage_options') ) {
$wpadmin_htaccess_file = WP_PLUGIN_DIR . '/bulletproof-security/admin/htaccess/wpadmin-secure.htaccess';
$write_test = "";
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		$text = '<strong><font color="black">'.__('wpadmin-secure.htaccess file writing is disabled.', 'bulletproof-security').'</font></strong><br>';
		echo $text;
	
	} else {

		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: wpadmin-secure.htaccess Master file is disabled.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
	
		} elseif ( ! file_exists($wpadmin_htaccess_file) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: A wpadmin-secure.htaccess Master file was NOT found.', 'bulletproof-security').'</strong></font><br>';
			echo $text;	
		
		} else {
		
			if ( file_exists($wpadmin_htaccess_file) ) {	

				if ( is_writable($wpadmin_htaccess_file) ) {
    			if ( ! $handle = fopen($wpadmin_htaccess_file, 'a+b') ) {
	    			exit;
    			}
    
				if ( fwrite($handle, $write_test) === FALSE ) {
	    			exit;
				}
		
					$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! The wpadmin-secure.htaccess Master file is writable.', 'bulletproof-security').'</strong></font><br>';
					echo $text;
				fclose($handle);
				}
			
				if ( ! is_writable($wpadmin_htaccess_file) ) {
					$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$wpadmin_htaccess_file . '</strong></font><br>';
					echo $text;
				}	
			}
		}
	}
}
	
	if ( isset( $_POST['submit4'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_4' );
		$newcontent4 = stripslashes($_POST['newcontent4']);
	
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: wpadmin-secure.htaccess Master file writing is disabled.', 'bulletproof-security').'</strong></font><br>';			
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		if ( ! is_writable($wpadmin_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the wpadmin-secure.htaccess Master file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		}	
	
		if ( is_writable($wpadmin_htaccess_file) ) {

    	if ( ! $handle = fopen($wpadmin_htaccess_file, 'w+b') ) {
			exit;
    	}
    	
		if ( fwrite($handle, $newcontent4) === FALSE ) {
			exit;
		}

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('The wpadmin-secure.htaccess Master file has been updated.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
    		echo $bps_bottomDiv;
		
		fclose($handle);
		}
	}

if ( current_user_can('manage_options') ) {
$root_htaccess_file = ABSPATH . '.htaccess';
$write_test = "";
$HFiles_options = get_option('bulletproof_security_options_htaccess_files');	
	
	if ( ! file_exists($root_htaccess_file) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
		$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: Root htaccess file does not exist.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	
	} elseif ( ! file_exists($root_htaccess_file) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
		$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: An htaccess file was NOT found in your root folder', 'bulletproof-security').'</strong></font><br>';
		echo $text;	
		
	} else {
		
		if ( file_exists($root_htaccess_file) ) {

			if ( is_writable($root_htaccess_file) ) {
    		
			if ( ! $handle = fopen($root_htaccess_file, 'a+b') ) {
	    		exit;
    		}
    		if ( fwrite($handle, $write_test) === FALSE ) {
	    		exit;
    		}
		
				$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! Your currently active root htaccess file is writable.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
			
			fclose($handle);
			}
			
			if ( ! is_writable($root_htaccess_file) ) {
				$text = '<font color="blue" style="font-size:12px;"><strong>'.__('Your root htaccess file is Locked with Read Only Permissions.', 'bulletproof-security').'<br>'.__('Use the Lock and Unlock buttons below to Lock or Unlock your root htaccess file for editing.', 'bulletproof-security').'</strong></font><br>';
				echo $text;
			}
		}
	}
}
	
	if ( isset( $_POST['submit5'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_5' );
		$newcontent5 = stripslashes($_POST['newcontent5']);
	
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: Root htaccess file writing is disabled.', 'bulletproof-security').'</strong></font><br>';			
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		if ( ! is_writable($root_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the Root htaccess file. If your Root htaccess file is locked you must unlock first.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		}	
	
		if ( is_writable($root_htaccess_file) ) {

    	if ( ! $handle = fopen($root_htaccess_file, 'w+b') ) {
			exit;
    	}
    	
		if ( fwrite($handle, $newcontent5) === FALSE ) {
			exit;
		}

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Your currently active root htaccess file has been updated.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
    		echo $bps_bottomDiv;
		
		fclose($handle);
		}
	}

if ( current_user_can('manage_options') ) {
$current_wpadmin_htaccess_file = ABSPATH . 'wp-admin/.htaccess';
$write_test = "";
	
	$HFiles_options = get_option('bulletproof_security_options_htaccess_files');
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	$GDMW_options = get_option('bulletproof_security_options_GDMW');	
	
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' || $GDMW_options['bps_gdmw_hosting'] == 'yes' ) {
		$text = '<font color="blue" style="font-size:12px;"><strong>'.__('wp-admin active htaccess file writing is disabled.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
	
	} else {

		if ( ! file_exists($root_htaccess_file) && $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			$text = '<font color="blue" style="font-size:12px;"><strong>'.__('htaccess Files Disabled: wp-admin folder htaccess file does not exist.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
	
		} elseif ( ! file_exists($root_htaccess_file) && $HFiles_options['bps_htaccess_files'] != 'disabled' ) {	
			$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('ERROR: An htaccess file was NOT found in your wp-admin folder', 'bulletproof-security').'</strong></font><br>';
			echo $text;	
		
		} else {
		
			if ( file_exists($current_wpadmin_htaccess_file) ) {

				if ( is_writable($current_wpadmin_htaccess_file) ) {
    			if ( ! $handle = fopen($current_wpadmin_htaccess_file, 'a+b') ) {
	    			exit;
    			}
    			
				if ( fwrite($handle, $write_test) === FALSE ) {
	    			exit;
    			}
		
					$text = '<font color="green" style="font-size:12px;"><strong>'.__('File Open and Write test successful! Your currently active wp-admin htaccess file is writable.', 'bulletproof-security').'</strong></font><br>';
					echo $text;
			
				fclose($handle);				
				}
	
				if ( ! is_writable($current_wpadmin_htaccess_file) ) {
					$text = '<font color="#fb0101" style="font-size:12px;"><strong>'.__('Cannot write to file: ', 'bulletproof-security').$current_wpadmin_htaccess_file . '</strong></font><br>';
					echo $text;
				}
			}
		}
	}
}
	
	if ( isset( $_POST['submit6'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_save_settings_6' );
		$newcontent6 = stripslashes($_POST['newcontent6']);
	
		if ( $HFiles_options['bps_htaccess_files'] == 'disabled' ) {
			echo $bps_topDiv;
			$text = '<font color="blue"><strong>'.__('htaccess Files Disabled: wp-admin htaccess file writing is disabled.', 'bulletproof-security').'</strong></font><br>';			
			echo $text;
    		echo $bps_bottomDiv;
			return;
		}

		if ( ! is_writable($current_wpadmin_htaccess_file) ) {
			echo $bps_topDiv;
			$text = '<font color="#fb0101"><strong>'.__('Error: Unable to write to the wp-admin htaccess file.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
			echo $bps_bottomDiv;
		}	
	
		if ( is_writable($current_wpadmin_htaccess_file) ) {

    	if ( ! $handle = fopen($current_wpadmin_htaccess_file, 'w+b') ) {
			exit;
    	}
    	
		if ( fwrite($handle, $newcontent6) === FALSE ) {
			exit;
		}

			echo $bps_topDiv;
			$text = '<font color="green"><strong>'.__('Your currently active wp-admin htaccess file has been updated.', 'bulletproof-security').'</strong></font><br>';
			echo $text;
    		echo $bps_bottomDiv;
		
		fclose($handle);
		}
	}
	
// Lock and Unlock Root .htaccess file 
if ( isset( $_POST['submit-ProFlockLock'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_flock_lock' );

	$bpsRootHtaccessOL = ABSPATH . '.htaccess';
	
	if ( file_exists($bpsRootHtaccessOL) ) {
		@chmod($bpsRootHtaccessOL, 0404);
		echo $bps_topDiv;
		$text = '<font color="green"><strong><br>'.__('Your Root htaccess file has been Locked.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	} else {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong><br>'.__('Unable to Lock your Root htaccess file.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	}
}
	
if ( isset( $_POST['submit-ProFlockUnLock'] ) && current_user_can('manage_options') ) {
	check_admin_referer( 'bulletproof_security_flock_unlock' );
	
	$bpsRootHtaccessOL = ABSPATH . '.htaccess';
		
	if ( file_exists($bpsRootHtaccessOL) ) {
		@chmod($bpsRootHtaccessOL, 0644);
		echo $bps_topDiv;
		$text = '<font color="green"><strong><br>'.__('Your Root htaccess file has been Unlocked.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	} else {
		echo $bps_topDiv;
		$text = '<font color="#fb0101"><strong><br>'.__('Unable to Unlock your Root htaccess file.', 'bulletproof-security').'</strong></font><br>';
		echo $text;
		echo $bps_bottomDiv;
	}
}
?>

</div>

</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>

<?php // Detect the SAPI - display form submit button only if sapi is cgi
	$sapi_type = php_sapi_name();
	if ( @substr($sapi_type, 0, 6) != 'apache' ) {	
?>    
 
<div id="bpsLockHtaccess">  
<form name="bpsFlockLockForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_flock_lock'); ?>
	<input type="submit" name="submit-ProFlockLock" value="<?php esc_attr_e('Lock htaccess File', 'bulletproof-security'); ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Lock your Root htaccess file or click Cancel.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Note: The File Open and Write Test window will still display the last status of the file as Unlocked. To see the current status refresh your browser.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>

<div id="bpsUnLockHtaccess">    
<form name="bpsFlockUnLockForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_flock_unlock'); ?>

	<input type="submit" name="submit-ProFlockUnLock" value="<?php esc_attr_e('Unlock htaccess File', 'bulletproof-security'); ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to Unlock your Root htaccess file or click Cancel.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Note: The File Open and Write Test window will still display the last status of the file as Locked. To see the current status refresh your browser.', 'bulletproof-security'); echo $text; ?>')" />
</form>
</div>

<div id="bpsAutoLockOn">
<form name="bpsRootAutoLock-On" action="options.php#bps-tabs-6" method="post">
    <?php settings_fields('bulletproof_security_options_autolock'); ?>
	<?php $options = get_option('bulletproof_security_options_autolock'); ?>
	<input type="hidden" name="bulletproof_security_options_autolock[bps_root_htaccess_autolock]" value="On" />
	<input type="submit" name="submit-RootHtaccessAutoLock-On" value="<?php esc_attr_e('Turn On AutoLock', 'bulletproof-security'); ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Turning AutoLock On will allow BPS Pro to automatically lock your Root .htaccess file. For some folks this causes a problem because their Web Hosts do not allow the Root .htaccess file to be locked. For most folks allowing BPS Pro to AutoLock the Root .htaccess file works fine.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Turn AutoLock On or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />

<?php if ( $options['bps_root_htaccess_autolock'] == '' || $options['bps_root_htaccess_autolock'] == 'On' ) { echo '<label class="autolock_status" style="font-weight:bold;">'.__('On', 'bulletproof-security').'</label>'; } ?>

</form>
</div>

<div id="bpsAutoLockOff">
<form name="bpsRootAutoLock-Off" action="options.php#bps-tabs-6" method="post">
    <?php settings_fields('bulletproof_security_options_autolock'); ?>
	<?php $options = get_option('bulletproof_security_options_autolock'); ?>
	<input type="hidden" name="bulletproof_security_options_autolock[bps_root_htaccess_autolock]" value="Off" />
	<input type="submit" name="submit-RootHtaccessAutoLock-Off" value="<?php esc_attr_e('Turn Off AutoLock', 'bulletproof-security'); ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Turning AutoLock Off will prevent BPS Pro from automatically locking your Root .htaccess file. For some folks this is necessary because their Web Hosts do not allow the Root .htaccess file to be locked. For most folks allowing BPS Pro to AutoLock the Root .htaccess file works fine.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Turn AutoLock Off or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />

<?php if ( $options['bps_root_htaccess_autolock'] == 'Off') { echo '<label class="autolock_status" style="font-weight:bold;">'.__('Off', 'bulletproof-security').'</label>'; } ?>

</form>
</div>

<?php } ?>

</td>
  <tr>
    <td colspan="2">
    
    <!-- jQuery UI File Editor Tab Menu -->
<div id="bps-edittabs" class="bps-edittabs-class">
		
	<ul>
		<li><a href="#bps-edittabs-1"><?php _e('secure.htaccess', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-2"><?php _e('default.htaccess', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-4"><?php _e('wpadmin-secure.htaccess', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-5"><?php _e('Your Current Root htaccess File', 'bulletproof-security'); ?></a></li>
		<li><a href="#bps-edittabs-6"><?php _e('Your Current wp-admin htaccess File', 'bulletproof-security'); ?></a></li>
	</ul>
       
<?php 
$scrollto1 = isset($_REQUEST['scrollto1']) ? (int) $_REQUEST['scrollto1'] : 0; 
$scrollto2 = isset($_REQUEST['scrollto2']) ? (int) $_REQUEST['scrollto2'] : 0;
$scrollto4 = isset($_REQUEST['scrollto4']) ? (int) $_REQUEST['scrollto4'] : 0;
$scrollto5 = isset($_REQUEST['scrollto5']) ? (int) $_REQUEST['scrollto5'] : 0;
$scrollto6 = isset($_REQUEST['scrollto6']) ? (int) $_REQUEST['scrollto6'] : 0;
?>

<div id="bps-edittabs-1" class="bps-edittabs-page-class">
<form name="template1" id="template1" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_1'); ?>
    <div>
    <textarea class="bps-text-area-600x700" name="newcontent1" id="newcontent1" tabindex="1"><?php echo bps_get_secure_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $secure_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto1" id="scrollto1" value="<?php echo esc_html( $scrollto1 ); ?>" />
    <p class="submit">
	<input type="submit" name="submit1" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
</div>
</form>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template1').submit(function(){ $('#scrollto1').val( $('#newcontent1').scrollTop() ); });
	$('#newcontent1').scrollTop( $('#scrollto1').val() ); 
});
/* ]]> */
</script>     
</div>

<div id="bps-edittabs-2" class="bps-edittabs-page-class">
<form name="template2" id="template2" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_2'); ?>
	<div>
    <textarea class="bps-text-area-600x700" name="newcontent2" id="newcontent2" tabindex="2"><?php echo bps_get_default_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $default_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto2" id="scrollto2" value="<?php echo esc_html( $scrollto2 ); ?>" />
    <p class="submit">
	<input type="submit" name="submit2" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
</div>
</form>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template2').submit(function(){ $('#scrollto2').val( $('#newcontent2').scrollTop() ); });
	$('#newcontent2').scrollTop( $('#scrollto2').val() );
});
/* ]]> */
</script>     
</div>

<div id="bps-edittabs-4" class="bps-edittabs-page-class">
<form name="template4" id="template4" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_4'); ?>
	<div>
    <textarea class="bps-text-area-600x700" name="newcontent4" id="newcontent4" tabindex="4"><?php echo bps_get_wpadmin_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $wpadmin_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto4" id="scrollto4" value="<?php echo esc_html( $scrollto4 ); ?>" />
    <p class="submit">
	<input type="submit" name="submit4" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
</div>
</form>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template4').submit(function(){ $('#scrollto4').val( $('#newcontent4').scrollTop() ); });
	$('#newcontent4').scrollTop( $('#scrollto4').val() );
});
/* ]]> */
</script>     
</div>

<?php
// File Editor Root .htaccess file Lock check with pop up Confirm message
function bpsStatusRHE() {
clearstatcache();
$file = ABSPATH . '.htaccess';
$perms = @substr(sprintf('%o', fileperms($file)), -4);
$sapi_type = php_sapi_name();
	
	if ( file_exists($file) && @substr( $sapi_type, 0, 6) != 'apache' ) {		
	return $perms;
	}
}
?>

<div id="bps-edittabs-5" class="bps-edittabs-page-class">
<form name="template5" id="template5" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_5'); ?>
	<div>
    <textarea class="bps-text-area-600x700" name="newcontent5" id="newcontent5" tabindex="5"><?php echo bps_get_root_htaccess(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $root_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto5" id="scrollto5" value="<?php echo esc_html( $scrollto5 ); ?>" />
    <p class="submit">
    
	<?php if ( @bpsStatusRHE($perms) == '0404' ) { ?>
	<input type="submit" name="submit5" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" class="button bps-button" onClick="return confirm('<?php $text = __('YOUR ROOT HTACCESS FILE IS LOCKED.', 'bulletproof-security').'\n\n'.__('YOUR FILE EDITS|CHANGES CANNOT BE SAVED.', 'bulletproof-security').'\n\n'.__('Click Cancel, copy the file editing changes you made to save them and then click the Unlock .htaccess File button to unlock your Root .htaccess file. After your Root .htaccess file is unlocked paste your file editing changes back into your Root .htaccess file and click this Update File button again to save your file edits/changes.', 'bulletproof-security'); echo $text; ?>')" />
	<?php } else { ?>
	<input type="submit" name="submit5" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
<?php } ?>

</div>
</form>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template5').submit(function(){ $('#scrollto5').val( $('#newcontent5').scrollTop() ); });
	$('#newcontent5').scrollTop( $('#scrollto5').val() );
});
/* ]]> */
</script>     
</div>

<div id="bps-edittabs-6" class="bps-edittabs-page-class">
<form name="template6" id="template6" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-6' ); ?>" method="post">
<?php wp_nonce_field('bulletproof_security_save_settings_6'); ?>
	<div>
    <textarea class="bps-text-area-600x700" name="newcontent6" id="newcontent6" tabindex="6"><?php echo bps_get_current_wpadmin_htaccess_file(); ?></textarea>
	<input type="hidden" name="action" value="update" />
    <input type="hidden" name="filename" value="<?php echo esc_attr( $current_wpadmin_htaccess_file ) ?>" />
	<input type="hidden" name="scrollto6" id="scrollto6" value="<?php echo esc_html( $scrollto6 ); ?>" />
    <p class="submit">
	<input type="submit" name="submit6" class="button bps-button" value="<?php esc_attr_e('Update File', 'bulletproof-security') ?>" /></p>
</div>
</form>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template6').submit(function(){ $('#scrollto6').val( $('#newcontent6').scrollTop() ); });
	$('#newcontent6').scrollTop( $('#scrollto6').val() );
});
/* ]]> */
</script>     
</div>
</div>

</td>
  </tr>
</table>

<?php } ?>

</td>
  </tr>
  <tr>
    <td class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>
</div>

<div id="bps-tabs-7" class="bps-tab-page">
<h2><?php _e('htaccess File Custom Code ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Save custom htaccess code for your Root and wp-admin htaccess Files permanently', 'bulletproof-security'); ?> <br /> <span class="cc-read-me-text"><?php _e('* Click the Read Me help button for Custom Code Setup Steps', 'bulletproof-security'); ?></span></span></h2>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">
    
<h3 style="margin:0px 0px 5px 0px;"><?php _e('Custom Code', 'bulletproof-security'); ?>  <button id="bps-open-modal10" class="button bps-modal-button"><?php _e('Read Me', 'bulletproof-security'); ?></button></h3>

<div id="ResetDismissNotices">
<form name="bpsResetDismissNotices" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_reset_dismiss_notices'); ?>
    
    <p><strong><label for="Status-Display"><?php _e('Reset|Recheck Dismiss Notices: ', 'bulletproof-security'); ?></label>
	<input type="hidden" name="bpsRDN" value="bps-RDN" />
	<input type="submit" name="bpsResetDismissSubmit" class="button bps-button" value="<?php esc_attr_e('Reset|Recheck', 'bulletproof-security') ?>" />
	</strong></p>
</form>
</div>

<div id="bps-modal-content10" title="<?php _e('Custom Code', 'bulletproof-security'); ?>">
	<p>
	<?php
        $text = '<strong>'.__('This Read Me Help window is draggable (top) and resizable (bottom right corner)', 'bulletproof-security').'</strong><br><br>';
		echo $text; 
		$text = '<strong><font color="blue">'.__('Forum Help Links: ', 'bulletproof-security').'</font></strong><br>'; 	
		echo $text;	
	?>
	<strong><a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Brute Force Login Page Protection code" target="_blank"><?php _e('Brute Force Login Page Protection code', 'bulletproof-security'); ?></a></strong><br /><br />

	<?php echo $bps_customcode_content; ?>
    
    </p>
</div>

<div id="CC-Help-Links">
<h3><?php $text = '<strong><a href="https://forum.ait-pro.com/video-tutorials/" target="_blank" title="Link opens in a new Browser window">'.__('Custom Code Video Tutorial', 'bulletproof-security').'</a></strong>'; echo $text; ?></h3>
<h3><?php $text = '<strong><a href="https://forum.ait-pro.com/read-me-first/" target="_blank" title="Link opens in a new Browser window">'.__('BulletProof Security Forum', 'bulletproof-security').'</a></strong>'; echo $text; ?></h3>
</div>

<?php
if ( ! current_user_can('manage_options') ) { 
	_e('Permission Denied', 'bulletproof-security'); 
	
	} else { 
	
	require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-export-import.php' );
}
?>   

<div id="CC-Import">
<form name="bpsImport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field('bulletproof_security_cc_import'); ?>
	<input type="file" name="bps_cc_import" id="bps_cc_import" />
	<input type="submit" name="Submit-CC-Import" class="button bps-button" style="margin-top:1px;" value="<?php esc_attr_e('Import', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will Import all of your Root and wp-admin Custom Code from the cc-master.zip file on your computer.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Import Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_CC_Import(); ?>
</form>
</div>

<div id="CC-Export">
<form name="bpsExport" id="bpsExport" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_cc_export'); ?>
    <input type="submit" name="Submit-CC-Export" class="button bps-button" value="<?php esc_attr_e('Export', 'bulletproof-security') ?>" onclick="return confirm('<?php 
$text = __('Clicking OK will Export (copy) all of your Root and wp-admin Custom Code into the cc-master.zip file, which you can then download to your computer by clicking the Download Zip Export button displayed in the Custom Code Export success message.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Export Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_CC_Export(); ?>
</form>
</div>

<div id="CC-Delete">
<form name="bpsDeleteCC" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
	<?php wp_nonce_field('bulletproof_security_cc_delete'); ?>
	<input type="submit" name="Submit-CC-Delete" class="button bps-button" value="<?php esc_attr_e('Delete', 'bulletproof-security') ?>" onclick="return confirm('<?php $text = __('Clicking OK will delete all of your Root and wp-admin Custom Code from all of the Custom Code text boxes.', 'bulletproof-security').'\n\n'.$bpsSpacePop.'\n\n'.__('Click OK to Delete Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
	<?php bpsPro_CC_Delete(); ?>
</form>
</div>

<?php 
if ( ! current_user_can('manage_options') ) { 
	_e('Permission Denied', 'bulletproof-security'); 
	
	} else { 

	require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/core/core-custom-code.php' );
}
?>
<br />

</td>
  </tr>
  <tr>
    <td class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>

</div>

<div id="bps-tabs-9" class="bps-tab-page">
<h2><?php _e('My Notes ~ ', 'bulletproof-security'); ?><span style="font-size:.75em;"><?php _e('Save Personal Notes and htaccess Code Notes to your WordPress Database', 'bulletproof-security'); ?></span></h2>

<?php if ( ! current_user_can('manage_options') ) { _e('Permission Denied', 'bulletproof-security'); } else { 
	
// My Notes Form
function bpsPro_My_Notes_values_form() {
global $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['myNotes_submit'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_My_Notes' );
		
		$MyNotes_Options = array( 'bps_my_notes' => stripslashes($_POST['bps_my_notes']) );

		foreach( $MyNotes_Options as $key => $value ) {
			update_option('bulletproof_security_options_mynotes', $MyNotes_Options);
		}		
	
	echo $bps_topDiv;
	$text = '<strong><font color="green">'.__('Your My Notes Personal Notes and/or htaccess Code Notes saved successfully to your WordPress Database.', 'bulletproof-security').'</font></strong>';
	echo $text;		
	echo $bps_bottomDiv;	
	
	}
}	
	
	$scrolltoNotes = isset($_REQUEST['scrolltoNotes']) ? (int) $_REQUEST['scrolltoNotes'] : 0;
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">

<form name="myNotes" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-9' ); ?>" method="post">
<?php 
	wp_nonce_field('bulletproof_security_My_Notes'); 
	bpsPro_My_Notes_values_form();
	$My_Notes_options = get_option('bulletproof_security_options_mynotes'); 
?>

<div>
    <textarea class="bps-text-area-600x700" name="bps_my_notes" tabindex="1"><?php echo $My_Notes_options['bps_my_notes']; ?></textarea>
    <input type="hidden" name="scrolltoNotes" value="<?php echo esc_html( $scrolltoNotes ); ?>" />
    <p class="submit">
	<input type="submit" name="myNotes_submit" class="button bps-button" value="<?php esc_attr_e('Save My Notes', 'bulletproof-security') ?>" /></p>
</div>
</form>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#myNotes').submit(function(){ $('#scrolltoNotes').val( $('#bps_my_notes').scrollTop() ); });
	$('#bps_my_notes').scrollTop( $('#scrolltoNotes').val() ); 
});
/* ]]> */
</script>

</td>
  </tr>
  <tr>
    <td class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>
<?php } ?>
</div>

<div id="bps-tabs-10">
<h2><?php _e('Whats New in ', 'bulletproof-security'); ?><?php echo $bps_version; _e(' and General Help Info & Tips', 'bulletproof-security'); ?></h2>
<h3><?php _e('The Whats New page lists new changes made in each new version release of BulletProof Security', 'bulletproof-security'); ?></h3>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-whats_new_table">
  <tr>
   <td width="1%" class="bps-table_title_no_border">&nbsp;</td>
   <td width="99%" class="bps-table_title_no_border">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border">&nbsp;</td>
    <td class="bps-table_cell_no_border"><?php $text = '<h2><strong>'.__('General Help Info & Tips:', 'bulletproof-security').'</strong></h2>'; echo $text; ?></td>
  </tr>
   <tr>
    <td class="bps-table_cell_no_border">&bull;</td>
    <td class="bps-table_cell_no_border"><?php $text = '<strong>'.__('If BPS plugin pages are not displaying visually correct you can ', 'bulletproof-security').'<a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/theme-skin/theme-skin.php' ).'" title="Script|Style Loader Filter (SLF) In BPS Plugin Pages">'.esc_attr__('Turn On the BPS SLF filter', 'bulletproof-security').'</a></strong>'; echo $text; ?></td>
  </tr> 
  <tr>
    <td class="bps-table_cell_no_border">&bull;</td>
    <td class="bps-table_cell_no_border"><?php $text = '<strong>'.__('BPS Video Tutorials|Setup Wizard: ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/video-tutorials/" target="_blank" title="BPS Video Tutorials">BPS Pro Video Tutorials</a></strong>'; echo $text; ?></td>
  </tr>   
   <tr>
    <td class="bps-table_cell_no_border">&nbsp;</td>
    <td class="bps-table_cell_no_border">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border">&bull;</td>
    <td class="bps-table_cell_no_border"><?php $text = '<strong>'.__('Troubleshooting Steps & The BPS Security Log: ', 'bulletproof-security').'</strong><br>'.__('All BPS plugin features can be turned Off/On individually to confirm, eliminate or isolate a problem or issue that may or may not be caused by BPS.', 'bulletproof-security').'<br><strong><a href="https://forum.ait-pro.com/forums/topic/read-me-first-pro/#bps-free-general-troubleshooting" target="_blank" title="BPS Troubleshooting Steps">Troubleshooting Steps</a></strong><br>'.__('The BPS Security Log is a primary troubleshooting tool. If BPS is blocking something legitimate in another plugin or theme then a Security Log entry will be logged for exactly what is being blocked. A whitelist rule can then be created to allow a plugin or theme to do what it needs to do without being blocked.', 'bulletproof-security').'<br><strong><a href="https://forum.ait-pro.com/video-tutorials/#security-log-firewall" target="_blank" title="BPS Security Log Video Tutorial">Security Log Video Tutorial</a></strong><br>'.__('Search the Forum site to see if a known issue or problem is already posted with a solution/whitelist rule in the Forum.', 'bulletproof-security').'<strong><br><a href="https://forum.ait-pro.com/forums/forum/bulletproof-security-free/" target="_blank" title="BPS Security Forum">BPS Security Forum</a></strong>'; echo $text; ?></td>
  </tr> 
   <tr>
    <td class="bps-table_cell_no_border">&nbsp;</td>
    <td class="bps-table_cell_no_border">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_no_border">&nbsp;</td>
    <td class="bps-table_cell_no_border"><?php $text = '<h3><strong>'.__('The BPS Changelog|Whats New page have been moved to the ', 'bulletproof-security').'<a href="https://forum.ait-pro.com/forums/topic/bps-changelog/" target="_blank" title="BulletProof Security Forum Changelog|Whats New Forum Topic">BulletProof Security Forum Changelog|Whats New Forum Topic</a></strong></h3><strong>'.__('Reasons for this Changelog|Whats New page change: ', 'bulletproof-security').'</strong>'.__('The BPS Changelog|Whats New page will not have to be translated by the WordPress PolyGlots Language Packs Team for each new version release of BPS, the Changelog|Whats New page will be much easier to maintain, the readme.txt file size will be much smaller in the BPS plugin, a complete history of all BPS version changes through the years and other beneficial reasons.', 'bulletproof-security').'</strong>'; echo $text; ?></td>
  </tr>
   <tr>
    <td class="bps-table_cell_no_border">&nbsp;</td>
    <td class="bps-table_cell_no_border">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_bottom_no_border">&nbsp;</td>
    <td class="bps-table_cell_bottom_no_border">&nbsp;</td>
  </tr>
</table>
</div>

<div id="bps-tabs-11">
<h2><?php _e('BulletProof Security Pro Feature Highlights', 'bulletproof-security'); ?></h2>

<div id="bpsPro-Features-Table">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
  <tr>
    <td colspan="2" class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    <td width="62%" valign="top" class="bps-table_cell_help">

<div id="bpsProLogo"><a href="https://affiliates.ait-pro.com/po/" target="_blank" title="Get BulletProof Security Pro">
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/bps-pro-logo.png'); ?>" /></a>
</div>

<div id="bpsProText">
<?php $text = '<h3><span class="blue-bold">'.__('The Ultimate Security Protection', 'bulletproof-security').'</span></h3>'; echo $text; ?>

<div id="bpsProLinks">
<div class="pro-links"><a href="https://forum.ait-pro.com/video-tutorials/" target="_blank" title="Link Opens in New Browser Window"><?php _e('BPS Pro One-Click Setup Wizard & Demo Video Tutorial', 'bulletproof-security'); ?></a></div><br /><br />
<div class="pro-links"><a href="https://www.ait-pro.com/bps-features/" target="_blank" title="Link Opens in New Browser Window"><?php _e('View All BPS Pro Features', 'bulletproof-security'); ?></a></div>
</div>
</div>

<div id="bpsProFeatures">

<?php $text = '<h3><span class="blue-bold">'.__('The Complete Website Security Solution for Hacker and Spammer Protection', 'bulletproof-security').'</span></h3>'; echo $text; ?>
<?php $text = '<h3><span class="blue-bold">'.__('Sometimes in life the best product is also the best priced product. See ', 'bulletproof-security').'<a href="https://affiliates.ait-pro.com/po/" target="_blank">'.__('Cost Comparison', 'bulletproof-security').'</a></span></h3>'; echo $text; ?>

<?php echo '<p><span class="blue-bold">'; _e('One-Click Setup Wizard Installation: ', 'bulletproof-security'); echo '</span>'; _e('Fast, simple and complete BPS Pro installation and setup.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('One-Click Upgrade: ', 'bulletproof-security'); echo '</span>'; _e('One-click Update Now plugin upgrade on the WordPress Plugins page.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('AutoRestore|Quarantine Intrusion Detection and Prevention System (IDPS): ', 'bulletproof-security'); echo '</span>'; _e('ARQ IDPS is a real-time file monitor that automatically quarantines malicious hacker files and autorestores legitimate website files if they have been altered or tampered with. ARQ utilizes countermeasure website security that has the capability to protect all of your website files, both WordPress and non-WordPress files, even if your Web Host Server is hacked or if your FTP password is cracked or stolen. Quarantine Options: Restore File, Delete File and View File. AutoRestore|Quarantine includes Displayed Alerts, Email Alerts and Logging. AutoRestore|Quarantine works seamlessly with WordPress Automatic Updates and Shiny Updates.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('Plugin Firewall|Plugin Firewall AutoPilot Mode: ', 'bulletproof-security'); echo '</span>'; _e('The Plugin Firewall|Plugins BulletProof Mode prevents/blocks/forbids Remote Access to the plugins folder from external sources (remote script execution, hacker recon, remote scanning, remote accessibility, etc.) and only allows internal access to the plugins folder based on this criteria: Domain name, Server IP Address and Public IP|Your Computer IP Address. True IP based Firewall that updates your IP address in real-time when it changes. AutoPilot Mode automatically creates Plugin Firewall whitelist rules in real-time.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('JTC Anti-Spam|Anti-Hacker: ', 'bulletproof-security'); echo '</span>'; _e('Blocks 100', 'bulletproof-security'); echo '% '; _e('of all SpamBot and HackerBot Brute Force Login attacks (auto-registering, auto-logins, auto-posting, auto-commenting). Hacker Protection|Spammer Protection|DoS/DDoS Login Attack Protection|Brute Force Login Attack Protection|SpamBot Trap. JTC Anti-Spam|Anti-Hacker provides website security protection as well as website Anti-Spam protection. JTC Anti-Spam|Anti-Hacker is user friendly Anti-Spam|Anti-Hacker Protection. You can customize and personalize your JTC ToolTip message and CAPTCHA to match your website concept. JTC Anti-Spam|Anti-Hacker protects these website pages/Forms: Login page|Form, Registration page|Form, Lost Password page|Form, Comment page|Form, BuddyPress Register page|Form and the BuddyPress Sidebar Login Form with a user friendly & customizable jQuery ToolTip CAPTCHA.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('Uploads Folder Anti-Exploit Guard (UAEG): ', 'bulletproof-security'); echo '</span>'; _e('Protects the WordPress Uploads folder. ONLY safe image files with valid image file extensions such as jpg, gif, png, etc. can be accessed, opened or viewed from the uploads folder. UAEG blocks files by file extension names in the uploads folder from being accessed, opened, viewed, processed or executed. Malicious files cannot be accessed, opened, viewed, processed or executed in the WordPress Uploads folder.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('DB Monitor Intrusion Detection System (IDS): ', 'bulletproof-security'); echo '</span>'; _e('The DB Monitor is an automated Intrusion Detection System (IDS) that alerts you via email anytime a change/modification occurs in your WordPress database or a new database table is created in your WordPress database. The DB Monitor email alert contains information about what database change/modification occurred and other relevant help info. Your DB Monitor Log also logs any changes/modifications to your WordPress database and other relevant help info.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('DB Diff Tool: ', 'bulletproof-security'); echo '</span>'; _e('The DB Diff Tool compares old database tables from DB backups to current database tables and displays any differences in the data/content of those 2 database tables. The DB Diff Tool can also be used to compare any data and not only just DB data.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('DB Status & Info: ', 'bulletproof-security'); echo '</span>'; _e('General DB Info shows commonly checked DB status and info about your WordPress database at a glance. Extensive DB Info shows extensive DB status information using: SHOW PRIVILEGES, SHOW TABLE STATUS|SIZE, SHOW STORAGE ENGINES, SHOW FULL PROCESSLIST, SHOW GLOBAL STATUS, SHOW SESSION STATUS, SHOW GLOBAL VARIABLES and SHOW SESSION VARIABLES.', 'bulletproof-security').'</p>'; ?>

<?php  echo '<p><span class="blue-bold">'; _e('S-Monitor: ', 'bulletproof-security'); echo '</span>'; _e('S-Monitor is the centralized Security Monitoring and Alerting Core where you can manage and choose BPS Pro settings for Dashboard Alerts, Dashboard Status Display|Inpage Status Display, Email Alerts, Automated Log file handling, Error checking, etc. Having BPS Pro monitoring, alerting and log file handling options all in one centralized location makes it simple and easy to change all/any BPS Pro settings to your particular preferences.', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('Advanced Real-Time Alerting & Heads Up Dashboard Status Display: ', 'bulletproof-security'); echo '</span>';  _e('BPS Pro checks and displays error, warning, notifications and alert messages in real time. You can choose how you want these messages displayed to you with S-Monitor Monitoring &amp; Alerting Options - Display in your WP Dashboard, BPS Pro pages only, Turned off, Email Alerts, Logging...', 'bulletproof-security'); echo '</p>'; ?>
<img src="<?php echo plugins_url('/bulletproof-security/admin/images/dashboard-status-display.jpg'); ?>" />

<?php echo '<p><span class="blue-bold">'; _e('Custom php.ini|ini_set Options: ', 'bulletproof-security'); echo '</span>'; _e('Quickly create a custom php.ini file for your website or use ini_set Options to increase security and performance with just a few clicks. Additional P-Security Features: All-purpose File Manager, All-purpose File Editor, Protected PHP Error Log, PHP Error Alerts, Secure phpinfo Viewer...', 'bulletproof-security').'</p>'; ?>

<?php echo '<p><span class="blue-bold">'; _e('Pro Tools: 16 mini-plugins: ', 'bulletproof-security'); echo '</span>'; _e('Online Base64 Decoder, Offline Base64 Decode|Encode, Mcrypt ~ Decrypt|Encrypt, Crypt Encryption, Scheduled Crons (display and reschedule/reset Cron Jobs), String|Function Finder (find any string - name of a function, code, text, etc. - in any files anywhere under your hosting account), String Replacer|Remover (search and replace any string/text/code in any files anywhere under your hosting account), DB String Finder (search your entire database for strings/text/code), DB Table Cleaner|Remover (empty/drop DB Tables), DNS Finder (find all DNS Records for websites by Domain Name), Ping Website|Server (check if a website domain is Up/Down/connection/blocking), cURL Scan (scan website Source Code for strings/text/code), Website Headers (check and display Headers using GET or HEAD Requests), WP Automatic Update (Turn WP Automatic Updates On or Off and other settings), Plugin Update Check (force new Plugin update check), XML-RPC Exploit Checker (check your website or a remote website to see if the website is protected against or vulnerable to an XML-RPC exploit).', 'bulletproof-security').'</p>'; ?>
</div>	

    </td>
    <td width="38%" valign="top" class="bps-table_cell_help">

<div id="bpsProVersions">
<div class="pro-links">
<a href="https://forum.ait-pro.com/forums/topic/bulletproof-security-pro-version-release-dates/" target="_blank" title="Link Opens in New Browser Window" style="font-size:22px;"><?php _e('BPS Pro Version Release Dates', 'bulletproof-security'); ?></a><br />
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.7', 'https://www.ait-pro.com/aitpro-blog/5430/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-7/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.6/12.6.1', 'https://www.ait-pro.com/aitpro-blog/5403/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-6/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.5', 'https://www.ait-pro.com/aitpro-blog/5388/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-5/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.4/12.4.1', 'https://www.ait-pro.com/aitpro-blog/5287/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12.3', 'https://www.ait-pro.com/aitpro-blog/5273/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12-3/' ).'<br>'; ?>
</div>
<div id="milestone">5 Year Milestone: 8-1-2016 | First Public Release: 8-1-2011</div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '12/12.1/12.2', 'https://www.ait-pro.com/aitpro-blog/5265/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-12/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.9/11.9.1', 'https://www.ait-pro.com/aitpro-blog/5253/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.8', 'https://www.ait-pro.com/aitpro-blog/5246/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.7/11.7.1', 'https://www.ait-pro.com/aitpro-blog/5237/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.6/11.6.1', 'https://www.ait-pro.com/aitpro-blog/5226/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.5', 'https://www.ait-pro.com/aitpro-blog/5221/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.4', 'https://www.ait-pro.com/aitpro-blog/5211/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.2/11.3', 'https://www.ait-pro.com/aitpro-blog/5201/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11.1', 'https://www.ait-pro.com/aitpro-blog/5195/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '11', 'https://www.ait-pro.com/aitpro-blog/5190/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-11/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.9', 'https://www.ait-pro.com/aitpro-blog/5183/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.8', 'https://www.ait-pro.com/aitpro-blog/5181/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.7', 'https://www.ait-pro.com/aitpro-blog/5177/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-7/' ).'<br>';
?>
</div>
<div id="milestone">4 Year Milestone: 8-1-2015 | First Public Release: 8-1-2011</div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.6', 'https://www.ait-pro.com/aitpro-blog/5169/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-6/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.4/10.5', 'https://www.ait-pro.com/aitpro-blog/5157/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.3', 'https://www.ait-pro.com/aitpro-blog/5150/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.2', 'https://www.ait-pro.com/aitpro-blog/5141/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10.1', 'https://www.ait-pro.com/aitpro-blog/5109/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '10', 'https://www.ait-pro.com/aitpro-blog/5094/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-10/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.9.1', 'https://www.ait-pro.com/aitpro-blog/5087/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-9-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.9', 'https://www.ait-pro.com/aitpro-blog/5080/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.8', 'https://www.ait-pro.com/aitpro-blog/5075/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.7', 'https://www.ait-pro.com/aitpro-blog/5066/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.6', 'https://www.ait-pro.com/aitpro-blog/5062/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.5', 'https://www.ait-pro.com/aitpro-blog/5056/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.3/9.4', 'https://www.ait-pro.com/aitpro-blog/5046/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.2', 'https://www.ait-pro.com/aitpro-blog/5039/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-2/' ).'<br>';
?>
</div>
<div id="milestone">3 Year Milestone: 8-1-2014 | First Public Release: 8-1-2011</div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.1', 'https://www.ait-pro.com/aitpro-blog/5027/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-1/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '9.0', 'https://www.ait-pro.com/aitpro-blog/5009/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-9-0/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.3', 'https://www.ait-pro.com/aitpro-blog/4994/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.2', 'https://www.ait-pro.com/aitpro-blog/4953/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.1', 'https://www.ait-pro.com/aitpro-blog/4940/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '8.0', 'https://www.ait-pro.com/aitpro-blog/4926/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-8-0/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.9', 'https://www.ait-pro.com/aitpro-blog/4916/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.8', 'https://www.ait-pro.com/aitpro-blog/4905/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.7', 'https://www.ait-pro.com/aitpro-blog/4900/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.6', 'https://www.ait-pro.com/aitpro-blog/4895/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.5', 'https://www.ait-pro.com/aitpro-blog/4889/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '7.0', 'https://www.ait-pro.com/aitpro-blog/4876/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-7-0/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '6.5', 'https://www.ait-pro.com/aitpro-blog/4845/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-6-5/' ).'<br>';
?>
</div>
<div id="milestone">2 Year Milestone: 8-1-2013 | First Public Release: 8-1-2011</div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '6.0', 'https://www.ait-pro.com/aitpro-blog/4827/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-6-0/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.9', 'https://www.ait-pro.com/aitpro-blog/4811/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-9/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.8/5.8.1/5.8.2', 'https://www.ait-pro.com/aitpro-blog/4780/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-8/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.7/5.7.1/5.7.2', 'https://www.ait-pro.com/aitpro-blog/4744/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.6/5.6.1', 'https://www.ait-pro.com/aitpro-blog/4709/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.5', 'https://www.ait-pro.com/aitpro-blog/4683/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.4/5.4.1', 'https://www.ait-pro.com/aitpro-blog/4653/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.3/5.3.1/5.3.2/5.3.3', 'https://www.ait-pro.com/aitpro-blog/4628/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-3/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.2/5.2.1/5.2.2', 'https://www.ait-pro.com/aitpro-blog/4563/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.9', 'https://www.ait-pro.com/aitpro-blog/4442/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-9/' ).'<br>';
?>
</div>
<div id="milestone">1 Year Milestone: 8-1-2012 | First Public Release: 8-1-2011</div>
<div class="pro-links">
<?php 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.8/5.1.8.1/5.1.8.2/5.1.8.3/5.1.8.4', 'https://www.ait-pro.com/aitpro-blog/4197/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-8/' ).'<br>'; 
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.7', 'https://www.ait-pro.com/aitpro-blog/4144/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-7/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.6', 'https://www.ait-pro.com/aitpro-blog/4029/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-6/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.5', 'https://www.ait-pro.com/aitpro-blog/3845/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-5/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.4', 'https://www.ait-pro.com/aitpro-blog/3732/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-4/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.3', 'https://www.ait-pro.com/aitpro-blog/3605/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-3' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.2', 'https://www.ait-pro.com/aitpro-blog/3529/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-2/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1.1', 'https://www.ait-pro.com/aitpro-blog/3510/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.1', 'https://www.ait-pro.com/aitpro-blog/3510/bulletproof-security-pro/whats-new-in-bulletproof-security-pro-5-1-1/' ).'<br>';
echo sprintf( __( '<a href="%2$s" target="_blank" title="Link Opens in New Browser Window">Whats New in BPS Pro %1$s</a>' ), '5.0', 'https://www.ait-pro.com/aitpro-blog/2835/bulletproof-security-pro/bulletproof-security-pro-features/' ).'<br>';
?>
</div>
<div id="milestone">BPS Pro 1.0 - 4.0 | 1-1-2011 - 8-1-2011 | Private Use|Development</div>
</div>  
    
    </td>
  </tr>
   <tr>
    <td class="bps-table_cell_help">&nbsp;</td>
    <td class="bps-table_cell_help">&nbsp;</td>
  </tr>
   <tr>
    <td colspan="2" class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>
</div>
</div>

<div id="bps-tabs-12">
<h2><?php _e('Help & FAQ', 'bulletproof-security'); ?></h2>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="bps-help_faq_table">
   <tr>
    <td colspan="2" class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    <td width="50%" class="bps-table_cell_help_links"><a href="https://www.ait-pro.com/aitpro-blog/category/bulletproof-security-contributors/" target="_blank"><?php _e('Contributors Page', 'bulletproof-security'); ?></a></td>
    <td width="50%" class="bps-table_cell_help_links"><a href="https://www.ait-pro.com/aitpro-blog/2304/wordpress-tips-tricks-fixes/permalinks-wordpress-custom-permalinks-wordpress-best-wordpress-permalinks-structure/" target="_blank"><?php _e('WP Permalinks - Custom Permalink Structure Help Info', 'bulletproof-security'); ?></a></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links"><a href="https://forum.ait-pro.com/forums/topic/security-log-event-codes/" target="_blank"><?php _e('Security Log Event Codes', 'bulletproof-security'); ?></a></td>
    <td class="bps-table_cell_help_links"><a href="https://www.ait-pro.com/aitpro-blog/2239/bulletproof-security-plugin-support/adding-a-custom-403-forbidden-page-htaccess-403-errordocument-directive-examples/" target="_blank"><?php _e('Adding a Custom 403 Forbidden Page For Your Website', 'bulletproof-security'); ?></a></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links"><a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" target="_blank"><?php _e('Forum: Search, Troubleshooting Steps & Post Questions For Assistance', 'bulletproof-security'); ?></a></td>
    <td class="bps-table_cell_help_links"><a href="https://forum.ait-pro.com/video-tutorials/" target="_blank"><?php _e('Custom Code Video Tutorial', 'bulletproof-security'); ?></a></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_links">&nbsp;</td>
    <td class="bps-table_cell_help_links">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>
</div>
       
<div id="AITpro-link">BulletProof Security <?php echo BULLETPROOF_VERSION; ?> Plugin by <a href="https://www.ait-pro.com/" target="_blank" title="AITpro Website Security">AITpro Website Security</a>
</div>
</div>
</div>