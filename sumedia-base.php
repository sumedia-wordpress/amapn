<?php

if (!defined('SUMEDIA_BASE_VERSION')) {
    define('SUMEDIA_PLUGIN_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    define('SUMEDIA_PLUGIN_URL', plugin_dir_url(__DIR__));

    require_once(__DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/vendor/sumedia-wordpress/base/sumedia-base.php'));
}