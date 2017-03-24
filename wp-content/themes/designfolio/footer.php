    <?php PC_Hooks::pc_after_opening_footer_tag(); /* Framework hook wrapper */ ?>
    <?php get_sidebar( 'footer' ); // Adds support for the four footer widget areas ?>
    <?php PC_Hooks::pc_before_closing_footer_tag(); /* Framework hook wrapper */ ?>

	</footer>
</div><!-- #body-container -->

<?php PC_Hooks::pc_after_closing_footer_tag(); /* Framework hook wrapper */ ?>
<?php wp_footer(); ?>

</body>
</html>