jQuery(document).ready(function($) {

$(".link-posttypes select").change(function(){
   var selectedVal = $(":selected",this).val();
   
   if(selectedVal=="post"){
		$(this).parent().nextAll(".link-pages").hide();
		$(this).parent().nextAll(".link-posts").slideDown('slow');
		$(this).parent().nextAll(".link-custom").hide();
   }else if(selectedVal=="page"){
		$(this).parent().nextAll(".link-pages").slideDown('slow');
		$(this).parent().nextAll(".link-posts").hide();
		$(this).parent().nextAll(".link-custom").hide();
   }else if(selectedVal!=""){
		$(this).parent().nextAll(".link-pages").hide();
		$(this).parent().nextAll(".link-posts").hide();
		$(this).parent().next().nextAll(".link-custom").slideDown('slow');
   }else{
		$(this).parent().nextAll(".link-pages").hide();
		$(this).parent().nextAll(".link-posts").hide();
		$(this).parent().nextAll(".link-custom").hide();
   }
});
});

jQuery(document).ready(function($) {

$(".image-content select").change(function(){
   var selectedVal = $(":selected",this).val();
   
   if(selectedVal=="content-limit"){
		$(this).parent().next().nextAll(".content-limit-chars").slideDown('slow');
		$(this).parent().nextAll(".content-custom").hide();
		$(this).parent().nextAll(".wpa-slides").hide();
   }else if(selectedVal=="custom-content"){
		$(this).parent().nextAll(".content-limit-chars").hide();
		$(this).parent().next().nextAll(".content-custom").slideDown('slow');
		$(this).parent().nextAll(".wpa-slides").hide();
   }else if(selectedVal=="wpa-slides"){
		$(this).parent().nextAll(".content-limit-chars").hide();
		$(this).parent().nextAll(".content-custom").hide();
		$(this).parent().next().nextAll(".wpa-slides").slideDown('slow');
   }else {
		$(this).parent().nextAll(".content-limit-chars").hide();
		$(this).parent().nextAll(".content-custom").hide();
		$(this).parent().nextAll(".wpa-slides").hide();
   }
});
});

jQuery(document).ready(function($) {
    $("tr .ap").hide();
    $(".mainSelector select").change(function(){
       var selectedVal = $(":selected",this).val();
       if(selectedVal=="true"){
            $("tr.ap").slideDown('slow');
       }else{
            $("tr.ap").hide();
       }
    });
});

jQuery(function($){
	
	// jquery fade toggle function
	$.fn.toggleFade = function(settings)
	{
		settings = jQuery.extend(
			{
			speedIn: "slow",
			speedOut: settings.speedIn
			}, settings
		);
		return this.each(function()
		{
			var isHidden = jQuery(this).is(":hidden");
			jQuery(this)[ isHidden ? "fadeIn" : "fadeOut" ]( isHidden ? settings.speedIn : settings.speedOut);
		});
	};
	
	// show tool tips on click
	$('a.wpa-help').click(function(e) {
		e.preventDefault();
		var showToolTip = {
			'text-decoration' : 'none',
			'visibility' : 'visible',
			'opacity' : '1',
			'-moz-transition' : 'all 0.2s linear',
			'-webkit-transition' : 'all 0.2s linear',
			'-o-transition' : 'all 0.2s linear',
			'transition' : 'all 0.2s linear'
		}
		var hideToolTip = {
			'visibility' : 'hidden',
			'opacity' : '0',
			'-moz-transition' : 'all 0.4s linear',
			'-webkit-transition' : 'all 0.4s linear',
			'-o-transition' : 'all 0.4s linear',
			'transition' : 'all 0.4s linear'
		}
		$(this).children().css(showToolTip);
		$(this).mouseout(function(){
			$(this).children().css(hideToolTip);
		});
	});
});

jQuery(document).ready(function() {
 
jQuery('#upload_image_button').click(function() {
											  window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_image').val(imgurl);
 tb_remove();
 
 
}
 
 
 tb_show('', 'media-upload.php?post_id=1&amp;type=image&amp;TB_iframe=true');
 return false;
});
 
 
});
 
jQuery(document).ready(function() {
 
jQuery('#upload_image_button2').click(function() {
											   window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_image2').val(imgurl);
 tb_remove();
 
 
}
 
 tb_show('', 'media-upload.php?post_id=1&amp;type=image&amp;TB_iframe=true');
 return false;
});
 
 
 
});