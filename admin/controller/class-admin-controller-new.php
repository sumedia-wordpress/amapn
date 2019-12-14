<?php

class Sumedia_Amapn_Admin_Controller_New extends Sumedia_Base_Controller
{
    /**
     * @var $this
     */
    protected static $instance;

    public function prepare()
    {
        $overview = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Overview');
        $overview->set_content_view(Sumedia_Base_Registry_View::get('Sumedia_Amapn_Admin_View_New'));

        $heading = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Heading');
        $heading->set_title(__('Amazon Partnernet', SUMEDIA_AMAPN_PLUGIN_NAME));
        $heading->set_side_title(__('Create new', SUMEDIA_AMAPN_PLUGIN_NAME));
        $heading->set_version(SUMEDIA_AMAPN_VERSION);
    }

    public function execute()
    {
        $form = Sumedia_Base_Registry_Form::get('Sumedia_Amapn_Admin_Form_New');
        if (!empty($_POST) && $form->is_valid($_POST)) {
            $link = $form->get_data('link');
            $refresh_after_hours = $form->get_data('refresh_after_hours');
            $parser = new Sumedia_Amapn_Linkparser();
            $uniqueid = $parser->parse($link);

            $fonts = Sumedia_Amapn_Repository_Links::get_instance();
            $fonts->create([
                'uniqueid' => $uniqueid,
                'link' => $link,
                'refresh_after_hours' => $refresh_after_hours,
                'date_created' => date('Y-m-d H:i:s')
            ]);

            $messenger = Sumedia_Base_Messenger::get_instance();
            $messenger->add_message($messenger::TYPE_SUCCESS, __('The ad has been fetched from amazon.', SUMEDIA_AMAPN_PLUGIN_NAME));

            wp_redirect('admin.php?page=sumedia&plugin=' . SUMEDIA_AMAPN_PLUGIN_NAME . '&action=adlist');
        }
    }
}
