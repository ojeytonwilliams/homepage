<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

require_once 'includes/aphsh-config.php';
delete_option(APHSH_OPTION);
delete_option(APHSH_OPTION_SHDATA);
delete_option(APHSH_OPTION_VERSION);
delete_option(APHSH_OPTION_NOTICE);
?>