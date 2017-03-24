<?php PC_Hooks::pc_before_head(); /* Framework hook wrapper */ ?>
<head>
<meta charset="utf-8" />

<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

<?php PC_Hooks::pc_head_top(); /* Framework hook wrapper */ ?>

<meta name="viewport" content="width=device-width">

<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="screen" />
<?php wp_head(); ?>
</head>

<body <?php body_class(PC_THEME_NAME_SLUG) ?>>

<div id="body-container">

	<?php $options = get_option( PC_OPTIONS_DB_NAME ); ?>

	<div id="header-container">
		<header class="cf">
		
			<?php if ( has_nav_menu( PC_CUSTOM_NAV_MENU_2 ) ) : ?>
				<nav class="secondary-menu">
					<?php
						$args = array(
						'theme_location' => PC_CUSTOM_NAV_MENU_2
						/*'container_class' => 'secondary-menu',
						'menu_class' => ''*/ );
						wp_nav_menu($args);
					?>
				</nav>
			<?php endif; ?>
		
			<div id="logo-wrap">
				<?php
					
					// Output a logo if defined, otherwise the site title
					PC_Utility::pc_display_theme_logo($options);

					$options = get_option( PC_OPTIONS_DB_NAME );
					if ( !isset($options[ 'chk_hide_description' ]) ) { ?>
						<div id="site-description"><?php bloginfo( 'description' ); ?></div>
					<?php } ?>

			</div><!-- #logo-wrap -->
			
			<?php get_sidebar( 'header' ); // Adds support for the header widget area ?>

		 	  <nav class="primary-menu cf">
		 	     <?php
		 	     	$args = array(
		 	     		'theme_location' => PC_CUSTOM_NAV_MENU_1
		 	     		/*'container_class' => 'primary-menu',
		 	     		'menu_class' => ''*/ );
		 	     	wp_nav_menu($args);
		 	     ?>
		 	  </nav>

			  <nav class="primary-menu-dropdown">
				 <?php
					$menu_name = PC_CUSTOM_NAV_MENU_1;
					$locations = get_nav_menu_locations();

					if ( has_nav_menu( $menu_name ) ) {
						$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
						$menu_items = wp_get_nav_menu_items($menu->term_id);
						$menu_list = '<select onchange="if (this.value) window.location.href=this.value">';
						$menu_list .= '<option selected="selected">-- Main Menu --</option>';
						
						foreach ( (array) $menu_items as $key => $menu_item )
							$menu_list .= '<option value="' . $menu_item->url . '">' . $menu_item->title . '</option>';
				
						$menu_list .= '</select>';
						echo $menu_list;
					}
					else { /* Display fallback menu. */
						$args = array( 'depth' => -1 );
						$menu_items = get_pages($args);
						$menu_list = '<select onchange="if (this.value) window.location.href=this.value">';
						$menu_list .= '<option selected="selected">-- Main Menu --</option>';
						foreach ( (array) $menu_items as $key => $menu_item ) {
							$permalink = get_permalink( $menu_item->ID );
							$menu_list .= '<option value="' . $permalink . '">' . $menu_item->post_title . '</option>';
						}
						$menu_list .= '</select>';
						echo $menu_list;
					}
		 	     ?>
		 	  </nav>

		 </header>

	</div><!-- #header-container -->