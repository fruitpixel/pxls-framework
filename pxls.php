<?php
/**
 * PXLS:Framework - A WordPress theme development framework.
 *
 * @package PXLS:Framework
 * @version 2.0.0
 * @author Richard Howarth <r.howarth@fruitpixel.com>
 * @copyright Copyright (c) 2013 Richard Howarth
 * @link http://fruitpixel.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


class PXLS {

	/**
	 * Constructor method for the PXLS class.  This method adds other methods of the class to 
	 * specific hooks within WordPress.  It controls the load order of the required files for running 
	 * the framework.
	 *
	 * @since 1.1.0
	 */
	function __construct() {
		global $pxls;

		/* Set up an empty class for the global $pxls object. */
		$pxls = new stdClass;

		/* Define framework, parent theme, and child theme constants. */
		add_action( 'after_setup_theme', array( &$this, 'constants' ), 1 );

		/* Load the core functions required by the rest of the framework. */
		add_action( 'after_setup_theme', array( &$this, 'core' ), 2 );

		/* Initialize the framework's default actions and filters. */
		add_action( 'after_setup_theme', array( &$this, 'default_filters' ), 3 );
		
	}


	/**
	 * Defines the constant paths for use within the core framework, parent theme, and child theme.  
	 * Constants prefixed with 'PXLS_' are for use only within the core framework and don't 
	 * reference other areas of the parent or child theme.
	 *
	 * @since 1.1.0
	 */
	function constants() {

		/* Sets the framework version number. */
		define( 'PXLS_VERSION', '2.0.0' );

		/* Sets the path to the child theme directory. */
		define( 'THEME_DIR', get_stylesheet_directory() );

		/* Sets the path to the child theme directory URI. */
		define( 'THEME_URI', get_stylesheet_directory_uri() );

		/* Sets the path to the parent theme directory. */
		define( 'PXLS_DIR', trailingslashit( get_template_directory() ) );

		/* Sets the path to the parent directory URI. */
		define( 'PXLS_URI', trailingslashit( get_template_directory_uri() ) );

		/* Sets the path to the pxls framework directory */
		define( 'PXLS_FRAMEWORK_DIR', trailingslashit( PXLS_DIR ) . 'framework' );

		/* Sets the path to the pxls framework URI */
		define( 'PXLS_FRAMEWORK_URI', trailingslashit( PXLS_URI ) . 'framework' );

		/* Sets the path to the core framework images directory URI. */
		define( 'PXLS_IMAGES', trailingslashit( PXLS_FRAMEWORK_URI ) . 'images' );

		define( 'PXLS_OPTIONS_URI', trailingslashit( PXLS_FRAMEWORK_URI ) . 'options' );

		/* Sets the path to the core framework JavaScript directory URI. */
		define( 'PXLS_JS', trailingslashit( PXLS_FRAMEWORK_URI ) . 'javascripts' );

		/* Define path to the Custom Meta Boxes framework - this overrides the 
		built-in path which defaults to the plugin folder */
		if ( ! defined( 'CMB_PATH') )
			define( 'CMB_PATH', trailingslashit( PXLS_FRAMEWORK_DIR ) . 'custom-meta-boxes' );

		/* Define URL to the Custom Meta Boxes framework - this overrides the 
		built-in path which defaults to the plugin folder url */
		if ( ! defined( 'CMB_URL' ) )
			define( 'CMB_URL', trailingslashit( PXLS_FRAMEWORK_URI ) . 'custom-meta-boxes' );
	}



	function core() {

		/* load the admin file */
		require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'admin.php' );                               
		
		require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'sidebars.php' );                             
		
		require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'widgets.php' );                             
		
		//require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'pxls-options.php' );  

		if ( !class_exists( 'ReduxFramework' ) && file_exists( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'ReduxFramework/ReduxCore/framework.php' ) ) {
		    require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'ReduxFramework/ReduxCore/framework.php' );
		}
		if ( !isset( $PXLS_Options ) && file_exists( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'pxls-options.php' ) ) {
		    require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'pxls-options.php' );
		}                       
		
		require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'comments_template.php' );                    
		
		require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'default.php' );                              

		require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'shortcodes.php' );  

		/* Custom Meta Boxes by HumanMade */
		require_once( trailingslashit( PXLS_FRAMEWORK_DIR ) . 'custom-meta-boxes/custom-meta-boxes.php' );       
		 
	}


	function default_filters() {

		/* include the google analytics script into the head section */
		add_action('wp_head', 'pxls_ga_code', 99999);

		add_filter( 'get_page_id', 'pxls_get_page_id', 10, 1 );
		add_filter( 'get_post_by_title', 'pxls_get_post_by_title', 10, 2 );
		add_filter( 'nav_menu_first_last', 'pxls_nav_menu_first_last' );
		add_filter( 'get_company_name', 'pxls_get_company_name' );
		add_filter( 'get_company_logo', 'pxls_get_company_logo' );
		add_filter( 'get_company_email', 'pxls_get_company_email' );
		add_filter( 'get_company_telephone', 'pxls_get_company_telephone' );
		add_filter( 'get_company_facebook', 'pxls_get_company_facebook' );
		add_filter( 'get_company_linkedin', 'pxls_get_company_linkedin' );
		add_filter( 'get_theme_colorscheme_folder_uri', 'pxls_get_theme_colorscheme_folder_uri' );
		add_filter( 'widget_text', 'do_shortcode' );
	}
}


