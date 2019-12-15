<?php

class Sumedia_Amapn_Admin_Controller_Delete extends Sumedia_Base_Controller
{
    /**
     * @var $this
     */
    protected static $instance;

    public function execute()
    {

        if (!wp_verify_nonce($_POST['_wpnonce'], 'bulk-plugins_page_sumedia')) {
            return;
        }

        $links = Sumedia_Base_Registry::get('Sumedia_Amapn_Repository_Links');
        foreach ($_POST['ids'] as $id) {
            if(!is_numeric($id)) {
                continue;
            }

            $data = $links->findOne('id', $id);
            if (!$data) {
                continue;
            }

            $files = [
                SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . Suma\ds('/cache/link/content/') . $data['uniqueid'] . '.html',
                SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . Suma\ds('/cache/link/data/') . $data['uniqueid'] . '.php',
                SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . Suma\ds('/cache/link/images/') . $data['uniqueid'] . '.jpg'
            ];
            foreach ($files as $file) {
                @unlink($file);
            }

            $links->delete($id);
        }

        $messenger = Sumedia_Base_Messenger::get_instance();
        $messenger->add_message($messenger::TYPE_SUCCESS, __('The ad has been successfully removed.', SUMEDIA_AMAPN_PLUGIN_NAME));

        wp_redirect('admin.php?page=sumedia&plugin=' . SUMEDIA_AMAPN_PLUGIN_NAME . '&action=adlist');

    }
}