<?php
$themename = "";
$shortname = "p2h";
$version = "1.1";

$option_group = $shortname . '_theme_option_group';
$option_name = $shortname . '_theme_options';


add_action('admin_enqueue_scripts', 'wptuts_add_color_picker');

function wptuts_add_color_picker($hook) {
    if (is_admin()) {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}

// Load stylesheet and jscript
add_action('admin_init', 'p2h_add_init');

function p2h_add_init() {
    $file_dir = get_stylesheet_directory_uri();
    wp_enqueue_style("p2hCss", $file_dir . "/functions/theme-options.css", false, "1.0", "all");
    wp_enqueue_script("p2hScript", $file_dir . "/functions/theme-options.js", false, "1.0");
}

// Create custom settings menu
add_action('admin_menu', 'p2h_create_menu');

function p2h_create_menu() {
    global $themename;
    add_dashboard_page('BRT Loja', 'BRT Loja', 'read', 'brtloja', 'p2h_settings_page', 40);
}

// Register settings
add_action('admin_init', 'register_settings');

function register_settings() {
    global $themename, $shortname, $version, $p2h_options, $option_group, $option_name;
    //register our settings
    register_setting($option_group, $option_name);
}

//Automatically List StyleSheets in Folder
/////////////////////////////////////////////

$alt_stylesheet_path = TEMPLATEPATH . '/inc/css/';
$alt_stylesheets = array();

if (is_dir($alt_stylesheet_path)) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path)) {
        while (($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false) {
            if ((stristr($alt_stylesheet_file, ".css") !== false) && (stristr($alt_stylesheet_file, "default") == false)) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }
    }
}
array_unshift($alt_stylesheets, "default.css");

global $p2h_options;
$functions_path = STYLESHEETPATH . '/functions/';
require_once($functions_path . 'theme-fild.php');

