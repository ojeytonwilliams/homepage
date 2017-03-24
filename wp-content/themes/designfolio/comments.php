<?php global $pc_theme_object; ?>

<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'presscoders' ); ?></p>
		</div><!-- #comments -->
		<?php return;
	endif; ?>

<?php if ( have_comments() ) : ?>
			<h3 id="comments-title"><?php
			printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'presscoders' ),
			number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
			?></h3>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'presscoders' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'presscoders' ) ); ?></div>
			</div> <!-- .navigation -->
<?php endif; // Check for comment navigation ?>

			<ol class="commentlist">
				<?php wp_list_comments( array( 'callback' => array( &$pc_theme_object->_ts_utility_callbacks_class, 'theme_comment' ) ) ); ?>
			</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'presscoders' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'presscoders' ) ); ?></div>
			</div><!-- .navigation -->
<?php endif; // Check for comment navigation ?>

<?php endif; // End have_comments() ?>

<?php comment_form(); ?>

</div><!-- #comments -->
