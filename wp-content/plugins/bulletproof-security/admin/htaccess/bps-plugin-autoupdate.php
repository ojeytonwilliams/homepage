<?php
## BPS AutoUpdate Version: 1.0
## This must-use file is created by the BulletProof Security plugin when you choose to allow Automatic Updates for the BPS plugin.
## Important Note: If you would like to add additional customizations to this file it is recommended that you copy this file, make your customizations and 
## then rename this file and the function name and upload it to your /wp-content/mu-plugins/ folder. Most likely additional things will be added/created in
## this BPS must-use file at a later time. So if you customize this BPS file then you will lose your customizations if/when this file is updated in the future.
##
## Uncommenting these filters below and commenting out this BPS filter: add_filter( 'auto_update_plugin', 'bpsPro_autoupdate_bps_plugin', 10, 2 );
## will allow ALL plugin and theme automatic updates on your website.
/** 
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );
**/ 

function bpsPro_autoupdate_bps_plugin( $update, $item ) {
    
	// Array of plugin slugs to AutoUpdate
    $plugins = array ( 
		'bulletproof-security',
    );
    
	// AutoUpdate plugins in the $plugins array
	if ( in_array( $item->slug, $plugins ) ) {
        return true;
    } else {
		// For any/all other plugins that are not in the $plugins array, return the WP $update API response
		return $update; 
    }
}

add_filter( 'auto_update_plugin', 'bpsPro_autoupdate_bps_plugin', 10, 2 );

?>