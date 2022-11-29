<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Updates
*
* @link			https://icopydoc.ru/
* @since		1.6.0
*/

class XFGMC_Data_Arr {
	private $data_arr = [
		array('xfgmc_status_sborki', '-1', 'private'),
		array('xfgmc_date_sborki', '0000000001', 'private'), // дата начала сборки
		array('xfgmc_date_sborki_end', '0000000001', 'private'), // дата завершения сборки
		array('xfgmc_date_save_set', '0000000001', 'private'), // дата сохранения настроек плагина
		array('xfgmc_count_products_in_feed', '-1', 'private'), // число товаров, попавших в фид
		array('xfgmc_file_url', '', 'private'),
		array('xfgmc_file_file', '', 'private'),
		array('xfgmc_errors', '', 'private'),
		array('xfgmc_status_cron', 'off', 'private'),
		
		array('xfgmc_run_cron', 'off', 'public'), 
		array('xfgmc_ufup', '0', 'public'), 
		array('xfgmc_feed_assignment', '', 'public'), 
		array('xfgmc_adapt_facebook', 'no', 'public'), 
		array('xfgmc_whot_export', 'all', 'public'), 
		array('xfgmc_desc', 'fullexcerpt', 'public'),
		array('xfgmc_the_content', 'enabled', 'public'),
		array('xfgmc_var_desc_priority', 'on', 'public'),

		array('xfgmc_target_country', 'RU', 'public'),
		array('xfgmc_wooc_currencies', '', 'public'),
		array('xfgmc_main_product', 'other', 'public'),
		array('xfgmc_step_export', '500', 'public'),
		array('xfgmc_cache', 'disabled', 'public'),
		array('xfgmc_def_store_code', '', 'public'),
		array('xfgmc_behavior_onbackorder', 'out_of_stock', 'public'),
		array('xfgmc_g_stock', 'disabled', 'public'),
		array('xfgmc_default_condition', 'new', 'public'),		
		array('xfgmc_skip_missing_products', '0', 'public'),	
		array('xfgmc_skip_backorders_products', '0', 'public'),
		array('xfgmc_no_default_png_products', '0', 'public'),
		array('xfgmc_one_variable', '0', 'public'),
		array('xfgmc_def_shipping_weight_unit', 'kg', 'public'),
		array('xfgmc_def_shipping_country', '', 'public'),
		array('xfgmc_def_delivery_area_type', 'region', 'public'),
		array('xfgmc_def_delivery_area_value', '', 'public'),
		array('xfgmc_def_shipping_service', '', 'public'),
		array('xfgmc_def_shipping_price', '', 'public'),
	
		array('xfgmc_tax_info', 'disabled', 'public'),
		array('xfgmc_def_shipping_label', '', 'public'),
		array('xfgmc_s_return_rule_label', 'disabled', 'public'),
		array('xfgmc_def_return_rule_label', '', 'public'),
		array('xfgmc_def_min_handling_time', '', 'public'),
		array('xfgmc_def_max_handling_time', '', 'public'),
		array('xfgmc_instead_of_id', 'default', 'public'),
		array('xfgmc_product_type', 'disabled', 'public'),
		array('xfgmc_product_type_home', '', 'public'),
		array('xfgmc_sale_price', 'no', 'public'),
		array('xfgmc_gtin', 'disabled', 'public'),
		array('xfgmc_gtin_post_meta', '', 'public'),
		array('xfgmc_mpn', 'disabled', 'public'),
		array('xfgmc_mpn_post_meta', '', 'public'),
		array('xfgmc_age', 'default', 'public'),
		array('xfgmc_age_group_post_meta', '', 'public'),	
		array('xfgmc_brand', 'off', 'public'),
		array('xfgmc_brand_post_meta', '', 'public'), 
		array('xfgmc_color', 'off', 'public'),
		array('xfgmc_material', 'off', 'public'),
		array('xfgmc_pattern', 'off', 'public'),
		array('xfgmc_gender', 'disabled', 'public'),
		array('xfgmc_gender_alt', 'disabled', 'public'),
		array('xfgmc_size', 'disabled', 'public'),
		array('xfgmc_size_type', 'disabled', 'public'),
		array('xfgmc_size_type_alt', 'disabled', 'public'),
		array('xfgmc_size_system', 'disabled', 'public'),
		array('xfgmc_size_system_alt', 'disabled', 'public'),
	];

	public function __construct($blog_title = '', $currency_id_xml = '', $data_arr = array()) {
		if (empty($blog_title)) {
			$blog_title = mb_strimwidth(get_bloginfo('name'), 0, 20);
			$this->blog_title = $blog_title;
		}
		if (empty($currency_id_xml)) {
			if (class_exists('WooCommerce')) {$currency_id_xml = get_woocommerce_currency();} else {$currency_id_xml = 'USD';}
			$this->currency_id_xml = $currency_id_xml;
		}
		if (!empty($data_arr)) {
			$this->data_arr = $data_arr;
		}
		array_push($this->data_arr,
			array('xfgmc_shop_name', $this->blog_title, 'public'),
			array('xfgmc_shop_description', $this->blog_title, 'public'),
			array('xfgmc_default_currency', $this->currency_id_xml, 'public')
		);

		$args_arr = array($this->blog_title, $this->currency_id_xml);
		$this->data_arr = apply_filters('xfgmc_set_default_feed_settings_result_arr_filter', $this->data_arr, $args_arr);
	}

	public function get_data_arr() {
		return $this->data_arr;
	}

	// @return array([0] => opt_key1, [1] => opt_key2, ...)
	public function get_opts_name($whot = '') {
		if ($this->data_arr) {
			$res_arr = array();		
			for ($i = 0; $i < count($this->data_arr); $i++) {
				switch ($whot) {
					case "public":
						if ($this->data_arr[$i][2] === 'public') {
							$res_arr[] = $this->data_arr[$i][0];
						}
					break;
					case "private":
						if ($this->data_arr[$i][2] === 'private') {
							$res_arr[] = $this->data_arr[$i][0];
						}
					break;
					default:
						$res_arr[] = $this->data_arr[$i][0];
				}
			}
			return $res_arr;
		} else {
			return array();
		}
	}

	// @return array(opt_name1 => opt_val1, opt_name2 => opt_val2, ...)
	public function get_opts_name_and_def_date($whot = 'all') {
		if ($this->data_arr) {
			$res_arr = array();		
			for ($i = 0; $i < count($this->data_arr); $i++) {
				switch ($whot) {
					case "public":
						if ($this->data_arr[$i][2] === 'public') {
							$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
						}
					break;
					case "private":
						if ($this->data_arr[$i][2] === 'private') {
							$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
						}
					break;
					default:
						$res_arr[$this->data_arr[$i][0]] = $this->data_arr[$i][1];
				}
			}
			return $res_arr;
		} else {
			return array();
		}
	}

	public function get_opts_name_and_def_date_obj($whot = 'all') {		
		$source_arr = $this->get_opts_name_and_def_date($whot);

		$res_arr = array();	
		foreach($source_arr as $key => $value) {
			$res_arr[] = new XFGMC_Data_Arr_Helper($key, $value); // return unit obj
		}
		return $res_arr;
	}
}
class XFGMC_Data_Arr_Helper {	
	private $opt_name;
	private $opt_def_value;

	function __construct($name = '', $def_value = '') {
		$this->opt_name = $name;
		$this->opt_def_value = $def_value;
	}

	function get_name() {
		return $this->opt_name;
	}

	function get_value() {
		return $this->opt_def_value;
	}
}
?>