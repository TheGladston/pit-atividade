<?php if ( ! defined('ABSPATH') ) { exit; }
function xfgmc_extensions_page() { ?>
	<style>.button-primary {text-align: center; margin: 0 auto !important;} .xfgmc_banner {max-width: 100%}</style>
	<div class="wrap"> 
		<!-- h1 style="font-size: 32px; text-align: center; color: #5b2942;"><?php _e('Extensions for XML for Google Merchant Center', 'xfgmc'); ?></h1 --> 
		<div id="dashboard-widgets-wrap"><div id="dashboard-widgets" class="metabox-holder">	
			<div id="postbox-container-1"><div class="meta-box-sortables">
				<div class="postbox">
					<a href="https://icopydoc.ru/product/plagin-xml-for-google-merchant-center-pro/?utm_source=xml-for-google-merchant-center&utm_medium=organic&utm_campaign=in-plugin-xml-for-google-merchant-center&utm_content=extensions&utm_term=banner-xml-pro" target="_blank"><img class="xfgmc_banner" src="<?php echo XFGMC_PLUGIN_DIR_URL; ?>/img/xml-for-google-merchant-center-pro-banner.jpg" alt="Upgrade to XML for Google Merchant Center Pro" /></a>
					<div class="inside">
						<table class="form-table"><tbody>
							<tr>
								<td class="overalldesc" style="font-size: 18px;">
									<h1 style="font-size: 24px; text-align: center; color: #5b2942;">XML for Google Merchant Center Pro</h1>
									<ul style="text-align: center;">
										<li>&#10004; <?php _e('The ability to exclude products from certain categories', 'xfgmc'); ?>;</li>
										<li>&#10004; <?php _e('Ability to exclude products by certain tags', 'xfgmc'); ?>;</li>
										<li>&#10004; <?php _e('The ability to exclude products at a price', 'xfgmc'); ?>;</li>	
										<li>&#10004; <?php _e('The ability to unload only products marked with a checkbox', 'xfgmc'); ?>;</li>
										<li>&#10004; <?php _e('Ability to assign labels as categories', 'xfgmc'); ?>;</li>
										<li>&#10004; <?php _e('Ability to download multiple images for products instead of one', 'xfgmc'); ?>;</li>
										<li>&#10004; <?php _e('Ability to remove the Visual Composer shortcodes from the description', 'xfgmc'); ?>;</li>			
										<li>&#10004; <?php _e('The ability to add one attribute to the beginning of the product name, add three attributes to the end of the product name', 'xfgmc'); ?>;</li>
										<li>&#10004; <?php _e('Support UTM tags', 'xfgmc'); ?>;</li>
										<li>&#10004; <?php _e('Even more stable work', 'xfgmc'); ?>!</li>
									</ul>
									<p style="text-align: center;"><a class="button-primary" href="https://icopydoc.ru/<?php if (version_compare(get_bloginfo('version'),'4.7', '>=')) {$res = get_user_locale(); if ($res !== 'ru_RU') {$lang = 'en/';} else {$lang = '';} echo $lang;} ?>product/plagin-xml-for-google-merchant-center-pro/?utm_source=xml-for-google-merchant-center&utm_medium=organic&utm_campaign=in-plugin-xml-for-google-merchant-center&utm_content=extensions&utm_term=poluchit-xml-google-pro" target="_blank"><?php _e('Get XML for Google Merchant Center Pro Now', 'xfgmc'); ?></a><br /></p>		   
								</td>
							</tr>
						</tbody></table>
					</div>
				</div> 
			</div></div> 
		</div></div> 
	</div> 
<?php
} /* end функция расширений xfgmc_extensions_page */
?>