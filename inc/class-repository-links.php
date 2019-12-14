<?php

class Sumedia_Amapn_Repository_Links extends Sumedia_Base_Repository
{

    /**
     * @var $this
     */
    protected static $instance;

    /**
     * @inheritDoc
     */
    public function get_table_name()
    {
        return 'sumedia_amapn_links';
    }

    /**
     * @inheritDoc
     */
    public function is_valid_data($data)
    {
        return true;
    }
}