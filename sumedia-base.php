<?php

if (!defined('SUMEDIA_BASE_VERSION')) {
    define('SUMEDIA_PLUGIN_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    define('SUMEDIA_PLUGIN_URL', plugin_dir_url(__DIR__));

    define('SUMEDIA_BASE_INLCUDING_PLUGIN', basename(__DIR__));

    define('SUMEDIA_BASE_PATH', dirname(__FILE__) . str_replace('/', DIRECTORY_SEPARATOR, '/vendor/sumedia-wordpress/base'));
    define('SUMEDIA_BASE_URL', SUMEDIA_PLUGIN_URL . SUMEDIA_BASE_INLCUDING_PLUGIN . '/vendor/sumedia-wordpress/base');

    require_once(__DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/vendor/sumedia-wordpress/base/sumedia-base.php'));
}