<?php
// Direct calls to this file are Forbidden when core files are not present 
if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}
	
$scrolltoCCode = isset( $_REQUEST['scrolltoCCode'] ) ? (int) $_REQUEST['scrolltoCCode'] : 0; 
$scrolltoCCodeWPA = isset( $_REQUEST['scrolltoCCodeWPA'] ) ? (int) $_REQUEST['scrolltoCCodeWPA'] : 0; 

// Custom Code Check BPS Query String DB option for invalid code
// .51.8: added check for Default WP Rewrite htaccess code
function bps_CustomCode_BPSQSE_check() {
global $bps_topDiv, $bps_bottomDiv;

$options = get_option('bulletproof_security_options_customcode');	
$pattern = '/RewriteCond\s%{REQUEST_FILENAME}\s!-f\s*RewriteCond\s%{REQUEST_FILENAME}\s!-d\s*RewriteRule\s\.(.*)\/index\.php\s\[L\]/';

	if ( preg_match( $pattern, htmlspecialchars_decode( $options['bps_customcode_bpsqse'], ENT_QUOTES ), $matches ) ) {
 		
		echo $bps_topDiv;
		$text = '<strong><font color="#fb0101">'.__('The BPS Query String Exploits Custom Code below is NOT valid.', 'bulletproof-security').'</font><br>'.__('Delete the code shown below from the CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS: text box and click the Save Root Custom Code button.', 'bulletproof-security').'</strong><br>';
 		echo $text;
		echo '<pre>';
 		print_r(htmlspecialchars($matches[0]));
 		echo '</pre>';
		echo $bps_bottomDiv;
	}

$pattern2 = '/#\sBEGIN\sWordPress\s*<IfModule\smod_rewrite\.c>\s*RewriteEngine\sOn\s*RewriteBase(.*)\s*RewriteRule(.*)\s*RewriteCond((.*)\s*){2}RewriteRule(.*)\s*<\/IfModule>\s*#\sEND\sWordPress/';

/*
Check these Custom Code DB option values:
CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE: bps_customcode_one
CUSTOM CODE WP REWRITE LOOP START: bps_customcode_wp_rewrite_start
CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS: bps_customcode_bpsqse
CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE: bps_customcode_three
*/

	if ( preg_match( $pattern2, htmlspecialchars_decode( $options['bps_customcode_one'], ENT_QUOTES ), $matches ) || preg_match( $pattern2, htmlspecialchars_decode( $options['bps_customcode_wp_rewrite_start'], ENT_QUOTES ), $matches ) || preg_match( $pattern2, htmlspecialchars_decode( $options['bps_customcode_bpsqse'], ENT_QUOTES ), $matches ) || preg_match( $pattern2, htmlspecialchars_decode( $options['bps_customcode_three'], ENT_QUOTES ), $matches ) ) {
 		
		echo $bps_topDiv;
		$text = '<strong><font color="#fb0101">'.__('Default WordPress Rewrite htaccess code has been added to BPS Custom Code.', 'bulletproof-security').'</font><br>'.__('The BPS plugin already uses/has Default WordPress Rewrite code. Delete the Default WordPress Rewrite htaccess code shown below from the CUSTOM CODE text box were it was added and click the Save Root Custom Code button.', 'bulletproof-security').'</strong><br>';
 		echo $text;
		echo '<pre>';
 		print_r(htmlspecialchars($matches[0]));
 		echo '</pre>';
		echo $bps_bottomDiv;
	}
}

bps_CustomCode_BPSQSE_check();