function p2h_settings_page() {
    global $themename, $shortname, $version, $p2h_options, $option_group, $option_name;
    ?>
    <div class="wrap">
        <div class="options_wrap">
            <?php screen_icon(); ?><h2><?php echo $themename; ?> Opções do tema</h2>
            <p class="top-notice">Opções para configuração do site</p>
            <?php if (isset($_POST['reset'])): ?>
                <?php
                // Delete Settings
                global $wpdb, $themename, $shortname, $version, $p2h_options, $option_group, $option_name;
                delete_option('p2h_theme_options');
                wp_cache_flush();
                ?>
                <div class="updated fade"><p><strong><?php _e($themename . ' options reset.'); ?></strong></p></div>

            <?php elseif (isset($_REQUEST['updated'])): ?>
                <div class="updated fade"><p><strong><?php _e($themename . ' options saved.'); ?></strong></p></div>
            <?php endif; ?>

            <form method="post" action="options.php">

                <?php settings_fields($option_group); ?>

                <?php $options = get_option($option_name); ?>        

                <?php
                foreach ($p2h_options as $value) {
                    if (isset($value['id'])) {
                        $valueid = $value['id'];
                    }
                    switch ($value['type']) {
                        case "section":
                            ?>
                            <div class="section_wrap">
                                <h3 class="section_title"><?php echo $value['name']; ?> 

                                    <?php
                                    break;
                                case "section-desc":
                                    ?>
                                    <span><?php echo $value['name']; ?></span></h3>
                                <div class="section_body">

                                    <?php
                                    break;
                                case 'text':
                                    ?>
                                    <div class="options_input options_text">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <span class="labels"><label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label></span>
                                        <input name="<?php echo $option_name . '[' . $valueid . ']'; ?>" id="<?php echo $option_name . '[' . $valueid . ']'; ?>" type="<?php echo $value['type']; ?>" value="<?php
                    if (isset($options[$valueid])) {
                        esc_attr_e($options[$valueid]);
                    } else {
                        esc_attr_e($value['std']);
                    }
                                    ?>" />
                                    </div>

                                    <?php
                                    break;
                                case 'color':
                                    ?>

                                    <div class="options_input options_text">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <span class="labels"><label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label></span>
                                        <input name="<?php echo $option_name . '[' . $valueid . ']'; ?>" id="<?php echo $option_name . '[' . $valueid . ']'; ?>" type="text" class="color-field" value="<?php
                    if (isset($options[$valueid])) {
                        esc_attr_e($options[$valueid]);
                    } else {
                        esc_attr_e($value['std']);
                    }
                                    ?>" />
                                    </div>

                                    <?php
                                    break;
                                case 'textarea':
                                    ?>
                                    <div class="options_input options_textarea">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <span class="labels"><label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label></span>
                                        <textarea name="<?php echo $option_name . '[' . $valueid . ']'; ?>" type="<?php echo $option_name . '[' . $valueid . ']'; ?>" cols="" rows="3"><?php
                    if (isset($options[$valueid])) {
                        esc_attr_e($options[$valueid]);
                    } else {
                        esc_attr_e($value['std']);
                    }
                                    ?></textarea>
                                    </div>
                                    <?php
                                    break;
                                case 'select':
                                    ?>
                                    <div class="options_input options_select">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <span class="labels"><label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label></span>
                                        <select name="<?php echo $option_name . '[' . $valueid . ']'; ?>" id="<?php echo $option_name . '[' . $valueid . ']'; ?>">
                                            <option value="default">Selecione</option>
                                            <?php foreach ($value['options'] as $option) { ?>
                                                <option <?php
                                                if ($options[$valueid] == $option) {
                                                    echo 'selected="selected"';
                                                }
                                                ?>><?php echo $option; ?></option><?php } ?>
                                        </select>
                                    </div>

                                    <?php
                                    break;
                                case 'category':
                                    $categories = get_categories();
                                    ?>
                                    <div class="options_input options_select">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <span class="labels"><label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label></span>
                                        <select name="<?php echo $option_name . '[' . $valueid . ']'; ?>" id="<?php echo $option_name . '[' . $valueid . ']'; ?>">
                                            <?php foreach ($value['options'] as $option) { ?>
                                                <option <?php
                                                if ($options[$valueid] == $option) {
                                                    echo 'selected="selected"';
                                                }
                                                ?>><?php echo $option; ?></option><?php } ?>
                                        </select>
                                    </div>    

                                    <?php
                                    break;
                                case "radio":
                                    ?>
                                    <div class="options_input options_select">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <span class="labels"><label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label></span>
                                        <?php
                                        foreach ($value['options'] as $key => $option) {
                                            $radio_setting = $options[$valueid];
                                            if ($radio_setting != '') {
                                                if ($key == $options[$valueid]) {
                                                    $checked = "checked=\"checked\"";
                                                } else {
                                                    $checked = "";
                                                }
                                            } else {
                                                if ($key == $value['std']) {
                                                    $checked = "checked=\"checked\"";
                                                } else {
                                                    $checked = "";
                                                }
                                            }
                                            ?>
                                            <input type="radio" id="<?php echo $option_name . '[' . $valueid . ']'; ?>" name="<?php echo $option_name . '[' . $valueid . ']'; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><?php echo $option; ?><br />
                                        <?php } ?>
                                    </div>

                                    <?php
                                    break;
                                case "checkbox":
                                    ?>
                                    <div class="options_input options_checkbox">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <?php
                                        if (isset($options[$valueid])) {
                                            $checked = "checked=\"checked\"";
                                        } else {
                                            $checked = "";
                                        }
                                        ?>
                                        <input type="checkbox" name="<?php echo $option_name . '[' . $valueid . ']'; ?>" id="<?php echo $option_name . '[' . $valueid . ']'; ?>" value="true" <?php echo $checked; ?> />
                                        <label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label>
                                    </div>
                                    <?php
                                    break;
                                case "upload":
                                    ?>
                                    <div class="options_input options_text">
                                        <div class="options_desc"><?php echo $value['desc']; ?></div>
                                        <span class="labels"><label for="<?php echo $option_name . '[' . $valueid . ']'; ?>"><?php echo $value['name']; ?></label></span>
                                        <div class="relative">
                                            <input class="<?php echo $option_name . '_' . $valueid; ?>" name="<?php echo $option_name . '[' . $valueid . ']'; ?>" id="<?php echo $option_name . '[' . $valueid . ']'; ?>" type="text" value="<?php
                    if (isset($options[$valueid])) {
                        esc_attr_e($options[$valueid]);
                    } else {
                        esc_attr_e($value['std']);
                    }
                                    ?>" />
                                            <input id="<?php echo $option_name . '_' . $valueid; ?>_button" class="" type="button" value="Enviar" />
                                        </div> 
                                    </div> 
                                    <script type="text/javascript">
                                        jQuery(document).ready(function () {
                                            jQuery('#<?php echo $option_name . '_' . $valueid; ?>_button').click(function () {
                                                uploadID = jQuery(this).prev('input');
                                                formfield = jQuery('.<?php echo $option_name . '_' . $valueid; ?>').attr('name');
                                                tb_show('Upload de imagem', 'media-upload.php?referer=theme-options&type=image&TB_iframe=true', false);

                                                return false;
                                            });
                                            //console.log('debug' + ajax_object.store_url);

                                            window.send_to_editor = function (html) {
                                                imgurl = jQuery('img', html).attr('src');
                                                newImg = imgurl.replace(ajax_object.store_url, '')
                                                uploadID.val(newImg);
                                                tb_remove();
                                            }
                                        });
                                    </script>
                                    <?php
                                    break;
                                case "close":
                                    ?>
                                </div><!--#section_body-->
                            </div><!--#section_wrap-->

                            <?php
                            break;
                    }
                }
                ?>

                <script type="text/javascript">

                    function confirmation() {

                        if (confirm("<?php echo utf8_encode("Atenção"); ?>:Deseja realmente deletar todos os valores dos campos?")) {
                            return true;
                        } else {
                            return false
                        }
                        return false;
                    }

                </script>

                <span class="submit">
                    <input class="button button-primary" type="submit" name="save" value="Salvar todas mudanças" />
                </span>
            </form>

            <form method="post" action="">
                <span class="button-right" class="submit">
                    <input class="button button-secondary" type="submit" name="reset" onclick="return confirmation()" value="Resetar/Deletar configuraçõeses" />
                    <input type="hidden" name="action" value="reset" />
                    <span>Atenção: Todas as configurações serão excluídas do banco de dados. Clique somente ao iniciar ou remover completamente o tema</span>
                </span>
            </form>
        </div><!--#options-wrap-->

    </div>

<?php
}

