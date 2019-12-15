<?php

class Sumedia_Amapn_Admin_Controller_Adlist extends Sumedia_Base_Controller
{
    /**
     * @var $this
     */
    protected static $instance;

    public function prepare()
    {
        $overview = Sumedia_Base_Registry::get('Sumedia_Base_Admin_View_Overview');
        $overview->set_content_view(Sumedia_Base_Registry::get('Sumedia_Amapn_Admin_View_Adlist'));

        $heading = Sumedia_Base_Registry::get('Sumedia_Base_Admin_View_Heading');
        $heading->set_title(__('Amazon Partnernet', SUMEDIA_AMAPN_PLUGIN_NAME));
        $heading->set_side_title(__('Adlist', SUMEDIA_AMAPN_PLUGIN_NAME));
        $heading->set_version(SUMEDIA_AMAPN_VERSION);
        $heading->set_options([
            [
                'name' => __('Back to the plugin overview'),
                'url' => admin_url('admin.php?page=sumedia')
            ]
        ]);
    }

    public function execute()
    {

    }
}
