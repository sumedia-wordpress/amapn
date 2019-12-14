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
        if (!isset($request_data['link'])) {
            $event = new Sumedia_Base_Event(function(){
                return '<div id="message" class="error fade"><p>' . esc_html(__('There has been no link transmitted.', 'sumedia-amapn')) . '</p></div>';
            });
            add_action('admin_notices',[$event, 'execute']);
            $valid = false;
        }

        if (!isset($request_data['refresh_after_hours'])) {
            $event = new Sumedia_Base_Event(function(){
                return '<div id="message" class="error fade"><p>' . esc_html(__('There has been no refresh_after_hours transmitted.', 'sumedia-amapn')) . '</p></div>';
            });
            add_action('admin_notices',[$event, 'execute']);
            $valid = false;
        }

        if ($valid && wp_http_validate_url($request_data['link'])) {
            $event = new Sumedia_Base_Event(function(){
                return '<div id="message" class="error fade"><p>' . esc_html(__('The Link seems to be no valid URL.', 'sumedia-amapn')) . '</p></div>';
            });
            add_action('admin_notices',[$event, 'execute']);
            $valid = false;
        }

        if ($valid && !is_numeric($request_data['refresh_after_hours'])) {
            $event = new Sumedia_Base_Event(function(){
                return '<div id="message" class="error fade"><p>' . esc_html(__('The given Refresh after Hours is not of the right type.', 'sumedia-amapn')) . '</p></div>';
            });
            add_action('admin_notices',[$event, 'execute']);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param array $request_data
     */
    public function do_request($request_data)
    {
        if (!$this->is_valid_data($request_data)) {
            return;
        }

        $this->link = $request_data['link'];
        $this->refresh_after_hours = $request_data['refresh_after_hours'];
    }

    public function save()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;

        if ($this->link) {
            $registry = Sumedia_Base_Registry::get_instance();
            $parser = $registry->get('sumedia_amapn_linkparser');
            $uniqueid = $parser->parse($this->link);

            $sql = "INSERT IGNORE INTO `" . $table_name . "` (`uniqueid`, `link`, `refresh_after_hours`, `date_created`) VALUES (%s, %s, %s, %s)";
            $prepare = $wpdb->prepare($sql, $uniqueid, $this->link, $this->refresh_after_hours, date('Y-m-d H:i:s'));
            $wpdb->query($prepare);
        }
    }
}