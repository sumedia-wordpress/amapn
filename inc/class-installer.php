<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class Sumedia_Amapn_Installer
{
    /**
     * @var string
     */
    protected $installedVersion;

    /**
     * @var string
     */
    protected $currentVersion;

    /**
     * @var string
     */
    protected $optionName = 'sumedia_amapn_version';

    /**
     * @var wpdb
     */
    protected $db;

    public function __construct()
    {
        global $wpdb;
        $this->installedVersion = get_option('sumedia_amapn_version');
        $this->currentVersion = SUMEDIA_AMAPN_VERSION;
        $this->db = $wpdb;
    }

    public function install()
    {

        if (-1 == version_compare($this->installedVersion, $this->currentVersion)) {
            if (-1 == version_compare($this->installedVersion, '0.1.0')) {
                $this->install_link_table();
            }
            add_option($this->optionName, $this->currentVersion);
        }
    }

    protected function install_link_table()
    {
        $charset_collate = $this->db->get_charset_collate();
        $table_name = $this->db->prefix . 'sumedia_amapn_links';

        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `uniqueid` VARCHAR(32) NOT NULL,
            `link` VARCHAR(512) NOT NULL,
            `refresh_after_hours` INT(11) NOT NULL,
            `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
        dbDelta($sql);

        $sql = "ALTER TABLE `$table_name` 
            ADD UNIQUE KEY `uniqueid` (`uniqueid`),
            ADD UNIQUE KEY `link` (`link`);";
        $this->db->query($sql);
    }
}