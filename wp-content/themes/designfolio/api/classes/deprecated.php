<?php

/**
 * Framework deprecated class. All deprecated framework functionality added here will be
 * deleted in a future version.
 *
 * @since 0.1.0
 */
class PC_Deprecated {

	/**
	 * PC_Deprecated class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		add_action( 'load-post.php', array( &$this, 'thickbox_bugfix' ) ); /* load only post.php */
		add_action( 'load-post-new.php', array( &$this, 'thickbox_bugfix' ) ); /* load only on post-new.php */
	}

	/**
	 * Remove the hook and callback function below when thickbox no longer used, or the bug
	 * with jQuery 1.6.1 and jQuery UI tabs on the "Post" editor has been fixed.
	 *
	 * @since 0.1.0
	 */
	public function thickbox_bugfix(){

		wp_register_script( 'override_tb', PC_THEME_ROOT_URI.'/api/deprecated/override_thickbox.js', array('thickbox') );
		wp_enqueue_script( 'override_tb' );
	}
}

?>