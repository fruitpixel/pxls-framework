/*
 *
 * pxls_Options_radio_img function
 * Changes the radio select option, and changes class on images
 *
 */
function pxls_radio_img_select(relid, labelclass){
	jQuery(this).prev('input[type="radio"]').prop('checked');

	jQuery('.pxls-radio-img-'+labelclass).removeClass('pxls-radio-img-selected');	
	
	jQuery('label[for="'+relid+'"]').addClass('pxls-radio-img-selected');
}//function