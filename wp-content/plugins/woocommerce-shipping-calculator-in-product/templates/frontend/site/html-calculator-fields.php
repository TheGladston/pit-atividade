<div id='shipping-calc'>

	<p><?= get_option('wscip_title', 'Consulte o prazo estimado e valor da entrega.'); ?></p>

	<input type='tel' id='wscp-postcode' autocomplete="off" placeholder="<?= get_option('wscip_placeholder', 'Digite aqui seu CEP') ?>" name='wscp-postcode' class='input-text text' />

	<input type='button' id='wscp-button' class='button wscp-button' value='OK' style="color: <?= get_option('wscip_btn_color_text'); ?>;">

	<a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank">Não sei meu CEP</a>

	<input type='hidden' name='wscp-nonce' id='wscp-nonce' value='<?= wp_create_nonce('wscp-nonce'); ?>'>

	<div id='wscp-response'></div>

</div>