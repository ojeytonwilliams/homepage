<?php
/**
 * Plugin Name: APH Syntax Highlighter
 * Description: Bringing Syntax Highlighter 4 by Alex Gorbatchev into WordPress easily, please go to <a href="options-general.php?page=aph-syntax-highlighter" target="_blank">Settings &raquo; Syntax Highlighter</a> to change some options, or you can leave it as is.
 * Version: 1.2.1
 * Author: Agus Prawoto Hadi
 * Author URI: http://www.webdevzoom.com
 */

include 'includes/aphsh-config.php';

if (is_admin())
{
	include 'includes/aphsh-admin-notices.php';
	include 'includes/aphsh-admin.php';
	include 'includes/aphsh-build.php';
	new Aphsh_Admin();
	
	include 'includes/aphsh-admin-editor.php';
	new Aphsh_Admin_Editor();
	
} else {
	include 'includes/aphsh-front.php';
	new Aphsh_Front();
}

?>