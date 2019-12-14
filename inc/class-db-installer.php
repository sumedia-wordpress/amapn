<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class Sumedia_Amapn_Db_Installer
{
    /**
     * @var string
     */
    protected $current_version;

    /**
     * @var string
     */
    protected $option_name;

    /**
     * @var string
     */
    protected $table_name;

    /**
     * Sumedia_Amapn_Installer constructor.
     */
    public function __construct()
    {
        $this->current_version = SUMEDIA_AMAPN_VERSION;
        $this->option_name = str_replace('-', '_', SUMEDIA_GFONT_PLUGIN_NAME) . '_version';
        $this->table_name = str_replace('-', '_', SUMEDIA_GFONT_PLUGIN_NAME) . '_fonts';
    }

    public function install()
    {
        $installed_version = get_option($this->option_name);
        if (!$installed_version || version_compare($installed_version, $this->current_version, '<')) {
            $this->install_link_table();
            add_option($this->option_name, $this->current_version);
        }
    }

    protected function install_link_table()
    {
        global $wpdb;

        $query = "SHOW TABLES LIKE '" . $wpdb->prefix . $this->table_name . "'";
        $row = $wpdb->get_row($query, ARRAY_A);
        if ($row) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . $this->table_name;

        $sql = "CREATE TABLE `$table_name` (
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
        $wpdb->query($sql);
    }
}