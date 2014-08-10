jQuery(document).ready(function(){
	
	
	if(jQuery('#last_tab').val() == ''){

		jQuery('.pxls-opts-group-tab:first').slideDown('fast');
		jQuery('#pxls-opts-group-menu li:first').addClass('active');
	
	}else{
		
		tabid = jQuery('#last_tab').val();
		jQuery('#'+tabid+'_section_group').slideDown('fast');
		jQuery('#'+tabid+'_section_group_li').addClass('active');
		
	}
	
	
	jQuery('input[name="'+pxls_opts.opt_name+'[defaults]"]').click(function(){
		if(!confirm(pxls_opts.reset_confirm)){
			return false;
		}
	});
	
	jQuery('.pxls-opts-group-tab-link-a').click(function(){
		relid = jQuery(this).attr('data-rel');
		
		jQuery('#last_tab').val(relid);
		
		jQuery('.pxls-opts-group-tab').each(function(){
			if(jQuery(this).attr('id') == relid+'_section_group'){
				jQuery(this).delay(400).fadeIn(1200);
			}else{
				jQuery(this).fadeOut('fast');
			}
			
		});
		
		jQuery('.pxls-opts-group-tab-link-li').each(function(){
				if(jQuery(this).attr('id') != relid+'_section_group_li' && jQuery(this).hasClass('active')){
					jQuery(this).removeClass('active');
				}
				if(jQuery(this).attr('id') == relid+'_section_group_li'){
					jQuery(this).addClass('active');
				}
		});
	});
	
	
	
	
	if(jQuery('#pxls-opts-save').is(':visible')){
		jQuery('#pxls-opts-save').delay(4000).slideUp('slow');
	}
	
	if(jQuery('#pxls-opts-imported').is(':visible')){
		jQuery('#pxls-opts-imported').delay(4000).slideUp('slow');
	}	
	
	jQuery('input, textarea, select').change(function(){
		jQuery('#pxls-opts-save-warn').slideDown('slow');
	});
	
	
	jQuery('#pxls-opts-import-code-button').click(function(){
		if(jQuery('#pxls-opts-import-link-wrapper').is(':visible')){
			jQuery('#pxls-opts-import-link-wrapper').fadeOut('fast');
			jQuery('#import-link-value').val('');
		}
		jQuery('#pxls-opts-import-code-wrapper').fadeIn('slow');
	});
	
	jQuery('#pxls-opts-import-link-button').click(function(){
		if(jQuery('#pxls-opts-import-code-wrapper').is(':visible')){
			jQuery('#pxls-opts-import-code-wrapper').fadeOut('fast');
			jQuery('#import-code-value').val('');
		}
		jQuery('#pxls-opts-import-link-wrapper').fadeIn('slow');
	});
	
	
	
	
	jQuery('#pxls-opts-export-code-copy').click(function(){
		if(jQuery('#pxls-opts-export-link-value').is(':visible')){jQuery('#pxls-opts-export-link-value').fadeOut('slow');}
		jQuery('#pxls-opts-export-code').toggle('fade');
	});
	
	jQuery('#pxls-opts-export-link').click(function(){
		if(jQuery('#pxls-opts-export-code').is(':visible')){jQuery('#pxls-opts-export-code').fadeOut('slow');}
		jQuery('#pxls-opts-export-link-value').toggle('fade');
	});
	
	

	
	
	
});