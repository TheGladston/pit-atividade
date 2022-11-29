<?php
/**
 * Plugin Name: LEE Framework
 * Plugin URI: http://themeforest.net/user/leebrosus
 * Description: Framework
 * Version: 1.0
 * Author: Derry Vu
 * Author URI: http://themeforest.net/user/leebrosus
 * License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: ltheme_domain
 * Domain Path: /languages
 */

$path_plugin = dirname( __FILE__ );
$url_plugin = plugin_dir_url( __FILE__ );

define('LEE_FRAMEWORK_PLUGIN_PATH', $path_plugin);
define('LEE_FRAMWRORK_PLUGIN_URL',  $url_plugin);

define('LEE_VISUAL_COMPOSER_ACTIVED', in_array('js_composer/js_composer.php', apply_filters('active_plugins', get_option('active_plugins'))));

require $path_plugin . '/post-type/footer.php';

?>