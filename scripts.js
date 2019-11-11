
jQuery(function (){
	jQuery('#ecl_del').click(function (){
		event.preventDefault();
		jQuery("#posts_exlude option:selected").removeAttr("selected");
	});

	jQuery('#inc_del').click(function (){
		event.preventDefault();
		jQuery("#posts_include option:selected").removeAttr("selected");
	});

	jQuery('#cat_del').click(function (){
		event.preventDefault();
		jQuery("#categories option:selected").removeAttr("selected");
	});
});