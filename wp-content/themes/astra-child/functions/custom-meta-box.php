<?php
$new_meta_boxes_produto = array(
    "valor" => array(
        "name" => "produto_valor",
        "std" => "",
        "title" => "Valor do produto",
        "type" => "text",
        "description" => "Entre com o valor do produto")
);

function my_meta_produto($new_meta_boxes_produto) {
    global $new_meta_boxes_produto;
    new_meta_boxes($new_meta_boxes_produto);
}

function new_meta_boxes($my_metas) {
    global $post;
    foreach ($my_metas as $meta_box) {

        if ($meta_box['type'] == 'text'):
            $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
            if ($meta_box_value == "")
                $meta_box_value = $meta_box['std'];
            echo'<input type="hidden" name="' . $meta_box['name'] . '_noncename" id="' . $meta_box['name'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
            echo'<p><strong>' . $meta_box['title'] . '</strong>';
            echo'<input type="text" name="' . $meta_box['name'] . '" value="' . $meta_box_value . '"  style="width: 100%; margin-top: 5px;" /></p>';
            echo'<p><em><label for="' . $meta_box['name'] . '">' . $meta_box['description'] . '</label></em></p>';
        endif;

        if ($meta_box['type'] == 'textarea'):
            $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
            if ($meta_box_value == "")
                $meta_box_value = $meta_box['std'];
            echo'<input type="hidden" name="' . $meta_box['name'] . '_noncename" id="' . $meta_box['name'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
            echo'<p><strong>' . $meta_box['title'] . '</strong>';
            if (is_array($meta_box_value))
                $meta_box_value = $meta_box_value['videos'];
            echo'<textarea name="' . $meta_box['name'] . '" style="width: 100%; margin-top: 5px;">' . $meta_box_value . '</textarea></p>';
            echo'<p><em><label for="' . $meta_box['name'] . '">' . $meta_box['description'] . '</label></em></p>';
        endif;

        if ($meta_box['type'] == 'select'):
            $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
            if ($meta_box_value == "")
                $meta_box_value = $meta_box['std'][0];
            echo'<input type="hidden" name="' . $meta_box['name'] . '_noncename" id="' . $meta_box['name'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

            echo'<p><strong>' . $meta_box['title'] . '</strong><br />';
            echo'<select name="' . $meta_box['name'] . '" >';
            for ($x = 0; $x < sizeof($meta_box['std']); $x++) {
                if ($meta_box_value == $meta_box['std'][$x]):
                    $selected = 'selected="selected"';
                else:
                    $selected = "";
                endif;
                echo'<option ' . $selected . ' value="' . $meta_box['std'][$x] . '">' . $meta_box['std'][$x] . '</option>';
            }
            echo'</select>';
            echo'<p><em><label for="' . $meta_box['name'] . '">' . $meta_box['description'] . '</label></em></p>';
        endif;

        if ($meta_box['type'] == 'date'):
            $meta_box_value = get_post_meta($post->ID, $meta_box['name'], true);
            if ($meta_box_value == "")
                $meta_box_value = $meta_box['std'];
            echo'<input type="hidden" name="' . $meta_box['name'] . '_noncename" id="' . $meta_box['name'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
            echo'<strong>' . $meta_box['title'] . '</strong>';

            echo'<p><select class="date-m" onchange="set_date_' . $meta_box['name'] . '()" name="date-m">';
            echo'<option value="01">jan</option>';
            echo'<option value="02">fev</option>';
            echo'<option value="03">mar</option>';
            echo'<option value="04">abr</option>';
            echo'<option value="05">mai</option>';
            echo'<option value="06">jun</option>';
            echo'<option value="07">jul</option>';
            echo'<option value="08">ago</option>';
            echo'<option value="09">set</option>';
            echo'<option value="10">out</option>';
            echo'<option value="11">nov</option>';
            echo'<option value="12">dez</option>';
            echo'</select>';

            echo'<input class="date-d" onkeyup="set_date_' . $meta_box['name'] . '()" type="text" name="date-d" size="2" maxlength="2" value="' . date('d') . '" />, ';

            echo'<input class="date-y" onkeyup="set_date_' . $meta_box['name'] . '()" type="text" name="date-y" size="4" maxlength="4" value="' . date('Y') . '" /></p>';

            echo'<input type="hidden" id="' . $meta_box['name'] . '" name="' . $meta_box['name'] . '" value="' . $meta_box_value . '"  style="width: 100%; margin-top: 5px;" /></p>';

            echo'<p><em><label for="' . $meta_box['name'] . '">' . $meta_box['description'] . '</label></em></p>';
            ?>
            <script type="text/javascript">
                function set_date_<?php echo $meta_box['name']; ?>() {
                    jQuery(document).ready(function ($) {
                        $('#<?php echo $meta_box['name']; ?>').val($('.date-d').val() + '-' + $('.date-m').val() + '-' + $('.date-y').val());
                    });
                }
            </script>
            <?php
        endif;
    }
}

function create_meta_box() {
    if (function_exists('add_meta_box')) {
        add_meta_box('new-meta-boxes-produto', 'Valor', 'my_meta_produto', 'produto', 'normal', 'high');
    }
}

function save_postdata($post_id, $my_metas) {
    global $post, $new_meta_boxes_img;
    foreach ($my_metas as $meta_box) {
        if (!wp_verify_nonce($_POST[$meta_box['name'] . '_noncename'], plugin_basename(__FILE__))) {
            return $post_id;
        }


        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        $data = $_POST[$meta_box['name']];

        if (get_post_meta($post_id, $meta_box['name']) == "")
            add_post_meta($post_id, $meta_box['name'], $data, true);
        elseif ($data != get_post_meta($post_id, $meta_box['name'], true))
            update_post_meta($post_id, $meta_box['name'], $data);
        elseif ($data == "")
            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
    }
}

function save_post_produto($new_meta_boxes_produto) {
    global $new_meta_boxes_produto;
    global $post_id;
    save_postdata($post_id, $new_meta_boxes_produto);
}

add_action('admin_menu', 'create_meta_box');
add_action('save_post', 'save_post_produto');
?>