function redes_sociais() {
    $options = get_option('p2h_theme_options');
    ?>
    <aside class="footer-widget-area widget-area site-footer-focus-item footer-widget-area-inner" data-section="sidebar-widgets-footer-widget-1" aria-label="Footer Widget 1">
        <section id="nav_menu-13" class="widget widget_nav_menu">
            <h2 class="widget-title">Siga-nos nas redes</h2>
            <ul class="social">
                <?php if (!empty($options['facebook_url'])) { ?>
                    <li>
                        <a target="_blank" href="<?php print $options['facebook_url'] ?>">
                            <i class="fab fa-facebook"></i>
                        </a>
                    </li>
                <?php } ?>
                <?php if (!empty($options['instagram_url'])) { ?>
                    <li>
                        <a target="_blank" href="<?php print $options['instagram_url'] ?>">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </li>
                <?php } ?>
                <?php if (!empty($options['youtube_url'])) { ?>
                    <li>
                        <a target="_blank" href="<?php print $options['youtube_url'] ?>">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </li>
                <?php } ?>
                <?php if (!empty($options['pinterest_url'])) { ?>
                    <li>
                        <a target="_blank" href="<?php print $options['pinterest_url'] ?>">
                            <i class="fab fa-pinterest"></i>
                        </a>
                    </li>
                <?php } ?>
                <?php if (!empty($options['twitter_url'])) { ?>
                    <li>
                        <a target="_blank" href="<?php print $options['twitter_url'] ?>">
                            <i class="fab fa-twitter-square"></i>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </section>
    </aside>
    <?php
}

add_shortcode('redes_sociais', 'redes_sociais');

function contato_cliente() {
    $options = get_option('p2h_theme_options');
    ?>
    <aside class="footer-widget-area widget-area site-footer-focus-item footer-widget-area-inner" data-section="sidebar-widgets-footer-widget-1" aria-label="Footer Widget 1">
        <section id="nav_menu-13" class="widget widget_nav_menu">
            <h2 class="widget-title">Contato</h2>
            <ul class="contatos">
                <?php if (isset($options['endereco']) && $options['endereco'] != '') { ?>
                    <li>
                        <i class="fas fa-map-marker"></i>
                        <span><?php echo nl2br($options['endereco']); ?></span>
                    </li>
                <?php } ?>
                <?php if (isset($options['fone_1']) && $options['fone_1'] != '') { ?>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span><?php echo $options['fone_1']; ?></span>
                    </li>
                <?php } ?>
                <?php if (isset($options['fone_2']) && $options['fone_2'] != '') { ?>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span><?php echo $options['fone_2']; ?></span>
                    </li>
                <?php } ?>
                <?php if (isset($options['whatsapp']) && $options['whatsapp'] != '') { ?>
                    <?php $whatsapp_link = str_replace(array('(', ')', '-', ' '), '', $options['whatsapp']); ?>
                    <li>
                        <a target="_blank" href="https://api.whatsapp.com/send?phone=55<?php echo $whatsapp_link; ?>">
                            <i class="fab fa-whatsapp"></i>
                            <span><?php echo $options['whatsapp']; ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if (isset($options['atendimento']) && $options['atendimento'] != '') { ?>
                    <li>
                        <i class="far fa-clock"></i>
                        <span><?php echo nl2br($options['atendimento']); ?></span>
                    </li>
                <?php } ?>
                <?php if (isset($options['email']) && $options['email'] != '') { ?>
                    <li>
                        <a href="mailto:<?php echo $options['email']; ?>">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo $options['email']; ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </section>
    </aside>
    <?php
}

add_shortcode('contato_cliente', 'contato_cliente');

function razao_social_footer() {
    $options = get_option('p2h_theme_options');
    ?>
    <?php
    if ((!empty($options['razao_social'])) && (!empty($options['cnpj']))) {
        ?>
        <p class="cnpj text-left">
            <?php echo $options['razao_social']; ?>
            <b>CNPJ: <?php echo $options['cnpj']; ?></b>
        </p>
    <?php } ?>
    <?php
}

add_shortcode('razao_social', 'razao_social_footer');

function texto_footer() {
    $options = get_option('p2h_theme_options');
    ?>
    <?php
    if ((!empty($options['razao_social'])) && (!empty($options['cnpj']))) {
        ?>
        <h1 class="texto_footer">
            <?php echo $options['footer_text']; ?>
        </h1>
    <?php } ?>
    <?php
}

add_shortcode('texto_footer', 'texto_footer');

function balao_whatsapp_footer() {
    $options = get_option('p2h_theme_options');

    if (isset($options['whatsapp_flutuante_position'])):
        switch ($options['whatsapp_flutuante_position']):
            case 'Direito' :
                $position = 'right';
                $bottom = '100px';
                break;
            case 'Esquerdo' :
                $position = 'left';
                $bottom = '30px';
                break;
            default :
                $position = 'right';
                $bottom = '100px';
        endswitch;
    endif;

    $style = '<style type="text/css" rel="stylesheet">.whatsapp_flutuante{transition:all .1s ease;background:#4cc95b;text-align:center;border-radius:50%;font-size:33px;position:fixed;cursor:pointer;height:60px;bottom:30px;color:#FFF;width:60px;' . $position . ':30px}.whatsapp_flutuante:hover i,.whatsapp_flutuante:hover{color:#FFF}.whatsapp_flutuante:hover i{transform:rotate(10deg)}.ast-scroll-to-top-right{bottom:' . $bottom . ';}</style>';

    if (isset($options['whatsapp']) && $options['whatsapp'] != ''):
        if (isset($options['whatsapp_flutuante']) && $options['whatsapp_flutuante'] == "true"):
            $whatsapp = str_replace(array('(', ')', '-', ' '), '', $options['whatsapp']);
            echo '<a class="whatsapp_flutuante" target="_blank" href="https://api.whatsapp.com/send?phone=55' . $whatsapp . '"><i class="fab fa-whatsapp"></i></a>';
            echo $style;
        endif;
    endif;
}

add_action('astra_footer_after', 'balao_whatsapp_footer', 30);
?>