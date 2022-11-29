<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Settings Page
*
* @link			https://icopydoc.ru/
* @since		1.9.0
*/

class XFGMC_Settings_Page {
	private $feed_id;
	private $feedback;

	public function __construct() {
		$this->feedback = new XFGMC_Feedback();

		$this->init_hooks(); // подключим хуки
		$this->listen_submit();

		$this->get_html_form();		
	}

	public function get_html_form() { ?>
		<div class="wrap">
  			<h1><?php _e('Exporter Google Merchant Center', 'xfgmc'); ?></h1>
			<?php $this->get_html_banner(); ?>
			<div id="poststuff">
				<?php $this->get_html_feeds_list(); ?>

				<div id="post-body" class="columns-2">

					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_info_block(); ?>
								
							<?php do_action('xfgmc_before_support_project'); ?>

							<?php $this->feedback->get_block_support_project(); ?>

							<?php do_action('xfgmc_between_container_1', $this->get_feed_id()); ?>
							
							<?php $this->feedback->get_form(); ?>

							<?php do_action('xfgmc_append_container_1', $this->get_feed_id()); ?>
						</div>
					</div><!-- /postbox-container-1 -->

					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables"><?php 
							if (isset($_GET['tab'])) {$tab = $_GET['tab'];} else {$tab = 'main_tab';}
							echo $this->get_html_tabs($tab); ?>

							<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
								<?php do_action('xfgmc_prepend_form_container_2', $this->get_feed_id()); ?>
								<input type="hidden" name="xfgmc_num_feed_for_save" value="<?php echo $this->get_feed_id(); ?>">
								<?php switch ($tab) : 
									case 'main_tab' : ?>
										<?php $this->get_html_main_settings(); ?>
										<?php break;
									case 'shop_data' : ?>
										<?php $this->get_html_shop_data(); ?>
										<?php $this->get_html_shipping_setting(); ?>
										<?php break;										
									case 'tags' : ?>
										<?php $this->get_html_tags_settings(); ?>
										<?php $xfgms_settings_feed_wp_list_table = new XFGMC_Settings_Feed_WP_List_Table($this->get_feed_id()); ?>
										<?php $xfgms_settings_feed_wp_list_table->prepare_items(); $xfgms_settings_feed_wp_list_table->display(); ?> 
										<?php break;
									case 'filtration': ?>
										<?php $this->get_html_filtration(); ?>										
										<?php do_action('xfgmc_after_main_param_block', $this->get_feed_id()); ?>
								<?php break; ?>
								<?php endswitch; ?>

								<?php do_action('xfgmc_after_optional_elemet_block', $this->get_feed_id()); ?>
								<div class="postbox">
									<div class="inside">
										<table class="form-table"><tbody>
											<tr>
												<th scope="row"><label for="button-primary"></label></th>
												<td class="overalldesc"><?php wp_nonce_field('xfgmc_nonce_action', 'xfgmc_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="xfgmc_submit_action" value="<?php 
												if ($tab === 'main_tab') {
													echo __('Save', 'xfgmc').' & '. __('Create feed', 'xfgmc'); 
												} else {
													_e('Save', 'xfgmc');
												}
												?>"/><br />
												<span class="description"><small><?php _e('Click to save the settings', 'xfgmc'); ?><small></span></td>
											</tr>
										</tbody></table>
									</div>
								</div>
							</form>
						</div>
					</div><!-- /postbox-container-2 -->

				</div>
			</div><!-- /poststuff -->
			<?php $this->get_html_icp_banners(); ?>
			<?php $this->get_html_my_plugins_list(); ?>
		</div><?php // end get_html_form();
	}

	public function get_html_banner() {
		return '<div class="notice notice-info">
			<p><span class="xfgmc_bold">XML for Google Merchant Center Pro</span> - '. __('a necessary extension for those who want to', 'xfgmc').' <span class="xfgmc_bold" style="color: green;">'. __('save on advertising budget', 'xfgmc').'</span> '. __('on Google', 'xfgmc').'! <a href="https://icopydoc.ru/product/plagin-xml-for-google-merchant-center-pro/?utm_source=xml-for-google-merchant-center&utm_medium=organic&utm_campaign=in-plugin-xml-for-google-merchant-center&utm_content=settings&utm_term=about-xml-google-pro"'. __('Learn More', 'xfgmc').'</a>.</p> 
		</div>';
	} // end get_html_banner();

	public function get_html_feeds_list() { 
		$xfgmcListTable = new XFGMC_WP_List_Table(); ?>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
			  <?php wp_nonce_field('xfgmc_nonce_action_add_new_feed', 'xfgmc_nonce_field_add_new_feed'); ?><input class="button" type="submit" name="xfgmc_submit_add_new_feed" value="<?php _e('Add New Feed', 'xfgmc'); ?>" />
		</form>
		<form method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<input type="hidden" name="xfgmc_form_id" value="xfgmc_wp_list_table" />
			<?php $xfgmcListTable->prepare_items(); $xfgmcListTable->display(); ?>
		</form><?php // end get_html_feeds_list();
	} // end get_html_feeds_list();

	public function get_html_info_block() { 
		$status_sborki = (int)xfgmc_optionGET('xfgmc_status_sborki', $this->get_feed_id());
		$xfgmc_file_url = urldecode(xfgmc_optionGET('xfgmc_file_url', $this->get_feed_id(), 'set_arr'));
		$xfgmc_date_sborki = xfgmc_optionGET('xfgmc_date_sborki', $this->get_feed_id(), 'set_arr');
		$xfgmc_date_sborki_end = xfgmc_optionGET('xfgmc_date_sborki_end', $this->get_feed_id(), 'set_arr');
		$xfgmc_status_cron = xfgmc_optionGET('xfgmc_status_cron', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_count_products_in_feed = xfgmc_optionGET('xfgmc_count_products_in_feed', $this->get_feed_id(), 'set_arr');
		?>
		<div class="postbox">
			<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';} ?>
			<h2 class="hndle"><?php _e('Feed', 'xfgmc'); ?> <?php echo $this->get_feed_id(); ?>: <?php if ($this->get_feed_id() !== '1') {echo $this->get_feed_id();} ?>feed-xml-<?php echo $cur_blog_id; ?>.xml <?php $assignment = xfgmc_optionGET('xfgmc_feed_assignment', $this->get_feed_id(), 'set_arr'); if ($assignment === '') {} else {echo '('.$assignment.')';} ?> <?php if (empty($xfgmc_file_url)) : ?><?php _e('not created yet', 'xfgmc'); ?><?php else : ?><?php if ($status_sborki !== -1) : ?><?php _e('updating', 'xfgmc'); ?><?php else : ?><?php _e('created', 'xfgmc'); ?><?php endif; ?><?php endif; ?></h2>	
			<div class="inside">
				<p><strong style="color: green;"><?php _e('Instruction', 'xfgmc'); ?>:</strong> <a href="https://icopydoc.ru/kak-sozdat-woocommerce-xml-instruktsiya/?utm_source=xml-for-google-merchant-center&utm_medium=organic&utm_campaign=in-plugin-xml-for-google-merchant-center&utm_content=settings&utm_term=main-instruction" target="_blank"><?php _e('How to create a XML-feed', 'xfgmc'); ?></a>.</p>
				<?php if (empty($xfgmc_file_url)) : ?> 
					<?php if ($status_sborki !== -1) : ?>
						<p><?php _e('We are working on automatic file creation. XML will be developed soon', 'xfgmc'); ?>.</p>
					<?php else : ?>		
						<p><?php _e('In order to do that, select another menu entry (which differs from "off") in the box called "Automatic file creation". You can also change values in other boxes if necessary, then press "Save"', 'xfgmc'); ?>.</p>
						<p><?php _e('After 1-7 minutes (depending on the number of products), the feed will be generated and a link will appear instead of this message', 'xfgmc'); ?>.</p>
					<?php endif; ?>
				<?php else : ?>
					<?php if ($status_sborki !== -1) : ?>
						<p><?php _e('We are working on automatic file creation. XML will be developed soon', 'xfgmc'); ?>.</p>
					<?php else : ?>
						<p><span class="fgmc_bold"><?php _e('Your XML feed here', 'xfgmc'); ?>:</span><br/><a target="_blank" href="<?php echo $xfgmc_file_url; ?>"><?php echo $xfgmc_file_url; ?></a>
						<br/><?php _e('File size', 'xfgmc'); ?>: <?php clearstatcache();
						if ($this->get_feed_id() === '1') {$prefFeed = '';} else {$prefFeed = $this->get_feed_id();}
						$upload_dir = (object)wp_get_upload_dir();
						if (is_multisite()) {
							$filename = $upload_dir->basedir."/".$prefFeed."feed-xml-".get_current_blog_id().".xml";
						} else {
							$filename = $upload_dir->basedir."/".$prefFeed."feed-xml-0.xml";				
						}
						if (is_file($filename)) {echo xfgmc_formatSize(filesize($filename));} else {echo '0 KB';} ?>
						<br/><?php _e('Start of generation ', 'xfgmc'); ?>: <?php echo $xfgmc_date_sborki; ?>
						<br/><?php _e('Generated', 'xfgmc'); ?>: <?php echo $xfgmc_date_sborki_end; ?>
						<br/><?php _e('Products', 'xfgmc'); ?>: <?php echo $xfgmc_count_products_in_feed; ?></p>
					<?php endif; ?>		
				<?php endif; ?>
				<p><?php _e('Please note that Google Merchant Center checks XML no more than 3 times a day! This means that the changes on the Google Merchant Center are not instantaneous', 'xfgmc'); ?>!</p>
			</div>
		</div><?php
	} // end get_html_info_block();

	public function get_html_tabs($current = 'main_tab') {
		$tabs = array(
			'main_tab' 		=> __('Main settings', 'xfgmc'),
			'shop_data'		=> __('Shop data', 'xfgmc'),			
			'tags'			=> __('Attribute settings', 'xfgmc'), 
			'filtration'	=> __('Filtration', 'xfgmc')
		);
		
		$html = '<div class="nav-tab-wrapper" style="margin-bottom: 10px;">';
			foreach ($tabs as $tab => $name) {
				if ($tab === $current) {
					$class = ' nav-tab-active';
				} else {
					$class = ''; 
				}
				if (isset($_GET['feed_id'])) {
					$nf = '&feed_id='.sanitize_text_field($_GET['feed_id']);
				} else {
					$nf = '';
				}
				$html .= sprintf('<a class="nav-tab%1$s" href="?page=xfgmcexport&tab=%2$s%3$s">%4$s</a>',$class, $tab, $nf, $name);
			}
		$html .= '</div>';

		return $html;
	} // end get_html_tabs();

	public function get_html_main_settings() { 	
		$xfgmc_status_cron = xfgmc_optionGET('xfgmc_status_cron', $this->get_feed_id(), 'set_arr');
		$xfgmc_ufup = xfgmc_optionGET('xfgmc_ufup', $this->get_feed_id(), 'set_arr');
		$xfgmc_feed_assignment = stripslashes(htmlspecialchars(xfgmc_optionGET('xfgmc_feed_assignment', $this->get_feed_id(), 'set_arr')));
		$xfgmc_adapt_facebook = xfgmc_optionGET('xfgmc_adapt_facebook', $this->get_feed_id(), 'set_arr');

		$xfgmc_target_country = xfgmc_optionGET('xfgmc_target_country', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_step_export = xfgmc_optionGET('xfgmc_step_export', $this->get_feed_id(), 'set_arr');  
		$xfgmc_cache = xfgmc_optionGET('xfgmc_cache', $this->get_feed_id(), 'set_arr'); 
		
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Main parameters', 'xfgmc'); ?> (<?php _e('Feed', 'xfgmc'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					
					<tr>
						<th scope="row"><label for="xfgmc_run_cron"><?php _e('Automatic file creation', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<select name="xfgmc_run_cron" id="xfgmc_run_cron">	
								<option value="off" <?php selected($xfgmc_status_cron, 'off' ); ?>><?php _e('Off', 'xfgmc'); ?></option>
								<?php $xfgmc_enable_five_min = xfgmc_optionGET('xfgmc_enable_five_min'); if ($xfgmc_enable_five_min === 'on') : ?>
								<option value="five_min" <?php selected($xfgmc_status_cron, 'five_min' );?> ><?php _e('Every five minutes', 'xfgmc'); ?></option>
								<?php endif; ?>
								<option value="hourly" <?php selected($xfgmc_status_cron, 'hourly' )?> ><?php _e('Hourly', 'xfgmc'); ?></option>
								<option value="six_hours" <?php selected($xfgmc_status_cron, 'six_hours' ); ?> ><?php _e('Every six hours', 'xfgmc'); ?></option>	
								<option value="twicedaily" <?php selected($xfgmc_status_cron, 'twicedaily' )?> ><?php _e('Twice a day', 'xfgmc'); ?></option>
								<option value="daily" <?php selected($xfgmc_status_cron, 'daily' )?> ><?php _e('Daily', 'xfgmc'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('The refresh interval on your feed', 'xfgmc'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_ufup"><?php _e('Update feed when updating products', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="xfgmc_ufup" id="xfgmc_ufup" <?php checked($xfgmc_ufup, 'on' ); ?>/>
						</td>
					</tr>
					<?php do_action('xfgmc_after_ufup_option', $this->get_feed_id()); /* С версии 2.1.0 */ ?>
					<tr>
						<th scope="row"><label for="xfgmc_feed_assignment"><?php _e('Feed assignment', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<input type="text" maxlength="25" name="xfgmc_feed_assignment" id="xfgmc_feed_assignment" value="<?php echo $xfgmc_feed_assignment; ?>" placeholder="<?php _e('For Google', 'xfgmc');?>" /><br />
							<span class="description"><small><?php _e('Not used in feed. Inner note for your convenience', 'xfgmc'); ?>.</small></span>
						</td>
					</tr>		 
					<tr>
						<th scope="row"><label for="xfgmc_adapt_facebook"><?php _e('Adapt for Facebook', 'xfgmc'); ?> (beta)</label></th>
						<td class="overalldesc">
							<select name="xfgmc_adapt_facebook" id="xfgmc_adapt_facebook">
								<option value="no" <?php selected($xfgmc_adapt_facebook, 'no' ); ?>><?php _e('No', 'xfgmc'); ?></option>
								<option value="yes" <?php selected($xfgmc_adapt_facebook, 'yes' ); ?>><?php _e('Yes', 'xfgmc'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('If you want to create a Facebook feed, set the value to ', 'xfgmc'); ?> "<?php _e('Yes', 'xfgmc'); ?>". <?php _e('If the feed is for Google Merchant Center, select', 'xfgmc'); ?> "<?php _e('No', 'xfgmc'); ?>"</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_target_country"><?php _e('Target country', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<select name="xfgmc_target_country" id="xfgmc_target_country">	
								<option value="BY" <?php selected($xfgmc_target_country, 'BY'); ?>><?php _e('Belarus', 'xfgmc'); ?></option>
								<option value="RU" <?php selected($xfgmc_target_country, 'RU'); ?>><?php _e('Russia', 'xfgmc'); ?></option>
								<option value="UA" <?php selected($xfgmc_target_country, 'UA'); ?>><?php _e('Ukraine', 'xfgmc'); ?></option>
								<option value="GB" <?php selected($xfgmc_target_country, 'GB'); ?>>United Kingdom</option>
								<option value="US" <?php selected($xfgmc_target_country, 'US'); ?>>United States</option>
								<option value="AF" <?php selected($xfgmc_target_country, 'AF'); ?>>Afghanistan</option>
								<option value="AX" <?php selected($xfgmc_target_country, 'AX'); ?>>Åland</option>
								<option value="AL" <?php selected($xfgmc_target_country, 'AL'); ?>>Albania</option>
								<option value="DZ" <?php selected($xfgmc_target_country, 'DZ'); ?>>Algeria</option>
								<option value="AS" <?php selected($xfgmc_target_country, 'AS'); ?>>American Samoa</option>
								<option value="AD" <?php selected($xfgmc_target_country, 'AD'); ?>>Andorra</option>
								<option value="AO" <?php selected($xfgmc_target_country, 'AO'); ?>>Angola</option>
								<option value="AI" <?php selected($xfgmc_target_country, 'AI'); ?>>Anguilla</option>
								<option value="AQ" <?php selected($xfgmc_target_country, 'AQ'); ?>>Antarctica</option>
								<option value="AG" <?php selected($xfgmc_target_country, 'AG'); ?>>Antigua and Barbuda</option>
								<option value="AR" <?php selected($xfgmc_target_country, 'AR'); ?>>Argentina</option>
								<option value="AM" <?php selected($xfgmc_target_country, 'AM'); ?>>Armenia</option>
								<option value="AW" <?php selected($xfgmc_target_country, 'AW'); ?>>Aruba</option>
								<option value="AU" <?php selected($xfgmc_target_country, 'AU'); ?>>Australia</option>
								<option value="AT" <?php selected($xfgmc_target_country, 'AT'); ?>>Austria</option>
								<option value="AZ" <?php selected($xfgmc_target_country, 'AZ'); ?>>Azerbaijan</option>
								<option value="BS" <?php selected($xfgmc_target_country, 'BS'); ?>>Bahamas</option>
								<option value="BH" <?php selected($xfgmc_target_country, 'BH'); ?>>Bahrain</option>
								<option value="BD" <?php selected($xfgmc_target_country, 'BD'); ?>>Bangladesh</option>
								<option value="BB" <?php selected($xfgmc_target_country, 'BB'); ?>>Barbados</option>
								<option value="BE" <?php selected($xfgmc_target_country, 'BE'); ?>>Belgium</option>
								<option value="BZ" <?php selected($xfgmc_target_country, 'BZ'); ?>>Belize</option>
								<option value="BJ" <?php selected($xfgmc_target_country, 'BJ'); ?>>Benin</option>
								<option value="BM" <?php selected($xfgmc_target_country, 'BM'); ?>>Bermuda</option>
								<option value="BT" <?php selected($xfgmc_target_country, 'BT'); ?>>Bhutan</option>
								<option value="BO" <?php selected($xfgmc_target_country, 'BO'); ?>>Bolivia</option>
								<option value="BQ" <?php selected($xfgmc_target_country, 'BQ'); ?>>Bonaire</option>
								<option value="BA" <?php selected($xfgmc_target_country, 'BA'); ?>>Bosnia and Herzegovina</option>
								<option value="BW" <?php selected($xfgmc_target_country, 'BW'); ?>>Botswana</option>
								<option value="BV" <?php selected($xfgmc_target_country, 'BV'); ?>>Bouvet Island</option>
								<option value="BR" <?php selected($xfgmc_target_country, 'BR'); ?>>Brazil</option>
								<option value="IO" <?php selected($xfgmc_target_country, 'IO'); ?>>British Indian Ocean Territory</option>
								<option value="VG" <?php selected($xfgmc_target_country, 'VG'); ?>>British Virgin Islands</option>
								<option value="BN" <?php selected($xfgmc_target_country, 'BN'); ?>>Brunei</option>
								<option value="BG" <?php selected($xfgmc_target_country, 'BG'); ?>>Bulgaria</option>
								<option value="BF" <?php selected($xfgmc_target_country, 'BF'); ?>>Burkina Faso</option>
								<option value="BI" <?php selected($xfgmc_target_country, 'BI'); ?>>Burundi</option>
								<option value="KH" <?php selected($xfgmc_target_country, 'KH'); ?>>Cambodia</option>
								<option value="CM" <?php selected($xfgmc_target_country, 'CM'); ?>>Cameroon</option>
								<option value="CA" <?php selected($xfgmc_target_country, 'CA'); ?>>Canada</option>
								<option value="CV" <?php selected($xfgmc_target_country, 'CV'); ?>>Cape Verde</option>
								<option value="KY" <?php selected($xfgmc_target_country, 'KY'); ?>>Cayman Islands</option>
								<option value="CF" <?php selected($xfgmc_target_country, 'CF'); ?>>Central African Republic</option>
								<option value="TD" <?php selected($xfgmc_target_country, 'TD'); ?>>Chad</option>
								<option value="CL" <?php selected($xfgmc_target_country, 'CL'); ?>>Chile</option>
								<option value="CN" <?php selected($xfgmc_target_country, 'CN'); ?>>China</option>
								<option value="CX" <?php selected($xfgmc_target_country, 'CX'); ?>>Christmas Island</option>
								<option value="CC" <?php selected($xfgmc_target_country, 'CC'); ?>>Cocos [Keeling] Islands</option>
								<option value="CO" <?php selected($xfgmc_target_country, 'CO'); ?>>Colombia</option>
								<option value="KM" <?php selected($xfgmc_target_country, 'KM'); ?>>Comoros</option>
								<option value="CK" <?php selected($xfgmc_target_country, 'CK'); ?>>Cook Islands</option>
								<option value="CR" <?php selected($xfgmc_target_country, 'CR'); ?>>Costa Rica</option>
								<option value="HR" <?php selected($xfgmc_target_country, 'HR'); ?>>Croatia</option>
								<option value="CU" <?php selected($xfgmc_target_country, 'CU'); ?>>Cuba</option>
								<option value="CW" <?php selected($xfgmc_target_country, 'CW'); ?>>Curacao</option>
								<option value="CY" <?php selected($xfgmc_target_country, 'CY'); ?>>Cyprus</option>
								<option value="CZ" <?php selected($xfgmc_target_country, 'CZ'); ?>>Czech Republic</option>
								<option value="CD" <?php selected($xfgmc_target_country, 'CD'); ?>>Democratic Republic of the Congo</option>
								<option value="DK" <?php selected($xfgmc_target_country, 'DK'); ?>>Denmark</option>
								<option value="DJ" <?php selected($xfgmc_target_country, 'DJ'); ?>>Djibouti</option>
								<option value="DM" <?php selected($xfgmc_target_country, 'DM'); ?>>Dominica</option>
								<option value="DO" <?php selected($xfgmc_target_country, 'DO'); ?>>Dominican Republic</option>
								<option value="TL" <?php selected($xfgmc_target_country, 'TL'); ?>>East Timor</option>
								<option value="EC" <?php selected($xfgmc_target_country, 'EC'); ?>>Ecuador</option>
								<option value="EG" <?php selected($xfgmc_target_country, 'EG'); ?>>Egypt</option>
								<option value="SV" <?php selected($xfgmc_target_country, 'SV'); ?>>El Salvador</option>
								<option value="GQ" <?php selected($xfgmc_target_country, 'GQ'); ?>>Equatorial Guinea</option>
								<option value="ER" <?php selected($xfgmc_target_country, 'ER'); ?>>Eritrea</option>
								<option value="EE" <?php selected($xfgmc_target_country, 'EE'); ?>>Estonia</option>
								<option value="ET" <?php selected($xfgmc_target_country, 'ET'); ?>>Ethiopia</option>
								<option value="FK" <?php selected($xfgmc_target_country, 'FK'); ?>>Falkland Islands</option>
								<option value="FO" <?php selected($xfgmc_target_country, 'FO'); ?>>Faroe Islands</option>
								<option value="FJ" <?php selected($xfgmc_target_country, 'FJ'); ?>>Fiji</option>
								<option value="FI" <?php selected($xfgmc_target_country, 'FI'); ?>>Finland</option>
								<option value="FR" <?php selected($xfgmc_target_country, 'FR'); ?>>France</option>
								<option value="GF" <?php selected($xfgmc_target_country, 'GF'); ?>>French Guiana</option>
								<option value="PF" <?php selected($xfgmc_target_country, 'PF'); ?>>French Polynesia</option>
								<option value="TF" <?php selected($xfgmc_target_country, 'TF'); ?>>French Southern Territories</option>
								<option value="GA" <?php selected($xfgmc_target_country, 'GA'); ?>>Gabon</option>
								<option value="GM" <?php selected($xfgmc_target_country, 'GM'); ?>>Gambia</option>
								<option value="GE" <?php selected($xfgmc_target_country, 'GE'); ?>>Georgia</option>
								<option value="DE" <?php selected($xfgmc_target_country, 'DE'); ?>>Germany</option>
								<option value="GH" <?php selected($xfgmc_target_country, 'GH'); ?>>Ghana</option>
								<option value="GI" <?php selected($xfgmc_target_country, 'GI'); ?>>Gibraltar</option>
								<option value="GR" <?php selected($xfgmc_target_country, 'GR'); ?>>Greece</option>
								<option value="GL" <?php selected($xfgmc_target_country, 'GL'); ?>>Greenland</option>
								<option value="GD" <?php selected($xfgmc_target_country, 'GD'); ?>>Grenada</option>
								<option value="GP" <?php selected($xfgmc_target_country, 'GP'); ?>>Guadeloupe</option>
								<option value="GU" <?php selected($xfgmc_target_country, 'GU'); ?>>Guam</option>
								<option value="GT" <?php selected($xfgmc_target_country, 'GT'); ?>>Guatemala</option>
								<option value="GG" <?php selected($xfgmc_target_country, 'GG'); ?>>Guernsey</option>
								<option value="GN" <?php selected($xfgmc_target_country, 'GN'); ?>>Guinea</option>
								<option value="GW" <?php selected($xfgmc_target_country, 'GW'); ?>>Guinea-Bissau</option>
								<option value="GY" <?php selected($xfgmc_target_country, 'GY'); ?>>Guyana</option>
								<option value="HT" <?php selected($xfgmc_target_country, 'HT'); ?>>Haiti</option>
								<option value="HM" <?php selected($xfgmc_target_country, 'HM'); ?>>Heard Island and McDonald Islands</option>
								<option value="HN" <?php selected($xfgmc_target_country, 'HN'); ?>>Honduras</option>
								<option value="HK" <?php selected($xfgmc_target_country, 'HK'); ?>>Hong Kong</option>
								<option value="HU" <?php selected($xfgmc_target_country, 'HU'); ?>>Hungary</option>
								<option value="IS" <?php selected($xfgmc_target_country, 'IS'); ?>>Iceland</option>
								<option value="IN" <?php selected($xfgmc_target_country, 'IN'); ?>>India</option>
								<option value="ID" <?php selected($xfgmc_target_country, 'ID'); ?>>Indonesia</option>
								<option value="IR" <?php selected($xfgmc_target_country, 'IR'); ?>>Iran</option>
								<option value="IQ" <?php selected($xfgmc_target_country, 'IQ'); ?>>Iraq</option>
								<option value="IE" <?php selected($xfgmc_target_country, 'IE'); ?>>Ireland</option>
								<option value="IM" <?php selected($xfgmc_target_country, 'IM'); ?>>Isle of Man</option>
								<option value="IL" <?php selected($xfgmc_target_country, 'IL'); ?>>Israel</option>
								<option value="IT" <?php selected($xfgmc_target_country, 'IT'); ?>>Italy</option>
								<option value="CI" <?php selected($xfgmc_target_country, 'CI'); ?>>Ivory Coast</option>
								<option value="JM" <?php selected($xfgmc_target_country, 'JM'); ?>>Jamaica</option>
								<option value="JP" <?php selected($xfgmc_target_country, 'JP'); ?>>Japan</option>
								<option value="JE" <?php selected($xfgmc_target_country, 'JE'); ?>>Jersey</option>
								<option value="JO" <?php selected($xfgmc_target_country, 'JO'); ?>>Jordan</option>
								<option value="KZ" <?php selected($xfgmc_target_country, 'KZ'); ?>>Kazakhstan</option>
								<option value="KE" <?php selected($xfgmc_target_country, 'KE'); ?>>Kenya</option>
								<option value="KI" <?php selected($xfgmc_target_country, 'KI'); ?>>Kiribati</option>
								<option value="XK" <?php selected($xfgmc_target_country, 'XK'); ?>>Kosovo</option>
								<option value="KW" <?php selected($xfgmc_target_country, 'KW'); ?>>Kuwait</option>
								<option value="KG" <?php selected($xfgmc_target_country, 'KG'); ?>>Kyrgyzstan</option>
								<option value="LA" <?php selected($xfgmc_target_country, 'LA'); ?>>Laos</option>
								<option value="LV" <?php selected($xfgmc_target_country, 'LV'); ?>>Latvia</option>
								<option value="LB" <?php selected($xfgmc_target_country, 'LB'); ?>>Lebanon</option>
								<option value="LS" <?php selected($xfgmc_target_country, 'LS'); ?>>Lesotho</option>
								<option value="LR" <?php selected($xfgmc_target_country, 'LR'); ?>>Liberia</option>
								<option value="LY" <?php selected($xfgmc_target_country, 'LY'); ?>>Libya</option>
								<option value="LI" <?php selected($xfgmc_target_country, 'LI'); ?>>Liechtenstein</option>
								<option value="LT" <?php selected($xfgmc_target_country, 'LT'); ?>>Lithuania</option>
								<option value="LU" <?php selected($xfgmc_target_country, 'LU'); ?>>Luxembourg</option>
								<option value="MO" <?php selected($xfgmc_target_country, 'MO'); ?>>Macao</option>
								<option value="MK" <?php selected($xfgmc_target_country, 'MK'); ?>>Macedonia</option>
								<option value="MG" <?php selected($xfgmc_target_country, 'MG'); ?>>Madagascar</option>
								<option value="MW" <?php selected($xfgmc_target_country, 'MW'); ?>>Malawi</option>
								<option value="MY" <?php selected($xfgmc_target_country, 'MY'); ?>>Malaysia</option>
								<option value="MV" <?php selected($xfgmc_target_country, 'MV'); ?>>Maldives</option>
								<option value="ML" <?php selected($xfgmc_target_country, 'ML'); ?>>Mali</option>
								<option value="MT" <?php selected($xfgmc_target_country, 'MT'); ?>>Malta</option>
								<option value="MH" <?php selected($xfgmc_target_country, 'MH'); ?>>Marshall Islands</option>
								<option value="MQ" <?php selected($xfgmc_target_country, 'MQ'); ?>>Martinique</option>
								<option value="MR" <?php selected($xfgmc_target_country, 'MR'); ?>>Mauritania</option>
								<option value="MU" <?php selected($xfgmc_target_country, 'MU'); ?>>Mauritius</option>
								<option value="YT" <?php selected($xfgmc_target_country, 'YT'); ?>>Mayotte</option>
								<option value="MX" <?php selected($xfgmc_target_country, 'MX'); ?>>Mexico</option>
								<option value="FM" <?php selected($xfgmc_target_country, 'FM'); ?>>Micronesia</option>
								<option value="MD" <?php selected($xfgmc_target_country, 'MD'); ?>>Moldova</option>
								<option value="MC" <?php selected($xfgmc_target_country, 'MC'); ?>>Monaco</option>
								<option value="MN" <?php selected($xfgmc_target_country, 'MN'); ?>>Mongolia</option>
								<option value="ME" <?php selected($xfgmc_target_country, 'ME'); ?>>Montenegro</option>
								<option value="MS" <?php selected($xfgmc_target_country, 'MS'); ?>>Montserrat</option>
								<option value="MA" <?php selected($xfgmc_target_country, 'MA'); ?>>Morocco</option>
								<option value="MZ" <?php selected($xfgmc_target_country, 'MZ'); ?>>Mozambique</option>
								<option value="MM" <?php selected($xfgmc_target_country, 'MM'); ?>>Myanmar [Burma]</option>
								<option value="NA" <?php selected($xfgmc_target_country, 'NA'); ?>>Namibia</option>
								<option value="NR" <?php selected($xfgmc_target_country, 'NR'); ?>>Nauru</option>
								<option value="NP" <?php selected($xfgmc_target_country, 'NP'); ?>>Nepal</option>
								<option value="NL" <?php selected($xfgmc_target_country, 'NL'); ?>>Netherlands</option>
								<option value="NC" <?php selected($xfgmc_target_country, 'NC'); ?>>New Caledonia</option>
								<option value="NZ" <?php selected($xfgmc_target_country, 'NZ'); ?>>New Zealand</option>
								<option value="NI" <?php selected($xfgmc_target_country, 'NI'); ?>>Nicaragua</option>
								<option value="NE" <?php selected($xfgmc_target_country, 'NE'); ?>>Niger</option>
								<option value="NG" <?php selected($xfgmc_target_country, 'NG'); ?>>Nigeria</option>
								<option value="NU" <?php selected($xfgmc_target_country, 'NU'); ?>>Niue</option>
								<option value="NF" <?php selected($xfgmc_target_country, 'NF'); ?>>Norfolk Island</option>
								<option value="KP" <?php selected($xfgmc_target_country, 'KP'); ?>>North Korea</option>
								<option value="MP" <?php selected($xfgmc_target_country, 'MP'); ?>>Northern Mariana Islands</option>
								<option value="NO" <?php selected($xfgmc_target_country, 'NO'); ?>>Norway</option>
								<option value="OM" <?php selected($xfgmc_target_country, 'OM'); ?>>Oman</option>
								<option value="PK" <?php selected($xfgmc_target_country, 'PK'); ?>>Pakistan</option>
								<option value="PW" <?php selected($xfgmc_target_country, 'PW'); ?>>Palau</option>
								<option value="PS" <?php selected($xfgmc_target_country, 'PS'); ?>>Palestine</option>
								<option value="PA" <?php selected($xfgmc_target_country, 'PA'); ?>>Panama</option>
								<option value="PG" <?php selected($xfgmc_target_country, 'PG'); ?>>Papua New Guinea</option>
								<option value="PY" <?php selected($xfgmc_target_country, 'PY'); ?>>Paraguay</option>
								<option value="PE" <?php selected($xfgmc_target_country, 'PE'); ?>>Peru</option>
								<option value="PH" <?php selected($xfgmc_target_country, 'PH'); ?>>Philippines</option>
								<option value="PN" <?php selected($xfgmc_target_country, 'PN'); ?>>Pitcairn Islands</option>
								<option value="PL" <?php selected($xfgmc_target_country, 'PL'); ?>>Poland</option>
								<option value="PT" <?php selected($xfgmc_target_country, 'PT'); ?>>Portugal</option>
								<option value="PR" <?php selected($xfgmc_target_country, 'PR'); ?>>Puerto Rico</option>
								<option value="QA" <?php selected($xfgmc_target_country, 'QA'); ?>>Qatar</option>
								<option value="CG" <?php selected($xfgmc_target_country, 'CG'); ?>>Republic of the Congo</option>
								<option value="RE" <?php selected($xfgmc_target_country, 'RE'); ?>>Réunion</option>
								<option value="RO" <?php selected($xfgmc_target_country, 'RO'); ?>>Romania</option>
								<option value="RW" <?php selected($xfgmc_target_country, 'RW'); ?>>Rwanda</option>
								<option value="BL" <?php selected($xfgmc_target_country, 'BL'); ?>>Saint Barthélemy</option>
								<option value="SH" <?php selected($xfgmc_target_country, 'SH'); ?>>Saint Helena</option>
								<option value="KN" <?php selected($xfgmc_target_country, 'KN'); ?>>Saint Kitts and Nevis</option>
								<option value="LC" <?php selected($xfgmc_target_country, 'LC'); ?>>Saint Lucia</option>
								<option value="MF" <?php selected($xfgmc_target_country, 'MF'); ?>>Saint Martin</option>
								<option value="PM" <?php selected($xfgmc_target_country, 'PM'); ?>>Saint Pierre and Miquelon</option>
								<option value="VC" <?php selected($xfgmc_target_country, 'VC'); ?>>Saint Vincent and the Grenadines</option>
								<option value="WS" <?php selected($xfgmc_target_country, 'WS'); ?>>Samoa</option>
								<option value="SM" <?php selected($xfgmc_target_country, 'SM'); ?>>San Marino</option>
								<option value="ST" <?php selected($xfgmc_target_country, 'ST'); ?>>São Tomé and Príncipe</option>
								<option value="SA" <?php selected($xfgmc_target_country, 'SA'); ?>>Saudi Arabia</option>
								<option value="SN" <?php selected($xfgmc_target_country, 'SN'); ?>>Senegal</option>
								<option value="RS" <?php selected($xfgmc_target_country, 'RS'); ?>>Serbia</option>
								<option value="SC" <?php selected($xfgmc_target_country, 'SC'); ?>>Seychelles</option>
								<option value="SL" <?php selected($xfgmc_target_country, 'SL'); ?>>Sierra Leone</option>
								<option value="SG" <?php selected($xfgmc_target_country, 'SG'); ?>>Singapore</option>
								<option value="SX" <?php selected($xfgmc_target_country, 'SX'); ?>>Sint Maarten</option>
								<option value="SK" <?php selected($xfgmc_target_country, 'SK'); ?>>Slovakia</option>
								<option value="SI" <?php selected($xfgmc_target_country, 'SI'); ?>>Slovenia</option>
								<option value="SB" <?php selected($xfgmc_target_country, 'SB'); ?>>Solomon Islands</option>
								<option value="SO" <?php selected($xfgmc_target_country, 'SO'); ?>>Somalia</option>
								<option value="ZA" <?php selected($xfgmc_target_country, 'ZA'); ?>>South Africa</option>
								<!--option value="GS" <?php selected($xfgmc_target_country, 'GS'); ?>>South Georgia and the South Sandwich Islands</option-->
								<option value="KR" <?php selected($xfgmc_target_country, 'KR'); ?>>South Korea</option>
								<option value="SS" <?php selected($xfgmc_target_country, 'SS'); ?>>South Sudan</option>
								<option value="ES" <?php selected($xfgmc_target_country, 'ES'); ?>>Spain</option>
								<option value="LK" <?php selected($xfgmc_target_country, 'LK'); ?>>Sri Lanka</option>
								<option value="SD" <?php selected($xfgmc_target_country, 'SD'); ?>>Sudan</option>
								<option value="SR" <?php selected($xfgmc_target_country, 'SR'); ?>>Suriname</option>
								<option value="SJ" <?php selected($xfgmc_target_country, 'SJ'); ?>>Svalbard and Jan Mayen</option>
								<option value="SZ" <?php selected($xfgmc_target_country, 'SZ'); ?>>Swaziland</option>
								<option value="SE" <?php selected($xfgmc_target_country, 'SE'); ?>>Sweden</option>
								<option value="CH" <?php selected($xfgmc_target_country, 'CH'); ?>>Switzerland</option>
								<option value="SY" <?php selected($xfgmc_target_country, 'SY'); ?>>Syria</option>
								<option value="TW" <?php selected($xfgmc_target_country, 'TW'); ?>>Taiwan</option>
								<option value="TJ" <?php selected($xfgmc_target_country, 'TJ'); ?>>Tajikistan</option>
								<option value="TZ" <?php selected($xfgmc_target_country, 'TZ'); ?>>Tanzania</option>
								<option value="TH" <?php selected($xfgmc_target_country, 'TH'); ?>>Thailand</option>
								<option value="TG" <?php selected($xfgmc_target_country, 'TG'); ?>>Togo</option>
								<option value="TK" <?php selected($xfgmc_target_country, 'TK'); ?>>Tokelau</option>
								<option value="TO" <?php selected($xfgmc_target_country, 'TO'); ?>>Tonga</option>
								<option value="TT" <?php selected($xfgmc_target_country, 'TT'); ?>>Trinidad and Tobago</option>
								<option value="TN" <?php selected($xfgmc_target_country, 'TN'); ?>>Tunisia</option>
								<option value="TR" <?php selected($xfgmc_target_country, 'TR'); ?>>Turkey</option>
								<option value="TM" <?php selected($xfgmc_target_country, 'TM'); ?>>Turkmenistan</option>
								<option value="TC" <?php selected($xfgmc_target_country, 'TC'); ?>>Turks and Caicos Islands</option>
								<option value="TV" <?php selected($xfgmc_target_country, 'TV'); ?>>Tuvalu</option>
								<option value="UM" <?php selected($xfgmc_target_country, 'UM'); ?>>U.S. Minor Outlying Islands</option>
								<option value="VI" <?php selected($xfgmc_target_country, 'VI'); ?>>U.S. Virgin Islands</option>
								<option value="UG" <?php selected($xfgmc_target_country, 'UG'); ?>>Uganda</option>
								<option value="AE" <?php selected($xfgmc_target_country, 'AE'); ?>>United Arab Emirates</option>
								<option value="UY" <?php selected($xfgmc_target_country, 'UY'); ?>>Uruguay</option>
								<option value="UZ" <?php selected($xfgmc_target_country, 'UZ'); ?>>Uzbekistan</option>
								<option value="VU" <?php selected($xfgmc_target_country, 'VU'); ?>>Vanuatu</option>
								<option value="VA" <?php selected($xfgmc_target_country, 'VA'); ?>>Vatican City</option>
								<option value="VE" <?php selected($xfgmc_target_country, 'VE'); ?>>Venezuela</option>
								<option value="VN" <?php selected($xfgmc_target_country, 'VN'); ?>>Vietnam</option>
								<option value="WF" <?php selected($xfgmc_target_country, 'WF'); ?>>Wallis and Futuna</option>
								<option value="EH" <?php selected($xfgmc_target_country, 'EH'); ?>>Western Sahara</option>
								<option value="YE" <?php selected($xfgmc_target_country, 'YE'); ?>>Yemen</option>
								<option value="ZM" <?php selected($xfgmc_target_country, 'ZM'); ?>>Zambia</option>
								<option value="ZW" <?php selected($xfgmc_target_country, 'ZW'); ?>>Zimbabwe</option>				
							</select><br />
							<span class="description"><small><?php _e('Select your target country', 'xfgmc'); ?></small></span>
						</td>
					</tr>
					<tr class="xfgmc_tr">
						<th scope="row"><label for="xfgmc_step_export"><?php _e('Step of export', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<select name="xfgmc_step_export" id="xfgmc_step_export">
							<?php do_action('xfgmc_before_step_export_option', $this->get_feed_id()); ?>
							<option value="80" <?php selected($xfgmc_step_export, '80'); ?>>80</option>
							<option value="200" <?php selected($xfgmc_step_export, '200'); ?>>200</option>
							<option value="300" <?php selected($xfgmc_step_export, '300'); ?>>300</option>
							<option value="450" <?php selected($xfgmc_step_export, '450'); ?>>450</option>
							<option value="500" <?php selected($xfgmc_step_export, '500'); ?>>500</option>
							<option value="800" <?php selected($xfgmc_step_export, '800'); ?>>800</option>
							<option value="1000" <?php selected($xfgmc_step_export, '1000'); ?>>1000</option>
							<?php do_action('xfgmc_after_step_export_option', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('The value affects the speed of file creation', 'xfgmc'); ?>. <?php _e('If you have any problems with the generation of the file - try to reduce the value in this field', 'xfgmc'); ?>. <?php _e('More than 500 can only be installed on powerful servers', 'xfgmc'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_cache"><?php _e('Ignore plugin cache', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<select name="xfgmc_cache" id="xfgmc_cache">
							<option value="disabled" <?php selected($xfgmc_cache, 'disabled'); ?>><?php _e('Disabled', 'xfgmc'); ?></option>
							<option value="enabled" <?php selected($xfgmc_cache, 'enabled'); ?>><?php _e('Enabled', 'xfgmc'); ?></option>
							</select><br />
							<span class="description"><small><?php _e("Changing this option can be useful if your feed prices don't change after syncing", 'xfgmc'); ?>. <a href="https://icopydoc.ru/pochemu-ne-obnovilis-tseny-v-fide-para-slov-o-tihih-pravkah/?utm_source=xml-for-google-merchant-center&utm_medium=organic&utm_campaign=in-plugin-xml-for-google-merchant-center&utm_content=settings&utm_term=about-cache"><?php _e('Learn More', 'xfgmc'); ?></a>.</small></span>
						</td>
					</tr>
					
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_main_settings();

	public function get_html_shop_data() { 
		$xfgmc_main_product = xfgmc_optionGET('xfgmc_main_product', $this->get_feed_id(), 'set_arr');
		$xfgmc_shop_name = stripslashes(htmlspecialchars(xfgmc_optionGET('xfgmc_shop_name', $this->get_feed_id(), 'set_arr')));  
		$xfgmc_def_store_code = xfgmc_optionGET('xfgmc_def_store_code', $this->get_feed_id(), 'set_arr');
		$xfgmc_default_currency = xfgmc_optionGET('xfgmc_default_currency', $this->get_feed_id(), 'set_arr');		
		$xfgmc_wooc_currencies = xfgmc_optionGET('xfgmc_wooc_currencies', $this->get_feed_id(), 'set_arr');
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Shop data', 'xfgmc'); ?> (<?php _e('Feed', 'xfgmc'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr>
						<th scope="row"><label for="xfgmc_main_product"><?php _e('What kind of products do you sell', 'xfgmc'); ?>?</label></th>
						<td class="overalldesc">
								<select name="xfgmc_main_product" id="xfgmc_main_product">	
								<option value="electronics" <?php selected($xfgmc_main_product, 'electronics'); ?>><?php _e('Electronics', 'xfgmc'); ?></option>
								<option value="computer" <?php selected($xfgmc_main_product, 'computer'); ?>><?php _e('Computer techologies', 'xfgmc'); ?></option>
								<option value="clothes_and_shoes" <?php selected($xfgmc_main_product, 'clothes_and_shoes'); ?>><?php _e('Clothes and shoes', 'xfgmc'); ?></option>
								<option value="auto_parts" <?php selected($xfgmc_main_product, 'auto_parts'); ?>><?php _e('Auto parts', 'xfgmc'); ?></option>
								<option value="products_for_children" <?php selected($xfgmc_main_product, 'products_for_children'); ?>><?php _e('Products for children', 'xfgmc'); ?></option>
								<option value="sporting_goods" <?php selected($xfgmc_main_product, 'sporting_goods'); ?>><?php _e('Sporting goods', 'xfgmc'); ?></option>
								<option value="goods_for_pets" <?php selected($xfgmc_main_product, 'goods_for_pets'); ?>><?php _e('Goods for pets', 'xfgmc'); ?></option>
								<option value="sexshop" <?php selected($xfgmc_main_product, 'sexshop'); ?>><?php _e('Sex shop (Adult products)', 'xfgmc'); ?></option>
								<option value="books" <?php selected($xfgmc_main_product, 'books'); ?>><?php _e('Books', 'xfgmc'); ?></option>
								<option value="health" <?php selected($xfgmc_main_product, 'health'); ?>><?php _e('Health products', 'xfgmc'); ?></option>	
								<option value="food" <?php selected($xfgmc_main_product, 'food'); ?>><?php _e('Food', 'xfgmc'); ?></option>
								<option value="construction_materials" <?php selected($xfgmc_main_product, 'construction_materials'); ?>><?php _e('Construction Materials', 'xfgmc'); ?></option>
								<option value="other" <?php selected($xfgmc_main_product, 'other'); ?>><?php _e('Other', 'xfgmc'); ?></option>					
							</select><br />
							<span class="description"><small><?php _e('Specify the main category', 'xfgmc'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_shop_name"><?php _e('Shop name', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
						<input maxlength="20" type="text" name="xfgmc_shop_name" id="xfgmc_shop_name" value="<?php echo $xfgmc_shop_name; ?>" /><br />
						<span class="description"><small><?php _e('Required element', 'xfgmc'); ?> <strong>title</strong>. <?php _e('The short name of the store should not exceed 20 characters', 'xfgmc'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_def_store_code"><?php _e('Store code', 'xfgmc'); ?></label><br />(<?php _e('In most cases, you can leave this field blank', 'xfgmc'); ?>)</th>
						<td class="overalldesc">
							<input type="text" name="xfgmc_def_store_code" id="xfgmc_def_store_code" value="<?php echo $xfgmc_def_store_code; ?>" /><br />
							<span class="description"><small><?php _e('Optional attribute', 'xfgmc'); ?> <strong>store_code</strong>. <a href="//support.google.com/merchants/answer/9673755" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a></small></span>
						</td>
					</tr>
					<?php do_action('xfgmc_before_default_currency', $this->get_feed_id()); ?>
					<tr>
						<th scope="row"><label for="xfgmc_default_currency"><?php _e('Store currency', 'xfgmc'); ?></label><br />(<?php _e('Uppercase letter', 'xfgmc'); ?>!)</th>
						<td class="overalldesc">
							<input type="text" placeholder="USD" name="xfgmc_default_currency" id="xfgmc_default_currency" value="<?php echo $xfgmc_default_currency; ?>" /><br />
							<span class="description"><small><?php _e('For example', 'xfgmc'); ?>: <strong>USD</strong>. <a href="//support.google.com/merchants/answer/160637" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a></small></span>
						</td>
					</tr>
					<?php if (class_exists('WOOCS')) : 		 
						global $WOOCS; $currencies_arr = $WOOCS->get_currencies(); 
						if (is_array($currencies_arr)) : $array_keys = array_keys($currencies_arr); ?>
						<tr>
							<th scope="row"><label for="xfgmc_wooc_currencies"><?php _e('Feed currency', 'xfgmc'); ?></label></th>
							<td class="overalldesc">
								<select name="xfgmc_wooc_currencies" id="xfgmc_wooc_currencies">
								<?php for ($i = 0; $i < count($array_keys); $i++) : ?>
									<option value="<?php echo $currencies_arr[$array_keys[$i]]['name']; ?>" <?php selected($xfgmc_wooc_currencies, $currencies_arr[$array_keys[$i]]['name']); ?>><?php echo $currencies_arr[$array_keys[$i]]['name']; ?></option>					
								<?php endfor; ?>
								</select><br />
								<span class="description"><small><?php _e('You have plugin installed', 'xfgmc'); ?> <strong class="xfgmc_bold">WooCommerce Currency Switcher by PluginUs.NET. Woo Multi Currency and Woo Multi Pay</strong><br />
								<?php _e('Indicate in what currency the prices should be', 'xfgmc'); ?>.<br /><strong class="xfgmc_bold"><?php _e('Please note', 'xfgmc'); ?>:</strong> <?php _e('The currency must match the one you specified in the field above', 'xfgmc'); ?></small>
								</span>
							</td>
						</tr>
						<?php endif; ?>
					<?php endif; ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_shop_data();

	public function get_html_shipping_setting() { 
		$xfgmc_def_shipping_weight_unit = xfgmc_optionGET('xfgmc_def_shipping_weight_unit', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_def_shipping_country	= xfgmc_optionGET('xfgmc_def_shipping_country', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_def_delivery_area_type = xfgmc_optionGET('xfgmc_def_delivery_area_type', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_def_delivery_area_value = xfgmc_optionGET('xfgmc_def_delivery_area_value', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_def_shipping_service = xfgmc_optionGET('xfgmc_def_shipping_service', $this->get_feed_id(), 'set_arr'); 		
		$xfgmc_def_shipping_price = xfgmc_optionGET('xfgmc_def_shipping_price', $this->get_feed_id(), 'set_arr'); ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Shipping', 'xfgmc'); ?></h2>
			<div class="inside">
				<p><i><?php _e('Google recommend that you set up shipping costs through Merchant Center settings instead of submitting the shipping attribute in the feed', 'xfgmc'); ?>. <a href="//support.google.com/merchants/answer/6069284" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a></i></p>
				<p><i><?php _e('To add this element to your feed make sure the fields are filled', 'xfgmc'); ?> "country" <?php _e('and', 'xfgmc'); ?> "<?php _e('Delivery area', 'xfgmc'); ?>". <a href="//support.google.com/merchants/answer/6324484" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a></i></p>
				<table class="form-table"><tbody>
					<tr>
						<th scope="row"><label for="xfgmc_def_shipping_weight_unit"><?php _e('Unit of measurement of', 'xfgmc'); ?> shipping_weight</label></th>
						<td class="overalldesc">
							<select name="xfgmc_def_shipping_weight_unit" id="xfgmc_def_shipping_weight_unit">					
								<option value="kg" <?php selected($xfgmc_def_shipping_weight_unit, 'kg')?> >kg</option>
								<option value="g" <?php selected($xfgmc_def_shipping_weight_unit, 'g')?> >g</option>
							</select>
						</td>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_def_shipping_country"><?php _e('Attribute', 'xfgmc'); ?> country</label></th>
						<td class="overalldesc">
						<input type="text" name="xfgmc_def_shipping_country" id="xfgmc_def_shipping_country" value="<?php echo $xfgmc_def_shipping_country; ?>" /><br />
						<span class="description"><small><?php _e('Required attribute', 'xfgmc'); ?> <strong>shipping_country</strong>. <?php _e('Leave this field blank if you do not want to add a default value', 'xfgmc'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Delivery area', 'xfgmc'); ?><select name="xfgmc_def_delivery_area_type"><option value="region" <?php selected($xfgmc_def_delivery_area_type, 'region'); ?>>region</option><option value="postal_code" <?php selected($xfgmc_def_delivery_area_type, 'postal_code'); ?>>postal_code</option><option value="location_id" <?php selected($xfgmc_def_delivery_area_type, 'location_id'); ?>>location_id</option><option value="location_group_name" <?php selected($xfgmc_def_delivery_area_type, 'location_group_name'); ?>>location_group_name</option></select></th>		  
						<td class="overalldesc">
							<input type="text" name="xfgmc_def_delivery_area_value" value="<?php echo $xfgmc_def_delivery_area_value; ?>" /><br />
							<span class="description"><small><?php _e('To specify a delivery area (which is optional), submit 1 of the 4 available options for the shipping attribute', 'xfgmc'); ?>. <a href="//support.google.com/merchants/answer/6324484" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_def_shipping_service"><?php _e('Attribute', 'xfgmc'); ?> service</label></th>
						<td class="overalldesc">
						<input type="text" name="xfgmc_def_shipping_service" id="xfgmc_def_shipping_service" value="<?php echo $xfgmc_def_shipping_service; ?>" /><br />
						<span class="description"><small><?php _e('Optional attribute', 'xfgmc'); ?> <strong>service</strong>. <?php _e('Leave this field blank if you do not want to add a default value', 'xfgmc'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_def_shipping_price"><?php _e('Attribute', 'xfgmc'); ?> price</label></th>
						<td class="overalldesc">
						<input type="text" name="xfgmc_def_shipping_price" id="xfgmc_def_shipping_price" value="<?php echo $xfgmc_def_shipping_price; ?>" /><br />
						<span class="description"><small><?php _e('Optional attribute', 'xfgmc'); ?> <strong>price</strong>. <?php _e('Leave this field blank if you do not want to add a default value', 'xfgmc'); ?>.</small></span>
						</td>
					</tr>
					<?php do_action('xfgmc_append_optional_elemet_table', $this->get_feed_id()); ?>	 
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_shipping_setting();

	public function get_html_tags_settings() {
		$xfgmc_shop_description = stripslashes(htmlspecialchars(xfgmc_optionGET('xfgmc_shop_description', $this->get_feed_id(), 'set_arr'))); 
		$xfgmc_behavior_onbackorder = xfgmc_optionGET('xfgmc_behavior_onbackorder', $this->get_feed_id(), 'set_arr');
		$xfgmc_product_type = xfgmc_optionGET('xfgmc_product_type', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_product_type_home = xfgmc_optionGET('xfgmc_product_type_home', $this->get_feed_id(), 'set_arr'); 

		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Tags settings', 'xfgmc'); ?> (<?php _e('Feed', 'xfgmc'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr>
						<th scope="row"><label for="xfgmc_shop_description"><?php _e('The name of your data feed', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="xfgmc_shop_description" id="xfgmc_shop_description" value="<?php echo $xfgmc_shop_description; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'xfgmc'); ?> <strong>description</strong>. <?php _e('The name of your data feed', 'xfgmc'); ?>.</small></span>
						</td>
					</tr>
					<tr class="xfgmc_tr">
						<th scope="row"><label for="xfgmc_behavior_onbackorder"><?php _e('For pre-order products, establish availability equal to', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<select name="xfgmc_behavior_onbackorder" id="xfgmc_behavior_onbackorder">
								<option value="in_stock" <?php selected($xfgmc_behavior_onbackorder, 'in_stock'); ?>>in_stock</option>
								<option value="out_of_stock" <?php selected($xfgmc_behavior_onbackorder, 'out_of_stock')?> >out_of_stock</option>
								<option value="onbackorder" <?php selected($xfgmc_behavior_onbackorder, 'onbackorder')?>>preorder</option>
							</select><br />
							<span class="description"><small><?php _e('For pre-order products, establish availability equal to', 'xfgmc'); ?> in_stock/out_of_stock/preorder</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_product_type"><?php _e('Product type', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<select name="xfgmc_product_type" id="xfgmc_product_type">
								<option value="disabled" <?php selected($xfgmc_product_type, 'disabled'); ?>><?php _e('Disabled', 'xfgmc'); ?></option>
								<option value="enabled" <?php selected($xfgmc_product_type, 'enabled'); ?>><?php _e('Enabled', 'xfgmc'); ?></option>					
							</select><br />
							<span class="description"><?php _e('Add root element', 'xfgmc'); ?>:</span><br />
							<input type="text" name="xfgmc_product_type_home" id="xfgmc_product_type_home" placeholder="<?php _e('Home', 'xfgmc'); ?>" value="<?php echo $xfgmc_product_type_home; ?>"/><br />
							<span class="description"><small><?php _e('Optional element', 'xfgmc'); ?> <strong>product_type</strong>.</small></span>
						</td>
					</tr>	 

				</tbody></table>
			</div>
		</div><?php
	} // end get_html_tags_settings();

	public function get_html_filtration() { 
		$xfgmc_whot_export = xfgmc_optionGET('xfgmc_whot_export', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_the_content = xfgmc_optionGET('xfgmc_the_content', $this->get_feed_id(), 'set_arr');
		$xfgmc_var_desc_priority = xfgmc_optionGET('xfgmc_var_desc_priority', $this->get_feed_id(), 'set_arr');
		$xfgmc_skip_missing_products = xfgmc_optionGET('xfgmc_skip_missing_products', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_skip_backorders_products = xfgmc_optionGET('xfgmc_skip_backorders_products', $this->get_feed_id(), 'set_arr');
		$xfgmc_no_default_png_products = xfgmc_optionGET('xfgmc_no_default_png_products', $this->get_feed_id(), 'set_arr'); 
		$xfgmc_one_variable = xfgmc_optionGET('xfgmc_one_variable', $this->get_feed_id(), 'set_arr'); 
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Filtration', 'xfgmc'); ?> (<?php _e('Feed', 'xfgmc'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr>
						<th scope="row"><label for="xfgmc_whot_export"><?php _e('Whot export', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<select name="xfgmc_whot_export" id="xfgmc_whot_export">
								<option value="all" <?php selected($xfgmc_whot_export, 'all' ); ?>><?php _e('Simple & Variable products', 'xfgmc'); ?></option>
								<option value="simple" <?php selected($xfgmc_whot_export, 'simple' ); ?>><?php _e('Only simple products', 'xfgmc'); ?></option>
								<option value="variable" <?php selected($xfgmc_whot_export, 'variable' ); ?>><?php _e('Only Variable products', 'xfgmc'); ?></option>
								<?php do_action('xfgmc_after_whot_export_option', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('Whot export', 'xfgmc'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_the_content"><?php _e('Use the filter', 'xfgmc'); ?> the_content</label></th>
						<td class="overalldesc">
							<select name="xfgmc_the_content" id="xfgmc_the_content">
							<option value="disabled" <?php selected($xfgmc_the_content, 'disabled'); ?>><?php _e('Disabled', 'xfgmc'); ?></option>
							<option value="enabled" <?php selected($xfgmc_the_content, 'enabled'); ?>><?php _e('Enabled', 'xfgmc'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Default', 'xfgmc'); ?>: <?php _e('Enabled', 'xfgmc'); ?></small></span>
						</td>
					</tr>	
					<tr>
						<th scope="row"><label for="xfgmc_var_desc_priority"><?php _e('The varition description takes precedence over others', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="xfgmc_var_desc_priority" id="xfgmc_var_desc_priority" <?php checked($xfgmc_var_desc_priority, 'on'); ?>/>
						</td>
					</tr>
					<tr class="xfgmc_tr">
						<th scope="row"><label for="xfgmc_skip_missing_products"><?php _e('Skip missing products', 'xfgmc'); ?> (<?php _e('except for products for which a pre-order is permitted', 'xfgmc'); ?>.)</label></th>
						<td class="overalldesc">
							<input type="checkbox" name="xfgmc_skip_missing_products" id="xfgmc_skip_missing_products" <?php checked($xfgmc_skip_missing_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_skip_backorders_products"><?php _e('Skip backorders products', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="xfgmc_skip_backorders_products" id="xfgmc_skip_backorders_products" <?php checked($xfgmc_skip_backorders_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="xfgmc_no_default_png_products"><?php _e('Remove default.png from XML', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="xfgmc_no_default_png_products" id="xfgmc_no_default_png_products" <?php checked($xfgmc_no_default_png_products, 'on' ); ?>/>
						</td>
					</tr> 
					<tr>
						<th scope="row"><label for="xfgmc_one_variable"><?php _e('Upload only the first variation', 'xfgmc'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="xfgmc_one_variable" id="xfgmc_one_variable" <?php checked($xfgmc_one_variable, 'on' ); ?>/>
						</td>
					</tr>

					<?php do_action('xfgmc_append_main_param_table', $this->get_feed_id()); ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_filtration();

	public function get_html_icp_banners() { ?>
		<div id="icp_slides" class="clear">
			<div class="icp_wrap">
				<input type="radio" name="icp_slides" id="icp_point1">
				<input type="radio" name="icp_slides" id="icp_point2">
				<input type="radio" name="icp_slides" id="icp_point3" checked>
				<input type="radio" name="icp_slides" id="icp_point4">
				<input type="radio" name="icp_slides" id="icp_point5">
				<input type="radio" name="icp_slides" id="icp_point6">
				<input type="radio" name="icp_slides" id="icp_point7">
				<div class="icp_slider">
					<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
					<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
					<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
					<div class="icp_slides icp_img4"><a href="//wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"></a></div>
					<div class="icp_slides icp_img5"><a href="//wordpress.org/plugins/xml-for-avito/" target="_blank"></a></div>
					<div class="icp_slides icp_img6"><a href="//wordpress.org/plugins/xml-for-o-yandex/" target="_blank"></a></div>
					<div class="icp_slides icp_img7"><a href="//wordpress.org/plugins/import-from-yml/" target="_blank"></a></div>
				</div>
				<div class="icp_control">
					<label for="icp_point1"></label>
					<label for="icp_point2"></label>
					<label for="icp_point3"></label>
					<label for="icp_point4"></label>
					<label for="icp_point5"></label>
					<label for="icp_point6"></label>
					<label for="icp_point7"></label>
				</div>
			</div> 
		</div><?php 
	} // end get_html_icp_banners()

	public function get_html_my_plugins_list() { ?>
		<div class="metabox-holder">
			<div class="postbox">
				<h2 class="hndle"><?php _e('My plugins that may interest you', 'xfgmc'); ?></h2>
				<div class="inside">
					<p><span class="xfgmc_bold">XML for Google Merchant Center</span> - <?php _e('Сreates a XML-feed to upload to Google Merchant Center', 'xfgmc'); ?>. <a href="https://wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p> 
					<p><span class="xfgmc_bold">YML for Yandex Market</span> - <?php _e('Сreates a YML-feed for importing your products to Yandex Market', 'xfgmc'); ?>. <a href="https://wordpress.org/plugins/yml-for-yandex-market/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p>
					<p><span class="xfgmc_bold">Import from YML</span> - <?php _e('Imports products from YML to your shop', 'xfgmc'); ?>. <a href="https://wordpress.org/plugins/import-from-yml/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p>
					<p><span class="xfgmc_bold">XML for Hotline</span> - <?php _e('Сreates a XML-feed for importing your products to Hotline', 'xfgmc'); ?>. <a href="https://wordpress.org/plugins/xml-for-hotline/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p>
					<p><span class="xfgmc_bold">Gift upon purchase for WooCommerce</span> - <?php _e('This plugin will add a marketing tool that will allow you to give gifts to the buyer upon purchase', 'xfgmc'); ?>. <a href="https://wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p>
					<p><span class="xfgmc_bold">Import products to ok.ru</span> - <?php _e('With this plugin, you can import products to your group on ok.ru', 'xfgmc'); ?>. <a href="https://wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p>
					<p><span class="xfgmc_bold">XML for Avito</span> - <?php _e('Сreates a XML-feed for importing your products to', 'xfgmc'); ?> Avito. <a href="https://wordpress.org/plugins/xml-for-avito/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p>
					<p><span class="xfgmc_bold">XML for O.Yandex (Яндекс Объявления)</span> - <?php _e('Сreates a XML-feed for importing your products to', 'xfgmc'); ?> Яндекс.Объявления. <a href="https://wordpress.org/plugins/xml-for-o-yandex/" target="_blank"><?php _e('Read more', 'xfgmc'); ?></a>.</p>
				</div>
			</div>
		</div><?php
	} // end get_html_my_plugins_list()

	private function init_hooks() {
		// наш класс, вероятно, вызывается во время срабатывания хука admin_menu.
		// admin_init - следующий в очереди срабатывания, хуки раньше admin_menu нет смысла вешать
		// add_action('admin_init', array($this, 'listen_submits'), 10);
		return;
	}

	private function get_feed_id() {
		return $this->feed_id;
	}

	private function save_plugin_set($opt_name, $feed_id, $save_if_empty = false) {
		if (isset($_POST[$opt_name])) {
			xfgmc_optionUPD($opt_name, sanitize_text_field($_POST[$opt_name]), $feed_id, 'yes', 'set_arr');
		} else {
			if ($save_if_empty === true) {
				xfgmc_optionUPD($opt_name, '0', $feed_id, 'yes', 'set_arr');
			}
		}
		return;
	}

	private function listen_submit() {
	// массовое удаление фидов по чекбоксу checkbox_xml_file
		if (isset($_GET['xfgmc_form_id']) && ($_GET['xfgmc_form_id'] === 'xfgmc_wp_list_table')) {
			if (is_array($_GET['checkbox_xml_file']) && !empty($_GET['checkbox_xml_file'])) {
				if ($_GET['action'] === 'delete' || $_GET['action2'] === 'delete') {
					$checkbox_xml_file_arr = $_GET['checkbox_xml_file'];
					$xfgmc_settings_arr = xfgmc_optionGET('xfgmc_settings_arr');
					for ($i = 0; $i < count($checkbox_xml_file_arr); $i++) {
						$feed_id = $checkbox_xml_file_arr[$i];
						unset($xfgmc_settings_arr[$feed_id]);
						wp_clear_scheduled_hook('xfgmc_cron_period', array($feed_id)); // отключаем крон
						wp_clear_scheduled_hook('xfgmc_cron_sborki', array($feed_id)); // отключаем крон
						$upload_dir = (object)wp_get_upload_dir();
						$name_dir = $upload_dir->basedir."/xfgmc";
		//				$filename = $name_dir.'/ids_in_xml.tmp'; if (file_exists($filename)) {unlink($filename);}
						xfgmc_remove_directory($name_dir.'/feed'.$feed_id);
						xfgmc_optionDEL('xfgmc_status_sborki', $i);

						$xfgmc_registered_feeds_arr = xfgmc_optionGET('xfgmc_registered_feeds_arr');
						for ($n = 1; $n < count($xfgmc_registered_feeds_arr); $n++) { // первый элемент не проверяем, тк. там инфо по последнему id
							if ($xfgmc_registered_feeds_arr[$n]['id'] === $feed_id) {
								unset($xfgmc_registered_feeds_arr[$n]);
								$xfgmc_registered_feeds_arr = array_values($xfgmc_registered_feeds_arr);
								xfgmc_optionUPD('xfgmc_registered_feeds_arr', $xfgmc_registered_feeds_arr);
								break;
							}
						}
					}
					xfgmc_optionUPD('xfgmc_settings_arr', $xfgmc_settings_arr);
					$feed_id = xfgmc_get_first_feed_id();
				}
			}
		}

		if (isset($_GET['feed_id'])) {
			if (isset($_GET['action'])) {
				$action = sanitize_text_field($_GET['action']);
				switch ($action) {
					case 'edit':
						$feed_id = sanitize_text_field($_GET['feed_id']);
						break;
					case 'delete':
						$feed_id = sanitize_text_field($_GET['feed_id']);
						$xfgmc_settings_arr = xfgmc_optionGET('xfgmc_settings_arr');
						unset($xfgmc_settings_arr[$feed_id]);
						wp_clear_scheduled_hook('xfgmc_cron_period', array($feed_id)); // отключаем крон
						wp_clear_scheduled_hook('xfgmc_cron_sborki', array($feed_id)); // отключаем крон
						$upload_dir = (object)wp_get_upload_dir();
						$name_dir = $upload_dir->basedir."/xfgmc";
		//				$filename = $name_dir.'/ids_in_xml.tmp'; if (file_exists($filename)) {unlink($filename);}
						xfgmc_remove_directory($name_dir.'/feed'.$feed_id);		
						xfgmc_optionUPD('xfgmc_settings_arr', $xfgmc_settings_arr);
						xfgmc_optionDEL('xfgmc_status_sborki', $feed_id);					
						$xfgmc_registered_feeds_arr = xfgmc_optionGET('xfgmc_registered_feeds_arr');
						for ($n = 1; $n < count($xfgmc_registered_feeds_arr); $n++) { // первый элемент не проверяем, тк. там инфо по последнему id
							if ($xfgmc_registered_feeds_arr[$n]['id'] === $feed_id) {
								unset($xfgmc_registered_feeds_arr[$n]);
								$xfgmc_registered_feeds_arr = array_values($xfgmc_registered_feeds_arr); 
								xfgmc_optionUPD('xfgmc_registered_feeds_arr', $xfgmc_registered_feeds_arr);
								break;
							}
						}
		
						$feed_id = xfgmc_get_first_feed_id();
						break;
					default:
						$feed_id = xfgmc_get_first_feed_id();
				}
			} else {$feed_id = sanitize_text_field($_GET['feed_id']);}
		} else {$feed_id = xfgmc_get_first_feed_id();}

		if (isset($_REQUEST['xfgmc_submit_add_new_feed'])) { // если создаём новый фид
			if (!empty($_POST) && check_admin_referer('xfgmc_nonce_action_add_new_feed', 'xfgmc_nonce_field_add_new_feed')) {
				$xfgmc_settings_arr = xfgmc_optionGET('xfgmc_settings_arr');
				
				if (is_multisite()) {
					$xfgmc_registered_feeds_arr = get_blog_option(get_current_blog_id(), 'xfgmc_registered_feeds_arr');
					$feed_id = $xfgmc_registered_feeds_arr[0]['last_id'];
					$feed_id++;
					$xfgmc_registered_feeds_arr[0]['last_id'] = (string)$feed_id;
					$xfgmc_registered_feeds_arr[] = array('id' => (string)$feed_id);
					update_blog_option(get_current_blog_id(), 'xfgmc_registered_feeds_arr', $xfgmc_registered_feeds_arr);
				} else {
					$xfgmc_registered_feeds_arr = get_option('xfgmc_registered_feeds_arr');
					$feed_id = $xfgmc_registered_feeds_arr[0]['last_id'];
					$feed_id++;
					$xfgmc_registered_feeds_arr[0]['last_id'] = (string)$feed_id;
					$xfgmc_registered_feeds_arr[] = array('id' => (string)$feed_id);
					update_option('xfgmc_registered_feeds_arr', $xfgmc_registered_feeds_arr);
				}

				$upload_dir = (object)wp_get_upload_dir();
				$name_dir = $upload_dir->basedir.'/xfgmc/feed'.$feed_id;
				if (!is_dir($name_dir)) {
					if (!mkdir($name_dir)) {
						error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: export.php; Строка: '.__LINE__, 0);
					}
				}

				$def_plugin_date_arr = new XFGMC_Data_Arr();
				$xfgmc_settings_arr[$feed_id] = $def_plugin_date_arr->get_opts_name_and_def_date('all');

				xfgmc_optionUPD('xfgmc_settings_arr', $xfgmc_settings_arr);
		
				xfgmc_optionADD('xfgmc_status_sborki', '-1', $feed_id);
				xfgmc_optionADD('xfgmc_last_element', '-1', $feed_id);
				print '<div class="updated notice notice-success is-dismissible"><p>'. __('Feed added', 'xfgmc').'. ID = '.$feed_id.'.</p></div>';
			}
		}

		$status_sborki = (int)xfgmc_optionGET('xfgmc_status_sborki', $feed_id);

		if (isset($_REQUEST['xfgmc_submit_action'])) {
			if (!empty($_POST) && check_admin_referer('xfgmc_nonce_action', 'xfgmc_nonce_field')) {
				do_action('xfgmc_prepend_submit_action', $feed_id);
			
				$feed_id = sanitize_text_field($_POST['xfgmc_num_feed_for_save']);
			
				$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT (Unix формат)
				xfgmc_optionUPD('xfgmc_date_save_set', $unixtime, $feed_id, 'yes', 'set_arr');
				
				if (isset($_POST['xfgmc_run_cron'])) {
					$arr_maybe = array("off", "five_min", "hourly", "six_hours", "twicedaily", "daily");
					$xfgmc_run_cron = sanitize_text_field($_POST['xfgmc_run_cron']);
				
					if (in_array($xfgmc_run_cron, $arr_maybe)) {		
						xfgmc_optionUPD('xfgmc_status_cron', $xfgmc_run_cron, $feed_id, 'yes', 'set_arr');
						if ($xfgmc_run_cron === 'off') {
							// отключаем крон
							wp_clear_scheduled_hook('xfgmc_cron_period', array($feed_id));
							xfgmc_optionUPD('xfgmc_status_cron', 'off', $feed_id, 'yes', 'set_arr');
						
							wp_clear_scheduled_hook('xfgmc_cron_sborki', array($feed_id));
							xfgmc_optionUPD('xfgmc_status_sborki', '-1', $feed_id);
						} else {
							$recurrence = $xfgmc_run_cron;
							wp_clear_scheduled_hook('xfgmc_cron_period', array($feed_id));
							wp_schedule_event(time(), $recurrence, 'xfgmc_cron_period', array($feed_id));
							new XFGMC_Error_Log('FEED № '.$feed_id.'; xfgmc_cron_period внесен в список заданий; Файл: export.php; Строка: '.__LINE__);
						}
					} else {
						new XFGMC_Error_Log('Крон '.$xfgmc_run_cron.' не зарегистрирован. Файл: export.php; Строка: '.__LINE__);
					}
				}
		
				$def_plugin_date_arr = new XFGMC_Data_Arr();
				$opts_name_and_def_date_arr = $def_plugin_date_arr->get_opts_name_and_def_date('public');
				foreach ($opts_name_and_def_date_arr as $key => $value) {
					$save_if_empty = false;
					switch ($key) {
						case 'xfgmc_status_cron': 
						case 'xfgmcp_exclude_cat_arr': // селект категорий в прошке
							continue 2;
							break;
						case 'xfgmc_var_desc_priority':
						case 'xfgmc_one_variable':
						case 'xfgmc_skip_missing_products':
						case 'xfgmc_skip_backorders_products':
						case 'xfgmc_no_default_png_products':
						/* И галки в прошке */
						case 'xfgmcp_use_del_vc':
						case 'xfgmcp_excl_thumb':
						case 'xfgmcp_use_utm':
							if (!isset($_GET['tab']) || ($_GET['tab'] !== 'filtration')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;
						case 'xfgmc_ufup':
							if (isset($_GET['tab']) && ($_GET['tab'] !== 'main_tab')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;
					}
					$this->save_plugin_set($key, $feed_id, $save_if_empty);
				}

			}
		} 

		$this->feed_id = $feed_id;
		return;
	}
}