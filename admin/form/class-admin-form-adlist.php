<?php

class Sumedia_Amapn_Admin_Form_Adlist extends Sumedia_Base_Form
{
    /**
     * @var string
     */
    public $table_name = 'sumedia_amapn_links';

    /**
     * @var string
     */
    protected $link;

    /**
     * @var int
     */
    protected $refresh_after_hours;

    public function is_valid_data($request_data)
    {
        $valid = true;

        $messenger = Sumedia_Base_Messenger::get_instance();

        if (!isset($request_data['link'])) {
            $messenger->add_message($messenger::TYPE_ERROR, __('There has been no link transmitted.', SUMEDIA_AMAPN_PLUGIN_NAME));
            $valid = false;
        }

        if (!isset($request_data['refresh_after_hours'])) {
            $messenger->add_message($messenger::TYPE_ERROR, __('There has been no refresh_after_hours transmitted.', SUMEDIA_AMAPN_PLUGIN_NAME));
            $valid = false;
        }

        if ($valid && wp_http_validate_url($request_data['link'])) {
            $messenger->add_message($messenger::TYPE_ERROR, __('The Link seems to be no valid URL.', SUMEDIA_AMAPN_PLUGIN_NAME));
            $valid = false;
        }

        if ($valid && !is_numeric($request_data['refresh_after_hours'])) {
            $messenger->add_message($messenger::TYPE_ERROR, __('The given Refresh after Hours is not of the right type.', SUMEDIA_AMAPN_PLUGIN_NAME));
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param array $request_data
     */
    public function is_valid($request_data)
    {
        if (!$this->is_valid_data($request_data)) {
            return;
        }

        $this->link = $request_data['link'];
        $this->refresh_after_hours = $request_data['refresh_after_hours'];
    }
}