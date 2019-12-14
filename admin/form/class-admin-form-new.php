<?php

class Sumedia_Amapn_Admin_Form_New extends Sumedia_Base_Form
{
    /**
     * @param array $request_data
     * @return bool
     */
    public function is_valid_data(array $request_data)
    {
        $valid = true;
        $messenger = Sumedia_Base_Messenger::get_instance();

        if (!isset($request_data['_wpnonce']) || !wp_verify_nonce($request_data['_wpnonce'])){
            $messenger->add_message($messenger::TYPE_ERROR, __('The form could not be verified, please try again', SUMEDIA_AMAPN_PLUGIN_NAME));
            $valid = false;
        }

        if (!isset($request_data['link'])) {
            $messenger->add_message($messenger::TYPE_ERROR, sprintf(__('Missing parameter: %s.', SUMEDIA_AMAPN_PLUGIN_NAME), 'link'));
            $valid = false;
        }

        if (!$this->is_valid_url($request_data['link'])) {
            $messenger->add_message($messenger::TYPE_ERROR, __('The given Amazon URL seems not to be valid.', SUMEDIA_AMAPN_PLUGIN_NAME));
            $valid = false;
        }

        if (!isset($request_data['refresh_after_hours'])) {
            $messenger->add_message($messenger::TYPE_ERROR, sprintf(__('Missing parameter: %s.', SUMEDIA_AMAPN_PLUGIN_NAME), 'refresh_after_hours'));
            $valid = false;
        }

        if (!is_numeric($request_data['refresh_after_hours'])){
            $messenger->add_message($messenger::TYPE_ERROR, __('Invalid Refresh Hour', SUMEDIA_AMAPN_PLUGIN_NAME));
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function is_valid_url($url)
    {
        $parsed = parse_url($url);
        if (!isset($parsed['host']) || substr($parsed['host'], 0, strrpos($parsed['host'], '.')) != 'www.amazon') {
            return false;
        }
        if (!isset($parsed['query']) || empty($parsed['query'])) {
            return false;
        }
        return true;
    }

    /**
     * @param array $request_data
     * @return bool
     */
    public function is_valid(array $request_data)
    {
        if ($this->is_valid_data($request_data)) {
            $this->set_data([
                'link' => $request_data['link'],
                'refresh_after_hours' => $request_data['refresh_after_hours']
            ]);
            return true;
        }
        return false;
    }
}
