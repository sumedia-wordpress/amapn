<?php

class Sumedia_Amapn_Linkparser
{
    /**
     * @var string
     */
    public $table_name = 'sumedia_amapn_links';

    /**
     * @var string
     */
    public $cache_dir;

    /**
     * @var string
     */
    public $content_cache_dir;

    /**
     * @var string
     */
    public $data_cache_dir;

    /**
     * @var string
     */
    public $image_cache_dir;

    /**
     * @var string
     */
    public $image_cache_url;

    /**
     * Sumedia_Amapn_Linkparser constructor.
     */
    public function __construct()
    {
        $this->cache_dir = SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . '/cache';
        $this->content_cache_dir = $this->cache_dir . '/link/content';
        $this->data_cache_dir = $this->cache_dir . '/link/data';
        $this->image_cache_dir = $this->cache_dir . '/link/images';
        $this->image_cache_url = SUMEDIA_PLUGIN_URL . SUMEDIA_AMAPN_PLUGIN_NAME . '/cache/link/images';
    }

    /**
     * @param string $amazon_link
     * @return string
     */
    public function get_uniqueid($amazon_link)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $this->table_name;
        $uniqueid = uniqid();

        $query = "SELECT `uniqueid` FROM `" . $table_name . "` WHERE `link` = %s";
        $prepare = $wpdb->prepare($query, $amazon_link);
        $row = $wpdb->get_row($prepare, ARRAY_A);
        if ($row) {
            $uniqueid = $row['uniqueid'];
        }

        return $uniqueid;
    }

    public function is_refresh_time($uniqueid)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $this->table_name;

        $query = "SELECT `refresh_after_hours`, `date_created` FROM `" . $table_name . "` WHERE `uniqueid` = %s";
        $prepare = $wpdb->prepare($query, $uniqueid);
        $row = $wpdb->get_row($prepare, ARRAY_A);
        if ($row) {
            $time = strtotime($row['date_created']);
            $seconds_pased = time() - $time;
            $hours_pased = $seconds_pased / 60 / 60;
            if ($hours_pased > $row['refresh_after_hours']) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $uniqueid
     * @param string $amazon_link
     * @return string
     */
    protected function get_page_content($uniqueid, $amazon_link)
    {
        $html_file = $this->get_page_content_filepath($uniqueid);
        if (!file_exists($html_file)) {
            $this->fetch_page_content($uniqueid, $amazon_link);
        }
        return file_get_contents($html_file);
    }

    /**
     * @param string $uniqueid
     * @return string
     */
    protected function get_page_content_filepath($uniqueid)
    {
        $file = $this->content_cache_dir . '/' . $uniqueid . '.html';
        $this->mkdir(dirname($file));
        return $file;
    }

    /**
     * @param string $uniqueid
     * @param string $amazon_link
     */
    protected function fetch_page_content($uniqueid, $amazon_link)
    {
        // avoid link tracking
        $parts = parse_url($amazon_link);
        $link = $parts['scheme'] . '://' . $parts['host'] . '/' . $parts['path'];

        $curl = curl_init($link);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; "."Windows NT 5.0)');
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_REFERER, '');
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cache_dir . '/cookie.txt');
        $content = curl_exec($curl);

        $content_file = $this->get_page_content_filepath($uniqueid);
        file_put_contents($content_file, $content);
    }

    /**
     * @param string $uniqueid
     * @param string $amazon_link
     * @return array
     */
    protected function get_data($uniqueid, $amazon_link)
    {
        $data_file = $this->get_data_filepath($uniqueid);
        if (!file_exists($data_file)) {
            $this->parse($amazon_link);
        }
        return require $data_file;
    }

    /**
     * @param string $uniqueid
     * @return string
     */
    protected function get_data_filepath($uniqueid)
    {
        $file = $this->data_cache_dir . '/' . $uniqueid . '.php';
        $this->mkdir(dirname($file));
        return $file;
    }

    /**
     * @param string $uniqueid
     * @param string $amazon_link
     */
    protected function fetch_data($uniqueid, $amazon_link)
    {
        $content = $this->get_page_content($uniqueid, $amazon_link);

        $dom = new DOMDocument();
        @$dom->loadHTML($content);

        $image = $dom->getElementById('imgBlkFront');
        $src = $image->getAttribute('src');
        $image_file = $this->get_image_filepath($uniqueid);
        file_put_contents($image_file, file_get_contents($src));

        $title = $dom->getElementById('productTitle');
        $value = $title->nodeValue;

        $buy = $dom->getElementById('buyNewSection');
        $spans = $buy->getElementsByTagName('span');
        foreach ($spans as $span) {
            if (false !== strpos($span->getAttribute('class'), 'offer-price')) {
                $price = $span->nodeValue;
                break;
            }
        }

        $data_file = $this->get_data_filepath($uniqueid);
        file_put_contents($data_file, '<?php return [
            "title" => "' . $value . '",
            "price" => "' . $price . '"
        ];');
    }

    /**
     * @param string $uniqueid
     * @return string
     */
    protected function get_image_filepath($uniqueid)
    {
        $file = $this->image_cache_dir . '/' . $uniqueid . '.jpg';
        $this->mkdir(dirname($file));
        return $file;
    }

    /**
     * @param string $dir
     */
    protected function mkdir($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * @param string $amazon_link
     * @return array
     */
    public function get_template_data($amazon_link)
    {
        $uniqueid = $this->get_uniqueid($amazon_link);
        if ($this->is_refresh_time($uniqueid)) {
            $this->parse($amazon_link);
        }
        $data = $this->get_data($uniqueid, $amazon_link);
        return array_merge($data, [
            'image' => $this->image_cache_url . '/' . $uniqueid . '.jpg',
            'amazon_link' => $amazon_link
        ]);
    }

    /**
     * @param string $amazon_link
     * @return string the uniqueid
     */
    public function parse($amazon_link)
    {
        global $wpdb;

        $uniqueid = $this->get_uniqueid($amazon_link);
        $this->fetch_page_content($uniqueid, $amazon_link);
        $this->fetch_data($uniqueid, $amazon_link);

        $table_name = $wpdb->prefix . $this->table_name;
        $query = "UPDATE `" . $table_name . "` SET `date_created` = %s WHERE `uniqueid` = %s";
        $prepare = $wpdb->prepare($query, date('Y-m-d H:i:s'), $uniqueid);
        $wpdb->query($prepare);

        return $uniqueid;
    }
}
