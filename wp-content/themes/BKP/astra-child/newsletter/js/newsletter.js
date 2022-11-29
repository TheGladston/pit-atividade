jQuery(document).ready(function () {
    jQuery('#newsletter').on('submit', function (e) {
        jQuery('#newsletter #submit i').attr('class', 'fa fa-refresh fa-spin');
        e.preventDefault();

        var dados = {};
        dados.error = false;
        dados.alert = 'warning';
        dados.action = 'newsletter';

        dados.nome = jQuery('#nome').val();
        dados.email = jQuery('#email').val();

        if (dados.nome === '' && jQuery('#newsletter #nome').size() > 0) {
            dados.mensagem = 'Informe seu nome.';
            dados.error = true;
        } else if (dados.email === '' && jQuery('#newsletter #email').size() > 0) {
            dados.mensagem = 'Informe seu e-mail.';
            dados.error = true;
        }

        if (dados.error === true) {

            jQuery('#newsletter #submit i').attr('class', 'fa fa-paper-plane');
            jQuery('#mensagem').attr('class', 'alert alert-' + dados.alert);
            jQuery('#mensagem').html('<p>' + dados.mensagem + '</p>');
            jQuery('#mensagem').fadeIn();

        } else {

            jQuery.post(ajax_object.ajax_url, dados, function (data) {

                if (data.status == 1) {

                    var mensagem = data.mensagem;
                    var alert = 'danger';

                } else {

                    var mensagem = data.mensagem;
                    var alert = 'success';

                }

                jQuery('#newsletter #submit i').attr('class', 'fa fa-paper-plane');
                jQuery('#mensagem').attr('class', 'alert alert-' + alert);
                jQuery('#mensagem').html('<p>' + mensagem + '</p>');
                jQuery('#mensagem').fadeIn();

            }, 'json');

        }
        return false;
    });
});
