<?php
define('DEPOIMENTO_DIR', get_stylesheet_directory_uri() . '/depoimento');

function main_scripts_depoimento() {
    //wp_enqueue_style('clientes', DEPOIMENTO_DIR . '/css/owl.carousel.min.css', false);
    //wp_enqueue_script('clientes', DEPOIMENTO_DIR . "/js/owl.carousel.min.js", array('jquery'));
    //wp_enqueue_script('clientes', DEPOIMENTO_DIR . "/js/depoimento.js", array('jquery'));
}

add_action('wp_enqueue_scripts', 'main_scripts_depoimento');

function depoimento_register() {

    $labels = array(
        'name' => _x('Depoimentos', 'post type general name'),
        'singular_name' => _x('Depoimento', 'post type singular name'),
        'add_new' => _x('Novo depoimento', 'depoimento item'),
        'add_new_item' => __('Adicionar Novo'),
        'edit_item' => __('Editar'),
        'new_item' => __('Novo depoimento'),
        'view_item' => __('Ver depoimento'),
        'search_items' => __('Procurar depoimento'),
        'not_found' => __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => 'dashicons-format-status',
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 4,
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type('depoimento', $args);
    flush_rewrite_rules();
}

add_action('init', 'depoimento_register');

function depoimento_meta() {
    global $post;
    $custom = get_post_custom($post->ID);
    $nome_depoimento = $custom["nome_depoimento"][0];
    $cargo_depoimento = $custom["cargo_depoimento"][0];
    $empresa_depoimento = $custom["empresa_depoimento"][0];
    $nota_depoimento = $custom["nota_depoimento"][0];
    ?>
    <label>Nome:</label><br>
    <input style="width: 100%;" name="nome_depoimento" value="<?php echo ($nome_depoimento != '') ? $nome_depoimento : ''; ?>" />
    <br>
    <label>Cargo:</label><br>
    <input style="width: 100%;" name="cargo_depoimento" value="<?php echo ($cargo_depoimento != '') ? $cargo_depoimento : ''; ?>" />
    <br>
    <!--    
    <label>Empresa:</label><br>
    <input style="width: 100%;" name="empresa_depoimento" value="<?php echo ($empresa_depoimento != '') ? $empresa_depoimento : ''; ?>" />
    <br>
    -->
    <label>Nota:</label><br>
    <select style="width: 100%;" name="nota_depoimento">
        <option value=""  <?php echo ($nota_depoimento == '' ) ? 'selected="selected"' : ''; ?>>Selecione</option>
        <option value="1" <?php echo ($nota_depoimento == '1') ? 'selected="selected"' : ''; ?>>1</option>
        <option value="2" <?php echo ($nota_depoimento == '2') ? 'selected="selected"' : ''; ?>>2</option>
        <option value="3" <?php echo ($nota_depoimento == '3') ? 'selected="selected"' : ''; ?>>3</option>
        <option value="4" <?php echo ($nota_depoimento == '4') ? 'selected="selected"' : ''; ?>>4</option>
        <option value="5" <?php echo ($nota_depoimento == '5') ? 'selected="selected"' : ''; ?>>5</option>
    </select>
    <br>
    <?php
}

function admin_init_depoimento() {
    add_meta_box("depoimento_meta", "Depoimento", "depoimento_meta", "depoimento", "side", "low");
}

add_action("admin_init", "admin_init_depoimento");

function save_depoimento() {
    global $post;
    $custom = get_post_custom($post->ID);
    $tipo = $_POST["tipo"];

    if ($post->post_type == 'depoimento') {
        update_post_meta($post->ID, "nome_depoimento", $_POST["nome_depoimento"]);
        update_post_meta($post->ID, "cargo_depoimento", $_POST["cargo_depoimento"]);
        update_post_meta($post->ID, "empresa_depoimento", $_POST["empresa_depoimento"]);
        update_post_meta($post->ID, "nota_depoimento", $_POST["nota_depoimento"]);
    }
}

add_action('save_post', 'save_depoimento');

function get_depoimentos() {
    $depoimento = query_posts(array(
        'status' => 'publich',
        'post_type' => 'depoimento',
        'post_per_page' => -1,
    ));
    wp_reset_query();
    return $depoimento;
}

function depoimentos_func($atts) {

    $html = '';
    $depoimentos = get_depoimentos();
    if (count($depoimentos) > 0):
        $d = shortcode_atts(array(
            'carousel' => 'true',
            'quant' => -1,
                ), $atts);

        if ($d['carousel'] == 'true'):
//            $html .= '<link rel="stylesheet" href="' . DEPOIMENTO_DIR . '/css/owl.carousel.min.css" />';
//            $html .= '<script src="' . DEPOIMENTO_DIR . '/js/owl.carousel.min.js" type = "text/javascript"></script>';
            $html .= '<script src="' . DEPOIMENTO_DIR . '/js/depoimento.js" type = "text/javascript"></script>';
        endif;

        $html .= ($d['carousel'] == 'true') ? '<div id="owl-depoimento" class="owl-carousel owl-theme">' : '<div id="owl-depoimento">';

        foreach ($depoimentos as $post):

            $nome_depoimento = get_post_meta($post->ID, 'nome_depoimento', true);
            $cargo_depoimento = get_post_meta($post->ID, 'cargo_depoimento', true);
            $empresa_depoimento = get_post_meta($post->ID, 'empresa_depoimento', true);
            $nota_depoimento = get_post_meta($post->ID, 'nota_depoimento', true);
            $src_img_cliente = get_the_post_thumbnail_src(get_the_post_thumbnail($post->ID, 'large'));

            $html .= '<div class="client">';
            $html .= '<div class="client-inner">';

            $html .= '<div class="client-content">';
            $html .= '<p>&quot;' . $post->post_content . '&quot</p>';
            $html .= '</div>';

            if ($nota_depoimento != ''):
                $nota = '';
                $html .= '<div class="client-star">';
                $html .= '<div class="star-rating">';
                for ($i = 1; $i <= 5; $i++):
                    if ($i > $nota_depoimento):
                        $nota .= '<i class="far fa-star"></i>';
                    else:
                        $nota .= '<i class="fas fa-star"></i>';
                    endif;
                endfor;

                $html .= $nota;
                
                $html .= '</div>';
                $html .= '</div><!--/client-star-->';
            endif;

            $html .= '<div class="client-info">';
            $html .= '<h3 class="client-name">' . $nome_depoimento . '</h3>';
            $html .= '</div><!--/client-info-->';

            $html .= '</div><!--/client-inner-->';
            $html .= '</div><!--/client-->';

        endforeach;
        $html .= '</div><!--/owl-depoimento-->';
    endif;

    return $html;
}

add_shortcode('depoimentos', 'depoimentos_func');