/**
 * [is_or_descendant_tax description]
 * @param  [type]  $terms    [description]
 * @param  [type]  $taxonomy [description]
 * @return boolean           [description]
 */
function is_or_descendant_tax( $terms, $taxonomy ) {
    if ( is_tax( $taxonomy, $terms ) ) {
            return true;
    }
    foreach ( (array) $terms as $term ) {
        // get_term_children() accepts integer ID only
        $descendants = get_term_children( (int) $term, $taxonomy );
        if ( $descendants && is_tax( $taxonomy, $descendants ) )
            return true;
    }
    return false;
}



/**
 *  Get the id of a page by its name
 */
function pxls_get_page_id( $page_name ) {
	global $wpdb;
	return $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'" );
}



/**
 * Returns the head meta tag for Content-Type
 * @return string 
 */
function pxls_content_type() {
    return "\n".'<meta http-equiv="Content-Type" content="'. get_bloginfo( 'html_type' ) .'; charset='. get_bloginfo( 'charset' ) .'" />' . "\n";
}



/**
 * Get the id of a post by its title
 */
function pxls_get_post_by_title( $page_title, $output = OBJECT ) {
    global $wpdb;
    $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s LIMIT 0, 1", $page_title ) );
    if ( $post )
        return get_post( $post, $output );
    return null;
}



/**
 * adds menu-item-first and menu-item-last
 * to the respective elements of a nav menu
 */
function pxls_nav_menu_first_last( $items ) {
	$position = strrpos( $items, 'class="menu-item', -1 );
	$items=substr_replace( $items, 'menu-item-last ', $position+7, 0 );
	$position = strpos( $items, 'class="menu-item' );
	$items=substr_replace( $items, 'menu-item-first ', $position+7, 0 );
	return $items;
}
//add_filter( 'wp_nav_menu_items', 'pxls_nav_menu_first_last' );



/**
 * Adds the specific classes required for the foundation top-bar to work correctly
 */
function pxls_foundation_nav_menu( $menu ) { 
	$menu = str_replace( 'menu-item-has-children', 'menu-item-has-children has-dropdown', $menu );
	$menu = str_replace( 'sub-menu', 'sub-menu dropdown', $menu );
	return $menu; 
}
add_filter('wp_nav_menu','pxls_foundation_nav_menu');



/**
 * custom function to get an excerpt with a custom length
 */
function pxls_excerpt( $length = 25, $morelink = 'false', $morelinktext = 'Read more' ) {
	global $post;
	$text = get_the_excerpt();
	$text = apply_filters( 'the_content', $text );
	$text = pxls_text_trim( $text, $length );
	$words = explode( ' ', $text, $length + 1 );
	if ( count( $words ) > $length ) {
		array_pop( $words );
		if ( $morelink != 'true' ) { array_push( $words, '...' ); }
		$text = implode( ' ', $words );
	}
	if ( $morelink == 'true' ) {
		$text .= ' <a class="more-link" href="'. get_permalink( $post->ID ) . '">' . $morelinktext . '</a>';
	}
	return $text;
}



