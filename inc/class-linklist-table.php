<?php

class Sumedia_Amapn_Linklist_Table extends WP_List_Table
{
    var $_table_name = 'sumedia_amapn_links';

    function get_columns()
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'id' => __('ID'),
            'wpcode' => __('WordPress Code', 'sumedia-amapn'),
            'uniqueid' => __('Unique ID', 'sumedia-amapn'),
            'link' => __('Link', 'sumedia-amapn'),
            'refresh_after_hours' => __('Refresh<br />after hours', 'sumedia-amapn'),
            'date_created' => __('Hours since<br />last parse', 'sumedia-amapn')
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
            case 'uniqueid':
            case 'link':
            case 'refresh_after_hours':
            case 'date_created':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function column_cb($item)
    {
        $checkbox = '<input type="hidden" name="ids[' . $item['id'] . ']" value="' . $item['id'] . '" />';
        $checkbox .= '<input type="checkbox" name="delete[' . $item['id'] . ']" value="' . $item['id'] . '" />';
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

        $registry = Sumedia_Base_Registry::get_instance();
        $parser = $registry->get('sumedia_amapn_linkparser');
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
        global $wpdb, $_wp_column_headers;

        $screen = get_current_screen();

        $columns = $this->get_columns();
        $hidden = array('id', 'uniqueid');
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );

        $table_name = $wpdb->prefix . $this->_table_name;
        $query = "SELECT * FROM `" . $table_name . "`";
        $this->items = $wpdb->get_results($query, ARRAY_A);

        usort($this->items, function($a, $b){
            $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'id';
            $order = isset($_REQUEST['order']) && $_REQUEST['order'] == 'DESC' ? 'DESC' : 'ASC';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'ASC' ? $result : -$result);
        });

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->items);
        $this->items = array_slice($this->items, (($current_page-1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

}