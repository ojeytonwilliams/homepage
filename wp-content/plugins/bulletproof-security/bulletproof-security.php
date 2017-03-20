<?php
/*
Plugin Name: BulletProof Security
Plugin URI: https://forum.ait-pro.com/read-me-first/
Text Domain: bulletproof-security
Domain Path: /languages/
Description: <strong>Feature Highlights:</strong> Setup Wizard &bull; .htaccess Website Security Protection (Firewalls) &bull; Security Logging|HTTP Error Logging &bull; DB Backup &bull; DB Table Prefix Changer &bull; Login Security & Monitoring &bull; Idle Session Logout (ISL) &bull; Auth Cookie Expiration (ACE) &bull; UI Theme Skin Changer &bull; System Info: Extensive System, Server and Security Status Information &bull; FrontEnd|BackEnd Maintenance Mode
Version: .54.5
Author: AITpro | Edward Alexander
Author URI: https://forum.ait-pro.com/read-me-first/
*/

/*  Copyright (C) 2010-2017 Edward Alexander | AITpro.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// BPS variables
define( 'BULLETPROOF_VERSION', '.54.5' );
$bps_last_version = '.54.4';
$bps_version = '.54.5';
$aitpro_bullet = '<img src="'.plugins_url('/bulletproof-security/admin/images/aitpro-bullet.png').'" style="padding:0px 3px 0px 3px;" />';
// Top div & bottom div
$bps_topDiv = '<div id="message" class="updated" style="background-color:#dfecf2;border:1px solid #999;-moz-border-radius-topleft:3px;-webkit-border-top-left-radius:3px;-khtml-border-top-left-radius:3px;border-top-left-radius:3px;-moz-border-radius-topright:3px;-webkit-border-top-right-radius:3px;-khtml-border-top-right-radius:3px;border-top-right-radius:3px;-webkit-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);-moz-box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);box-shadow: 3px 3px 5px -1px rgba(153,153,153,0.7);"><p>';
$bps_bottomDiv = '</p></div>';

// Load BPS Global class - not doing anything with this Class in BPS Free
//require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/class.php' );

add_action( 'init', 'bulletproof_security_load_plugin_textdomain' );

// Load i18n Language Translation
function bulletproof_security_load_plugin_textdomain() {
	load_plugin_textdomain('bulletproof-security', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
}

// BPS upgrade functions
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/functions.php' );
// BPS HUD Dimiss functions
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/hud-dismiss-functions.php' );
// BPS Zip & Email Log File Cron functions
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/zip-email-cron-functions.php' );
// General functions
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/general-functions.php' );
// BPS Login Security
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/login-security.php' );
// BPS DB Backup
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/db-security.php' );
// BPS Hidden Plugin Folders|Files (HPF) Cron
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/hidden-plugin-folders-cron.php' );
// Idle Session Logout (ISL)
$BPS_ISL_options = get_option('bulletproof_security_options_idle_session');
if ( $BPS_ISL_options['bps_isl'] == 'On' ) {
require_once( WP_PLUGIN_DIR . '/bulletproof-security/includes/idle-session-logout.php' );
}

// If in single site Admin Dashboard
if ( is_admin() ) {
    
require_once( WP_PLUGIN_DIR . '/bulletproof-security/admin/includes/admin.php' );
	
	register_activation_hook(__FILE__, 'bulletproof_security_install');
	register_deactivation_hook(__FILE__, 'bulletproof_security_deactivation');
    register_uninstall_hook(__FILE__, 'bulletproof_security_uninstall');

	add_action( 'admin_init', 'bulletproof_security_admin_init' );
    add_action( 'admin_menu', 'bulletproof_security_admin_menu' );
}

// If in Network Admin Dashboard for BPS Uninstaller
if ( is_multisite() && is_network_admin() ) {
	add_action( 'network_admin_menu', 'bulletproof_security_network_admin_menu' ); 	
}

// "Settings" link on Plugins Options Page 
function bps_plugin_actlinks( $links, $file ) {
static $this_plugin;
	if ( ! $this_plugin ) 
		$this_plugin = plugin_basename(__FILE__);
	if ( $file == $this_plugin ) {
		if ( ! is_multisite() ) {	
		$links[] = '<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="'.esc_attr( 'BPS Setup Wizard' ).'">'.__('Setup Wizard', 'bulletproof-security').'</a>';
		$links[] = '<br><a href="'.admin_url( 'plugins.php?page=bulletproof-security/admin/includes/uninstall.php' ).'" title="'.esc_attr( 'Select an uninstall option for BPS plugin deletion' ).'">'.__('Uninstall Options', 'bulleproof-security').'</a>';
		} elseif ( is_multisite() ) {
		$links[] = '<br><a href="'.admin_url( 'admin.php?page=bulletproof-security/admin/wizard/wizard.php' ).'" title="'.esc_attr( 'BPS Setup Wizard' ).'">'.__('Setup Wizard', 'bulletproof-security').'</a>';		
		// The Uninstall Options Form does not work on Network|Multisite so do not show the Uninstall Options link in Action Links
		//$links[] = '<br><a href="'.network_admin_url( 'plugins.php?page=bulletproof-security/admin/includes/uninstall.php' ).'" title="'.esc_attr( 'Select an uninstall option for BPS plugin deletion' ).'">'.__('Uninstall Options', 'bulleproof-security').'</a>';
		}
	}
	return $links;
}

add_filter( 'plugin_action_links', 'bps_plugin_actlinks', 10, 2 );
add_filter( 'network_admin_plugin_action_links', 'bps_plugin_actlinks', 10, 2 );

// Add links on plugins page
function bps_plugin_extra_links( $links, $file ) {
static $this_plugin;
	if ( ! current_user_can('install_plugins') )
		return $links;
	if ( ! $this_plugin ) 
		$this_plugin = plugin_basename(__FILE__);
	if ( $file == $this_plugin ) {
		$links[] = '<a href="https://forum.ait-pro.com/forums/topic/plugin-conflicts-actively-blocked-plugins-plugin-compatibility/" title="BulletProof Security Forum" target="_blank">'.__('Forum - Support', 'bulleproof-security').'</a>';
		$links[] = '<a href="https://affiliates.ait-pro.com/po/" title="Upgrade to BPS Pro" target="_blank">'.__('Upgrade', 'bulleproof-security').'</a>';
		$links[] = '<a href="https://www.ait-pro.com/bps-features/" title="BPS Pro Features" target="_blank">'.__('BPS Pro Features', 'bulleproof-security').'</a>';
	}
	return $links;
}

add_filter( 'plugin_row_meta', 'bps_plugin_extra_links', 10, 2 );

?>