// Root Custom Code Form
function bpsPro_CC_Root_values_form() {
global $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['bps_customcode_submit'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_CC_Root' );
		
	if ( ! is_multisite() ) {

		$Root_CC_Options = array(
		'bps_customcode_one' 				=> stripslashes($_POST['bps_customcode_one']), 
		'bps_customcode_server_signature' 	=> stripslashes($_POST['bps_customcode_server_signature']), 
		'bps_customcode_directory_index' 	=> stripslashes($_POST['bps_customcode_directory_index']), 
		'bps_customcode_server_protocol' 	=> stripslashes($_POST['bps_customcode_server_protocol']), 
		'bps_customcode_error_logging' 		=> stripslashes($_POST['bps_customcode_error_logging']), 
		'bps_customcode_deny_dot_folders' 	=> stripslashes($_POST['bps_customcode_deny_dot_folders']), 
		'bps_customcode_admin_includes' 	=> stripslashes($_POST['bps_customcode_admin_includes']), 
		'bps_customcode_wp_rewrite_start' 	=> stripslashes($_POST['bps_customcode_wp_rewrite_start']), 
		'bps_customcode_request_methods' 	=> stripslashes($_POST['bps_customcode_request_methods']), 
		'bps_customcode_two' 				=> stripslashes($_POST['bps_customcode_two']), 
		'bps_customcode_timthumb_misc' 		=> stripslashes($_POST['bps_customcode_timthumb_misc']), 
		'bps_customcode_bpsqse' 			=> stripslashes($_POST['bps_customcode_bpsqse']), 
		'bps_customcode_deny_files' 		=> stripslashes($_POST['bps_customcode_deny_files']), 
		'bps_customcode_three' 				=> stripslashes($_POST['bps_customcode_three']) 
		);
				
	} else {
					
		$Root_CC_Options = array(
		'bps_customcode_one' 				=> stripslashes($_POST['bps_customcode_one']), 
		'bps_customcode_server_signature' 	=> stripslashes($_POST['bps_customcode_server_signature']), 
		'bps_customcode_directory_index' 	=> stripslashes($_POST['bps_customcode_directory_index']), 
		'bps_customcode_server_protocol' 	=> stripslashes($_POST['bps_customcode_server_protocol']), 
		'bps_customcode_error_logging' 		=> stripslashes($_POST['bps_customcode_error_logging']), 
		'bps_customcode_deny_dot_folders' 	=> stripslashes($_POST['bps_customcode_deny_dot_folders']), 
		'bps_customcode_admin_includes' 	=> stripslashes($_POST['bps_customcode_admin_includes']), 
		'bps_customcode_wp_rewrite_start' 	=> stripslashes($_POST['bps_customcode_wp_rewrite_start']), 
		'bps_customcode_request_methods' 	=> stripslashes($_POST['bps_customcode_request_methods']), 
		'bps_customcode_two' 				=> stripslashes($_POST['bps_customcode_two']), 
		'bps_customcode_timthumb_misc' 		=> stripslashes($_POST['bps_customcode_timthumb_misc']), 
		'bps_customcode_bpsqse' 			=> stripslashes($_POST['bps_customcode_bpsqse']), 
		'bps_customcode_wp_rewrite_end' 	=> stripslashes($_POST['bps_customcode_wp_rewrite_end']), 
		'bps_customcode_deny_files' 		=> stripslashes($_POST['bps_customcode_deny_files']), 
		'bps_customcode_three' 				=> stripslashes($_POST['bps_customcode_three']) 
		);					
	}

		foreach( $Root_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode', $Root_CC_Options);
		}		
	
	echo $bps_topDiv;
	$text = '<strong><font color="green">'.__('Root Custom Code saved successfully! Go to the Security Modes tab page and click the Root Folder BulletProof Mode Activate button to add/create your new Custom Code in your Root htaccess file.', 'bulletproof-security').'</font></strong>';
	echo $text;		
	echo $bps_bottomDiv;	
	
	}
}

// wp-admin Custom Code Form
function bpsPro_CC_WPA_values_form() {
global $bps_topDiv, $bps_bottomDiv;

	if ( isset( $_POST['bps_customcode_submit_wpa'] ) && current_user_can('manage_options') ) {
		check_admin_referer( 'bulletproof_security_CC_WPA' );
		
		$wpadmin_CC_Options = array(
		'bps_customcode_deny_files_wpa' => stripslashes($_POST['bps_customcode_deny_files_wpa']), 
		'bps_customcode_one_wpa' 		=> stripslashes($_POST['bps_customcode_one_wpa']), 
		'bps_customcode_two_wpa' 		=> stripslashes($_POST['bps_customcode_two_wpa']), 
		'bps_customcode_bpsqse_wpa' 	=> stripslashes($_POST['bps_customcode_bpsqse_wpa']) 
		);

		foreach( $wpadmin_CC_Options as $key => $value ) {
			update_option('bulletproof_security_options_customcode_WPA', $wpadmin_CC_Options);
		}		
	
	echo $bps_topDiv;
	$text = '<strong><font color="green">'.__('wp-admin Custom Code saved successfully! Go to the Security Modes tab page and click wp-admin Folder BulletProof Mode Activate button to add/create your new Custom Code in your wp-admin htaccess file.', 'bulletproof-security').'</font></strong>';
	echo $text;		
	echo $bps_bottomDiv;	
	
	}
}

	$Apache_Mod_options = get_option('bulletproof_security_options_apache_modules');
?>
        
<div id="bps-accordion-2" class="bps-accordion-main-2">
    <h3><?php _e('Root htaccess File Custom Code', 'bulletproof-security'); ?></h3>
<div id="cc-accordion-inner">

<table width="100%" border="0" cellspacing="0" cellpadding="10" class="bps-help_faq_table">
  <tr>
    <td colspan="2" class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    
    <td class="bps-table_cell_help_custom_code">
    
