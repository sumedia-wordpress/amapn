<?php

class Sumedia_Amapn_Admin_Table_Adlist extends WP_List_Table
{
    var $_table_name = 'sumedia_amapn_links';

    function get_columns()
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'id' => __('ID'),
            'wpcode' => __('WordPress Code', 'sumedia-amapn'),
            'link' => __('Link', 'sumedia-amapn'),
            'refresh_after_hours' => __('Refresh after hours', 'sumedia-amapn'),
            'date_created' => __('Hours since last parse', 'sumedia-amapn')
        );
    }

    function get_sortable_columns()
    {
        return array();
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'link':
            case 'refresh_after_hours':
            case 'date_created':
                return $item[$column_name];
        }
    }

    function column_cb($item)
    {
        $checkbox = '<input type="checkbox" name="ids[' . $item['id'] . ']" value="' . $item['id'] . '" />';
        return $checkbox;
    }

    function column_date_created($item)
    {
        $time = strtotime($item['date_created']);
        $seconds_pased = time() - $time;
        return round($seconds_pased / 60 / 60, 2) . ' hours';
    }

    function column_link($item)
    {
        $parts = parse_url($item['link']);
        $link = $parts['scheme'] . '://' . $parts['host'] . '/' . $parts['path'];

        return '<a target="_blank" href="' . $link . '">[' . __('Open link (none tracking)') . ' ]</a><br />' . $item['link'];
    }

    function column_wpcode($item)
    {
        $uniqueid = $item['uniqueid'];
        $content = '[sumedia_amapn_link id="' . $uniqueid . '"]';

        $parser = new Sumedia_Amapn_Linkparser();
        $data = $parser->get_template_data($item['link']);

        $content .= '<div class="suma-amapn-linklist-preview">';
        $content .= '   <img class="suma-amapn-linklist-image" src="' . $data['image'] . '" />';
        $content .=     $data['price'] . '<br />' . substr($data['title'], 0, 80);
        $content .= '</div>';

        return $content;
    }

    function get_bulk_actions()
    {
        return array(
            'delete' => __('Delete', 'sumedia-amapn')
        );
    }

    function prepare_items()
    {
        global $wpdb;

        $columns = $this->get_columns();
        $hidden = array('id');
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );

        $per_page = 20;
        $current_page = $this->get_pagenum();

        $table_name = $wpdb->prefix . $this->_table_name;
        $query = "SELECT COUNT(`id`) AS item_count FROM `" . $table_name . "`";
        $row = $wpdb->get_row($query, ARRAY_A);
        $total_items = $row['item_count'];

        $query = "SELECT * FROM `" . $table_name . "`";
        if (isset($_REQUEST['s'])) {
            $s = $_REQUEST['s'];
            $query .= " WHERE `link` LIKE \"" . $wpdb->_real_escape('%' . $s . '%') . "\"";
            $query .= " OR `link` LIKE \"" . $wpdb->_real_escape('%' . $s . '%') . "\"";
        }
        if (isset($_REQUEST['orderby'])) {
            $query .= " ORDER BY " . $wpdb->_real_escape($_REQUEST['orderby']);
        }
        if (isset($_REQUEST['order'])) {
            $query .= " " . ($_REQUEST['order'] == 'desc' ? 'DESC' : 'ASC');
        }
        $query .= " LIMIT " . $per_page . " OFFSET " . (((int) $current_page-1) * $per_page);

        $this->items = $wpdb->get_results($query, ARRAY_A);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

}