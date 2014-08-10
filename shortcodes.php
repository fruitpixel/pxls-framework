<?php


/**
 * [pxls_contact_details description]
 * @param  [type] $atts [description]
 * @return 
 */
function pxls_contact_details( $atts ){
	echo ('<p id="header-contact-details">');
	if ( pxls_get_company_telephone() ) : echo ('<span>tel:</span><br/>' . pxls_get_company_telephone() . '<br/>'); endif;
	if ( pxls_get_company_fax() ) : echo ('<span>fax:</span><br/>' . pxls_get_company_fax() . '<br/>'); endif;
	echo('</p>');
}
add_shortcode( 'pxls-contact-details', 'pxls_contact_details' );
