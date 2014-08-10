<?php 

/**
 * pxls_setup_theme()
 * 
 * Creates the default theme settings upon installation
 *
 * @since  0.01
 */
function pxls_setup_theme() {
	$pxls_theme_status = get_option( 'pxls_theme_setup_status' );
	if ( $pxls_theme_status !== '1' ) {
		update_option( 'pxls_theme_setup_status', '1' );		
		if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == "themes.php" ) {
			wp_redirect( 'admin.php?page=theme-options.php' );
		}
	}
} 
add_action('after_setup_theme', 'pxls_setup_theme' );

			