/**
 * changes the default 'more' link
 */
function pxls_excerpt_more( $more = NULL ) {
    global $post;
    $more = 'More...';
	return ' <a class="more-link" href="'. get_permalink( $post->ID ) . '">'.$more.'</a>';
}
add_filter( 'excerpt_more', 'pxls_excerpt_more' );



/**
 * trims the text to the required number of words
 */
function pxls_text_trim( $text, $words = 50 ) {
	$matches = preg_split( "/\s+/", $text, $words + 1 );
	$sz = count( $matches );
	if ( $sz > $words )
	{
		unset( $matches[$sz-1] );
		return implode( ' ',$matches ) . " ...";
	}
	return $text;
}



/**
 * returns the company name if set in options; otherwise returns the site name
 */
function pxls_get_company_name() {
	global $PXLS_Options;
	$companyname = $PXLS_Options['pxls_company_name'];
	if ( '' == $companyname ) {
		$companyname = get_bloginfo( 'name' );
	}
	return $companyname;
}



/**
 * outputs the tracking code for google analytics
 */
function pxls_ga_code() {
	global $PXLS_Options;
	$enable = $PXLS_Options['ga_enable'];
	if ( $enable ) { ?>
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo $PXLS_Options['analytics_id']; ?>']);
			_gaq.push(['_trackPageview']);
			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>

	<?php }
}



/**
 * changes the logo on the login screen
 */
function pxls_get_company_logo() {
	global $PXLS_Options;
	$companylogo = $PXLS_Options['pxls_company_logo']['url'];
    return $companylogo;
}



/**
 * override the style on the login screen
 * includes logo, background etc.
 */
function pxls_login_style() {
	global $PXLS_Options;
	$loginlogo = $PXLS_Options['pxls_login_logo']['url'];
	if ( $loginlogo ) {
		echo '<style type="text/css">';
		echo 'h1 a { background-image:url(' . $loginlogo . ') !important; }';
		echo '</style>';
	}
}
add_action( 'login_head', 'pxls_login_style' );



/**
 * changes the default url linked to by clicking on the logo in the login screen
 */
function pxls_url_login(){
	global $PXLS_Options;
	$loginlogourl = $PXLS_Options['pxls_login_logo_url'];
	if ( $loginlogourl && $loginlogourl != '' ) {
		return $loginlogourl;
	}
	else {
		// use the site home url if there is no user defined one
		return home_url( '/' );
	}
}
add_filter( 'login_headerurl', 'pxls_url_login' );



/**
 * changes the default title of the logo on the login screen
 */
function pxls_login_logo_title() {
	return "Powered by WordPress and PXLS:Themes";
}
add_action( 'login_headertitle', 'pxls_login_logo_title' );



/**
 * make sure that any contact form 7 forms are using the 'nice form' styling class for zurb foundation
 */
function cf7_niceform_style( $class ) {
	$class .= ' nice';
	return $class;
}
add_filter( 'wpcf7_form_class_attr', 'cf7_niceform_style' );



/**
 * Returns the permalink for a page based on the incoming slug.
 * Snippet by Tom McFarlin - http://tommcfarlin.com/permalink-by-slug/
 *
 * @param 	string	$slug	The slug of the page to which we're going to link.
 * @return	string			The permalink of the page
 * @since	1.0
 */
function get_permalink_by_slug( $slug, $post_type = '' ) {

	// Initialize the permalink value
	$permalink = null;

	// Build the arguments for WP_Query
	$args =	array(
		'name' 			=> $slug,
		'max_num_posts' => 1
	);

	// If the optional argument is set, add it to the arguments array
	if( '' != $post_type ) {
		$args = array_merge( $args, array( 'post_type' => $post_type ) );
	}

	// Run the query (and reset it)
	$query = new WP_Query( $args );
	if( $query->have_posts() ) {
		$query->the_post();
		$permalink = get_permalink( get_the_ID() );
	}
	wp_reset_postdata();
	return $permalink;
}




