<?php

/**
 * Gateway class
 *
 * @package Pix_For_WooCommerce/Classes/Gateway
 * @version 1.3.6
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Gateway.
 */
class WC_Pix_Gateway extends WC_Payment_Gateway
{

	/**
	 * Constructor for the gateway.
	 */
	public function __construct()
	{
		$this->domain = 'woocommerce-pix';
		$this->id = 'pix_gateway';
		$color = $this->get_option('icon_color') ? $this->get_option('icon_color') : 'black';
		$this->icon = apply_filters('woocommerce_gateway_icon', WC_PIX_PLUGIN_URL . 'assets/icon-pix-' . $color . '.png');
		$this->has_fields = false;
		$this->method_title = __('Pix', $this->domain);
		$this->method_description = __('Receba pagamentos via PIX', $this->domain);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title = $this->get_option('title');
		$this->description = $this->get_option('description');
		$this->instructions = $this->get_option('instructions');
		$this->key = $this->get_option('key');
		$this->prefix_transaction_id = $this->get_option('prefix_transaction_id') ? $this->get_option('prefix_transaction_id') : 'ID';
		$this->merchant = $this->get_option('merchant');
		$this->city = $this->get_option('city');
		$this->whatsapp = $this->get_option('whatsapp');
		$this->telegram = $this->get_option('telegram');
		$this->email = $this->get_option('email');
		$this->send_on_hold_email = $this->get_option('send_on_hold_email');

		// Actions
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
		if ('yes' == $this->send_on_hold_email) {
			add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 4);
		}
		if (is_account_page()) {
			add_action('woocommerce_order_details_after_order_table', array($this, 'order_page'));
		}
	}

	/**
	 * Get templates path.
	 *
	 * @return string
	 */
	public static function get_templates_path()
	{
		return plugin_dir_path(WC_PIX_PLUGIN_FILE) . 'templates/';
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	public function using_supported_currency()
	{
		return 'BRL' === get_woocommerce_currency();
	}

	/**
	 * Get WhatsApp.
	 *
	 * @return string
	 */
	public function get_whatsapp()
	{
		return $this->whatsapp;
	}

	/**
	 * Get Telegram.
	 *
	 * @return string
	 */
	public function get_telegram()
	{
		return $this->telegram;
	}

	/**
	 * Get Email.
	 *
	 * @return string
	 */
	public function get_email()
	{
		return $this->email;
	}

	/**
	 * Get key.
	 *
	 * @return string
	 */
	public function get_key()
	{
		return $this->key;
	}

	/**
	 * Get prefix_transaction_id.
	 *
	 * @return string
	 */
	public function get_prefix_transaction_id()
	{
		return $this->prefix_transaction_id;
	}

	/**
	 * Get lojista.
	 *
	 * @return string
	 */
	public function get_merchant()
	{
		return $this->merchant;
	}

	/**
	 * Get city.
	 *
	 * @return string
	 */
	public function get_city()
	{
		return $this->city;
	}

	/**
	 * Returns a value indicating the the Gateway is available or not. It's called
	 * automatically by WooCommerce before allowing customers to use the gateway
	 * for payment.
	 *
	 * @return bool
	 */
	public function is_available()
	{
		// Test if is valid for use.
		$available = 'yes' === $this->get_option('enabled') && '' !== $this->get_key() && '' !== $this->get_city() && '' !== $this->get_merchant() && $this->using_supported_currency();

		return $available;
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'              => array(
				'title'   => __('Habilitar/Desabilitar', 'woocommerce-pix'),
				'type'    => 'checkbox',
				'label'   => __('Habilitar Pix', 'woocommerce-pix'),
				'default' => 'yes',
			),
			'title'                => array(
				'title'       => __('T??tulo', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Representa o t??tulo vis??vel para o usu??rio comprador', 'woocommerce-pix'),
				'desc_tip'    => true,
				'default'     => __('Fa??a um Pix', 'woocommerce-pix'),
			),
			'icon_color'                => array(
				'title'       => __('Cor do ??cone do PIX', 'woocommerce-pix'),
				'type'        => 'select',
				'description' => __('Cor do ??cone que vai aparecer ao lado do t??tulo', 'woocommerce-pix'),
				'desc_tip'    => true,
				'options'     => array(
					'black' => 'Preto',
					'white' => 'Branco',
					'green' => 'Verde'
				),
				'default'     => 'green',
			),
			'description'          => array(
				'title'       => __('Descri????o', 'woocommerce-pix'),
				'type'        => 'textarea',
				'description' => __('Representa a descri????o que o usu??rio ver?? na tela de checkout', 'woocommerce-pix'),
				'default'     => __('Ao finalizar a compra, iremos gerar o c??digo Pix para pagamento na pr??xima tela e disponibilizar um n??mero WhatsApp para voc?? compartilhar o seu comprovante.', 'woocommerce-pix'),
			),
			'integration'          => array(
				'title'       => __('Integra????o', 'woocommerce-pix'),
				'type'        => 'title',
				'description' => '',
			),
			'key'                => array(
				'title'       => __('Chave Pix (obrigat??rio)', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Por favor, informe sua chave PIX. Ela ?? necess??ria para poder processar os pagamentos.', 'woocommerce-pix'),
				'default'     => '',
				'required'	  => true,
			),
			'prefix_transaction_id' => array(
				'title'       => __('Prefixo Transaction ID (obrigat??rio)', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Por favor, informe o prefixo da Transaction ID.<br>M??ximo de 10 caracteres.<br>Ela ?? importante identificar o pagamento no extrato do PIX.<br>Somente alfan??merico (tabela ASCII): <code>A-Z</code>, <code>a-z</code> e <code>0-9</code>.', 'woocommerce-pix'),
				'default'     => '',
				'required'	  => true,
				'custom_attributes' => [
					'maxlength' => 10
				]
			),
			'merchant'                => array(
				'title'       => __('Nome do titular (obrigat??rio)', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Por favor, informe o nome do titular da conta banc??ria da chave PIX cadastrada.<br>M??ximo de 25 caracteres.<br>N??o abreviar o nome, apenas descartar os caracteres que excederem esse limite.<br>Retirar acentua????o para melhor compatibilidade entre bancos: utilize apenas <code>A-Z</code>, <code>a-z</code> e <code>espa??o</code>.', 'woocommerce-pix'),
				'default'     => '',
				'required'	  => true,
				'custom_attributes' => [
					'maxlength' => 25
				]
			),
			'city'                => array(
				'title'       => __('Cidade do titular (obrigat??rio)', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Por favor, informe a cidade do titular da conta banc??ria da chave PIX cadastrada.<br>M??ximo de 15 caracteres.<br>N??o abreviar a cidade, apenas descartar os caracteres que excederem esse limite.<br>Retirar acentua????o para melhor compatibilidade entre bancos: utilize apenas <code>A-Z</code>, <code>a-z</code> e <code>espa??o</code>', 'woocommerce-pix'),
				'default'     => '',
				'required'	  => true,
				'custom_attributes' => [
					'maxlength' => 15
				]
			),
			'whatsapp'                => array(
				'title'       => __('WhatsApp para contato', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Seu n??mero de WhatsApp ser?? informado ao cliente para compartilhar o comprovante de pagamento. Modelo: 5548999999999', 'woocommerce-pix'),
				'default'     => '',
			),
			'telegram'                => array(
				'title'       => __('Telegram para contato', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Seu username do Telegram ser?? informado ao cliente para compartilhar o comprovante de pagamento. Informe o username sem @.
				Exemplo: jondoe.', 'woocommerce-pix'),
				'default'     => '',
			),
			'email'                => array(
				'title'       => __('Email para contato', 'woocommerce-pix'),
				'type'        => 'email',
				'description' => __('Seu email ser?? informado ao cliente para compartilhar o comprovante de pagamento.', 'woocommerce-pix'),
				'default'     => get_option('admin_email'),
			),
			'instructions' => array(
				'title'       => __('Instru????es', 'woocommerce-pix'),
				'type'        => 'textarea',
				'description' => __('Instru????es na p??gina de obrigado pela compra', 'woocommerce-pix'),
				'default'     => 'Utilize o seu aplicativo favorito do Pix para ler o QR Code ou copiar o c??digo abaixo e efetuar o pagamento.',
			),
			'send_on_hold_email' => array(
				'title'       => __('Enviar o QR Code e o c??digo Pix no e-mail para pagamento?', 'woocommerce-pix'),
				'type'    => 'checkbox',
				'label'   => __('Enviar o QR Code e o c??digo Pix no e-mail para pagamento?', 'woocommerce-pix'),
				'description' => __('A imagem de cada QR Code ser?? salva no seu servidor para ser renderizada no e-mail.', 'woocommerce-pix'),
				'default' => 'no',
			),
		);
	}

	/**
	 * Admin page.
	 */
	public function admin_options()
	{

		include dirname(__FILE__) . '/admin/views/html-admin-page.php';
	}

	/**
	 * Send email notification.
	 *
	 * @param string $subject Email subject.
	 * @param string $title   Email title.
	 * @param string $message Email message.
	 */
	protected function send_email($subject, $title, $message)
	{
		$mailer = WC()->mailer();

		$mailer->send(get_option('admin_email'), $subject, $mailer->wrap_message($title, $message));
	}

	/**
	 * Payment fields.
	 */
	public function payment_fields()
	{

		$description = $this->get_description();
		if ($description) {
			echo wpautop(wptexturize($description)); // WPCS: XSS ok.
		}
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array
	 */
	public function process_payment($order_id)
	{
		$order = wc_get_order($order_id);

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status('on-hold', __('Awaiting offline payment', $this->domain));

		// Remove cart
		WC()->cart->empty_cart();

		// Reduce stock for billets.
		if (function_exists('wc_reduce_stock_levels')) {
			wc_reduce_stock_levels($order_id);
		}

		// Return thankyou redirect
		return array(
			'result'     => 'success',
			'redirect'    => $this->get_return_url($order)
		);
	}

	/**
	 * Render Pix code.
	 *
	 * @param int $order_id Order ID.
	 */
	public function render_pix($order_id)
	{
		$order = wc_get_order($order_id);
		if ($order->get_payment_method() != 'pix_gateway') {
			return;
		}

		$pix = $this->generate_pix($order_id);
		if (!empty($pix)) { ?>
			<div class="wcpix-container" style="text-align: center;margin: 20px 0">
				<div class="wcpix-instructions">
					<?php
					if ($this->instructions) {
						echo wpautop(wptexturize($this->instructions));
					}
					?>
				</div>
				<input type="hidden" value="<?php echo wp_kses_post($pix['link']); ?>" id="copiar">
				<img style="cursor:pointer; display: initial;" class="wcpix-img-copy-code" onclick="copyCode()" src="<?php echo wp_kses_post($pix['image']); ?>" alt="QR Code" />
				<br>
				<p class="wcpix-p" style="font-size: 14px;margin-bottom:0;word-break: break-all;"><?php echo wp_kses_post($pix['link']); ?></p>
				<br><button class="button wcpix-button-copy-code" style="margin-bottom: 20px;margin-left: auto;margin-right: auto;" onclick="copyCode()"><?php echo wp_kses_post(__('Clique aqui para copiar o C??digo acima', 'woocommerce-pix')); ?> </button><br>
				<div class="wcpix-response-output inactive" style="margin: 2em 0.5em 1em;padding: 0.2em 1em;border: 2px solid #46b450;display: none;" aria-hidden="true" style=""><?php echo wp_kses_post(__('O c??digo foi copiado para a ??rea de transfer??ncia.', 'woocommerce-pix')); ?></div>
				<?php
				if ($this->whatsapp || $this->telegram || $this->email) {
					echo wp_kses_post('<br>' . __('<span class="wcpix-explain">Voc?? pode compartilhar conosco o comprovante via:</span>', 'woocommerce-pix'));
					if ($this->whatsapp) {
						echo wp_kses_post(' <a class="wcpix-whatsapp" style="margin-right: 15px;" target="_blank" href="https://wa.me/' . $this->whatsapp . '?text=Segue%20meu%20comprovante%20para%20o%20pedido%20' . $order_id . '"> WhatsApp </a>');
					}
					if ($this->telegram) {
						echo wp_kses_post(' <a class="wcpix-telegram" style="margin-right: 15px;" target="_blank" href="https://t.me/' . $this->telegram . '?text=Segue%20meu%20comprovante%20para%20o%20pedido%20' . $order_id . '">Telegram </a>');
					}
					if ($this->email) {
						echo wp_kses_post(' <a class="wcpix-email" style="margin-right: 15px;" target="_blank" href="mailto:' . $this->email . '?subject=Comprovante%20pedido%20' . $order_id . '&body=Segue%20meu%20comprovante%20anexo%20para%20o%20pedido%20' . $order_id . '">Email.</a>');
					}
				}
				?>
			</div>
			<script>
				function copyCode() {
					var copyText = document.getElementById("copiar");
					copyText.type = "text";
					copyText.select();
					copyText.setSelectionRange(0, 99999)
					document.execCommand("copy");
					copyText.type = "hidden";

					if (jQuery("div.wcpix-response-output")) {
						jQuery("div.wcpix-response-output").show();
					} else {
						alert('O c??digo foi copiado para a ??rea de transfer??ncia.');
					}

					return false;
				}
			</script>
<?php
		}
	}

	/**
	 * Order Page message.
	 *
	 * @param int $order Order.
	 */
	public function order_page($order)
	{
		$order_id = $order->get_id();
		return $this->render_pix($order_id);
	}

	/**
	 * Thank You page message.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_page($order_id)
	{
		return $this->render_pix($order_id);
	}

	public function generate_pix($order_id)
	{
		$order = wc_get_order($order_id);
		$pix = new ICPFW_QRCode();
		$pix->chave($this->key);
		$pix->valor($order->get_total());
		$pix->cidade($this->city);
		$pix->lojista($this->merchant);
		$pix->moeda(986); // Real brasileiro (BRL) - Conforme ISO 4217: https://pt.wikipedia.org/wiki/ISO_4217
		$pix->txId($this->prefix_transaction_id . $order_id);
		$pix->info($this->prefix_transaction_id . $order_id);
		$link = $pix->toCode();
		$image = $pix->toImage();
		$pix = array(
			'image' => $image,
			'link' => $link,
			'instructions' => $this->instructions,
		);
		return $pix;
	}

	/**
	 * Add content to the WC emails.
	 */
	public function email_instructions($order, $sent_to_admin, $plain_text, $email)
	{
		if ($order->get_payment_method() === $this->id && get_class($email) === 'WC_Email_Customer_On_Hold_Order') {
			$pix = $this->generate_pix($order->get_id());
			wc_get_template(
				'email-on-hold-pix.php',
				$pix,
				'',
				$this->get_templates_path()
			);
		}
	}
}
