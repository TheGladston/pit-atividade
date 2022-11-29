<?php // https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html https://wp-kama.ru/function/wp_list_table
class XFGMC_Settings_Feed_WP_List_Table extends WP_List_Table {
	private $feed_id;

	function __construct($feed_id) {
		$this->feed_id = $feed_id;

		global $status, $page;
		parent::__construct( array(
			'plural'	=> '', 		// По умолчанию: '' ($this->screen->base); Название для множественного числа, используется во всяких заголовках, например в css классах, в заметках, например 'posts', тогда 'posts' будет добавлен в класс table.
			'singular'	=> '', 		// По умолчанию: ''; Название для единственного числа, например 'post'. 
			'ajax'		=> false,	// По умолчанию: false; Должна ли поддерживать таблица AJAX. Если true, класс будет вызывать метод _js_vars() в подвале, чтобы передать нужные переменные любому скрипту обрабатывающему AJAX события.
			'screen'	=> null, 	// По умолчанию: null; Строка содержащая название хука, нужного для определения текущей страницы. Если null, то будет установлен текущий экран.
		) );
		add_action('admin_footer', array($this, 'admin_header')); // меняем ширину колонок	
	}

	/*	Сейчас у таблицы стандартные стили WordPress. Чтобы это исправить, вам нужно адаптировать классы CSS, которые были 
	*	автоматически применены к каждому столбцу. Название класса состоит из строки «column-» и ключевого имени 
	* 	массива $columns, например «column-isbn» или «column-author».
	*	В качестве примера мы переопределим ширину столбцов (для простоты, стили прописаны непосредственно в HTML разделе head)
	*/
	function admin_header() {
/*		echo '<style type="text/css">'; 
		echo '#xfgmc_google_attribute, .column-xfgmc_google_attribute {width: 7%;}';
		echo '</style>';*/
	}

