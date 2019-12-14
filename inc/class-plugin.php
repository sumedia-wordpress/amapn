<?php

class Sumedia_Amapn_Plugin
{
    public function init()
    {
        $this->textdomain();
        $this->plugin_view();
        $this->controller();
        $this->shortcode();
        add_action('admin_print_styles', [$this, 'enqueue_admin_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_shortcode_styles']);
    }

    public function install()
    {
        $installer = new Sumedia_Amapn_Db_Installer;
        $installer->install();
    }

    function textdomain()
    {
        load_plugin_textdomain(
            SUMEDIA_AMAPN_PLUGIN_NAME,
            false,
            SUMEDIA_AMAPN_PLUGIN_NAME . Suma\DS . 'languages'
        );
    }

    public function plugin_view()
    {
        $plugins = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Plugins');
        $plugins->add_plugin(SUMEDIA_AMAPN_PLUGIN_NAME, [
            'name' => 'Amazon Partnernet',
            'version' => SUMEDIA_AMAPN_VERSION,
            'options' => [
                [
                    'name' => __('Adlist', SUMEDIA_AMAPN_PLUGIN_NAME),
                    'url' => admin_url('admin.php?page=sumedia&plugin=' . SUMEDIA_AMAPN_PLUGIN_NAME . '&action=adlist')
                ],
                [
                    'name' => __('Create new', SUMEDIA_AMAPN_PLUGIN_NAME),
                    'url' => admin_url('admin.php?page=sumedia&plugin=' . SUMEDIA_AMAPN_PLUGIN_NAME . '&action=new')
                ]
            ],
            'description_template' => Suma\ds(SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . '/admin/templates/plugin.phtml')
        ]);
    }

    public function controller()
    {
        if (isset($_GET['page']) && isset($_GET['plugin']) && isset($_GET['action'])) {
            if ($_GET['page'] == 'sumedia' && $_GET['plugin'] == SUMEDIA_AMAPN_PLUGIN_NAME)
            {
                if ($_GET['action'] == 'adlist') {
                    $controller = Sumedia_Amapn_Admin_Controller_Adlist::get_instance();
                } elseif($_GET['action'] == 'new') {
                    $controller = Sumedia_Amapn_Admin_Controller_New::get_instance();
                } elseif(isset($_POST['action']) && $_POST['action'] == 'delete') {
                    $controller = Sumedia_Amapn_Admin_Controller_Delete::get_instance();
                }

                if (isset($controller)) {
                    add_action('admin_init', [$controller, 'prepare']);
                    add_action('admin_init', [$controller, 'execute']);
                }
            }
        }
    }

    public function enqueue_admin_styles()
    {
        $cssFile = SUMEDIA_PLUGIN_URL . SUMEDIA_AMAPN_PLUGIN_NAME . '/assets/css/style.css';
        wp_enqueue_style('suma_amapn_style', $cssFile);
    }

    public function enqueue_shortcode_styles()
    {
        $cssFile = SUMEDIA_PLUGIN_URL . SUMEDIA_AMAPN_PLUGIN_NAME . '/assets/css/shortcode.css';
        wp_enqueue_style('suma_amapn_shortcode_style', $cssFile);
    }

    public function shortcode()
    {
        add_shortcode( 'sumedia_amapn_link', function($attrs){
            $data = shortcode_atts(array('id' => ''), $attrs);
            if ($data['id']) {
                $shortcode = Sumedia_Base_Registry_View::get('Sumedia_Amapn_View_Shortcode');
                $view = $shortcode->fetch_by_uniqueid($data['id']);
                return $view->render(true);
            }
        });
    }
}
