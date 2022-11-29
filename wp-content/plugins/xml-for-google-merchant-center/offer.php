<?php if (!defined('ABSPATH')) {exit;}
include_once ABSPATH . 'wp-admin/includes/plugin.php'; // без этого не будет работать вне адмники is_plugin_active
function xfgmc_feed_header($numFeed = '1') {
 xfgmc_error_log('FEED № '.$numFeed.'; Стартовала xfgmc_feed_header; Файл: offer.php; Строка: '.__LINE__, 0);

 $xfgmc_cache = xfgmc_optionGET('xfgmc_cache', $numFeed, 'set_arr');
 if ($xfgmc_cache === 'enabled') {
 	$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT (Unix формат)
 	xfgmc_optionUPD('xfgmc_date_save_set', $unixtime, $numFeed, 'yes', 'set_arr');
 }

 $result_xml = '';
 $unixtime = current_time('Y-m-d H:i'); // время в unix формате 
 xfgmc_optionUPD('xfgmc_date_sborki', $unixtime, $numFeed, 'yes', 'set_arr');		
 $shop_name = stripslashes(xfgmc_optionGET('xfgmc_shop_name', $numFeed, 'set_arr'));
 $shop_description = stripslashes(xfgmc_optionGET('xfgmc_shop_description', $numFeed, 'set_arr'));
 $result_xml .= '<?xml version="1.0"?>'.PHP_EOL;
 $result_xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">'.PHP_EOL;
 $result_xml .= '<channel>'.PHP_EOL;  
 $result_xml .= '<title>'.htmlspecialchars($shop_name).'</title>'.PHP_EOL;
 $result_xml .= "<link>".home_url('/')."</link>".PHP_EOL; 
 $result_xml .= '<description>'.htmlspecialchars($shop_description).'</description>'.PHP_EOL;	
 /* общие параметры */		
 /* end общие параметры */		
 do_action('xfgmc_before_offers', $numFeed);
 return $result_xml;
}

