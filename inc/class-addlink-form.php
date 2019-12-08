<?php

class Sumedia_Amapn_Addlink_Form
{
    /**
     * @var string
     */
    public $table_name = 'sumedia_amapn_links';

    /**
     * @var string
     */
    public $link;

    /**
     * @var int
     */
    public $refresh_after_hours;

    /**
     * @param array $request_data
     */
    public function do_request($request_data)
    {
        if (!isset($request_data['link']) || !isset($request_data['refresh_after_hours'])) {
            return;
        }

        if (!wp_http_validate_url($request_data['link'])) {
            return;
        }

        if (!is_numeric($request_data['refresh_after_hours'])) {
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