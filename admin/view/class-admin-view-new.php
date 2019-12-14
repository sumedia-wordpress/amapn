<?php

class Sumedia_Amapn_Admin_View_New extends Sumedia_Base_View
{
    public function __construct()
    {
        $this->set_template(SUMEDIA_PLUGIN_PATH . SUMEDIA_AMAPN_PLUGIN_NAME . Suma\ds('/admin/templates/new.phtml'));
    }

}