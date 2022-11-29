var owl_slider = {
    init: function () {

    },
    update_owl_slider_options: function () {

        var dados = {};
        dados.error = false;
        dados.alert = 'error';
        dados.action = 'update_owl_slider_options';
        dados.version = jQuery('#version').val();
        dados.add_script = jQuery('#add_script').is(':checked');
        dados.add_css = jQuery('#add_css').is(':checked');
        dados.nav = jQuery('#nav').is(':checked');
        dados.dots = jQuery('#dots').is(':checked');
        
        dados.dw = jQuery('#dw').val();
        dados.dh = jQuery('#dh').val();
        
        dados.mw = jQuery('#mw').val();
        dados.mh = jQuery('#mh').val();

        jQuery.ajax({
            url: ajax_object_owl_slider.ajax_url,
            type: 'post',
            data: dados,
            dataType: 'json',
            success: function (data) {
                owl_slider.fn_notice('success', 'Gravado com sucesso.');
            },
            error: function (data) {
                owl_slider.fn_notice('error', 'Algo deu errado.');
            },
        });

        return false;
    },
    fn_notice: function (alert, message) {
        var notice = '<div class="notice notice-' + alert + ' is-dismissible custom-dismissible"><p>' + message + '</p><button type="button" class="notice-dismiss" onclick="jQuery(this).parent().fadeOut();"><span class="screen-reader-text">Dispensar este aviso.</span></button></div>';
        jQuery('.wp-header-end').after(notice);
        jQuery('html, body').animate({
            scrollTop: jQuery(".wp-header-end").offset().top - 50
        }, 1000);
    },
}
jQuery(document).ready(function () {
    owl_slider.init();
});