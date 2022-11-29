jQuery(document).ready(function() {
	jQuery('body').on('click', '.ocscw_open', function() {

		var loader_image = jQuery(this).data("image");

		jQuery('body').addClass("ocscw_body_sizechart");
		jQuery('body').append('<div class="ocscw_loading"><img src="'+ loader_image +'" class="ocscw_loader"></div>');
		var loading = jQuery('.ocscw_loading');
		loading.show();

        var product_id = jQuery(this).data("id");
        var chart_id = jQuery(this).data("cid");
        var current = jQuery(this);

        jQuery.ajax({
	        url: ocscw_object.ocscw_ajax_url,
            type:'POST',
	        data:'action=ocscw_sizechart&product_id='+product_id+'&chart_id='+chart_id,
	        success : function(response) {
	        	var loading = jQuery('.ocscw_loading');
				loading.remove();

	            jQuery("#ocscw_sizechart_popup").css("display", "block");
	            jQuery("#ocscw_sizechart_popup").html(response);
	            jQuery('#ocscw_schart_popup_cls').css("display", "block");	
	        },
	        error: function() {
	            alert('Error occured');
	        }
	    });
       return false;
    });

	jQuery(document).on('click','.ocscw_popup_close',function() {
		jQuery("#ocscw_sizechart_popup").css("display", "none");
		jQuery('#ocscw_schart_popup_cls').css("display", "none");
		jQuery('body').removeClass("ocscw_body_sizechart");
	});

	jQuery("body").on('click', '#ocscw_schart_popup_cls', function() {
    	jQuery('#ocscw_sizechart_popup').css("display", "none");
        jQuery('#ocscw_schart_popup_cls').css("display", "none");
        jQuery('body').removeClass("ocscw_body_sizechart");
    });


	jQuery("body").on("click", ".ocscw_schart_sidpp_overlay", function() {
		jQuery(".ocscw_schart_sdpopup_main").removeClass("active");
      	jQuery(".ocscw_schart_sidpp_overlay").removeClass("active");
      	jQuery("body").removeClass("ocscw_sdpp_body");
	});
  	//sizingpopup js end


	jQuery('body').on('click','ul.ocscw_front_tabs li', function() {
		var closesta = jQuery(this).closest(".ocscw_tableclass");
        var tab_id = jQuery(this).attr('data-tab');
        closesta.find('ul.ocscw_front_tabs li').removeClass('current');
        closesta.find('.ocscw_front_tab_content').removeClass('current');
        jQuery(this).addClass('current');
        closesta.find("#"+tab_id).addClass('current');
    })

    jQuery('body').on('click','ul.ocscw_sdpp_front_tabs li', function() {
		var closesta = jQuery(this).closest(".ocscw_sdpp_table");
        var tab_id = jQuery(this).attr('data-tab');
        closesta.find('ul.ocscw_sdpp_front_tabs li').removeClass('current');
        closesta.find('.ocscw_sdpp_frtab_content').removeClass('current');
        jQuery(this).addClass('current');
        closesta.find("#"+tab_id).addClass('current');
    })
})