function xfgmc_unit($postId, $numFeed = '1') {
 /* индивидуальные параметры товара */
 xfgmc_error_log('FEED № '.$numFeed.'; Стартовала xfgmc_unit. postId = '.$postId.'; Файл: offer.php; Строка: '.__LINE__, 0);
 $result_arr = array('result_xml' => '', 'ids_in_xml' => '', 'skip_reason' => array());
 $result_xml = ''; $ids_in_xml = ''; $skip_reason = array(); $stop_flag = false; $skip_flag = false; 

 if (class_exists('WOOCS')) { 
	$xfgmc_wooc_currencies = xfgmc_optionGET('xfgmc_wooc_currencies', $numFeed, 'set_arr');
	if ($xfgmc_wooc_currencies !== '') {
		global $WOOCS;
		$WOOCS->set_currency($xfgmc_wooc_currencies);
	}
 }
			
 $product = wc_get_product($postId);
 if ($product == null) {
	xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к get_post вернула null; Файл: offer.php; Строка: '.__LINE__, 0); 
	$result_arr['skip_reason'][] = __('There is no product with this ID', 'xfgmc');
	return $result_arr;
 }
 
 if ($product->is_type('grouped')) {
	xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к сгруппированный; Файл: offer.php; Строка: '.__LINE__, 0); 
	$result_arr['skip_reason'][] = __('Product is grouped', 'xfgmc');
	return $result_arr;
 }
 
 // что выгружать
 $xfgmc_whot_export = xfgmc_optionGET('xfgmc_whot_export', $numFeed, 'set_arr');
 if ($product->is_type('variable')) {	
	if ($xfgmc_whot_export === 'simple') {
		xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к вариативный; Файл: offer.php; Строка: '.__LINE__, 0); 
		$result_arr['skip_reason'][] = __('Product is simple', 'xfgmc');
		return $result_arr;
	}
 }
 if ($product->is_type('simple')) {	
	if ($xfgmc_whot_export === 'variable') {
		xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к простой; Файл: offer.php; Строка: '.__LINE__, 0); 
		$result_arr['skip_reason'][] = __('Product is variable', 'xfgmc');
		return $result_arr;
	}
 }

 $special_data_for_flag = '';
 $special_data_for_flag = apply_filters('xfgmc_special_data_for_flag_filter', $special_data_for_flag, $product, $numFeed); /* с версии 2.2.7 */ 

 $skip_flag = apply_filters('xfgmc_skip_flag', $skip_flag, $postId, $product, $special_data_for_flag, $numFeed); /* c версии 2.2.0, с версии 2.2.7 добавелн $special_data_for_flag */
 if ($skip_flag === true) {
	xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен по флагу; Файл: offer.php; Строка: '.__LINE__, 0); 
	$result_arr['skip_reason'][] = __('Flag', 'xfgmc');
	return $result_arr;
 }
  
 /* общие данные для вариативных и обычных товаров */
 $base_measure_xml = ''; $pricing_measure_xml = '';

 $xfgmc_default_currency = xfgmc_optionGET('xfgmc_default_currency', $numFeed, 'set_arr');
 if ($xfgmc_default_currency == '') {
	$currencyId_xml = get_woocommerce_currency();
 } else {
	$currencyId_xml = $xfgmc_default_currency;
 }
 $currencyId_xml = apply_filters('xfgmc_change_price_currency', $currencyId_xml, $product, $numFeed); /* с версии 2.2.10 */
		  
 $result_xml_name = htmlspecialchars($product->get_title(), ENT_NOQUOTES); // название товара
 $result_xml_name = apply_filters('xfgmc_change_name', $result_xml_name, $postId, $product, $numFeed);
  
 // описание
 $xfgmc_desc = xfgmc_optionGET('xfgmc_desc', $numFeed, 'set_arr');
 $xfgmc_the_content = xfgmc_optionGET('xfgmc_the_content', $numFeed, 'set_arr');

// $result_xml_desc = ''; 
 switch ($xfgmc_desc) { 
	case "full": $description_xml = $product->get_description(); break;
	case "excerpt": $description_xml = $product->get_short_description(); break;
	case "fullexcerpt": 
		$description_xml = $product->get_description(); 
		if (empty($description_xml)) {
			$description_xml = $product->get_short_description();
		}
	break;
	case "excerptfull": 
		$description_xml = $product->get_short_description();		 
		if (empty($description_xml)) {
			$description_xml = $product->get_description();
		} 
	break;
	case "fullplusexcerpt": 
		$description_xml = $product->get_description().'<br/>'.$product->get_short_description();
	break;
	case "excerptplusfull": 
		$description_xml = $product->get_short_description().'<br/>'.$product->get_description(); 
	break;		
	default: $description_xml = $product->get_description();
 }
 $xfgmc_adapt_facebook = xfgmc_optionGET('xfgmc_adapt_facebook', $numFeed, 'set_arr');
 $result_xml_desc = '';
 $description_xml = apply_filters('xfgmc_description_xml_filter', $description_xml, $postId, $product, $numFeed); /* с версии 2.2.1 */
 if (!empty($description_xml)) {
	$enable_tags = '<p>,<h2>,<h3>,<em>,<ul>,<li>,<ol>,<br/>,<br>,<strong>,<sub>,<sup>,<div>,<span>,<dl>,<dt>,<dd>';
	if ($xfgmc_the_content === 'enabled') {
		$description_xml = html_entity_decode(apply_filters('the_content', $description_xml)); /* с версии 2.2.4 */
	}		
	$enable_tags = apply_filters('xfgmc_enable_tags_filter', $enable_tags, $numFeed); /* с версии 2.0.7 */
	if ($xfgmc_adapt_facebook === 'yes') {$enable_tags = '';} /* с версии 2.3.3 */
	$description_xml = strip_tags($description_xml, $enable_tags);
	$description_xml = strip_shortcodes($description_xml);
	$description_xml = xfgmc_max_lim_text($description_xml, 5000);
	$description_xml = apply_filters('xfgmc_description_filter', $description_xml, $postId, $product, $numFeed);
	$description_xml = trim($description_xml);
	if ($description_xml !== '') {
		$result_xml_desc = '<g:description><![CDATA['.$description_xml.']]></g:description>'.PHP_EOL;
	}
 }

 $xfgmc_shipping_xml = '';
 $xfgmc_def_shipping_country = xfgmc_optionGET('xfgmc_def_shipping_country', $numFeed, 'set_arr');
 $xfgmc_def_delivery_area_type = xfgmc_optionGET('xfgmc_def_delivery_area_type', $numFeed, 'set_arr');
 $xfgmc_def_delivery_area_value = xfgmc_optionGET('xfgmc_def_delivery_area_value', $numFeed, 'set_arr');
 if ($xfgmc_def_shipping_country !== '' &&  $xfgmc_def_delivery_area_type !== '' && $xfgmc_def_delivery_area_value !== '') {
	$xfgmc_def_shipping_service = xfgmc_optionGET('xfgmc_def_shipping_service', $numFeed, 'set_arr');
	$xfgmc_def_shipping_price = xfgmc_optionGET('xfgmc_def_shipping_price', $numFeed, 'set_arr'); 

	$xfgmc_shipping_xml = '<g:shipping>'.PHP_EOL;
	$xfgmc_shipping_xml .= '<g:country>'.$xfgmc_def_shipping_country.'</g:country>'.PHP_EOL;
	$xfgmc_shipping_xml .= '<g:'.$xfgmc_def_delivery_area_type.'>'.$xfgmc_def_delivery_area_value.'</g:'.$xfgmc_def_delivery_area_type.'>'.PHP_EOL;
	if ($xfgmc_def_shipping_service !== '') {
		$xfgmc_shipping_xml .= '<g:service>'.$xfgmc_def_shipping_service.'</g:service>'.PHP_EOL;
	}
	if ($xfgmc_def_shipping_price !== '') {
		$xfgmc_shipping_xml .= '<g:price>'.$xfgmc_def_shipping_price.' '.$currencyId_xml.'</g:price>'.PHP_EOL;
	}
	$xfgmc_shipping_xml .= '</g:shipping>'.PHP_EOL;   
 }

 if (get_post_meta($postId, 'xfgmc_google_product_category', true) !== '') {
	$xfgmc_google_product_category = get_post_meta($postId, 'xfgmc_google_product_category', true);
	$result_xml_google_cat = '<g:google_product_category>'.htmlspecialchars($xfgmc_google_product_category).'</g:google_product_category>'.PHP_EOL;
 } else {
	$result_xml_google_cat = '';
	xfgmc_error_log('FEED № '.$numFeed.'; У товара '.$postId.'. Нет данных о google_product_category. $xfgmc_google_product_category = ""; Файл: offer.php; Строка: '.__LINE__, 0);
 }
 if (get_post_meta($postId, '_xfgmc_fb_product_category', true) !== '') {
	$xfgmc_fb_product_category = get_post_meta($postId, '_xfgmc_fb_product_category', true);
	$result_xml_facebook_cat = '<g:fb_product_category>'.htmlspecialchars($xfgmc_fb_product_category).'</g:fb_product_category>'.PHP_EOL;
 } else {$result_xml_facebook_cat = '';}

 $xfgmc_tax_info = xfgmc_optionGET('xfgmc_tax_info', $numFeed, 'set_arr');
 if ($xfgmc_tax_info === 'enabled') {
	if (get_post_meta($postId, '_xfgmc_tax_category', true) !== '') {
		$xfgmc_tax_category = get_post_meta($postId, '_xfgmc_tax_category', true);
		$result_xml_tax_category = '<g:tax_category>'.htmlspecialchars($xfgmc_tax_category).'</g:tax_category>'.PHP_EOL;
	} else {
		$result_xml_tax_category = '';
		xfgmc_error_log('FEED № '.$numFeed.'; У товара '.$postId.'. Нет данных о tax_category. $xfgmc_tax_category = ""; Файл: offer.php; Строка: '.__LINE__, 0);
	} 
 }
 
 if ((get_post_meta($postId, 'xfgmc_identifier_exists', true) !== '') && (get_post_meta($postId, 'xfgmc_identifier_exists', true) !== 'off')) {
	$xfgmc_identifier_exists = get_post_meta($postId, 'xfgmc_identifier_exists', true);
	$result_identifier_exists = '<g:identifier_exists>'.$xfgmc_identifier_exists.'</g:identifier_exists>'.PHP_EOL;
	if ($xfgmc_identifier_exists === 'no') {
		$result_identifier_exists .= "<g:gtin></g:gtin>".PHP_EOL;
		$result_identifier_exists .= "<g:mpn></g:mpn>".PHP_EOL;
	}
 } else {$result_identifier_exists = '';}

 if ((get_post_meta($postId, 'xfgmc_adult', true) !== '') && (get_post_meta($postId, 'xfgmc_adult', true) !== 'off')) {
	$xfgmc_adult = get_post_meta($postId, 'xfgmc_adult', true);
	$result_adult = '<g:adult>'.$xfgmc_adult.'</g:adult>'.PHP_EOL;
 } else {$result_adult = '';} 

 if ((get_post_meta($postId, '_xfgmc_is_bundle', true) === 'yes') || (get_post_meta($postId, '_xfgmc_is_bundle', true) === 'no')) {
	$xfgmc_is_bundle = get_post_meta($postId, '_xfgmc_is_bundle', true);
	$result_is_bundle = '<g:is_bundle>'.$xfgmc_is_bundle.'</g:is_bundle>'.PHP_EOL;
 } else {$result_is_bundle = '';}

 if (get_post_meta($postId, '_xfgmc_multipack', true) !== '') {
	$xfgmc_multipack = get_post_meta($postId, '_xfgmc_multipack', true);
	$result_multipack = '<g:multipack>'.$xfgmc_multipack.'</g:multipack>'.PHP_EOL;
 } else {$result_multipack = '';}
 
 $xfgmc_condition = get_post_meta($postId, 'xfgmc_condition', true);
 switch ($xfgmc_condition) { 
	case '': 
		$xfgmc_default_condition = xfgmc_optionGET('xfgmc_default_condition', $numFeed, 'set_arr');
		$result_condition = '<g:condition>'.$xfgmc_default_condition.'</g:condition>'.PHP_EOL;
	break;
	case 'off': 
		$result_condition = '';
	break;
	case 'default': 
		$xfgmc_default_condition = xfgmc_optionGET('xfgmc_default_condition', $numFeed, 'set_arr');
		$result_condition = '<g:condition>'.$xfgmc_default_condition.'</g:condition>'.PHP_EOL;
	break;
	default: $result_condition = '<g:condition>'.$xfgmc_condition.'</g:condition>'.PHP_EOL;
 }

 $result_shipping_label = '';
 if (get_post_meta($postId, '_xfgmc_shipping_label', true) !== '') {
	$xfgmc_shipping_label = get_post_meta($postId, '_xfgmc_shipping_label', true);	
	$result_shipping_label = '<g:shipping_label>'.$xfgmc_shipping_label.'</g:shipping_label>'.PHP_EOL;
 } else {
	$xfgmc_shipping_label = xfgmc_optionGET('xfgmc_def_shipping_label', $numFeed, 'set_arr'); 
	if ($xfgmc_shipping_label === '') {} else {
		$result_shipping_label = '<g:shipping_label>'.$xfgmc_shipping_label.'</g:shipping_label>'.PHP_EOL;
	}
 }

 $result_return_rule_label = '';
 /* if (get_post_meta($postId, '_xfgmc_return_rule_label', true) !== '') {
	$xfgmc_return_rule_label = get_post_meta($postId, '_xfgmc_return_rule_label', true);	
	$result_return_rule_label = '<g:return_rule_label>'.$xfgmc_return_rule_label.'</g:return_rule_label>'.PHP_EOL;
 } else {
	$xfgmc_return_rule_label = xfgmc_optionGET('xfgmc_def_return_rule_label', $numFeed, 'set_arr'); 
	if ($xfgmc_return_rule_label === '') {} else {
		$result_return_rule_label = '<g:return_rule_label>'.$xfgmc_return_rule_label.'</g:return_rule_label>'.PHP_EOL;
	}
 } */
 if (get_post_meta($postId, '_xfgmc_return_rule_label', true) !== '') {
	$xfgmc_return_rule_label = get_post_meta($postId, '_xfgmc_return_rule_label', true);	
	$result_return_rule_label = '<g:return_rule_label>'.$xfgmc_return_rule_label.'</g:return_rule_label>'.PHP_EOL;
 } else {
	$xfgmc_s_return_rule_label = xfgmc_optionGET('xfgmc_s_return_rule_label', $numFeed, 'set_arr');
	switch ($xfgmc_s_return_rule_label) { /* disabled, default_value, post_meta */
		case "default_value":
			$xfgmc_return_rule_label = xfgmc_optionGET('xfgmc_def_return_rule_label', $numFeed, 'set_arr'); 
			if ($xfgmc_return_rule_label === '') {} else {
				$result_return_rule_label = '<g:return_rule_label>'.$xfgmc_return_rule_label.'</g:return_rule_label>'.PHP_EOL;
			}
		break;
		case "post_meta":
			$xfgmc_return_rule_label = xfgmc_optionGET('xfgmc_def_return_rule_label', $numFeed, 'set_arr'); 
			$xfgmc_return_rule_label_id = trim($xfgmc_return_rule_label);
			if (get_post_meta($postId, $xfgmc_return_rule_label_id, true) !== '') {
				$result_return_rule_label_xml = get_post_meta($postId, $xfgmc_return_rule_label_id, true);
				$result_return_rule_label = '<g:return_rule_label>'.$result_return_rule_label_xml.'</g:return_rule_label>'.PHP_EOL;
			}
		break;
	}
 } 

 $result_store_code = '';
 if (get_post_meta($postId, '_xfgmc_store_code', true) !== '') {
	$xfgmc_store_code = get_post_meta($postId, '_xfgmc_store_code', true);	
	$result_store_code = '<g:store_code>'.$xfgmc_store_code.'</g:store_code>'.PHP_EOL;
 } else {
	$xfgmc_store_code = xfgmc_optionGET('xfgmc_def_store_code', $numFeed, 'set_arr'); 
	if ($xfgmc_store_code === '') {} else {
		$result_store_code = '<g:store_code>'.$xfgmc_store_code.'</g:store_code>'.PHP_EOL;
	}
 }

 $result_min_handling_time = '';
 if (get_post_meta($postId, '_xfgmc_min_handling_time', true) !== '') {
	$xfgmc_min_handling_time = get_post_meta($postId, '_xfgmc_min_handling_time', true);	
	$result_min_handling_time = '<g:min_handling_time>'.$xfgmc_min_handling_time.'</g:min_handling_time>'.PHP_EOL;
 } else {
	$xfgmc_min_handling_time = xfgmc_optionGET('xfgmc_def_min_handling_time', $numFeed, 'set_arr'); 
	if ($xfgmc_min_handling_time === '') {} else {
		$result_min_handling_time = '<g:min_handling_time>'.$xfgmc_min_handling_time.'</g:min_handling_time>'.PHP_EOL;
	}
 }
 
 $result_max_handling_time = '';
 if (get_post_meta($postId, '_xfgmc_max_handling_time', true) !== '') {
	$xfgmc_max_handling_time = get_post_meta($postId, '_xfgmc_max_handling_time', true);	
	$result_max_handling_time = '<g:max_handling_time>'.$xfgmc_max_handling_time.'</g:max_handling_time>'.PHP_EOL;
 } else {
	$xfgmc_max_handling_time = xfgmc_optionGET('xfgmc_def_max_handling_time', $numFeed, 'set_arr'); 
	if ($xfgmc_max_handling_time === '') {} else {
		$result_max_handling_time = '<g:max_handling_time>'.$xfgmc_max_handling_time.'</g:max_handling_time>'.PHP_EOL;
	}
 } 

 $result_custom_label = '';
 if (get_post_meta($postId, 'xfgmc_custom_label_0', true) !== '') {
	$xfgmc_custom_label_0 = get_post_meta($postId, 'xfgmc_custom_label_0', true);	
	$result_custom_label .= '<g:custom_label_0>'.$xfgmc_custom_label_0.'</g:custom_label_0>'.PHP_EOL;
 }
 if (get_post_meta($postId, 'xfgmc_custom_label_1', true) !== '') {
	$xfgmc_custom_label_1 = get_post_meta($postId, 'xfgmc_custom_label_1', true);	
	$result_custom_label .= '<g:custom_label_1>'.$xfgmc_custom_label_1.'</g:custom_label_1>'.PHP_EOL;
 }
 if (get_post_meta($postId, 'xfgmc_custom_label_2', true) !== '') {
	$xfgmc_custom_label_2 = get_post_meta($postId, 'xfgmc_custom_label_2', true);	
	$result_custom_label .= '<g:custom_label_2>'.$xfgmc_custom_label_2.'</g:custom_label_2>'.PHP_EOL;
 }
 if (get_post_meta($postId, 'xfgmc_custom_label_3', true) !== '') {
	$xfgmc_custom_label_3 = get_post_meta($postId, 'xfgmc_custom_label_3', true);	
	$result_custom_label .= '<g:custom_label_3>'.$xfgmc_custom_label_3.'</g:custom_label_3>'.PHP_EOL;
 }
 if (get_post_meta($postId, 'xfgmc_custom_label_4', true) !== '') {
	$xfgmc_custom_label_4 = get_post_meta($postId, 'xfgmc_custom_label_4', true);	
	$result_custom_label .= '<g:custom_label_4>'.$xfgmc_custom_label_4.'</g:custom_label_4>'.PHP_EOL;
 }

 $unit_germanized = '';
 if (class_exists('WooCommerce_Germanized')) { // https://plugintests.com/plugins/wporg/woocommerce-germanized/latest/structure/classes/WC_GZD_Product 
	// if (get_post_meta($postId, '_unit', true) !== '') {$unit_germanized = ' '.get_post_meta($postId, '_unit', true);}
	if (!empty(wc_gzd_get_gzd_product($postId)->get_unit())) {
		$unit_germanized = ' '.wc_gzd_get_gzd_product($postId)->get_unit();
	};
 }
 
 // "Категории 
 $catid = '';  
 if (class_exists('WPSEO_Primary_Term')) {		  
	$catWPSEO = new WPSEO_Primary_Term('product_cat', $postId);
	$catidWPSEO = $catWPSEO->get_primary_term();	
	if ($catidWPSEO !== false) { 
	 $catid = $catidWPSEO;
	} else {
	 $termini = get_the_terms($postId, 'product_cat');	
	 if ($termini !== false) {
	  foreach ($termini as $termin) {
		$catid = $termin->term_id;
		break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
	  }
	 } else { // если база битая. фиксим id категорий
	  xfgmc_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: offer.php; Строка: '.__LINE__, 0);
	  $product_cats = wp_get_post_terms($postId, 'product_cat', array("fields" => "ids"));	  
	  // Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
	  // wp_set_object_terms($postId, $product_cats, 'product_cat');
	  if (is_array($product_cats) && count($product_cats)) {
		$catid = $product_cats[0];
		xfgmc_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' база наверняка битая. wp_get_post_terms вернула массив. $catid = '.$catid.'; Файл: offer.php; Строка: '.__LINE__, 0);
	  }
	 }
	}
 } else if (class_exists('RankMath')) {
	$primary_cat_id = get_post_meta($postId, 'rank_math_primary_category', true);
	if ($primary_cat_id) {
		$product_cat = get_term($primary_cat_id, 'product_cat');
		$catid = $product_cat->term_id;
	} else {
		$termini = get_the_terms($postId, 'product_cat');
		if ($termini !== false) {
		 foreach ($termini as $termin) {
		   $catid = $termin->term_id;
		   break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
		 }
		} else { // если база битая. фиксим id категорий
		 xfgmc_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: offer.php; Строка: '.__LINE__, 0);
		 $product_cats = wp_get_post_terms($postId, 'product_cat', array("fields" => "ids"));	  
		 // Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
		 // wp_set_object_terms($postId, $product_cats, 'product_cat');
		 if (is_array($product_cats) && count($product_cats)) {
		   $catid = $product_cats[0];
		   xfgmc_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' база наверняка битая. wp_get_post_terms вернула массив. $catid = '.$catid.'; Файл: offer.php; Строка: '.__LINE__, 0);
		 }
		}
	}
 } else {	
	$termini = get_the_terms($postId, 'product_cat');	
	if ($termini !== false) {
	 foreach ($termini as $termin) {
	   $catid = $termin->term_id;
	   break; // т.к. у товара может быть лишь 1 категория - выходим досрочно.
	 }
	} else { // если база битая. фиксим id категорий
	 xfgmc_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' get_the_terms = false. Возможно база битая. Пробуем задействовать wp_get_post_terms; Файл: offer.php; Строка: '.__LINE__, 0);
	 $product_cats = wp_get_post_terms($postId, 'product_cat', array("fields" => "ids"));	  
	 // Раскомментировать строку ниже для автопочинки категорий в БД (место 1 из 2)
	 // wp_set_object_terms($postId, $product_cats, 'product_cat');
	 if (is_array($product_cats) && count($product_cats)) {
	   $catid = $product_cats[0];
	   xfgmc_error_log('FEED № '.$numFeed.'; WARNING: Для товара $postId = '.$postId.' база наверняка битая. wp_get_post_terms вернула массив. $catid = '.$catid.'; Файл: offer.php; Строка: '.__LINE__, 0);
	 }
	}
 }

 if ($xfgmc_adapt_facebook === 'yes') {
	$in_stock = 'in stock'; $out_of_stock = 'out of stock'; $onbackorder = 'available for order';
 } else {
	$in_stock = 'in_stock'; $out_of_stock = 'out_of_stock'; $onbackorder = 'preorder';
 }

 /* Вариации */
 // если вариация - нам нет смысла выгружать общее предложение
 if ($product->is_type('variable')) {
	xfgmc_error_log('FEED № '.$numFeed.'; У нас вариативный товар. Файл: offer.php; Строка: '.__LINE__, 0);	
	$xfgmc_var_desc_priority = xfgmc_optionGET('xfgmc_var_desc_priority', $numFeed, 'set_arr');
	$variations = array();
	if ($product->is_type('variable')) {
		$variations = $product->get_available_variations();
		$variation_count = count($variations);
	} 
	
	$n = 0; // число вариаций, которые попали в фид (с версии 1.1.3)
	for ($i = 0; $i<$variation_count; $i++) {	
		$offer_id = (($product->is_type('variable')) ? $variations[$i]['variation_id'] : $product->get_id());
		$offer = new WC_Product_Variation($offer_id); // получим вариацию
		$result_xml_name = apply_filters('xfgmc_variable_change_name', $result_xml_name, $postId, $product, $offer, $numFeed);

		/*
		* $offer->get_price() - актуальная цена (равна sale_price или regular_price если sale_price пуст)
		* $offer->get_regular_price() - обычная цена
		* $offer->get_sale_price() - цена скидки
		*/
			 
		$price_xml = $offer->get_price(); // цена вариации
		// если цены нет - пропускаем вариацию 			 
		if ($price_xml == 0 || empty($price_xml)) {
			xfgmc_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к нет цены; Файл: offer.php; Строка: '.__LINE__, 0); 
			$result_arr['skip_reason'][] = __('The variation ID', 'xfgmc').' = '. $offer->get_price(). ': ' . __('Price not specified', 'xfgmc');
			continue;
		}
		
		if (class_exists('XmlforGoogleMerchantCenterPro')) {
			if ((xfgmc_optionGET('xfgmcp_compare_value', $numFeed, 'set_arr') !== false) && (xfgmc_optionGET('xfgmcp_compare_value', $numFeed, 'set_arr') !== '')) {
			 $xfgmcp_compare_value = xfgmc_optionGET('xfgmcp_compare_value', $numFeed, 'set_arr');
			 $xfgmcp_compare = xfgmc_optionGET('xfgmcp_compare', $numFeed, 'set_arr');			 
			 if ($xfgmcp_compare == '>=') {
				if ($price_xml < $xfgmcp_compare_value) {
					$result_arr['skip_reason'][] = __('The variation ID', 'xfgmc').' = '. $offer->get_price(). ': < ' . $xfgmcp_compare_value;
					continue;
				}
			 } else {
				if ($price_xml >= $xfgmcp_compare_value) {
					$result_arr['skip_reason'][] = __('The variation ID', 'xfgmc').' = '. $offer->get_price(). ': >= ' . $xfgmcp_compare_value;
					continue;
				}
			 }
			}
		}
		
		// пропуск вариаций, которых нет в наличии
		$xfgmc_skip_missing_products = xfgmc_optionGET('xfgmc_skip_missing_products', $numFeed, 'set_arr');
		if ($xfgmc_skip_missing_products == 'on') {
			if ($offer->is_in_stock() == false) { 
				xfgmc_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к ее нет в наличии; Файл: offer.php; Строка: '.__LINE__, 0); 
				$result_arr['skip_reason'][] = __('The variation ID', 'xfgmc').' = '. $offer->get_price().': '. __('Skip missing products', 'xfgmc');
				continue;
			}
		}
			 
		// пропускаем вариации на предзаказ
		$skip_backorders_products = xfgmc_optionGET('xfgmc_skip_backorders_products', $numFeed, 'set_arr');
		if ($skip_backorders_products == 'on') {
		 if ($offer->get_manage_stock() == true) { // включено управление запасом			  
			if (($offer->get_stock_quantity() < 1) && ($offer->get_backorders() !== 'no')) {
				xfgmc_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.' пропущена т.к запрещен предзаказ и включено управление запасом; Файл: offer.php; Строка: '.__LINE__, 0); 
				$result_arr['skip_reason'][] = __('The variation ID', 'xfgmc').' = '. $offer->get_price().': '. __('Skip backorders products', 'xfgmc');
				continue;
			}
		 }
		}
				
		$stop_flag = apply_filters('xfgmc_before_variable_offer_stop_flag', $stop_flag, $i, $n, $variation_count, $offer_id, $offer, $special_data_for_flag, $numFeed); /* c версии 2.2.7 */
		if ($stop_flag == true) {break;}		

		$skip_flag = apply_filters('xfgmc_skip_flag_variable', $skip_flag, $postId, $product, $offer, $special_data_for_flag, $numFeed); /* c версии 2.2.0, с версии 2.2.7 добавелн $special_data_for_flag */
		if ($skip_flag === true) {
			xfgmc_error_log('FEED № '.$numFeed.'; Вариация товара с postId = '.$postId.', offer_id = '.$offer_id.' пропущен по флагу; Файл: offer.php; Строка: '.__LINE__, 0); 
			$result_arr['skip_reason'][] = __('The variation ID', 'xfgmc').' = '. $offer->get_price().': '. __('Flag', 'xfgmc');
			continue;
		}		
			 
		do_action('xfgmc_before_variable_offer', $numFeed);
		
		$result_xml .= '<item>'.PHP_EOL;
		$result_xml_id = '<g:id>'.$offer_id.'</g:id>'.PHP_EOL;
		$xfgmc_instead_of_id = xfgmc_optionGET('xfgmc_instead_of_id', $numFeed, 'set_arr');
		if ($xfgmc_instead_of_id === 'sku') {
			$sku_xml = $offer->get_sku(); // артикул
			if (!empty($sku_xml)) {$result_xml_id = '<g:id>'.htmlspecialchars($sku_xml).'</g:id>'.PHP_EOL;}
		}
		$result_xml_id = apply_filters('xfgmc_variable_result_xml_id', $result_xml_id, $product, $offer, $xfgmc_instead_of_id, $numFeed); /* c версии 2.3.12 */
		$result_xml .= $result_xml_id;
		$result_xml .= '<g:item_group_id>'.$product->get_id().'</g:item_group_id>'.PHP_EOL;
		$result_xml .= "<g:title>".htmlspecialchars($result_xml_name, ENT_NOQUOTES)."</g:title>".PHP_EOL;
		
		// Описание.		
		if ($xfgmc_var_desc_priority === 'on' || empty($description_xml)) {
			switch ($xfgmc_desc) { 
				case "excerptplusfull": 
					$description_xml = $product->get_short_description().'<br/>'.$offer->get_description(); 
				break;					
				case "fullplusexcerpt": 
					$description_xml = $offer->get_description().'<br/>'.$product->get_short_description();
				break;	
				default: $description_xml = $offer->get_description();
			}		
		}
		if (!empty($description_xml)) {
			$enable_tags = '<p>,<h2>,<h3>,<em>,<ul>,<li>,<ol>,<br/>,<br>,<strong>,<sub>,<sup>,<div>,<span>,<dl>,<dt>,<dd>';	
			if ($xfgmc_the_content === 'enabled') {
				$description_xml = html_entity_decode(apply_filters('the_content', $description_xml)); /* с версии 2.2.4 */
			}					
			$enable_tags = apply_filters('xfgmc_enable_tags_filter', $enable_tags, $numFeed); /* с версии 2.0.7 */
			if ($xfgmc_adapt_facebook === 'yes') {$enable_tags = '';} /* с версии 2.3.3 */
			$description_xml = strip_tags($description_xml, $enable_tags);
			$description_xml = strip_shortcodes($description_xml);			
			$description_xml = xfgmc_max_lim_text($description_xml, 5000);			
			$description_xml = apply_filters('xfgmc_description_filter', $description_xml, $postId, $product, $numFeed);
			$description_xml = apply_filters('xfgmc_description_filter_variable', $description_xml, $postId, $product, $offer, $numFeed); /* с версии 2.2.1 */			
			$description_xml = trim($description_xml);
			if ($description_xml !== '') {
				$result_xml .= '<g:description><![CDATA['.$description_xml.']]></g:description>'.PHP_EOL;
			}
		} else {
			// если у вариации нет своего описания - пробуем подставить общее
			if (!empty($result_xml_desc)) {$result_xml .= $result_xml_desc;}
		}

		// категория гугл
		if ($result_xml_google_cat !== '') {
			$result_xml .= $result_xml_google_cat;
		} else {
			if (get_term_meta($catid, 'xfgmc_google_product_category', true) !== '') {
				$xfgmc_google_product_category = get_term_meta($catid, 'xfgmc_google_product_category', true);
				$result_xml_google_cat = '<g:google_product_category>'.htmlspecialchars($xfgmc_google_product_category).'</g:google_product_category>'.PHP_EOL;
				$result_xml_google_cat = apply_filters('xfgmc_xml_google_cat_variable_filter', $result_xml_google_cat, $catid, $product, $offer, $numFeed);	
				$result_xml .= $result_xml_google_cat;
			} 		
		}
		// категория фейсбук
		if ($result_xml_facebook_cat !== '') {
			$result_xml .= $result_xml_facebook_cat;
		} else {
			if (get_term_meta($catid, 'xfgmc_fb_product_category', true) !== '') {
				$xfgmc_fb_product_category = get_term_meta($catid, 'xfgmc_fb_product_category', true);
				$result_xml_facebook_cat = '<g:fb_product_category>'.htmlspecialchars($xfgmc_fb_product_category).'</g:fb_product_category>'.PHP_EOL;
				$result_xml_facebook_cat = apply_filters('xfgmc_xml_facebook_cat_variable_filter', $result_xml_facebook_cat, $catid, $product, $offer, $numFeed);	
				$result_xml .= $result_xml_facebook_cat;
			} 		
		}

		if ($xfgmc_tax_info === 'enabled') {
			if ($result_xml_tax_category !== '') {
				$result_xml .= $result_xml_tax_category;
			} else {
				if (get_term_meta($catid, 'xfgmc_tax_category', true) !== '') {
					$xfgmc_tax_category = get_term_meta($catid, 'xfgmc_tax_category', true);
					$result_xml_tax_category = '<g:tax_category>'.htmlspecialchars($xfgmc_tax_category).'</g:tax_category>'.PHP_EOL;
					$result_xml_tax_category = apply_filters('xfgmc_xml_google_tax_category_variable_filter', $result_xml_tax_category, $catid, $product, $offer, $numFeed);	
					$result_xml .= $result_xml_tax_category;
				} 		
			}
		}

		$xfgmc_product_type = xfgmc_optionGET('xfgmc_product_type', $numFeed, 'set_arr');
		if ($xfgmc_product_type === 'enabled') {
			$product_type_res = xfgmc_product_type($catid, $numFeed);
			$product_type_res = apply_filters('xfgmc_product_type_res_variable_filter', $product_type_res, $catid, $product, $offer, $numFeed);	
			if ($product_type_res === '') {} else {
				$result_xml .= '<g:product_type>'.htmlspecialchars($product_type_res, ENT_NOQUOTES).'</g:product_type>';
			}
		}

		$result_url = htmlspecialchars(get_permalink($offer->get_id()));
		$result_url = apply_filters('xfgmc_url_filter_var', $result_url, $product, $offer, $catid, $numFeed); /* с версии 2.0.5 */
		$result_xml .= "<g:link>".$result_url."</g:link>".PHP_EOL;
			 
		$thumb_xml = get_the_post_thumbnail_url($offer->get_id(), 'full');
		if (empty($thumb_xml)) {
			$no_default_png_products = xfgmc_optionGET('xfgmc_no_default_png_products', $numFeed, 'set_arr');
			if (($no_default_png_products === 'on') && (!has_post_thumbnail($postId))) {$picture_xml = '';} else {
				$thumb_id = get_post_thumbnail_id($postId);			 
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);	
				$thumb_xml = $thumb_url[0]; /* урл оригинал миниатюры товара */
				$picture_xml = '<g:image_link>'.xfgmc_deleteGET($thumb_xml).'</g:image_link>'.PHP_EOL;
			}
		} else {
			$picture_xml = '<g:image_link>'.xfgmc_deleteGET($thumb_xml).'</g:image_link>'.PHP_EOL;
		}
		$picture_xml = apply_filters('xfgmc_pic_variable_offer_filter', $picture_xml, $product, $numFeed, $offer);
		$result_xml .= $picture_xml;

		// наличие
		if ($offer->get_manage_stock() == true) { // включено управление запасом
			if ($offer->get_stock_quantity() > 0) {
				$available = $in_stock;
			} else {
				if ($offer->get_backorders() === 'no') { // предзаказ запрещен
					$available = $out_of_stock;
				} else {
					$xfgmc_behavior_onbackorder = xfgmc_optionGET('xfgmc_behavior_onbackorder', $numFeed, 'set_arr');
					switch ($xfgmc_behavior_onbackorder) { 
						case "out_of_stock": 
							$available = $out_of_stock; 
						break;
						case "in_stock": 
							$available = $in_stock;
						break;
						case "onbackorder": 
							$available = $onbackorder;
						break;
						default:
							$available = $onbackorder;
					}					
				}
			}
		} else { // отключено управление запасом
			if ($offer->get_stock_status() === 'instock') {
				$available = $in_stock;
			} else if ($offer->get_stock_status() === 'outofstock') { 
				$available = $out_of_stock;
			} else {
				$xfgmc_behavior_onbackorder = xfgmc_optionGET('xfgmc_behavior_onbackorder', $numFeed, 'set_arr');
				switch ($xfgmc_behavior_onbackorder) { 
					case "out_of_stock": 
						$available = $out_of_stock; 
					break;
					case "in_stock": 
						$available = $in_stock;
					break;
					case "onbackorder": 
						$available = $onbackorder;
					break;
					default:
						$available = $onbackorder;
				}
			}
 		}

		$xfgmc_g_stock = xfgmc_optionGET('xfgmc_g_stock', $numFeed, 'set_arr');
		if ($xfgmc_g_stock === 'enabled') {
			 if ($offer->get_manage_stock() == true) { // включено управление запасом
				$stock_quantity = $offer->get_stock_quantity();
				$result_xml .= '<g:quantity>'.$stock_quantity.'</g:quantity>'.PHP_EOL; 
			 } else {
				if ($product->get_manage_stock() == true) { // включено управление запасом  
					$stock_quantity = $product->get_stock_quantity();
					$result_xml .= '<g:quantity>'.$stock_quantity.'</g:quantity>'.PHP_EOL;
				}
			 }
		}
		/*
		if ($offer->get_manage_stock() == true) { // включено управление запасом
		 if ($offer->get_stock_quantity() > 0) {
			$available = $in_stock;
		 } else {
			$available = $out_of_stock;
		 }
		}		
		 }
		} else { // отключено управление запасом
		 if ($offer->is_in_stock() == true) {$available = $in_stock;} else {$available = $out_of_stock;}
		} */

		$result_xml .= '<g:availability>'.$available.'</g:availability>'.PHP_EOL;

		$result_xml .= $result_identifier_exists;
		
		$result_xml .= $result_adult;
		
		$result_xml .= $result_is_bundle;

		$result_xml .= $result_multipack;

		$result_xml .= $result_condition;

		$result_xml .= $result_custom_label;
		
		do_action('xfgmc_prepend_variable_offer', $numFeed);

		$price_xml = apply_filters('xfgmc_variable_price_xml_filter', $price_xml, $product, $offer, $numFeed); /* с версии 2.0.6 */
		$xfgmc_sale_price = xfgmc_optionGET('xfgmc_sale_price', $numFeed, 'set_arr');
		if ($xfgmc_sale_price === 'yes') {
			$sale_price = (float)$offer->get_sale_price();
			$sale_price = apply_filters('xfgmc_variable_sale_price_xml_filter', $sale_price, $product, $offer, $numFeed); /* с версии 2.3.1 */
			$price_xml = (float)$price_xml;
			if (($sale_price > 0) && ($price_xml === $sale_price)) {
				$sale_price_xml = $offer->get_regular_price();
				$result_xml .= '<g:price>'.$sale_price_xml.' '.$currencyId_xml.'</g:price>'.PHP_EOL;
				$result_xml .= '<g:sale_price>'.$price_xml.' '.$currencyId_xml.'</g:sale_price>'.PHP_EOL;

				$sales_price_from = $offer->get_date_on_sale_from();
				$sales_price_to = $offer->get_date_on_sale_to();		
				if (!empty($sales_price_from) && !empty($sales_price_to)) {
					$sales_price_from = date(DATE_ISO8601, strtotime($sales_price_from));
					$sales_price_to = date(DATE_ISO8601, strtotime($sales_price_to));
					$result_xml .='<g:sale_price_effective_date>'.$sales_price_from.'/'.$sales_price_to.'</g:sale_price_effective_date>'.PHP_EOL;
				}

				$result_xml = apply_filters('xfgmc_variable_sale_price_filter', $result_xml, $product, $offer, $numFeed); /* с версии 2.0.6 */
			} else {
				$result_xml .= '<g:price>'.$price_xml.' '.$currencyId_xml.'</g:price>'.PHP_EOL;
			}
		} else {
			$result_xml .= '<g:price>'.$price_xml.' '.$currencyId_xml.'</g:price>'.PHP_EOL;
		} 

		$result_xml .= $result_shipping_label;	
		$result_xml .= $result_return_rule_label;
		$result_xml .= $result_store_code;
		$result_xml .= $result_min_handling_time;
		$result_xml .= $result_max_handling_time;
		$result_xml .= $xfgmc_shipping_xml;
			 
		$weight_xml = $offer->get_weight(); // вес
		if (!empty($weight_xml)) {
			$xfgmc_def_shipping_weight_unit = xfgmc_optionGET('xfgmc_def_shipping_weight_unit', $numFeed, 'set_arr');
			if ($xfgmc_def_shipping_weight_unit == '') {$xfgmc_def_shipping_weight_unit = 'kg';}
			$xfgmc_def_shipping_weight_unit = apply_filters('xfgmc_variable_def_shipping_weight_unit_filter', $xfgmc_def_shipping_weight_unit, $product, $offer, $numFeed); /* с версии 2.4.0 */
			$weight_xml = round(wc_get_weight($weight_xml, $xfgmc_def_shipping_weight_unit), 3);
			$result_xml .= "<g:weight>".$weight_xml." ".$xfgmc_def_shipping_weight_unit."</g:weight>".PHP_EOL;
		}
			 
		if ($offer->has_dimensions()) {
			$length_xml = $offer->get_length();
			if (!empty($length_xml)) {
				$length_xml = round(wc_get_dimension($length_xml, 'cm'), 3);	
				$result_xml .= "<g:shipping_length>".$length_xml." cm</g:shipping_length>".PHP_EOL;
			}
			
			$width_xml = $offer->get_width();
			if (!empty($width_xml)) {
				$width_xml = round(wc_get_dimension($width_xml, 'cm'), 3);
				$result_xml .= "<g:shipping_width>".$width_xml." cm</g:shipping_width>".PHP_EOL;	
			}
			   
			$height_xml = $offer->get_height();
			if (!empty($length_xml)) {
				$height_xml = round(wc_get_dimension($height_xml, 'cm'), 3);
				$result_xml .= "<g:shipping_height>".$height_xml." cm</g:shipping_height>".PHP_EOL;
			}
		}
		
		// штрихкод			 
		$gtin = xfgmc_optionGET('xfgmc_gtin', $numFeed, 'set_arr');
		switch ($gtin) { /* disabled, sku, post_meta или id */
			case "disabled":	
				// выгружать штрихкод нет нужды
			break; 
			case "no":
				if ($xfgmc_identifier_exists !== 'no') {
					$result_xml .= "<g:gtin></g:gtin>".PHP_EOL;
				}
			break; 
			case "sku":
				// выгружать из артикула
				$sku_xml = $offer->get_sku(); // артикул
				if (!empty($sku_xml)) {
					$result_xml .= "<g:gtin>".$sku_xml."</g:gtin>".PHP_EOL;
				} else {
					// своего артикула у вариации нет. Пробуем подставить общий sku
					$sku_xml = $product->get_sku();
					if (!empty($sku_xml)) {
						$result_xml .= "<g:gtin>".$sku_xml."</g:gtin>".PHP_EOL;
					}
				}
			break;
			case "post_meta":
				$gtin_post_meta_id = xfgmc_optionGET('xfgmc_gtin_post_meta', $numFeed, 'set_arr');
				$gtin_post_meta_id = trim($gtin_post_meta_id);

				if (get_post_meta($postId, $gtin_post_meta_id, true) !== '') {					
					$gtin_xml = get_post_meta($postId, $gtin_post_meta_id, true);
					$result_xml .= "<g:gtin>".$gtin_xml."</g:gtin>".PHP_EOL;
				}
			break;
			case "germanized":
				if (class_exists('WooCommerce_Germanized')) {
					$var_id = $offer->get_id();
					if (get_post_meta($var_id, '_ts_gtin', true) !== '') {
						$gtin_xml = get_post_meta($var_id, '_ts_gtin', true);
						$result_xml .= "<g:gtin>".$gtin_xml."</g:gtin>".PHP_EOL;
					} else {
						if (get_post_meta($postId, '_ts_gtin', true) !== '') {
							$gtin_xml = get_post_meta($postId, '_ts_gtin', true);
							$result_xml .= "<g:gtin>".$gtin_xml."</g:gtin>".PHP_EOL;
						}
					}
				}
			break;				 
			default:
				$gtin = (int)$gtin;
				$xfgmc_gtin_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($gtin));					
				if (!empty($xfgmc_gtin_xml)) {
					$result_xml .= '<g:gtin>'.xfgmc_replace_decode($xfgmc_gtin_xml).'</g:gtin>'.PHP_EOL;
				}
			}	

		$mpn = xfgmc_optionGET('xfgmc_mpn', $numFeed, 'set_arr');
		switch ($mpn) { /* disabled, sku, post_meta или id */
			case "disabled":	
				// выгружать штрихкод нет нужды
			break; 
			case "no":	
				if ($xfgmc_identifier_exists !== 'no') {
					$result_xml .= "<g:mpn></g:mpn>".PHP_EOL;
				}
			break; 
			case "sku":
				// выгружать из артикула
				$sku_xml = $offer->get_sku(); // артикул
				if (!empty($sku_xml)) {
					$result_xml .= "<g:mpn>".htmlspecialchars($sku_xml)."</g:mpn>".PHP_EOL;
				} else {
					// своего артикула у вариации нет. Пробуем подставить общий sku
					$sku_xml = $product->get_sku();
					if (!empty($sku_xml)) {
						$result_xml .= "<g:mpn>".htmlspecialchars($sku_xml)."</g:mpn>".PHP_EOL;
					}
				}
			break;
			case "post_meta":
				$mpn_post_meta_id = xfgmc_optionGET('xfgmc_mpn_post_meta', $numFeed, 'set_arr');
				$mpn_post_meta_id = trim($mpn_post_meta_id);
				if (get_post_meta($postId, $mpn_post_meta_id, true) !== '') {
					$mpn_xml = get_post_meta($postId, $mpn_post_meta_id, true);
					$result_xml .= "<g:mpn>".htmlspecialchars($mpn_xml)."</g:mpn>".PHP_EOL;
				}
			break;	
			case "germanized":
				if (class_exists('WooCommerce_Germanized')) {
					$var_id = $offer->get_id();
					if (get_post_meta($var_id, '_ts_mpn', true) !== '') {
						$mpn_xml = get_post_meta($var_id, '_ts_mpn', true);
						$result_xml .= "<g:mpn>".htmlspecialchars($mpn_xml)."</g:mpn>".PHP_EOL;
					} else {
						if (get_post_meta($postId, '_ts_mpn', true) !== '') {
							$mpn_xml = get_post_meta($postId, '_ts_mpn', true);
							$result_xml .= "<g:mpn>".htmlspecialchars($mpn_xml)."</g:mpn>".PHP_EOL;
						}
					}
				}
			break;			 		 
			default:
				$mpn = (int)$mpn;
				$xfgmc_mpn_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($mpn));					
				if (!empty($xfgmc_mpn_xml)) {
					$result_xml .= '<g:mpn>'.xfgmc_replace_decode($xfgmc_mpn_xml).'</g:mpn>'.PHP_EOL;
				}
		}			

		// возраст
		$age = xfgmc_optionGET('xfgmc_age', $numFeed, 'set_arr');
		switch ($age) { /* disabled, sku, post_meta или id */
			case "disabled":	
				// выгружать штрихкод нет нужды
			break; 
			case "default_value":
				$xfgmc_age_group_post_meta = xfgmc_optionGET('xfgmc_age_group_post_meta', $numFeed, 'set_arr');
				$result_xml .= "<g:age_group>".$xfgmc_age_group_post_meta."</g:age_group>".PHP_EOL;
			break;			
			case "post_meta":
				$age_post_meta_id = xfgmc_optionGET('xfgmc_age_post_meta', $numFeed, 'set_arr');
				$age_post_meta_id = trim($age_post_meta_id);
				if (get_post_meta($postId, $age_post_meta_id, true) !== '') {
					$age_xml = get_post_meta($postId, $age_post_meta_id, true);
					$result_xml .= "<g:age_group>".$age_xml."</g:age_group>".PHP_EOL;
				}
			break;				 		 
			default:
				$age = (int)$age;
				$age_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($age));
				if (!empty($age_xml)) {	
					$result_xml .= "<g:age_group>".ucfirst(xfgmc_replace_decode($age_xml))."</g:age_group>".PHP_EOL;		
				} else {
					$age_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($age));
					if (!empty($age_xml)) {	
						$result_xml .= "<g:age_group>".ucfirst(xfgmc_replace_decode($age_xml))."</g:age_group>".PHP_EOL;		
					}
				}
		}
		
		// brand [марка]
		$brand = xfgmc_optionGET('xfgmc_brand', $numFeed, 'set_arr');
		if (!empty($brand) && $brand !== 'off') {
			if ((is_plugin_active('perfect-woocommerce-brands/perfect-woocommerce-brands.php') || is_plugin_active('perfect-woocommerce-brands/main.php') || class_exists('Perfect_Woocommerce_Brands')) && $brand === 'sfpwb') {
				$barnd_terms = get_the_terms($product->get_id(), 'pwb-brand');
				if ($barnd_terms !== false) {
				 foreach($barnd_terms as $barnd_term) {
					$result_xml .= '<g:brand>'. $barnd_term->name .'</g:brand>'.PHP_EOL;
					break;
				 }
				}
			} else if ((is_plugin_active('premmerce-woocommerce-brands/premmerce-brands.php')) && ($brand === 'premmercebrandsplugin')) {
				$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
				if ($barnd_terms !== false) {
				 foreach($barnd_terms as $barnd_term) {
					$result_xml .= '<g:brand>'. $barnd_term->name .'</g:brand>'.PHP_EOL;
					break;
				 }
				}			
			} else if ((is_plugin_active('woocommerce-brands/woocommerce-brands.php')) && ($brand === 'woocommerce_brands')) {
				$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
				if ($barnd_terms !== false) {
				 foreach($barnd_terms as $barnd_term) {
					$result_xml .= '<g:brand>'. $barnd_term->name .'</g:brand>'.PHP_EOL;
					break;
				 }
				}
			} else if ($brand === 'post_meta') {
				$brand_post_meta_id = xfgmc_optionGET('xfgmc_brand_post_meta', $numFeed, 'set_arr');
				$brand_post_meta_id = trim($brand_post_meta_id);
				if (get_post_meta($postId, $brand_post_meta_id, true) !== '') {
					$brand_xml = get_post_meta($postId, $brand_post_meta_id, true);
					$result_xml .= "<g:brand>".$brand_xml."</g:brand>".PHP_EOL;
				}
			} else if ($brand === 'default_value') {
				$xfgmc_brand_post_meta = xfgmc_optionGET('xfgmc_brand_post_meta', $numFeed, 'set_arr');
				$result_xml .= "<g:brand>".$xfgmc_brand_post_meta."</g:brand>".PHP_EOL;
			} else {
				$brand = (int)$brand;
				$brand_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($brand));
				if (!empty($brand_xml)) {	
				   $result_xml .= "<g:brand>".ucfirst(xfgmc_replace_decode($brand_xml))."</g:brand>".PHP_EOL;		
				} else {
				   $brand_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($brand));
				   if (!empty($brand_xml)) {	
					   $result_xml .= "<g:brand>".ucfirst(xfgmc_replace_decode($brand_xml))."</g:brand>".PHP_EOL;		
				   }		
				}
			}
		}

		// цвет
		$color = xfgmc_optionGET('xfgmc_color', $numFeed, 'set_arr');
		if (!empty($color) && $color !== 'off') {
		 $color = (int)$color;
		 $color_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($color));
		 if (!empty($color_xml)) {	
			$result_xml .= "<g:color>".ucfirst(xfgmc_replace_decode($color_xml))."</g:color>".PHP_EOL;		
		 } else {
			$color_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($color));
			if (!empty($color_xml)) {	
				$result_xml .= "<g:color>".ucfirst(xfgmc_replace_decode($color_xml))."</g:color>".PHP_EOL;		
			}		
		 }
		}	

		// материал
		$material = xfgmc_optionGET('xfgmc_material', $numFeed, 'set_arr');
		if (!empty($material) && $material !== 'off') {
		 $material = (int)$material;
		 $material_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($material));
		 if (!empty($material_xml)) {	
			$result_xml .= "<g:material>".ucfirst(xfgmc_replace_decode($material_xml))."</g:material>".PHP_EOL;		
		 } else {
			$material_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($material));
			if (!empty($material_xml)) {	
				$result_xml .= "<g:material>".ucfirst(xfgmc_replace_decode($material_xml))."</g:material>".PHP_EOL;		
			}		
		 }
		}	
		
		// узор
		$pattern = xfgmc_optionGET('xfgmc_pattern', $numFeed, 'set_arr');
		if (!empty($pattern) && $pattern !== 'off') {
		 $pattern = (int)$pattern;
		 $pattern_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($pattern));
		 if (!empty($pattern_xml)) {	
			$result_xml .= "<g:pattern>".ucfirst(xfgmc_replace_decode($pattern_xml))."</g:pattern>".PHP_EOL;		
		 } else {
			$pattern_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($pattern));
			if (!empty($pattern_xml)) {	
				$result_xml .= "<g:pattern>".ucfirst(xfgmc_replace_decode($pattern_xml))."</g:pattern>".PHP_EOL;		
			}		
		 }
		}
		
		// пол
		$gender = xfgmc_optionGET('xfgmc_gender', $numFeed, 'set_arr');
		if (!empty($gender) && $gender !== 'off') {
		 $gender = (int)$gender;
		 $gender_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($gender));
		 if (!empty($gender_xml)) {	
			$result_xml .= "<g:gender>".ucfirst(xfgmc_replace_decode($gender_xml))."</g:gender>".PHP_EOL;		
		 } else {		 
			$gender_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($gender));
			if (!empty($gender_xml)) {	
				$result_xml .= "<g:gender>".ucfirst(xfgmc_replace_decode($gender_xml))."</g:gender>".PHP_EOL;		
			} else {
				$gender_alt = xfgmc_optionGET('xfgmc_gender_alt', $numFeed, 'set_arr');
				if ($gender_alt !== 'off') {
					$result_xml .= "<g:gender>".$gender_alt."</g:gender>".PHP_EOL;		
				}
			}
		 }
		}	
		
		// размер
		if (get_term_meta($catid, 'xfgmc_size', true) == '' || get_term_meta($catid, 'xfgmc_size', true) === 'default') {
			$size = xfgmc_optionGET('xfgmc_size', $numFeed, 'set_arr');
			if (!empty($size) && $size !== 'off') {
				$size = (int)$size;
				$size_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($size));
				if (!empty($size_xml)) {	
					$result_xml .= "<g:size>".ucfirst(xfgmc_replace_decode($size_xml))."</g:size>".PHP_EOL;		
				} else {
					$size_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size));
					if (!empty($size_xml)) {	
						$result_xml .= "<g:size>".ucfirst(xfgmc_replace_decode($size_xml))."</g:size>".PHP_EOL;		
					}
				}
			} 
		} else {
			$size = (int)get_term_meta($catid, 'xfgmc_size', true);
			$size_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($size));
			if (!empty($size_xml)) {	
				$result_xml .= "<g:size>".ucfirst(xfgmc_replace_decode($size_xml))."</g:size>".PHP_EOL;		
			} else {
				$size_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size));
				if (!empty($size_xml)) {	
					$result_xml .= "<g:size>".ucfirst(xfgmc_replace_decode($size_xml))."</g:size>".PHP_EOL;		
				}
			}
		}

		// тип размера
		if (get_term_meta($catid, 'xfgmc_size_type', true) == '' || get_term_meta($catid, 'xfgmc_size_type', true) === 'default') {
			$size_type = xfgmc_optionGET('xfgmc_size_type', $numFeed, 'set_arr');
			if (!empty($size_type) && $size_type !== 'off') {
				$size_type = (int)$size_type;
				$size_type_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($size_type));
				if (!empty($size_type_xml)) {	
					$result_xml .= "<g:size_type>".ucfirst(xfgmc_replace_decode($size_type_xml))."</g:size_type>".PHP_EOL;		
				} else {		 
					$size_type_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size_type));
					if (!empty($size_type_xml)) {	
						$result_xml .= "<g:size_type>".ucfirst(xfgmc_replace_decode($size_type_xml))."</g:size_type>".PHP_EOL;		
					} else {
						$size_type_alt = xfgmc_optionGET('xfgmc_gender_alt', $numFeed, 'set_arr');
						if ($size_type_alt !== 'off') {
							$result_xml .= "<g:size_type>".$size_type_alt."</g:size_type>".PHP_EOL;		
						}
					}
				}
			}
		} else {
			$size_type = (int)get_term_meta($catid, 'xfgmc_size_type', true);
			$size_type_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($size_type));
			if (!empty($size_type_xml)) {	
				$result_xml .= "<g:size_type>".ucfirst(xfgmc_replace_decode($size_type_xml))."</g:size_type>".PHP_EOL;		
			} else {		 
				$size_type_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size_type));
				if (!empty($size_type_xml)) {	
					$result_xml .= "<g:size_type>".ucfirst(xfgmc_replace_decode($size_type_xml))."</g:size_type>".PHP_EOL;		
				} else {
					$size_type_alt = get_term_meta($catid, 'xfgmc_size_type_alt', true);
					// $size_type_alt = xfgmc_optionGET('xfgmc_gender_alt', $numFeed, 'set_arr');
					if ($size_type_alt !== 'default') {
						$result_xml .= "<g:size_type>".$size_type_alt."</g:size_type>".PHP_EOL;		
					}
				}
			}
		}		

		// система_размеров
		$size_system = xfgmc_optionGET('xfgmc_size_system', $numFeed, 'set_arr');
		if (!empty($size_system) && $size_system !== 'off') {
		 $size_system = (int)$size_system;
		 $size_system_xml = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($size_system));
		 if (!empty($size_system_xml)) {	
			$result_xml .= "<g:size_system>".ucfirst(xfgmc_replace_decode($size_system_xml))."</g:size_system>".PHP_EOL;		
		 } else {		 
			$size_system_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size_system));
			if (!empty($size_system_xml)) {	
				$result_xml .= "<g:size_system>".ucfirst(xfgmc_replace_decode($size_system_xml))."</g:size_system>".PHP_EOL;		
			} else {
				$size_system_alt = xfgmc_optionGET('xfgmc_gender_alt', $numFeed, 'set_arr');
				if ($size_system_alt !== 'off') {
					$result_xml .= "<g:size_system>".$size_system_alt."</g:size_system>".PHP_EOL;		
				}
			}
		 }
		}
		
		$var_id = $offer->get_id();
		if (get_post_meta($var_id, '_xfgmc_unit_pricing_measure', true) !== '') {					
		   $unit_pricing_measure_xml = get_post_meta($var_id, '_xfgmc_unit_pricing_measure', true);
		   $pricing_measure_xml = "<g:unit_pricing_measure>".$unit_pricing_measure_xml."</g:unit_pricing_measure>".PHP_EOL;
		} else {
		   if (get_post_meta($postId, '_xfgmc_unit_pricing_measure', true) !== '') {					
			   $unit_pricing_measure_xml = get_post_meta($postId, '_xfgmc_unit_pricing_measure', true);
			   $pricing_measure_xml = "<g:unit_pricing_measure>".$unit_pricing_measure_xml."</g:unit_pricing_measure>".PHP_EOL;
		   } 
		}

		if (class_exists('WooCommerce_Germanized')) {	
			// https://plugintests.com/plugins/wporg/woocommerce-germanized/latest/structure/classes/WC_GZD_Product 
			if (!empty(wc_gzd_get_gzd_product($var_id)->get_unit_product())) {
				$pricing_measure_xml = "<g:unit_pricing_measure>".wc_gzd_get_gzd_product($var_id)->get_unit_product().$unit_germanized."</g:unit_pricing_measure>".PHP_EOL;
			};
		}
		$result_xml .= $pricing_measure_xml;

		if (get_post_meta($var_id, '_xfgmc_unit_pricing_base_measure', true) !== '') {					
		   $unit_pricing_base_measure_xml = get_post_meta($var_id, '_xfgmc_unit_pricing_base_measure', true);
		   $base_measure_xml = "<g:unit_pricing_base_measure>".$unit_pricing_base_measure_xml."</g:unit_pricing_base_measure>".PHP_EOL;
		} else {
		   if (get_post_meta($postId, '_xfgmc_unit_pricing_base_measure', true) !== '') {					
			   $unit_pricing_base_measure_xml = get_post_meta($postId, '_xfgmc_unit_pricing_base_measure', true);
			   $base_measure_xml = "<g:unit_pricing_base_measure>".$unit_pricing_base_measure_xml."</g:unit_pricing_base_measure>".PHP_EOL;
		   } 
		}
		if (class_exists('WooCommerce_Germanized')) {
			if (!empty(wc_gzd_get_gzd_product($var_id)->get_unit_base())) {
				$base_measure_xml = "<g:unit_pricing_base_measure>".wc_gzd_get_gzd_product($var_id)->get_unit_base().$unit_germanized."</g:unit_pricing_base_measure>".PHP_EOL;
			};
		}
		$result_xml .= $base_measure_xml;


		do_action('xfgmc_append_variable_offer', $numFeed);
		$result_xml = apply_filters('xfgmc_append_variable_offer_filter', $result_xml, $product, $offer, $result_url, $numFeed); /* с версии 2.2.1 добавлен $result_url */

		$result_xml .= '</item>'.PHP_EOL;
		$n++;
		
		do_action('xfgmc_after_variable_offer', $numFeed);
		
		$ids_in_xml .= $postId.';'.$offer_id.';'.$price_xml.';'.PHP_EOL; /* с версии 2.0.0 */

		/* с версии 1.1.3 */
		$one_variable = xfgmc_optionGET('xfgmc_one_variable', $numFeed, 'set_arr');
		if ($one_variable == 'on') {break;}	
		$stop_flag = apply_filters('xfgmc_after_variable_offer_stop_flag', $stop_flag, $i, $n, $variation_count, $offer_id, $offer, $special_data_for_flag, $numFeed); /* с версии 2.2.7 в фильтр добавелн $special_data_for_flag */
		if ($stop_flag == true) {break;}		
	} // end for ($i = 0; $i<$variation_count; $i++) 
	
	xfgmc_error_log('FEED № '.$numFeed.'; Все вариации выгрузили. Файл: functions.php; Строка: '.__LINE__, 0);	
	 xfgmc_error_log('FEED № '.$numFeed.'; Все вариации выгрузили. Файл: functions.php; Строка: '.__LINE__, 0);	
	xfgmc_error_log('FEED № '.$numFeed.'; Все вариации выгрузили. Файл: functions.php; Строка: '.__LINE__, 0);	
	$result_arr = array('result_xml' => $result_xml, 'ids_in_xml' => $ids_in_xml, 'skip_reason' => $skip_reason);
	return $result_arr; // все вариации выгрузили	
 } // end if ($product->is_type('variable'))	 
 /* end Вариации */

  xfgmc_error_log('FEED № '.$numFeed.'; У нас обычный товар. Файл: offer.php; Строка: '.__LINE__, 0);

 // если цена не указана - пропускаем товар
 $price_xml = $product->get_price();
 if ($price_xml == 0 || empty($price_xml)) { 
	xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет цены; Файл: offer.php; Строка: '.__LINE__, 0); 
	$result_arr['skip_reason'][] = __('Price not specified', 'xfgmc');
	return $result_arr;
 }

 if (class_exists('XmlforGoogleMerchantCenterPro')) {
	if ((xfgmc_optionGET('xfgmcp_compare_value', $numFeed, 'set_arr') !== false) && (xfgmc_optionGET('xfgmcp_compare_value', $numFeed, 'set_arr') !== '')) {
		$xfgmcp_compare_value = xfgmc_optionGET('xfgmcp_compare_value', $numFeed, 'set_arr');
		$xfgmcp_compare = xfgmc_optionGET('xfgmcp_compare', $numFeed, 'set_arr');			 
		if ($xfgmcp_compare == '>=') {
			if ($price_xml < $xfgmcp_compare_value) {
				$result_arr['skip_reason'][] = __('The product price', 'xfgmc').' '. $product->get_price(). ': < ' . $xfgmcp_compare_value;
				return $result_arr;
			}
		} else {
			if ($price_xml >= $xfgmcp_compare_value) {
				$result_arr['skip_reason'][] = __('The product price', 'xfgmc').' '. $product->get_price(). ': >= ' . $xfgmcp_compare_value;
				return $result_arr;
			}
		}
	}
 }

 // пропуск товаров, которых нет в наличии
 $xfgmc_skip_missing_products = xfgmc_optionGET('xfgmc_skip_missing_products', $numFeed, 'set_arr');
 if ($xfgmc_skip_missing_products == 'on') {
	if ($product->is_in_stock() == false) { 
		xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к нет в наличии; Файл: offer.php; Строка: '.__LINE__, 0); 
		$result_arr['skip_reason'][] = __('Skip missing products', 'xfgmc');
		return $result_arr;
	}
 }		  

 // пропускаем товары на предзаказ
 $skip_backorders_products = xfgmc_optionGET('xfgmc_skip_backorders_products', $numFeed, 'set_arr');
 if ($skip_backorders_products == 'on') {
	if ($product->get_manage_stock() == true) { // включено управление запасом  
		if (($product->get_stock_quantity() < 1) && ($product->get_backorders() !== 'no')) { 
			xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к запрещен предзаказ и включено управление запасом; Файл: offer.php; Строка: '.__LINE__, 0); 
			$result_arr['skip_reason'][] = __('Skip backorders products', 'xfgmc');
			return $result_arr; 	
			/*continue;*/
		}
	} else {
		if ($product->get_stock_status() !== 'instock') { 
			xfgmc_error_log('FEED № '.$numFeed.'; Товар с postId = '.$postId.' пропущен т.к запрещен предзаказ; Файл: offer.php; Строка: '.__LINE__, 0); 
			$result_arr['skip_reason'][] = __('Skip backorders products', 'xfgmc');			
			return $result_arr; 
			/*continue;*/
		}
	}
 }   
		  
 do_action('xfgmc_before_simple_offer', $numFeed);
		  
 /* Обычный товар */
 $result_xml .= '<item>'.PHP_EOL; 
 
 do_action('xfgmc_prepend_simple_offer', $numFeed);

 $result_xml_id = '<g:id>'.$postId.'</g:id>'.PHP_EOL;
 $xfgmc_instead_of_id = xfgmc_optionGET('xfgmc_instead_of_id', $numFeed, 'set_arr');
 if ($xfgmc_instead_of_id === 'sku') {
	$sku_xml = $product->get_sku(); // артикул
	if (!empty($sku_xml)) {$result_xml_id = '<g:id>'.htmlspecialchars($sku_xml).'</g:id>'.PHP_EOL;}
 }
 $result_xml_id = apply_filters('xfgmc_simple_result_xml_id', $result_xml_id, $product, $xfgmc_instead_of_id, $numFeed); /* c версии 2.3.12 */
 $result_xml .= $result_xml_id;  
 $result_xml .= "<g:title>".htmlspecialchars($result_xml_name, ENT_NOQUOTES)."</g:title>".PHP_EOL;
		
 // описание
 $result_xml .= $result_xml_desc;
 
 // категория гугл
 if ($result_xml_google_cat !== '') {
	$result_xml .= $result_xml_google_cat;
 } else {
	if (get_term_meta($catid, 'xfgmc_google_product_category', true) !== '') {
		$xfgmc_google_product_category = get_term_meta($catid, 'xfgmc_google_product_category', true);
		$result_xml_google_cat = '<g:google_product_category>'.htmlspecialchars($xfgmc_google_product_category).'</g:google_product_category>'.PHP_EOL;
		$result_xml_google_cat = apply_filters('xfgmc_xml_google_cat_filter', $result_xml_google_cat, $catid, $product, $numFeed);
		$result_xml .= $result_xml_google_cat;
	} 		
 }
 // категория фейсбук
 if ($result_xml_facebook_cat !== '') {
	$result_xml .= $result_xml_facebook_cat;
 } else {
	if (get_term_meta($catid, 'xfgmc_fb_product_category', true) !== '') {
		$xfgmc_fb_product_category = get_term_meta($catid, 'xfgmc_fb_product_category', true);
		$result_xml_facebook_cat = '<g:fb_product_category>'.htmlspecialchars($xfgmc_fb_product_category).'</g:fb_product_category>'.PHP_EOL;
		$result_xml_facebook_cat = apply_filters('xfgmc_xml_facebook_cat_filter', $result_xml_facebook_cat, $catid, $product, $numFeed);	
		$result_xml .= $result_xml_facebook_cat;
	} 		
 }

 if ($xfgmc_tax_info === 'enabled') {
	if ($result_xml_tax_category !== '') {
		$result_xml .= $result_xml_tax_category;
	} else {
		if (get_term_meta($catid, 'xfgmc_tax_category', true) !== '') {
			$xfgmc_tax_category = get_term_meta($catid, 'xfgmc_tax_category', true);
			$result_xml_tax_category = '<g:tax_category>'.htmlspecialchars($xfgmc_tax_category).'</g:tax_category>'.PHP_EOL;
			$result_xml_tax_category = apply_filters('xfgmc_xml_google_tax_category_simple_filter', $result_xml_tax_category, $catid, $product, $numFeed);	
			$result_xml .= $result_xml_tax_category;
		} 		
	}
 } 

 $xfgmc_product_type = xfgmc_optionGET('xfgmc_product_type', $numFeed, 'set_arr');
 if ($xfgmc_product_type === 'enabled') {
	$product_type_res = xfgmc_product_type($catid, $numFeed);
	$product_type_res = apply_filters('xfgmc_product_type_res_simple_filter', $product_type_res, $catid, $product, $numFeed);	
	if ($product_type_res === '') {} else {
		$result_xml .= '<g:product_type>'.htmlspecialchars($product_type_res, ENT_NOQUOTES).'</g:product_type>';
	}
 }

 $result_url = htmlspecialchars(get_permalink($product->get_id())); // урл товара
 $result_url = apply_filters('xfgmc_url_filter_simple', $result_url, $product, $catid, $numFeed); /* с версии 2.0.5 */
 $result_xml .= "<g:link>".$result_url."</g:link>".PHP_EOL;	
 
 // убираем default.png из фида
 $no_default_png_products = xfgmc_optionGET('xfgmc_no_default_png_products', $numFeed, 'set_arr');
 if (($no_default_png_products === 'on') && (!has_post_thumbnail($postId))) {$picture_xml = '';} else { 
	$thumb_id = get_post_thumbnail_id($postId);
	$thumb_url = wp_get_attachment_image_src($thumb_id, 'full', true);	
	$thumb_xml = $thumb_url[0]; /* урл оригинал миниатюры товара */
	$picture_xml = '<g:image_link>'.xfgmc_deleteGET($thumb_xml).'</g:image_link>'.PHP_EOL;
 }
 $picture_xml = apply_filters('xfgmc_pic_simple_offer_filter', $picture_xml, $product, $numFeed);
 $result_xml .= $picture_xml;
		   
 if ($product->get_manage_stock() == true) { // включено управление запасом
	if ($product->get_stock_quantity() > 0) {
		$available = $in_stock;
	} else {
		if ($product->get_backorders() === 'no') { // предзаказ запрещен
			$available = $out_of_stock;
		} else {
			$xfgmc_behavior_onbackorder = xfgmc_optionGET('xfgmc_behavior_onbackorder', $numFeed, 'set_arr');
			switch ($xfgmc_behavior_onbackorder) { 
				case "out_of_stock": 
					$available = $out_of_stock; 
				break;
				case "in_stock": 
					$available = $in_stock;
				break;
				case "onbackorder": 
					$available = $onbackorder;
				break;
				default:
					$available = $onbackorder;
			}	
		}
	}
 } else { // отключено управление запасом
	if ($product->get_stock_status() === 'instock') {
		$available = $in_stock;
	} else if ($product->get_stock_status() === 'outofstock') { 
		$available = $out_of_stock;
	} else {
		$xfgmc_behavior_onbackorder = xfgmc_optionGET('xfgmc_behavior_onbackorder', $numFeed, 'set_arr');
		switch ($xfgmc_behavior_onbackorder) { 
			case "out_of_stock": 
				$available = $out_of_stock; 
			break;
			case "in_stock": 
				$available = $in_stock;
			break;
			case "onbackorder": 
				$available = $onbackorder;
			break;
			default:
				$available = $onbackorder;
		}	
	}
 }

 $xfgmc_g_stock = xfgmc_optionGET('xfgmc_g_stock', $numFeed, 'set_arr');
 if ($xfgmc_g_stock === 'enabled') {
	if ($product->get_manage_stock() == true) { // включено управление запасом  
		$stock_quantity = $product->get_stock_quantity();
		$result_xml .= '<g:quantity>'.$stock_quantity.'</g:quantity>'.PHP_EOL;
	}
 } 
 /*
 if ($product->get_manage_stock() == true) { // включено управление запасом
	if ($product->get_stock_quantity() > 0) {
		$available = $in_stock;
	} else {
		$available = $out_of_stock;
	}
 } else { // отключено управление запасом
	if ($product->is_in_stock() == true) {$available = $in_stock;} else {$available = $out_of_stock;}
 } */
 $result_xml .= '<g:availability>'.$available.'</g:availability>'.PHP_EOL; 
 
 $result_xml .= $result_identifier_exists;
 
 $result_xml .= $result_adult; 

 $result_xml .= $result_is_bundle;

 $result_xml .= $result_multipack;
 
 $result_xml .= $result_condition;

 $result_xml .= $result_custom_label;

 $price_xml = apply_filters('xfgmc_simple_price_xml_filter', $price_xml, $product, $numFeed); /* с версии 2.0.6 */
 $xfgmc_sale_price = xfgmc_optionGET('xfgmc_sale_price', $numFeed, 'set_arr');
 if ($xfgmc_sale_price === 'yes') {
	$sale_price = (float)$product->get_sale_price();
	$sale_price = apply_filters('xfgmc_simple_sale_price_xml_filter', $sale_price, $product, $numFeed); /* с версии 2.3.1 */
	$price_xml = (float)$price_xml;
	if (($sale_price > 0) && ($price_xml === $sale_price)) {
		$sale_price_xml = $product->get_regular_price();
		$result_xml .= '<g:price>'.$sale_price_xml.' '.$currencyId_xml.'</g:price>'.PHP_EOL;
		$result_xml .= '<g:sale_price>'.$price_xml.' '.$currencyId_xml.'</g:sale_price>'.PHP_EOL;

		$sales_price_from = $product->get_date_on_sale_from();
		$sales_price_to = $product->get_date_on_sale_to();		
		if (!empty($sales_price_from) && !empty($sales_price_to)) {
			$sales_price_from = date(DATE_ISO8601, strtotime($sales_price_from));
			$sales_price_to = date(DATE_ISO8601, strtotime($sales_price_to));			
			$result_xml .='<g:sale_price_effective_date>'.$sales_price_from.'/'.$sales_price_to.'</g:sale_price_effective_date>'.PHP_EOL;
		}

		$result_xml = apply_filters('xfgmc_simple_sale_price_filter', $result_xml, $product, $numFeed); /* с версии 2.0.6 */
	} else {
		$result_xml .= '<g:price>'.$price_xml.' '.$currencyId_xml.'</g:price>'.PHP_EOL;
	}
 } else {
	$result_xml .= '<g:price>'.$price_xml.' '.$currencyId_xml.'</g:price>'.PHP_EOL;
 } 

 $result_xml .= $result_shipping_label; 
 $result_xml .= $result_return_rule_label;
 $result_xml .= $result_store_code;
 $result_xml .= $result_min_handling_time;
 $result_xml .= $result_max_handling_time; 
 $result_xml .= $xfgmc_shipping_xml;

 // вес
 $weight_xml = $product->get_weight();
 if (!empty($weight_xml)) {
	$xfgmc_def_shipping_weight_unit = xfgmc_optionGET('xfgmc_def_shipping_weight_unit', $numFeed, 'set_arr');
	if ($xfgmc_def_shipping_weight_unit == '') {$xfgmc_def_shipping_weight_unit = 'kg';}
	$xfgmc_def_shipping_weight_unit = apply_filters('xfgmc_simple_def_shipping_weight_unit_filter', $xfgmc_def_shipping_weight_unit, $product, $numFeed); /* с версии 2.4.0 */
	$weight_xml = round(wc_get_weight($weight_xml, $xfgmc_def_shipping_weight_unit), 3);
	$result_xml .= "<g:shipping_weight>".$weight_xml." ".$xfgmc_def_shipping_weight_unit."</g:shipping_weight>".PHP_EOL;
 }

 /*$dimensions = $product->get_dimensions();
 if (!empty($dimensions)) {*/
 $dimensions = wc_format_dimensions($product->get_dimensions(false));
 if ($product->has_dimensions()) {
	$length_xml = $product->get_length();
	if (!empty($length_xml)) {
		$length_xml = round(wc_get_dimension($length_xml, 'cm'), 3);	
		$result_xml .= "<g:shipping_length>".$length_xml." cm</g:shipping_length>".PHP_EOL;
	}
	
	$width_xml = $product->get_width();
	if (!empty($width_xml)) {
		$width_xml = round(wc_get_dimension($width_xml, 'cm'), 3);
		$result_xml .= "<g:shipping_width>".$width_xml." cm</g:shipping_width>".PHP_EOL;	
	}
	   
	$height_xml = $product->get_height();
	if (!empty($length_xml)) {
		$height_xml = round(wc_get_dimension($height_xml, 'cm'), 3);
		$result_xml .= "<g:shipping_height>".$height_xml." cm</g:shipping_height>".PHP_EOL;
	}
 }
 
 // штрихкод			 
 $gtin = xfgmc_optionGET('xfgmc_gtin', $numFeed, 'set_arr');
 switch ($gtin) { /* disabled, sku, post_meta или id */
	case "disabled":	
			// выгружать штрихкод нет нужды
	break; 
	case "no":	
		if ($xfgmc_identifier_exists !== 'no') {
			$result_xml .= "<g:gtin></g:gtin>".PHP_EOL;
		}
	break; 
	case "sku":
	// выгружать из артикула
	$sku_xml = $product->get_sku();		
	if (!empty($sku_xml)) {
		$result_xml .= "<g:gtin>".$sku_xml."</g:gtin>".PHP_EOL;
	}			
	break;
	case "post_meta":
		$gtin_post_meta_id = xfgmc_optionGET('xfgmc_gtin_post_meta', $numFeed, 'set_arr');
		$gtin_post_meta_id = trim($gtin_post_meta_id);
		if (get_post_meta($postId, $gtin_post_meta_id, true) !== '') {
			$gtin_xml = get_post_meta($postId, $gtin_post_meta_id, true);
			$result_xml .= "<g:gtin>".$gtin_xml."</g:gtin>".PHP_EOL;
		}
	break;	
	case "germanized":
		if (class_exists('WooCommerce_Germanized')) {
			if (get_post_meta($postId, '_ts_gtin', true) !== '') {
				$gtin_xml = get_post_meta($postId, '_ts_gtin', true);
				$result_xml .= "<g:gtin>".$gtin_xml."</g:gtin>".PHP_EOL;
			}
		}
	break;			
	default:
		$gtin = (int)$gtin;
		$xfgmc_gtin_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($gtin));				
		if (!empty($xfgmc_gtin_xml)) {
			$result_xml .= '<g:gtin>'.xfgmc_replace_decode($xfgmc_gtin_xml).'</g:gtin>'.PHP_EOL;
		}
 }


 $mpn = xfgmc_optionGET('xfgmc_mpn', $numFeed, 'set_arr');
 switch ($mpn) { /* disabled, sku, post_meta или id */
	case "disabled":	
		// выгружать штрихкод нет нужды
	break; 
	case "no":
		if ($xfgmc_identifier_exists !== 'no') {
			$result_xml .= "<g:mpn></g:mpn>".PHP_EOL;
		}
	break;
	case "sku":
	// выгружать из артикула
	$sku_xml = $product->get_sku();		
	if (!empty($sku_xml)) {
		$result_xml .= "<g:mpn>".htmlspecialchars($sku_xml)."</g:mpn>".PHP_EOL;
	}			
	break;
	case "post_meta":
		$mpn_post_meta_id = xfgmc_optionGET('xfgmc_mpn_post_meta', $numFeed, 'set_arr');
		$mpn_post_meta_id = trim($mpn_post_meta_id);
		if (get_post_meta($postId, $mpn_post_meta_id, true) !== '') {
			$mpn_xml = get_post_meta($postId, $mpn_post_meta_id, true);
			$result_xml .= "<g:mpn>".htmlspecialchars($mpn_xml)."</g:mpn>".PHP_EOL;
		}
	break;	
	case "germanized":
		if (class_exists('WooCommerce_Germanized')) {
			if (get_post_meta($postId, '_ts_mpn', true) !== '') {
				$mpn_xml = get_post_meta($postId, '_ts_mpn', true);
				$result_xml .= "<g:mpn>".htmlspecialchars($mpn_xml)."</g:mpn>".PHP_EOL;
			}
		}
	break;
	default:
		$mpn = (int)$mpn;
		$xfgmc_mpn_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($mpn));				
		if (!empty($xfgmc_mpn_xml)) {
			$result_xml .= '<g:mpn>'.xfgmc_replace_decode($xfgmc_mpn_xml).'</g:mpn>'.PHP_EOL;
		}
 }

 // возраст
 $age = xfgmc_optionGET('xfgmc_age', $numFeed, 'set_arr');
 switch ($age) { /* disabled, sku, post_meta или id */
	case "disabled":	
		// выгружать штрихкод нет нужды
	break; 
	case "default_value":
		$xfgmc_age_group_post_meta = xfgmc_optionGET('xfgmc_age_group_post_meta', $numFeed, 'set_arr');
		$result_xml .= "<g:age_group>".$xfgmc_age_group_post_meta."</g:age_group>".PHP_EOL;
	break;		
	case "post_meta":
		$age_post_meta_id = xfgmc_optionGET('xfgmc_age_group_post_meta', $numFeed, 'set_arr');
		$age_post_meta_id = trim($age_post_meta_id);
		if (get_post_meta($postId, $age_post_meta_id, true) !== '') {
			$age_xml = get_post_meta($postId, $age_post_meta_id, true);
			$result_xml .= "<g:age_group>".$age_xml."</g:age_group>".PHP_EOL;
		}
	break;				 		 
	default:
		$age = (int)$age;
		$age_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($age));
		if (!empty($age_xml)) {	
			$result_xml .= "<g:age_group>".ucfirst(xfgmc_replace_decode($age_xml))."</g:age_group>".PHP_EOL;		
		}
 }
 
 // brand [марка]
 $brand = xfgmc_optionGET('xfgmc_brand', $numFeed, 'set_arr');
 if (!empty($brand) && $brand !== 'off') {	 
	if ((is_plugin_active('perfect-woocommerce-brands/perfect-woocommerce-brands.php') || is_plugin_active('perfect-woocommerce-brands/main.php') || class_exists('Perfect_Woocommerce_Brands')) && $brand === 'sfpwb') {
		$barnd_terms = get_the_terms($product->get_id(), 'pwb-brand');
		if ($barnd_terms !== false) {
		 foreach($barnd_terms as $barnd_term) {
			$result_xml .= '<g:brand>'. $barnd_term->name .'</g:brand>'.PHP_EOL;
			break;
		 }
		}
	} else if ((is_plugin_active('premmerce-woocommerce-brands/premmerce-brands.php')) && ($brand === 'premmercebrandsplugin')) {
		$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
		if ($barnd_terms !== false) {
		 foreach($barnd_terms as $barnd_term) {
			$result_xml .= '<g:brand>'. $barnd_term->name .'</g:brand>'.PHP_EOL;
			break;
		 }
		}
	} else if ((is_plugin_active('woocommerce-brands/woocommerce-brands.php')) && ($brand === 'woocommerce_brands')) {
		$barnd_terms = get_the_terms($product->get_id(), 'product_brand');
		if ($barnd_terms !== false) {
		 foreach($barnd_terms as $barnd_term) {
			$result_xml .= '<g:brand>'. $barnd_term->name .'</g:brand>'.PHP_EOL;
			break;
		 }
		}
	} else if ($brand === 'post_meta') {
		$brand_post_meta_id = xfgmc_optionGET('xfgmc_brand_post_meta', $numFeed, 'set_arr');
		$brand_post_meta_id = trim($brand_post_meta_id);
		if (get_post_meta($postId, $brand_post_meta_id, true) !== '') {
			$brand_xml = get_post_meta($postId, $brand_post_meta_id, true);
			$result_xml .= "<g:brand>".$brand_xml."</g:brand>".PHP_EOL;
		}
	} else if ($brand === 'default_value') {
		$xfgmc_brand_post_meta = xfgmc_optionGET('xfgmc_brand_post_meta', $numFeed, 'set_arr');
		$result_xml .= "<g:brand>".$xfgmc_brand_post_meta."</g:brand>".PHP_EOL;		
	} else {		
		$brand = (int)$brand;
		$brand_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($brand));
		if (!empty($brand_xml)) {	
			$result_xml .= "<g:brand>".ucfirst(xfgmc_replace_decode($brand_xml))."</g:brand>".PHP_EOL;		
		}
	}
 } 
 
 // цвет
 $color = xfgmc_optionGET('xfgmc_color', $numFeed, 'set_arr');
 if (!empty($color) && $color !== 'off') {	
	$color = (int)$color;
	$color_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($color));
	if (!empty($color_xml)) {	
		$result_xml .= "<g:color>".ucfirst(xfgmc_replace_decode($color_xml))."</g:color>".PHP_EOL;		
	}
 } 
 
 // материал
 $material = xfgmc_optionGET('xfgmc_material', $numFeed, 'set_arr');
 if (!empty($material) && $material !== 'off') {	
	$material = (int)$material;
	$material_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($material));
	if (!empty($color_xml)) {	
		$result_xml .= "<g:material>".ucfirst(xfgmc_replace_decode($material_xml))."</g:material>".PHP_EOL;		
	}
 } 

 // узор
 $pattern = xfgmc_optionGET('xfgmc_pattern', $numFeed, 'set_arr');
 if (!empty($pattern) && $pattern !== 'off') {	
	$pattern = (int)$pattern;
	$pattern_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($pattern));
	if (!empty($pattern_xml)) {	
		$result_xml .= "<g:pattern>".ucfirst(xfgmc_replace_decode($pattern_xml))."</g:pattern>".PHP_EOL;		
	}
 } 
 
 // пол
 $gender = xfgmc_optionGET('xfgmc_gender', $numFeed, 'set_arr');
 if (!empty($gender) && $gender !== 'off') {	
	$gender = (int)$gender;
	$gender_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($gender));
	if (!empty($gender_xml)) {	
		$result_xml .= "<g:gender>".ucfirst(xfgmc_replace_decode($gender_xml))."</g:gender>".PHP_EOL;		
	} else {
 		$gender_alt = xfgmc_optionGET('xfgmc_gender_alt', $numFeed, 'set_arr');
		if ($gender_alt !== 'off') {
			$result_xml .= "<g:gender>".$gender_alt."</g:gender>".PHP_EOL;		
		}		
	}
 }
 
 // размер
 if (get_term_meta($catid, 'xfgmc_size', true) == '' || get_term_meta($catid, 'xfgmc_size', true) === 'default') {
	$size = xfgmc_optionGET('xfgmc_size', $numFeed, 'set_arr');
	if (!empty($size) && $size !== 'off') {	
		$size = (int)$size;
		$size_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size));
		if (!empty($size_xml)) {	
			$result_xml .= "<g:size>".ucfirst(xfgmc_replace_decode($size_xml))."</g:size>".PHP_EOL;		
		}
	}
 } else {
	$size = (int)get_term_meta($catid, 'xfgmc_size', true);
	$size_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size));
	if (!empty($size_xml)) {	
		$result_xml .= "<g:size>".ucfirst(xfgmc_replace_decode($size_xml))."</g:size>".PHP_EOL;		
	}
 }
 
 // тип размера
 if (get_term_meta($catid, 'xfgmc_size_type', true) == '' || get_term_meta($catid, 'xfgmc_size_type', true) === 'default') {
	$size_type = xfgmc_optionGET('xfgmc_size_type', $numFeed, 'set_arr');
	if (!empty($size_type) && $size_type !== 'off') {	
		$size_type = (int)$size_type;
		$size_type_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size_type));
		if (!empty($size_type_xml)) {	
			$result_xml .= "<g:size_type>".ucfirst(xfgmc_replace_decode($size_type_xml))."</g:size_type>".PHP_EOL;		
		} else {
			$size_type_alt = xfgmc_optionGET('xfgmc_size_type_alt', $numFeed, 'set_arr');
			if ($size_type_alt !== 'off') {
				$result_xml .= "<g:size_type>".$size_type_alt."</g:size_type>".PHP_EOL;		
			}
		}
	}
 } else {
	$size_type = (int)get_term_meta($catid, 'xfgmc_size_type', true);
	$size_type_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size_type));
	if (!empty($size_type_xml)) {	
		$result_xml .= "<g:size_type>".ucfirst(xfgmc_replace_decode($size_type_xml))."</g:size_type>".PHP_EOL;		
	} else {
		$size_type_alt = get_term_meta($catid, 'xfgmc_size_type_alt', true);
		// $size_type_alt = xfgmc_optionGET('xfgmc_size_type_alt', $numFeed, 'set_arr');
		if ($size_type_alt !== 'default') {
			$result_xml .= "<g:size_type>".$size_type_alt."</g:size_type>".PHP_EOL;		
		}
	}
 }
 
 // система размеров
 $size_system = xfgmc_optionGET('xfgmc_size_system', $numFeed, 'set_arr');
 if (!empty($size_system) && $size_system !== 'off') {	
	$size_system = (int)$size_system;
	$size_system_xml = $product->get_attribute(wc_attribute_taxonomy_name_by_id($size_system));
	if (!empty($size_system_xml)) {	
		$result_xml .= "<g:size_system>".ucfirst(xfgmc_replace_decode($size_system_xml))."</g:size_system>".PHP_EOL;		
	} else {
 		$size_system_alt = xfgmc_optionGET('xfgmc_size_system_alt', $numFeed, 'set_arr');
		if ($size_system_alt !== 'off') {
			$result_xml .= "<g:size_system>".$size_system_alt."</g:size_system>".PHP_EOL;		
		}		
	}
 } 

 if (get_post_meta($postId, '_xfgmc_unit_pricing_measure', true) !== '') {					
	$unit_pricing_measure_xml = get_post_meta($postId, '_xfgmc_unit_pricing_measure', true);
	$pricing_measure_xml = "<g:unit_pricing_measure>".$unit_pricing_measure_xml."</g:unit_pricing_measure>".PHP_EOL;
 } 
 if (class_exists('WooCommerce_Germanized')) {
	if (!empty(wc_gzd_get_gzd_product($postId)->get_unit_product())) {
		$pricing_measure_xml = "<g:unit_pricing_measure>".wc_gzd_get_gzd_product($postId)->get_unit_product().$unit_germanized."</g:unit_pricing_measure>".PHP_EOL;
	}; 
 } 
 $result_xml .= $pricing_measure_xml;

 if (get_post_meta($postId, '_xfgmc_unit_pricing_base_measure', true) !== '') {					
	$unit_pricing_base_measure_xml = get_post_meta($postId, '_xfgmc_unit_pricing_base_measure', true);
	$base_measure_xml = "<g:unit_pricing_base_measure>".$unit_pricing_base_measure_xml."</g:unit_pricing_base_measure>".PHP_EOL;
 }
 if (class_exists('WooCommerce_Germanized')) {
	if (!empty(wc_gzd_get_gzd_product($postId)->get_unit_base())) {
		$base_measure_xml = "<g:unit_pricing_base_measure>".wc_gzd_get_gzd_product($postId)->get_unit_base().$unit_germanized."</g:unit_pricing_base_measure>".PHP_EOL;
	};
 }
 $result_xml .= $base_measure_xml;
 
	  
 do_action('xfgmc_append_simple_offer', $numFeed); 
 $result_xml = apply_filters('xfgmc_append_simple_offer_filter', $result_xml, $product, $result_url, $numFeed); /* с версии 2.2.1 добавлены $result_url, $numFeed */

 $result_xml .= '</item>'.PHP_EOL;
		  
 do_action('xfgmc_after_simple_offer', $numFeed);
 
 $ids_in_xml .= $postId.';'.$postId.';'.$price_xml.';'.PHP_EOL;
 
 $result_arr = array('result_xml' => $result_xml, 'ids_in_xml' => $ids_in_xml, 'skip_reason' => $skip_reason);
 return $result_arr;

 /*
 *	[unit_pricing_measure] ==> Regulärer Grundpreis (€) - _unit_price_regular - variable_unit_price_regular[0]
 *	[unit_pricing_base_measure] ==> Grundpreiseinheiten - _unit_base _unit_product
 */ 
} // end function xfgmc_unit($postId) {
?>