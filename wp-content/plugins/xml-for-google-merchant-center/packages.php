<?php if (!defined('ABSPATH')) {exit;}
require_once plugin_dir_path(__FILE__).'/includes/old-php-add-functions.php';
require_once plugin_dir_path(__FILE__).'/includes/icopydoc-useful-functions.php';
require_once plugin_dir_path(__FILE__).'/includes/wc-add-functions.php';
require_once plugin_dir_path(__FILE__).'/functions.php'; // Подключаем файл функций
require_once plugin_dir_path(__FILE__).'/includes/backward-compatibility.php';

require_once plugin_dir_path(__FILE__).'/classes/generation/traits-xfgmc-global-variables.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/traits-xfgmc-simple.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/traits-xfgmc-variable.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-get-closed-tag.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-get-open-tag.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-get-paired-tag.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-get-unit.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-get-unit-offer.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-get-unit-offer-simple.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-get-unit-offer-variable.php';
require_once plugin_dir_path(__FILE__).'/classes/generation/class-xfgmc-generation-xml.php';

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
require_once plugin_dir_path(__FILE__).'/classes/system/class-xfgmc-wp-list-table.php';
require_once plugin_dir_path(__FILE__).'/classes/system/class-xfgmc-settings-feed-wp-list-table.php';
require_once plugin_dir_path(__FILE__).'/classes/system/class-xfgmc-error-log.php';
require_once plugin_dir_path(__FILE__).'/classes/system/class-xfgmc-feedback.php';
require_once plugin_dir_path(__FILE__).'/classes/system/class-xfgmc-settings-page.php';
require_once plugin_dir_path(__FILE__).'/classes/system/class-xfgmc-data-arr.php';
?>