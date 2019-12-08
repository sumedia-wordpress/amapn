<?php

class Sumedia_Amapn_Plugin
{
    public function textdomain()
    {
        //$event = new Sumedia_Base_Event(function () {
        load_plugin_textdomain(
            'sumedia-amapn',
            false,
            SUMEDIA_AMAPN_PLUGIN_NAME . '/languages/');
        //});
        //add_action('plugins_loaded', [$event, 'execute']);
    }

    public function installer()
    {
        $installer = new Sumedia_Amapn_Installer;
        register_activation_hook(__FILE__, [$installer, 'install']);
    }

    public function view()
    {
        $view = Sumedia_Base_Registry::get_instance('view');
        $plugins = $view->get('sumedia_base_admin_view_plugins');
        $plugins->plugins[SUMEDIA_AMAPN_PLUGIN_NAME] = [
            'description_template' => SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . ds('/admin/templates/plugin.phtml'),
        ];

        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'sumedia' && isset($_REQUEST['plugin']) && $_REQUEST['plugin'] == 'amapn') {
            $view->get('sumedia_base_admin_view_menu')->template = SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . ds('/admin/templates/config.phtml');

            $heading = $view->get('sumedia_base_admin_view_heading');
            $heading->title = __('Amazon Partnernet');
            $heading->side_title = __('Configuration');
            $heading->version = SUMEDIA_AMAPN_VERSION;
        }

        $event = new Sumedia_Base_Event(function(){
            $cssFile = SUMEDIA_PLUGIN_URL . SUMEDIA_AMAPN_PLUGIN_NAME . '/assets/css/style.css';
            wp_enqueue_style('suma_amapn_style', $cssFile);
        });
        add_action('admin_print_styles', [$event, 'execute']);

        $event = new Sumedia_Base_Event(function(){
            $cssFile = SUMEDIA_PLUGIN_URL . SUMEDIA_AMAPN_PLUGIN_NAME . '/assets/css/shortcode.css';
            wp_enqueue_style('suma_amapn_shortcode_style', $cssFile);
        });
        add_action('wp_enqueue_scripts', [$event, 'execute']);

        function sumedia_amapn_link_shortcode($attrs)
        {
            $data = shortcode_atts(array('id' => ''), $attrs);
            if ($data['id']) {
                $view = Sumedia_Base_Registry::get_instance('view');
                $shortcode = new Sumedia_Amapn_View_Shortcode();
                $view->set('sumedia_amapn_view_shortcode', $shortcode);
                $shortcode->fetch_by_uniqueid($data['id']);
                return $shortcode->render(true);
            }
        }
        add_shortcode( 'sumedia_amapn_link', 'sumedia_amapn_link_shortcode' );
    }

    public function linksparser(){
        $registry = Sumedia_Base_Registry::get_instance();
        $parser = new Sumedia_Amapn_Linkparser();
        $registry->set('sumedia_amapn_linkparser', $parser);
    }

    public function post_add_link()
    {
        if (isset($_GET['plugin']) && $_GET['plugin'] == 'amapn'
            && isset($_GET['action']) && $_GET['action'] == 'add_link'
            && isset($_POST['add_link_nonce'])
        ) {
            if (wp_verify_nonce($_POST['add_link_nonce'], 'sumedia-amapn-add-link')) {
                $form = new Sumedia_Amapn_Addlink_Form();
                $form->do_request($_POST);
                $form->save();
            }
            $event = new Sumedia_Base_Event(function() {
                wp_redirect(admin_url('admin.php?page=sumedia&plugin=gfont'));
            });
            add_action('template_redirect', [$event, 'execute']);
        }
    }

    public function post_delete_links()
    {
        if (isset($_GET['plugin']) && $_GET['plugin'] = 'amapn'
                && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['_wpnonce'])) {
            if (wp_verify_nonce($_POST['_wpnonce'], 'bulk-plugins_page_sumedia')) {
                $table_name = $wpdb->prefix . 'sumedia_amapn_links';
                if (isset($_POST['ids'])) {
                    foreach ($_POST['ids'] as $id) {
                        if (!is_numeric($id)) {
                            continue;
                        }
                        $query = "DELETE FROM `" . $table_name . "` WHERE `id` = %s";
                        $prepare = $wpdb->prepare($query, $id);
                        $wpdb->query($prepare);
                    }
                }
            }
            $event = new Sumedia_Base_Event(function() {
                wp_redirect(admin_url('admin.php?page=sumedia&plugin=amapn'));
            });
            add_action('template_redirect', [$event, 'execute']);
        }
    }
}