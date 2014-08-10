<?php

/**
 * 
 * Require the framework class before doing anything else, so we can use the defined urls and dirs
 * Also if running on windows you may have url problems, which can be fixed by defining the framework url first
 *
 */
if ( ! class_exists( 'PXLS_Options' ) ) {
	require_once( dirname( __FILE__ ) . '/options/options.php' );
}



/**
 * 
 * Custom function for filtering the sections array given by theme, good for child themes to override or add to the sections.
 * Simply include this function in the child themes functions.php file.
 *
 * NOTE: the defined constansts for urls, and dir will NOT be available at this point in a child theme, so you must use
 * get_template_directory_uri() if you want to use any of the built in icons
 *
 * @since  1.0.0
 *
 */
function add_another_section( $sections ){
	
	//$sections = array();
	$sections[] = array(
				'title' => __('A Section added by hook', 'pxls-opts'),
				'desc' => __('<p class="description">This is a section created by adding a filter to the sections array, great to allow child themes, to add/remove sections from the options.</p>', 'pxls-opts'),
				//all the glyphicons are included in the options folder, so you can hook into them, or link to your own custom ones.
				//You dont have to though, leave it blank for default.
				'icon' => trailingslashit(get_template_directory_uri()).'options/img/glyphicons/glyphicons_062_attach.png',
				//Lets leave this as a blank section, no options just some intro text set above.
				'fields' => array()
				);
	
	return $sections;
	
}//function
//add_filter('pxls-opts-sections', 'add_another_section');




/**
 * 
 * Custom function for filtering the args array given by theme, good for child themes to override or add to the args array.
 *
 * @since  1.0.0
 *
 */
function change_framework_args( $args ){
	
	//$args['dev_mode'] = false;
	
	return $args;
	
}//function
//add_filter('pxls-opts-args-twenty_eleven', 'change_framework_args');









/**
 * setup_framework_options()
 * 
 * This is the meat of creating the optons page
 *
 * Override some of the default values, uncomment the args and change the values
 * - no $args are required, but there there to be over ridden if needed.
 *
 * @since  1.0.0
 */