<form name="bpsCustomCodeForm" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
<?php  
	wp_nonce_field('bulletproof_security_CC_Root'); 
	bpsPro_CC_Root_values_form();
	$CC_Options_root = get_option('bulletproof_security_options_customcode'); 
?>    

    <strong><label for="bps-CCode"><?php echo number_format_i18n( 1 ).'. '; _e('CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE:<br>Add php/php.ini handler code, cache code and/or <a href="https://forum.ait-pro.com/forums/topic/htaccess-caching-code-speed-boost-cache-code/" title="Link opens in a new Browser window" target="_blank">Speed Boost Cache Code</a>', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid php/php.ini handler htaccess code and/or cache htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_one" tabindex="1"><?php echo $CC_Options_root['bps_customcode_one']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your php/php.ini handler and/or cache htaccess code will be created in your root htaccess file. If you have php/php.ini handler and/or cache htaccess code, copy and paste it into the CUSTOM CODE TOP PHP/PHP.INI HANDLER/CACHE CODE text box to the left.</span><pre># PHP/PHP.INI HANDLER/CACHE CODE<br /># Use BPS Custom Code to add php/php.ini Handler and Cache htaccess code and to save it permanently.<br /># Most Hosts do not have/use/require php/php.ini Handler htaccess code</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 2 ).'. '; _e('CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire TURN OFF YOUR SERVER SIGNATURE section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_server_signature" tabindex="2"><?php echo @$CC_Options_root['bps_customcode_server_signature']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE text box. Go to the htaccess File Editor tab page and copy your actual TURN OFF YOUR SERVER SIGNATURE root htaccess file code and paste it into the CUSTOM CODE TURN OFF YOUR SERVER SIGNATURE text box to the left.</span><pre># TURN OFF YOUR SERVER SIGNATURE<br /># Suppresses the footer line server version number and ServerName of the serving virtual host<br />ServerSignature Off</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 3 ).'. '; _e('CUSTOM CODE DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_directory_index" tabindex="3"><?php echo $CC_Options_root['bps_customcode_directory_index']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX text box. Go to the htaccess File Editor tab page and copy your actual DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX root htaccess file code and paste it into the CUSTOM CODE DO NOT SHOW DIRECTORY LISTING/DIRECTORY INDEX text box to the left.</span><pre style="max-height:130px;"># DO NOT SHOW DIRECTORY LISTING<br /># Disallow mod_autoindex from displaying a directory listing<br /># If a 500 Internal Server Error occurs when activating Root BulletProof Mode<br /># copy the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code<br /># and paste it into BPS Custom Code and comment out Options -Indexes<br /># by adding a # sign in front of it.<br /># Example: #Options -Indexes<br />Options -Indexes<br /><br /># DIRECTORY INDEX FORCE INDEX.PHP<br /># Use index.php as default directory index file. index.html will be ignored.<br /># If a 500 Internal Server Error occurs when activating Root BulletProof Mode<br /># copy the entire DO NOT SHOW DIRECTORY LISTING and DIRECTORY INDEX sections of code<br /># and paste it into BPS Custom Code and comment out DirectoryIndex<br /># by adding a # sign in front of it.<br /># Example: #DirectoryIndex index.php index.html /index.php<br />DirectoryIndex index.php index.html /index.php</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 4 ).'. '; _e('CUSTOM CODE BRUTE FORCE LOGIN PAGE PROTECTION:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('This Custom Code text box is for optional/Bonus code. To get this code click the link below:', 'bulletproof-security').'<br><a href="https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/" title="Link opens in a new Browser window" target="_blank">Brute Force Login Page Protection Code</a></font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_server_protocol" tabindex="4"><?php echo $CC_Options_root['bps_customcode_server_protocol']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your Brute Force Login Page Protection code will be created in your root htaccess file if you decide to add the option/Bonus code. You can get the code by clicking the Brute Force Login Page Protection Code link. Copy and paste it into the CUSTOM CODE BRUTE FORCE LOGIN PAGE PROTECTION text box to the left.</span><pre># BRUTE FORCE LOGIN PAGE PROTECTION<br /># PLACEHOLDER ONLY<br /># Use BPS Custom Code to add Brute Force Login protection code and to save it permanently.<br /># See this link: https://forum.ait-pro.com/forums/topic/protect-login-page-from-brute-force-login-attacks/<br /># for more information.</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 5 ).'. '; _e('CUSTOM CODE ERROR LOGGING AND TRACKING:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire ERROR LOGGING AND TRACKING section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_error_logging" tabindex="5"><?php echo $CC_Options_root['bps_customcode_error_logging']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE ERROR LOGGING AND TRACKING text box. Go to the htaccess File Editor tab page and copy your actual ERROR LOGGING AND TRACKING root htaccess file code and paste it into the CUSTOM CODE ERROR LOGGING AND TRACKING text box to the left.</span><pre style="max-height:145px;"># BPS PRO ERROR LOGGING AND TRACKING<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># BPS Pro has premade 400 Bad Request, 403 Forbidden, 404 Not Found, 405 Method Not Allowed and<br /># 410 Gone template logging files that are used to track and log 400, 403, 404, 405 and 410 errors<br />.....<br />.....<br />ErrorDocument 400 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/400.php<br />ErrorDocument 401 default<br />ErrorDocument 403 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/403.php<br />ErrorDocument 404 /404.php<br />ErrorDocument 405 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/405.php<br />ErrorDocument 410 <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/410.php</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 6 ).'. '; _e('CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS:', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_deny_dot_folders" tabindex="6"><?php echo @$CC_Options_root['bps_customcode_deny_dot_folders']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS text box. Go to the htaccess File Editor tab page and copy your actual DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS root htaccess file code and paste it into the CUSTOM CODE DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS text box to the left.</span><pre># DENY ACCESS TO PROTECTED SERVER FILES AND FOLDERS<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># Files and folders starting with a dot: .htaccess, .htpasswd, .errordocs, .logs<br />RedirectMatch 403 \.(htaccess|htpasswd|errordocs|logs)$</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 7 ).'. '; _e('CUSTOM CODE WP-ADMIN/INCLUDES: DO NOT add wp-admin .htaccess code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('Add one pound sign # below to prevent the WP-ADMIN/INCLUDES section of code from being created in your root .htaccess file', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_admin_includes" tabindex="7"><?php echo $CC_Options_root['bps_customcode_admin_includes']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE WP-ADMIN/INCLUDES text box. Go to the htaccess File Editor tab page and copy your actual WP-ADMIN/INCLUDES root htaccess file code and paste it into the CUSTOM CODE WP-ADMIN/INCLUDES text box to the left.</span><pre># WP-ADMIN/INCLUDES<br /># Use BPS Custom Code to remove this code permanently.<br />RewriteEngine On<br />RewriteBase /<br />RewriteRule ^wp-admin/includes/ - [F]<br />RewriteRule !^wp-includes/ - [S=3]<br />RewriteRule ^wp-includes/[^/]+\.php$ - [F]<br />RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F]<br />RewriteRule ^wp-includes/theme-compat/ - [F]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 8 ).'. '; _e('CUSTOM CODE WP REWRITE LOOP START: www/non-www http/https Rewrite code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire WP REWRITE LOOP START section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_wp_rewrite_start" tabindex="8"><?php echo $CC_Options_root['bps_customcode_wp_rewrite_start']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE WP REWRITE LOOP START text box. Go to the htaccess File Editor tab page and copy your actual WP REWRITE LOOP START root htaccess file code and paste it into the CUSTOM CODE WP REWRITE LOOP START text box to the left.</span><br /><pre># CUSTOM CODE WP REWRITE LOOP START<br /># WP REWRITE LOOP START<br />RewriteEngine On<br />RewriteBase /<br />RewriteRule ^index\.php$ - [L]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
     <strong><label for="bps-CCode">
	<?php echo number_format_i18n( 9 ).'. '; _e('CUSTOM CODE REQUEST METHODS FILTERED:', 'bulletproof-security'); ?><br />
	<?php _e('Whitelist User Agents and allow HEAD Requests', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire REQUEST METHODS FILTERED section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes. To Allow HEAD Requests, comment out these 2 lines of code with # signs: ', 'bulletproof-security').'#RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC] and #RewriteRule ^(.*)$ - [R=405,L].</font>'; echo $text ; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_request_methods" tabindex="9"><?php echo $CC_Options_root['bps_customcode_request_methods']; ?></textarea>   
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;">
    
<?php if ( preg_match( '/R=405/', $CC_Options_root['bps_customcode_request_methods'] ) ) { ?>

<span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE REQUEST METHODS FILTERED text box. Go to the htaccess File Editor tab page and copy your actual REQUEST METHODS FILTERED root htaccess file code and paste it into the CUSTOM CODE REQUEST METHODS FILTERED text box to the left.</span><pre># REQUEST METHODS FILTERED<br /># If you want to allow HEAD Requests use BPS Custom Code and copy<br /># this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code<br /># text box: CUSTOM CODE REQUEST METHODS FILTERED.<br /># See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.<br />RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]<br />RewriteRule ^(.*)$ - [F]<br />RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]<br />RewriteRule ^(.*)$ - [R=405,L]</pre>

<?php } else { ?>   

    <span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE REQUEST METHODS FILTERED text box. Go to the htaccess File Editor tab page and copy your actual REQUEST METHODS FILTERED root htaccess file code and paste it into the CUSTOM CODE REQUEST METHODS FILTERED text box to the left.</span><pre># REQUEST METHODS FILTERED<br /># If you want to allow HEAD Requests use BPS Custom Code and copy<br /># this entire REQUEST METHODS FILTERED section of code to this BPS Custom Code<br /># text box: CUSTOM CODE REQUEST METHODS FILTERED.<br /># See the CUSTOM CODE REQUEST METHODS FILTERED help text for additional steps.<br />RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK|DEBUG) [NC]<br />RewriteRule ^(.*)$ - [F]<br />RewriteCond %{REQUEST_METHOD} ^(HEAD) [NC]<br />RewriteRule ^(.*)$ <?php echo '/'.$bps_plugin_dir; ?>/bulletproof-security/405.php [L]</pre>

<?php } ?>
   
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 10 ).'. '; _e('CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES:<br>Add personal plugin/theme skip/bypass rules here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_two" tabindex="10"><?php echo $CC_Options_root['bps_customcode_two']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your plugin/theme skip/bypass rules code will be created in your root htaccess file. If you have plugin/theme skip/bypass rules, copy and paste it into the CUSTOM CODE PLUGIN/THEME SKIP/BYPASS RULES text box to the left. Click the Read Me help button for more information about plugin/theme skip/bypass rules code.</span><pre style="max-height:145px;"># PLUGINS/THEMES AND VARIOUS EXPLOIT FILTER SKIP RULES<br /># To add plugin/theme skip/bypass rules use BPS Custom Code.<br /># The [S] flag is used to skip following rules. Skip rule [S=12] will skip 12 following RewriteRules.<br /># The skip rules MUST be in descending consecutive number order: 12, 11, 10, 9...<br /># If you delete a skip rule, change the other skip rule numbers accordingly.<br /># Examples: If RewriteRule [S=5] is deleted than change [S=6] to [S=5], [S=7] to [S=6], etc.<br /># If you add a new skip rule above skip rule 12 it will be skip rule 13: [S=13]<br /><br /><div style="background-color:#FFFF00;padding:3px;">Your plugin/theme skip/bypass rules will be created here in your root htaccess file</div><br /># Adminer MySQL management tool data populate<br />RewriteCond %{REQUEST_URI} ^/<?php echo $bps_plugin_dir; ?>/adminer/ [NC]<br />RewriteRule . - [S=12]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 11 ).'. '; _e('CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE:', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire TIMTHUMB FORBID RFI section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_timthumb_misc" tabindex="11"><?php echo $CC_Options_root['bps_customcode_timthumb_misc']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE text box. Go to the htaccess File Editor tab page and copy your actual TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE root htaccess file code and paste it into the CUSTOM CODE text box to the left.</span><pre style="max-height:145px;"># TIMTHUMB FORBID RFI and MISC FILE SKIP/BYPASS RULE<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># Remote File Inclusion (RFI) security rules<br />.....<br />.....<br /># Example: Whitelist additional misc files: (example\.php|another-file\.php|phpthumb\.php|thumb\.php|thumbs\.php)<br />RewriteCond %{REQUEST_URI} (timthumb\.php|phpthumb\.php|thumb\.php|thumbs\.php) [NC]<br /># Example: Whitelist additional website domains: RewriteCond %{HTTP_REFERER} ^.*(YourWebsite.com|AnotherWebsite.com).*<br />RewriteCond %{HTTP_REFERER} ^.*<?php echo $bps_get_domain_root; ?>.*<br />RewriteRule . - [S=1]</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 12 ).'. '; _e('CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS:', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire BPSQSE QUERY STRING EXPLOITS section of code from your root .htaccess file from # BEGIN BPSQSE BPS QUERY STRING EXPLOITS to # END BPSQSE BPS QUERY STRING EXPLOITS into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_bpsqse" tabindex="12"><?php echo $CC_Options_root['bps_customcode_bpsqse']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:90px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS text box. Go to the htaccess File Editor tab page and copy your actual BPSQSE BPS QUERY STRING EXPLOITS root htaccess file code and paste it into the CUSTOM CODE BPSQSE BPS QUERY STRING EXPLOITS text box to the left.</span><pre># BEGIN BPSQSE BPS QUERY STRING EXPLOITS<br /># The libwww-perl User Agent is forbidden - Many bad bots use libwww-perl modules, but some good bots use it too.<br /># Good sites such as W3C use it for their W3C-LinkChecker.<br /># Use BPS Custom Code to add or remove user agents temporarily or permanently from the<br />.....<br />.....<br />RewriteCond %{QUERY_STRING} (sp_executesql) [NC]<br />RewriteRule ^(.*)$ - [F]<br /># END BPSQSE BPS QUERY STRING EXPLOITS</pre></td>
  </tr>

<?php if ( is_multisite() ) { ?>

  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 12 ).'. '; _e('CUSTOM CODE WP REWRITE LOOP END: Add WP Rewrite Loop End code here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('This is a Special Custom Code text box that should only be used if the correct WP REWRITE LOOP END code is not being created in your root .htaccess file. See the Read Me help button for more information.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_wp_rewrite_end" tabindex="13"><?php echo $CC_Options_root['bps_customcode_wp_rewrite_end']; ?></textarea>

</td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: The actual WP REWRITE LOOP END code for your website may be different. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE WP REWRITE LOOP END text box. Go to the htaccess File Editor tab page and copy your actual WP REWRITE LOOP END root htaccess file code and paste it into the CUSTOM CODE WP REWRITE LOOP END text box to the left.</span><br /><pre># END BPSQSE BPS QUERY STRING EXPLOITS<br /><div style="background-color:#FFFF00;padding:3px;">RewriteCond %{REQUEST_FILENAME} -f [OR]<br />RewriteCond %{REQUEST_FILENAME} -d<br />RewriteRule ^ - [L]<br />RewriteRule ^[_0-9a-zA-Z-]+/(wp-(content|admin|includes).*) $1 [L]<br />RewriteRule ^[_0-9a-zA-Z-]+/(.*\.php)$ $1 [L]<br />RewriteRule . index.php [L]<br /># WP REWRITE LOOP END</div></pre>
	</td>
  </tr>

<?php } ?>

  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 13 ).'. '; _e('CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES:', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire DENY BROWSER ACCESS section of code from your root .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_deny_files" tabindex="14"><?php echo $CC_Options_root['bps_customcode_deny_files']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:75px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you which root htaccess file code goes in the CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES text box. Go to the htaccess File Editor tab page and copy your actual DENY BROWSER ACCESS TO THESE FILES root htaccess file code and paste it into the CUSTOM CODE DENY BROWSER ACCESS TO THESE FILES text box to the left.</span>
    
<?php if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) { ?>   
    
<pre style="max-height:145px;"># DENY BROWSER ACCESS TO THESE FILES<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># wp-config.php, bb-config.php, php.ini, php5.ini, readme.html<br /># To be able to view these files from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Require all denied and Uncomment: Require ip 127.0.0.1<br /># Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br />&lt;FilesMatch &quot;^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)&quot;&gt;<br />&lt;IfModule mod_authz_core.c&gt;<br />Require all denied<br />#Require ip 127.0.0.1<br />&lt;/IfModule&gt;<br /><br />&lt;IfModule !mod_authz_core.c&gt;<br />&lt;IfModule mod_access_compat.c&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/IfModule&gt;<br />&lt;/IfModule&gt;<br />&lt;/FilesMatch&gt;</pre>
    
<?php } elseif ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' ) { ?>

<pre style="max-height:145px;"># DENY BROWSER ACCESS TO THESE FILES<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># wp-config.php, bb-config.php, php.ini, php5.ini, readme.html<br /># To be able to view these files from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br />&lt;FilesMatch &quot;^(wp-config\.php|php\.ini|php5\.ini|readme\.html|bb-config\.php)&quot;&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/FilesMatch&gt;</pre>

<?php } ?>  
    
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 14 ).'. '; _e('CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE: Add miscellaneous code here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_three" tabindex="15"><?php echo $CC_Options_root['bps_customcode_three']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for Custom Code Setup Steps. This example code is a visual reference to show you where your custom htaccess code will be created in your root htaccess file. If you have Hotlinking, Redirect, IP Blocking htaccess code then copy and paste it into the CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE text box to the left.</span><pre># CUSTOM CODE BOTTOM HOTLINKING/FORBID COMMENT SPAMMERS/BLOCK BOTS/BLOCK IP/REDIRECT CODE<br /># PLACEHOLDER ONLY<br /># Use BPS Custom Code to add custom code and save it permanently here.</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">

    <input type="hidden" name="scrolltoCCode" value="<?php echo esc_html( $scrolltoCCode ); ?>" />
	<input type="submit" name="bps_customcode_submit" value="<?php esc_attr_e('Save Root Custom Code', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to save your Root Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
    </form>
    
    </td>
    <td class="bps-table_cell_help_custom_code">&nbsp;</td>
    </tr>
  <tr>
    <td class="bps-table_cell_help">&nbsp;</td>
    <td class="bps-table_cell_help">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#bpsCustomCodeForm').submit(function(){ $('#scrolltoCCode').val( $('#bps_customcode_one').scrollTop() ); });
	$('#bps_customcode_one').scrollTop( $('#scrolltoCCode').val() ); 
});
/* ]]> */
</script>

