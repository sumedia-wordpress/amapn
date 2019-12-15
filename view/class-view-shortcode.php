<?php


class Sumedia_Amapn_View_Shortcode extends Sumedia_Base_View
{
    /**
     * @var string
     */
    public $imageurl;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $price;

    /**
     * @var string
     */
    public $link;

    /**
     * Sumedia_Amapn_View_Shortcode constructor.
     */
    public function __construct()
    {
        $this->template = Suma\ds(SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . '/templates/shortcode.phtml');
    }

    /**
     * @param $uniqueid
     * @return null|Sumedia_Amapn_View_Shortcode
     */
    public function fetch_by_uniqueid($uniqueid)
    {
        $links = Sumedia_Base_Registry::get('Sumedia_Amapn_Repository_Links');
        $link = $links->findOne('uniqueid', $uniqueid);
        if ($link) {
            $parser = Sumedia_Base_Registry::get('Sumedia_Amapn_Linkparser');
            if ($parser->is_refresh_time($uniqueid)) {
                $parser->parse($link['link']);
            }
            $view = new self();
            $view->imageurl = SUMEDIA_PLUGIN_URL . SUMEDIA_AMAPN_PLUGIN_NAME . '/cache/link/images/' . $uniqueid . '.jpg';
            $data = require SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . '/cache/link/data/' . $uniqueid . '.php';
            $view->title = $data['title'];
            $view->price = $data['price'];
            $view->link = $link['link'];
            return $view;
        }
    }
}