function setup_framework_options() {
	$args = array();

	//Set it to dev mode to view the class settings/info in the form - default is false
	$args['dev_mode'] = false;

	//Remove the default stylesheet? make sure you enqueue another one all the page will look whack!
	//$args['stylesheet_override'] = true;

	//Add HTML before the form
	$args['intro_text'] = __('', 'pxls-opts');

	//Setup custom links in the footer for share icons
	$args['share_icons']['twitter'] = array();

	/*array(
											'link' => 'http://twitter.com/fruitpixel',
											'title' => 'Follow me on Twitter', 
											'img' => PXLS_OPTIONS_URL.'img/glyphicons/glyphicons_322_twitter.png'
											); */

	$args['share_icons']['linked_in'] = array();

	/* array(
											'link' => 'http://www.linkedin.com/pub/richard-howarth/18/35/295',
											'title' => 'Find me on LinkedIn', 
											'img' => PXLS_OPTIONS_URL.'img/glyphicons/glyphicons_337_linked_in.png'
											); */

	//Choose to disable the import/export feature
	//$args['show_import_export'] = false;

	//Choose a custom option name for your theme options, the default is the theme name in lowercase with spaces replaced by underscores
	$args['opt_name'] = 'pxls';

	//Custom menu icon
	$args['menu_icon'] =  PXLS_OPTIONS_URL.'img/menu_icon.png';

	//Custom menu title for options page - default is "Options"
	$args['menu_title'] = __( get_option( 'pxls_themename' ) , 'pxls-opts');

	//Custom Page Title for options page - default is "Options"
	$args['page_title'] = __( get_option( 'pxls_themename' ) , 'pxls-opts');

	//Custom page slug for options page (wp-admin/themes.php?page=***) - default is "pxls_theme_options"
	$args['page_slug'] = 'pxls_theme_options';

	//Custom page capability - default is set to "manage_options"
	$args['page_cap'] = 'publish_pages';

	//page type - "menu" (adds a top menu section) or "submenu" (adds a submenu) - default is set to "menu"
	$args['page_type'] = 'menu';

	//parent menu - default is set to "themes.php" (Appearance)
	//the list of available parent menus is available here: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	$args['page_parent'] = 'admin.php';

	//custom page location - default 100 - must be unique or will override other items
	$args['page_position'] = 564;

	//Custom page icon class (used to override the page icon next to heading)
	//$args['page_icon'] = 'icon-themes';

	//Want to disable the sections showing as a submenu in the admin? uncomment this line
	//$args['allow_sub_menu'] = false;
		
	//Set ANY custom page help tabs - displayed using the new help tab API, show in order of definition		
	$args['help_tabs'][] = array(
								'id' => 'pxls-opts-1',
								'title' => __('Theme Information 1', 'pxls-opts'),
								'content' => __('<p>This is the tab content, HTML is allowed.</p>', 'pxls-opts')
								);
	$args['help_tabs'][] = array(
								'id' => 'pxls-opts-2',
								'title' => __('Theme Information 2', 'pxls-opts'),
								'content' => __('<p>This is the tab content, HTML is allowed.</p>', 'pxls-opts')
								);

	//Set the Help Sidebar for the options page - no sidebar by default										
	$args['help_sidebar'] = __( '<p>Welcome to the <img src="' . trailingslashit( PXLS_URI ) .'images/pxls-slug.png" alt="PXLS:Themes" /> Framework.</p>', 'pxls-opts' );



	$sections = array();

			
	$tabs = array();
			
	if ( function_exists( 'wp_get_theme' ) ) {
		$theme_data = wp_get_theme();
		$theme_uri = $theme_data->get( 'ThemeURI' );
		$description = $theme_data->get( 'Description' );
		$author = $theme_data->get( 'Author' );
		$version = $theme_data->get( 'Version' );
		$tags = $theme_data->get( 'Tags' );
	}else{
		$theme_data = wp_get_theme( trailingslashit( get_stylesheet_directory() ) . 'style.css' );
		$theme_uri = $theme_data['URI'];
		$description = $theme_data['Description'];
		$author = $theme_data['Author'];
		$version = $theme_data['Version'];
		$tags = $theme_data['Tags'];
	}	

	$theme_info = '<div class="pxls-opts-section-desc">';
	$theme_info .= '<p class="pxls-opts-theme-data description theme-uri">' . __( '<strong>Theme URL:</strong> ', 'pxls-opts' ).'<a href="' . $theme_uri . '" target="_blank">' . $theme_uri . '</a></p>';
	$theme_info .= '<p class="pxls-opts-theme-data description theme-author">' . __( '<strong>Author:</strong> ', 'pxls-opts' ) . $author . '</p>';
	$theme_info .= '<p class="pxls-opts-theme-data description theme-version">' . __( '<strong>Version:</strong> ', 'pxls-opts' ) . $version . '</p>';
	$theme_info .= '<p class="pxls-opts-theme-data description theme-description">' . $description . '</p>';
	$theme_info .= '<p class="pxls-opts-theme-data description theme-tags">' . __( '<strong>Tags:</strong> ', 'pxls-opts' ).implode( ', ', $tags ).'</p>';
	$theme_info .= '</div>';

	$tabs['theme_info'] = array(
					'icon' => PXLS_OPTIONS_URL.'img/glyphicons/glyphicons_195_circle_info.png',
					'title' => __( 'Theme Information', 'pxls-opts' ),
					'content' => $theme_info
					);
    
    
	if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'README.html' ) ) {
		$tabs['theme_docs'] = array(
						'icon' => PXLS_OPTIONS_URL.'img/glyphicons/glyphicons_071_book.png',
						'title' => __('Documentation', 'pxls-opts'),
						'content' => nl2br( file_get_contents( trailingslashit( get_stylesheet_directory() ) . 'README.html' ) )
						);
	}

	global $PXLS_Options;
	$PXLS_Options = new PXLS_Options( $sections, $args, $tabs );

}
add_action( 'init', 'setup_framework_options', 0 );




function pxls_framework_settings( $sections ) {
		
	
	$sections[] = array(
		'icon' => PXLS_OPTIONS_URL.'img/glyphicons/glyphicons_040_stats.png',
		'title' => __( 'Google Analytics', 'pxls-opts' ),
		'desc' => __( '', 'pxls-opts' ),
		'fields' => array(
			array(
				'id' => 'ga_enable',
				'type' => 'select',
				'title' => __( 'Enable Google Analytics?', 'pxls-opts' ), 
				'desc' => __( '', 'pxls-opts' ),
				'sub_desc' => __( 'Enable if you wish to use Google Analytics', 'pxls-opts' ),
				'options' => array(
					'0' => __( 'No', 'pxls-opts' ),
					'1' => __( 'Yes', 'pxls-opts' )
				),//Must provide key => value pairs for select options
				'std' => '0'
			),
			array(
				'id' => 'analytics_id', //must be unique
				'type' => 'text', //the field type
				'title' => __( 'Site ID', 'pxls-opts' ),
				'sub_desc' => __( "Enter your site's ID", 'pxls-opts' ),
				'desc' => __( '', 'pxls-opts' ),
				'std' => 'UA-XXXXX-X'
			)
		)
	);

	return $sections;

}
add_filter( 'pxls-opts-sections-pxls', 'pxls_framework_settings' );




/**
 * 
 * Custom function for the callback referenced above
 *
 */
function my_custom_field( $field, $value ) {
	print_r( $field );
	print_r( $value );

}



/**
 * 
 * Custom function for the callback validation referenced above
 *
 */
function validate_callback_function( $field, $value, $existing_value ) {
	
	$error = false;
	$value =  'just testing';
	/*
	do your validation
	
	if(something){
		$value = $value;
	}elseif(somthing else){
		$error = true;
		$value = $existing_value;
		$field['msg'] = 'your custom error message';
	}
	*/
	
	$return['value'] = $value;
	if ( $error == true ) {
		$return['error'] = $field;
	}
	return $return;
	
}