</div>
	
<?php 
	$BPS_wpadmin_Options = get_option('bulletproof_security_options_htaccess_res');
	
	if ( $BPS_wpadmin_Options['bps_wpadmin_restriction'] == 'disabled' ) {

	} else {
?>
    <h3><?php _e('wp-admin htaccess File Custom Code', 'bulletproof-security'); ?></h3>
<div id="cc-accordion-inner">

<table width="100%" border="0" cellspacing="0" cellpadding="10" class="bps-help_faq_table">
  <tr>
    <td colspan="2" class="bps-table_title">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    
<form name="bpsCustomCodeFormWPA" action="<?php echo admin_url( 'admin.php?page=bulletproof-security/admin/core/core.php#bps-tabs-7' ); ?>" method="post">
<?php 
	wp_nonce_field('bulletproof_security_CC_WPA'); 
	bpsPro_CC_WPA_values_form();
	$CC_Options_wpadmin = get_option('bulletproof_security_options_customcode_WPA'); 
?>

<strong><label for="bps-CCode"><?php echo number_format_i18n( 1 ).'. '; _e('CUSTOM CODE WPADMIN DENY BROWSER ACCESS TO FILES:<br>Add additional wp-admin files that you would like to block here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire WPADMIN DENY BROWSER ACCESS TO FILES section of code from your wp-admin .htaccess file into this text box first. You can then edit and modify the code in this text window and save your changes. Add one pound sign # below to prevent the WPADMIN DENY BROWSER ACCESS TO FILES section of code from being created in your wp-admin .htaccess file', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_deny_files_wpa" tabindex="1"><?php echo $CC_Options_wpadmin['bps_customcode_deny_files_wpa']; ?></textarea>    
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:105px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you which wp-admin htaccess file code goes in the CUSTOM CODE WPADMIN DENY BROWSER ACCESS TO FILES text box. Go to the htaccess File Editor tab page and copy your actual WPADMIN DENY BROWSER ACCESS TO FILES wp-admin htaccess file code and paste it into the CUSTOM CODE text box to the left.</span>
    
<?php if ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'Yes' ) { ?>   
    
<pre style="max-height:145px;"># WPADMIN DENY BROWSER ACCESS TO FILES<br /># Deny Browser access to /wp-admin/install.php<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># To be able to view the install.php file from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Require all denied and Uncomment: Require ip 127.0.0.1<br /># Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br /># BEGIN BPS WPADMIN DENY ACCESS TO FILES<br />&lt;FilesMatch &quot;^(install\.php)&quot;&gt;<br />&lt;IfModule mod_authz_core.c&gt;<br />Require all denied<br />#Require ip 127.0.0.1<br />&lt;/IfModule&gt;<br />&lt;IfModule !mod_authz_core.c&gt;<br />&lt;IfModule mod_access_compat.c&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/IfModule&gt;<br />&lt;/IfModule&gt;<br />&lt;/FilesMatch&gt;<br /># END BPS WPADMIN DENY ACCESS TO FILES</pre>
    
<?php } elseif ( $Apache_Mod_options['bps_apache_mod_ifmodule'] == 'No' ) { ?>

<pre style="max-height:145px;"># WPADMIN DENY BROWSER ACCESS TO FILES<br /># Deny Browser access to /wp-admin/install.php<br /># Use BPS Custom Code to modify/edit/change this code and to save it permanently.<br /># To be able to view the install.php file from a Browser, replace 127.0.0.1 with your actual<br /># current IP address. Comment out: #Deny from all and Uncomment: Allow from 127.0.0.1<br /># Note: The BPS System Info page displays which modules are loaded on your server.<br /><br /># BEGIN BPS WPADMIN DENY ACCESS TO FILES
&lt;FilesMatch &quot;^(install\.php)&quot;&gt;<br />Order Allow,Deny<br />Deny from all<br />#Allow from 127.0.0.1<br />&lt;/FilesMatch&gt;<br /># END BPS WPADMIN DENY ACCESS TO FILES</pre>

<?php } ?> 
    
    </td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 2 ).'. '; _e('CUSTOM CODE WPADMIN TOP:<br>wp-admin password protection & miscellaneous custom code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_one_wpa" tabindex="2"><?php echo $CC_Options_wpadmin['bps_customcode_one_wpa']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you where your wp-admin custom htaccess code will be created in your wp-admin htaccess file. If you have custom wp-admin htaccess code, copy and paste it into the CUSTOM CODE WPADMIN TOP text box to the left.</span><pre># BEGIN OPTIONAL WP-ADMIN ADDITIONAL SECURITY MEASURES:<br /><br /># BEGIN CUSTOM CODE WPADMIN TOP<br /># Use BPS wp-admin Custom Code to modify/edit/change this code and to save it permanently.<br /><div style="background-color:#FFFF00;padding:3px;"># CCWTOP - Your custom code will be created here when you activate wp-admin BulletProof Mode</div># END CUSTOM CODE WPADMIN TOP</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 3 ).'. '; _e('CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES:<br>Add wp-admin plugin/file skip rules code here', 'bulletproof-security'); ?> </label></strong><br />
 <strong><?php $text = '<font color="#2ea2cc">'.__('ONLY add valid htaccess code below or text commented out with a pound sign #', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_two_wpa" tabindex="3"><?php echo $CC_Options_wpadmin['bps_customcode_two_wpa']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:60px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you where your wp-admin plugin/file skip rules code will be created in your wp-admin htaccess file. If you have wp-admin plugin/file skip rules code, copy and paste it into the CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES text box to the left.</span><pre># BEGIN CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES<br /># To add wp-admin plugin skip/bypass rules use BPS wp-admin Custom Code.<br /># If a plugin is calling a wp-admin file in a way that it is being blocked/forbidden<br />...<br />...<br /><div style="background-color:#FFFF00;padding:3px;"># CCWPF - Your custom code will be created here when you activate wp-admin BulletProof Mode</div># END CUSTOM CODE WPADMIN PLUGIN/FILE SKIP RULES</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    <strong><label for="bps-CCode"><?php echo number_format_i18n( 4 ).'. '; _e('CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS:<br>Modify Query String Exploit code here', 'bulletproof-security'); ?> </label></strong><br />
<strong><?php $text = '<font color="#2ea2cc">'.__('You MUST copy and paste the entire BPS QUERY STRING EXPLOITS section of code from your wp-admin .htaccess file from # BEGIN BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS to # END BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS into this text box first. You can then edit and modify the code in this text window and save your changes.', 'bulletproof-security').'</font>'; echo $text; ?></strong><br />
    <textarea class="bps-text-area-custom-code" name="bps_customcode_bpsqse_wpa" tabindex="4"><?php echo $CC_Options_wpadmin['bps_customcode_bpsqse_wpa']; ?></textarea>
    </td>
    <td class="bps-table_cell_help_custom_code" style="padding-top:105px;"><span style="color:#2ea2cc;font-weight:bold;">Example Code: Click the Read Me help button for wp-admin Custom Code Setup Steps. This example code is a visual reference to show you which wp-admin htaccess file code goes in the CUSTOM CODE BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS text box. Go to the htaccess File Editor tab page and copy your actual BPS QUERY STRING EXPLOITS AND FILTERS wp-admin htaccess file code and paste it into the CUSTOM CODE text box to the left.</span><pre># BEGIN BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS<br /># WORDPRESS WILL BREAK IF ALL THE BPSQSE FILTERS ARE DELETED<br /># Use BPS wp-admin Custom Code to modify/edit/change this code and to save it permanently.<br />RewriteCond %{HTTP_USER_AGENT} (%0A|%0D|%27|%3C|%3E|%00) [NC,OR]<br />.....<br />.....<br />RewriteCond %{QUERY_STRING} (sp_executesql) [NC]<br />RewriteRule ^(.*)$ - [F]<br /># END BPSQSE-check BPS QUERY STRING EXPLOITS AND FILTERS</pre></td>
  </tr>
  <tr>
    <td class="bps-table_cell_help_custom_code">
    
    <input type="hidden" name="scrolltoCCodeWPA" value="<?php echo esc_html( $scrolltoCCodeWPA ); ?>" />
	<input type="submit" name="bps_customcode_submit_wpa" value="<?php esc_attr_e('Save wp-admin Custom Code', 'bulletproof-security') ?>" class="button bps-button" onclick="return confirm('<?php $text = __('Click OK to save your wp-admin Custom Code or click Cancel.', 'bulletproof-security'); echo $text; ?>')" />
</form>

	</td>
    <td class="bps-table_cell_help_custom_code">&nbsp;</td>
  </tr>
  <tr>
    <td class="bps-table_cell_help">&nbsp;</td>
    <td class="bps-table_cell_help">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="bps-table_cell_bottom">&nbsp;</td>
  </tr>
</table>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#bpsCustomCodeFormWPA').submit(function(){ $('#scrolltoCCodeWPA').val( $('#bps_customcode_deny_files_wpa').scrollTop() ); });
	$('#bps_customcode_deny_files_wpa').scrollTop( $('#scrolltoCCodeWPA').val() ); 
});
/* ]]> */
</script>

</div>

<?php } ?>
</div>