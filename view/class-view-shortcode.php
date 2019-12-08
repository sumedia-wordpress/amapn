<?php


class Sumedia_Amapn_View_Shortcode extends Sumedia_Base_View
{
    public $imageurl;

    public $title;

    public $price;

    public $link;

    public function __construct()
    {
        $this->template = Suma\ds(SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . '/templates/shortcode.phtml');
    }

    public function fetch_by_uniqueid($uniqueid)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sumedia_amapn_links';
        $query = "SELECT * FROM `" . $table_name . "` WHERE `uniqueid` = %s";
        $prepare = $wpdb->prepare($query, $uniqueid);
        $row = $wpdb->get_row($prepare, ARRAY_A);
        if ($row) {
            $this->imageurl = SUMEDIA_PLUGIN_URL . SUMEDIA_AMAPN_PLUGIN_NAME . '/cache/link/images/' . $uniqueid . '.jpg';
            $data = require SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . '/cache/link/data/' . $uniqueid . '.php';
            $this->title = $data['title'];
            $this->price = $data['price'];
            $this->link = $row['link'];
        }
    }
}