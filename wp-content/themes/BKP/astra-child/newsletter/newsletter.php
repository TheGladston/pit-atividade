<?php
define('PLUGIN_DIR', get_stylesheet_directory_uri() . '/newsletter');

function main_scripts() {
    wp_enqueue_style('style', PLUGIN_DIR . '/css/newsletter.css');
    wp_enqueue_script('scripts', PLUGIN_DIR . "/js/newsletter.js", array('jquery'));
    wp_localize_script('scripts', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'main_scripts');

function create_table_newsletter() {
    global $wpdb;
    $table = $wpdb->prefix . "newsletter";
    if ($wpdb->get_var("show tables like '$table'") != $table) {
        $sql = "CREATE TABLE " . $table . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`nome` mediumtext NOT NULL,
		`email` tinytext NOT NULL,
		UNIQUE KEY id (id)
		);";
    }

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    return dbDelta($sql);
}

add_action('wp_ajax_newsletter', 'newsletter');
add_action('wp_ajax_nopriv_newsletter', 'newsletter');

function newsletter() {
    global $wpdb;
    $table = $wpdb->prefix . "newsletter";
    $error = false;
    $mensagem = '';

    $dados = array();
    $dados['nome'] = empty($_REQUEST["nome"]) ? '-' : $_REQUEST["nome"];
    $dados['email'] = $_REQUEST["email"];

    if ($dados['nome'] == '' && $error == false) {
        $error = true;
        $mensagem = 'Informe seu nome.';
    }
    if ($dados['email'] == '' && $error == false) {
        $error = true;
        $mensagem = 'Informe seu e-mail.';
    }

    $array = array();
    $array['nome'] = $dados["nome"];
    $array['email'] = $dados["email"];

    $format = array();
    $format['nome'] = '%s';
    $format['email'] = '%s';

    if ($error == false) {
        $create_table = create_table_newsletter();
        $result = $wpdb->insert($table, $array, $format);
    }

    if ($result) {
        $data = json_encode(
                array(
                    'alert' => ($error) ? 'danger' : 'success',
                    'mensagem' => 'Cadastrado com sucesso!',
                    'table' => $create_table,
                )
        );
    } else {
        $data = json_encode(
                array(
                    'alert' => ($error) ? 'danger' : 'success',
                    'mensagem' => array($mensagem, $wpdb->last_error),
                    'table' => $create_table,
                )
        );
    }

    echo $data;
    wp_die();
}

function conteudo_lista_cliente() {
    global $wpdb;
    $table = $wpdb->prefix . "newsletter";
    $sql_clientes = ("SELECT * FROM {$table}");
    $clientes = $wpdb->get_results($sql_clientes);
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Clientes</h1>
        <br>
        <br>
        <hr class="wp-header-end">
        <form id="update_cliente" action="#">
            <h2 class="screen-reader-text">Lista de Clientes Newsletter</h2>
            <table class="wp-list-table widefat striped posts">
                <thead>
                    <tr>
                        <th scope="col" id="title" class="manage-column column-title column-primary">Nome</th>
                        <th scope="col" id="author" class="manage-column column-author">E-mail</th>
                    </tr>
                </thead>

                <tbody id="the-list-cliente">
                    <?php if (isset($clientes) && count($clientes) > 0) { ?>
                        <?php foreach ($clientes as $cliente) { ?>
                            <tr id="cliente-<?php echo $cliente->id; ?>">
                                <td class="title column-title has-row-actions column-primary page-title" data-colname="Nome">
                                    <strong>
                                        <?php echo $cliente->nome; ?>
                                    </strong>
                                </td>
                                <td class="author column-author" data-colname="E-mail / Domínio">
                                    <?php echo $cliente->email; ?><br>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="2">Nenhum cliente</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>

                </tfoot>
            </table>
        </form>

        <div id="ajax-response"></div>
        <br class="clear">
    </div>
    <?php
}

function lista_clientes() {
    add_dashboard_page('Newsletter', 'Newsletter', 'read', 'clientes', 'conteudo_lista_cliente', 40);
}

add_action('admin_menu', 'lista_clientes');

function form_newsletter() {
    $html = '';

    $html .= '<div class="newsletter-wrapper">';
    $html .= '<div class="box-title">';
    $html .= '<h3>Fique por dentro das nossas novidades</h3>';
    $html .= '</div>';
    $html .= '<div class="box-form">';
    $html .= '<form id="newsletter" action="#" method="post">';
    $html .= '<div class="input-group">';
    //$html .= '<input type="text" class="form-control" id="nome" placeholder="Informe seu nome">';
    $html .= '<input type="text" class="form-control" id="email" placeholder="Informe seu e-mail">';
    $html .= '<button class="btn btn-default" type="submit">CADASTRAR</button>';
    $html .= '<div class="clear"></div>';
    $html .= '</div>';
    $html .= '<div id="mensagem"></div>';
    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

add_shortcode('form_newsletter', 'form_newsletter');