	/*	Метод get_columns() необходим для маркировки столбцов внизу и вверху таблицы. 
	*	Ключи в массиве должны быть теми же, что и в массиве данных, 
	*	иначе соответствующие столбцы не будут отображены.
	*/
	function get_columns() {
		$columns = array(
//			'cb'							=> '<input type="checkbox" />', // флажок сортировки. см get_bulk_actions и column_cb
			'xfgmc_google_attribute'		=> __('Google attribute', 'xfgmc'),
			'xfgmc_attribute_description'	=> __('Attribute description', 'xfgmc'),
			'xfgmc_value'					=> __('Value', 'xfgmc'),
			'xfgmc_default_value'			=> __('Default value', 'xfgmc'),
		);
		return $columns;
	}
	/*	
	*	Метод вытаскивает из БД данные, которые будут лежать в таблице
	*	$this->table_data();
	*/
	private function table_data() {
		$result_arr = array();

		$feed_id = $this->get_feed_id();

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", 'ID', 'g:id'),
			'xfgmc_attribute_description' 	=> __('Product ID', 'xfgmc'),
			'xfgmc_value' 					=> __('For default the value is automatically added to your feed from WooCommerce Product ID or Variable ID', 'xfgmc'),
			'xfgmc_default_value'			=> $this->get_select_html_v2('xfgmc_instead_of_id', $feed_id, array(
													'default' => __('Default', 'xfgmc'),
													'sku' => __('Sku', 'xfgmc'),
												)),
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Group ID for Variable products', 'xfgmc'), 'g:item_group_id'),
			'xfgmc_attribute_description' 	=> __('Product ID for Variable products', 'xfgmc'),
			'xfgmc_value' 					=> __('The value is automatically added to your feed for variable products from WooCommerce Product ID', 'xfgmc'),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);
		
		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Product name', 'xfgmc'), 'g:title'),
			'xfgmc_attribute_description' 	=> __('Product name', 'xfgmc'),
			'xfgmc_value' 					=> __('The value is automatically added to your feed from the WooCommerce product name', 'xfgmc'),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Product description', 'xfgmc'), 'g:description'),
			'xfgmc_attribute_description' 	=> __('Product description', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_desc_html('xfgmc_desc', $feed_id),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Product link', 'xfgmc'), 'g:link'),
			'xfgmc_attribute_description' 	=> __('Product link', 'xfgmc'),
			'xfgmc_value' 					=> __('The value is automatically added to your feed for variable products from WooCommerce', 'xfgmc'),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);		

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Image link', 'xfgmc'), 'g:image_link'),
			'xfgmc_attribute_description' 	=> __('Image link', 'xfgmc'),
			'xfgmc_value' 					=> __('The value is automatically added to your feed from the "Product Image" block from the product card in WooCommerce', 'xfgmc'),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);		

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Quantity', 'xfgmc'), 'g:quantity'),
			'xfgmc_attribute_description' 	=> __('Add info about stock quantity', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html_v2('xfgmc_g_stock', $feed_id, array(
													'disabled' => __('Disabled', 'xfgmc'),
													'enabled' => __('Enabled', 'xfgmc'),
												)),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Сondition', 'xfgmc'), 'g:condition'),
			'xfgmc_attribute_description' 	=> '',
			'xfgmc_value' 					=> '',
			'xfgmc_default_value'			=> $this->get_select_html_v2('xfgmc_default_condition', $feed_id, array(
													'new' => __('New', 'xfgmc'),
													'refurbished' => __('Refurbished', 'xfgmc'),
													'used' => __('Used', 'xfgmc')
												)),
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Availability', 'xfgmc'), 'g:availability'),
			'xfgmc_attribute_description' 	=> __('Availability', 'xfgmc'),
			'xfgmc_value' 					=> __('The value is automatically added to your feed from the "Inventory" block - "Stock status" from the product card in WooCommerce', 'xfgmc'),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Sale price', 'xfgmc'), 'g:sale_price'),
			'xfgmc_attribute_description' 	=> __('In sale_price indicates the new price of the products, which must necessarily be lower than the old price', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html_v2('xfgmc_sale_price', $feed_id, array(
													'no' => __('No', 'xfgmc'),
													'yes' => __('Yes', 'xfgmc'),
												)),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Tax info', 'xfgmc'), 'g:tax_info'),
			'xfgmc_attribute_description' 	=> __('The value is indicated on the product edit page or category edit page', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html_v2('xfgmc_tax_info', $feed_id, array(
				'disabled' => __('Disabled', 'xfgmc'),
				'enabled' =>  __('Enabled', 'xfgmc'),
			)),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Definition', 'xfgmc').' shipping_label', 'g:shipping_label'),
			'xfgmc_attribute_description' 	=> __('Leave this field blank if you do not want to add a default value', 'xfgmc'),
			'xfgmc_value' 					=> '',
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_def_shipping_label', $feed_id, 'type2')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Definition', 'xfgmc').' return_rule_label', 'g:return_rule_label'),
			'xfgmc_attribute_description' 	=> '',
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_s_return_rule_label', $feed_id, array('default_value' => true, 'post_meta' => true)),
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_def_return_rule_label', $feed_id, 'type3')
		);	

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Definition', 'xfgmc').' min_handling_time', 'g:min_handling_time'),
			'xfgmc_attribute_description' 	=> __('Leave this field blank if you do not want to add a default value', 'xfgmc'),
			'xfgmc_value' 					=> '',
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_def_min_handling_time', $feed_id, 'type2')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Definition', 'xfgmc').' max_handling_time', 'g:max_handling_time'),
			'xfgmc_attribute_description' 	=> __('Leave this field blank if you do not want to add a default value', 'xfgmc'),
			'xfgmc_value' 					=> '',
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_def_max_handling_time', $feed_id, 'type2')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('GTIN', 'xfgmc'), 'g:gtin'),
			'xfgmc_attribute_description' 	=> __('GTIN', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_gtin', $feed_id, array('no' => true, 'post_meta' => true, 'sku' => true, 'germanized' => true)),
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_gtin_post_meta', $feed_id)
		);
		
		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('MPN', 'xfgmc'), 'g:mpn'),
			'xfgmc_attribute_description' 	=> __('MPN', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_mpn', $feed_id, array('no' => true, 'post_meta' => true, 'sku' => true, 'germanized' => true)),
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_mpn_post_meta', $feed_id)
		);
		
		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Age', 'xfgmc'), 'g:age'),
			'xfgmc_attribute_description' 	=> __('Age', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_age', $feed_id, array('default_value' => true, 'post_meta' => true)),
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_age_group_post_meta', $feed_id, 'type3')
		);		
		
		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Brand', 'xfgmc'), 'g:brand'),
			'xfgmc_attribute_description' 	=> __('Brand', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_brand', $feed_id, array('default_value' => true, 'post_meta' => true, 'brands' => true)),
			'xfgmc_default_value'			=> $this->get_input_html('xfgmc_brand_post_meta', $feed_id, 'type3')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Color', 'xfgmc'), 'g:color'),
			'xfgmc_attribute_description' 	=> __('Color', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_color', $feed_id, array()),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Material', 'xfgmc'), 'g:material'),
			'xfgmc_attribute_description' 	=> __('Material', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_material', $feed_id, array()),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Pattern', 'xfgmc'), 'g:pattern'),
			'xfgmc_attribute_description' 	=> __('Pattern', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_pattern', $feed_id, array()),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Gender', 'xfgmc'), 'g:gender'),
			'xfgmc_attribute_description' 	=> __('Gender', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_gender', $feed_id, array()),
			'xfgmc_default_value'			=> $this->get_select_html_v2('xfgmc_gender_alt', $feed_id, array(
												'disabled' => __('Disabled', 'xfgmc'),
												'male' => 'Male',
												'female' => 'Female',
												'unisex' => 'Unisex',
											)),
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Size', 'xfgmc'), 'g:size'),
			'xfgmc_attribute_description' 	=> __('Size', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_size', $feed_id, array()),
			'xfgmc_default_value'			=> __('There are no default settings', 'xfgmc')
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Size type', 'xfgmc'), 'g:size_type'),
			'xfgmc_attribute_description' 	=> __('Size type', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_size_type', $feed_id, array()),
			'xfgmc_default_value'			=> $this->get_select_html_v2('xfgmc_size_type_alt', $feed_id, array(
													'disabled' => __('Disabled', 'xfgmc'),
													'regular' => 'Regular',
													'petite' => 'petite',
													'plus' => 'plus',
													'bigandtall' => 'Big and tall',
													'maternity' => 'Maternity',
												)),
		);

		$result_arr[] = array(
			'xfgmc_google_attribute' 		=> sprintf("<span class='xfgmc_bold'>%1\$s</span><br/>[%2\$s]", __('Size system', 'xfgmc'), 'g:size_system'),
			'xfgmc_attribute_description' 	=> __('Size system', 'xfgmc'),
			'xfgmc_value' 					=> $this->get_select_html('xfgmc_size_system', $feed_id, array()),
			'xfgmc_default_value'			=> $this->get_select_html_v2('xfgmc_size_system_alt', $feed_id, array(
												'disabled' => __('None', 'xfgmc'),
												'AU' => 'AU',
												'BR' => 'BR',
												'CN' => 'CN',
												'DE' => 'DE',
												'EU' => 'EU',
												'FR' => 'FR',
												'IT' => 'IT',
												'JP' => 'JP',
												'MEX' => 'MEX',
												'UK' => 'UK',
												'US' => 'US'
											)),
		);
		/* '[custom_label_0]<br />
		[custom_label_1]<br />
		[custom_label_2]<br />
		[custom_label_3]<br />
		[custom_label_4]<br />'; */

		return $result_arr;
	}

	private function get_input_html($opt_name, $feed_id = '1', $type_placeholder = 'type1') {
		$opt_value = xfgmc_optionGET($opt_name, $feed_id, 'set_arr');

		switch ($type_placeholder) {
			case 'type1':
				$placeholder = __('Name post_meta', 'xfgmc');
				break;
			case 'type2':
				$placeholder = __('Default value', 'xfgmc');
				break;
			case 'type3':
				$placeholder = __('Value', 'xfgmc') .' / '. __('Name post_meta', 'xfgmc');
				break;
			default:
				$placeholder = __('Name post_meta', 'xfgmc');
		}

		return '<input type="text" maxlength="25" name="'.$opt_name.'" id="'.$opt_name.'" value="'.$opt_value.'" placeholder="'.$placeholder.'" />';
	}
	
	private function get_select_html_v2($opt_name, $feed_id = '1', $otions_arr = array()) {
		$opt_value = xfgmc_optionGET($opt_name, $feed_id, 'set_arr');

		$res = '<select name="'.$opt_name.'" id="'.$opt_name.'">';
		foreach ($otions_arr as $key => $value) {
			$res .= '<option value="'.$key.'" '.selected($opt_value, $key, false).'>'.$value.'</option>';
		}
		$res .= '</select>';
		return $res;
	}

	private function get_select_desc_html($opt_name, $feed_id = '1', $otions_arr = array()) {
		$opt_value = xfgmc_optionGET($opt_name, $feed_id, 'set_arr');

		$res = '<select name="'.$opt_name.'" id="'.$opt_name.'">
					<option value="excerpt" '.selected($opt_value, 'excerpt', false).'>'. __('Only Excerpt description', 'xfgmc').'</option>
					<option value="full" '.selected($opt_value, 'full', false).'>'. __('Only Full description', 'xfgmc').'</option>
					<option value="excerptfull" '.selected($opt_value, 'excerptfull', false).'>'. __('Excerpt or Full description', 'xfgmc').'</option>
					<option value="fullexcerpt" '.selected($opt_value, 'fullexcerpt', false).'>'. __('Full or Excerpt description', 'xfgmc').'</option>
					<option value="excerptplusfull" '.selected($opt_value, 'excerptplusfull', false).'>'. __('Excerpt plus Full description', 'xfgmc').'</option>
					<option value="fullplusexcerpt" '.selected($opt_value, 'fullplusexcerpt', false).'>'. __('Full plus Excerpt description', 'xfgmc').'</option>';
					$res = apply_filters('xfgmc_append_select_xfgmc_desc_filter', $res, $opt_value, $feed_id); 
		$res .= '</select>';
		return $res;
	}

	private function get_select_html($opt_name, $feed_id = '1', $otions_arr = array()) {
		$opt_value = xfgmc_optionGET($opt_name, $feed_id, 'set_arr');

		$res = '<select name="'.$opt_name.'" id="'.$opt_name.'">
					<option value="disabled" '.selected($opt_value, 'disabled', false).'>'. __('Disabled', 'xfgmc').'</option>';

					if (isset($otions_arr['yes'])) {
						$res .= '<option value="yes" '.selected($opt_value, 'yes', false).'>'. __('Yes', 'xfgmc').'</option>';
					}

					if (isset($otions_arr['no'])) {
						$res .= '<option value="no" '.selected($opt_value, 'no', false).'>'. __('No', 'xfgmc').'</option>';
					}

					if (isset($otions_arr['sku'])) {
						$res .= '<option value="sku" '. selected($opt_value, 'sku', false).'>'. __('Substitute from SKU', 'xfgmc').'</option>';
					}

					if (isset($otions_arr['post_meta'])) {
						$res .= '<option value="post_meta" '. selected($opt_value, 'post_meta', false).'>'. __('Substitute from post meta', 'xfgmc').'</option>';
					}

					if (isset($otions_arr['default_value'])) {
						$res .= '<option value="default_value" '.selected($opt_value, 'default_value', false).'>'. __('Default value from field', 'xfgmc').' "'.__('Default value', 'xfgmc').'"</option>';
					}

					if (class_exists('WooCommerce_Germanized')) {
						if (isset($otions_arr['germanized'])) {
							$res .= '<option value="germanized" '. selected($opt_value, 'germanized', false).'>'. __('Substitute from', 'xfgmc'). 'WooCommerce Germanized</option>';
						}
					}	
					
					if (isset($otions_arr['brands'])) {
						if (is_plugin_active('perfect-woocommerce-brands/perfect-woocommerce-brands.php') || is_plugin_active('perfect-woocommerce-brands/main.php') || class_exists('Perfect_Woocommerce_Brands')) {
							$res .= '<option value="sfpwb" '. selected($opt_value, 'sfpwb', false).'>'. __('Substitute from', 'xfgmc'). 'Perfect Woocommerce Brands</option>';
						}
						if (is_plugin_active('premmerce-woocommerce-brands/premmerce-brands.php')) {
							$res .= '<option value="premmercebrandsplugin" '. selected($opt_value, 'premmercebrandsplugin', false).'>'. __('Substitute from', 'xfgmc'). 'Premmerce Brands for WooCommerce</option>';
						}
						if (is_plugin_active('woocommerce-brands/woocommerce-brands.php')) {
							$res .= '<option value="woocommerce_brands" '. selected($opt_value, 'woocommerce_brands', false).'>'. __('Substitute from', 'xfgmc'). 'WooCommerce Brands</option>';
						}
						if (class_exists('woo_brands')) {
							$res .= '<option value="woo_brands" '. selected($opt_value, 'woo_brands', false).'>'. __('Substitute from', 'xfgmc'). 'Woocomerce Brands Pro</option>';
						}	
					}

					foreach (xfgmc_get_attributes() as $attribute) {
						$res .= '<option value="'.$attribute['id'].'" '.selected($opt_value, $attribute['id'], false).'>'.$attribute['name'].'</option>';
					}
		$res .= '</select>';
		return $res;
	}
	/*
	*	prepare_items определяет два массива, управляющие работой таблицы:
	*	$hidden определяет скрытые столбцы https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html#screen-options
	*	$sortable определяет, может ли таблица быть отсортирована по этому столбцу.
	*
	*/
	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns(); // вызов сортировки
		$this->_column_headers = array($columns, $hidden, $sortable);
		// блок пагинации пропущен
		$this->items = $this->table_data();
	}
	/*
	* 	Данные таблицы.
	*	Наконец, метод назначает данные из примера на переменную представления данных класса — items.
	*	Прежде чем отобразить каждый столбец, WordPress ищет методы типа column_{key_name}, например, function column_xfgmc_attribute_description. 
	*	Такой метод должен быть указан для каждого столбца. Но чтобы не создавать эти методы для всех столбцов в отдельности, 
	*	можно использовать column_default. Эта функция обработает все столбцы, для которых не определён специальный метод:
	*/ 
	function column_default($item, $column_name) {
		switch($column_name) {
			case 'xfgmc_google_attribute':
			case 'xfgmc_attribute_description':
			case 'xfgmc_value':
			case 'xfgmc_default_value':
				return $item[$column_name];
			default:
				return print_r($item, true) ; // Мы отображаем целый массив во избежание проблем
		}
	}
	// Флажки для строк должны быть определены отдельно. Как упоминалось выше, есть метод column_{column} для отображения столбца. cb-столбец – особый случай:
/*	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="checkbox_xml_file[]" value="%s" />', $item['xfgmc_google_attribute']
		);
	}*/

	private function get_feed_id() {
		return $this->feed_id;
	}
}
?>