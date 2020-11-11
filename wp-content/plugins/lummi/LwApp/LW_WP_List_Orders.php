<?php

namespace LW;

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LW_WP_List_Orders extends \WP_List_Table
{
    function __construct(){
        parent::__construct();
    }


    /**
     * Set no default column
     */
    public function column_default($item, $column_name){
        switch($column_name){
            case 'display_name' :
            case 'order_date':
            case 'user_phone':
            case 'user_email':
            case 'order_id':
            case 'order_status':
            case 'order_items':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }

    function column_user_email($item){

        //Return the title contents
        return sprintf('<span style="color:silver"><a href="%2$s">%1$s</a></span>',
            /*$1%s*/ $item['user_email'],
            /*$2%s*/ esc_attr('mailto:'.$item['user_email'])
        );
    }

//    function column_ID($item){
//        return sprintf('<input class="rm_users" type="checkbox" value="%1$s" name="rm_user[]" style="color:silver">',$item['ID']);
//    }

    /**
     * This method dictates the table's columns and titles.
     * Return an array where the key is the column slug (and class) and the value
     * is the column's title text.
     *
     * @return array
     */
    public function get_columns(){

    	if( ! current_user_can('administrator')){
		    $columns = array(
			    'display_name' => 'User Name',
			    'order_date' => 'Order Date',
			    'user_phone' => 'Phone',
			    'user_email' => 'Email',
			    'order_id' => 'Order ID',
			    'order_status' => 'Status',
			    'order_items' => 'Items',
		    );
	    }else{
		    $columns = array(
			    'display_name' => 'User Name',
			    'order_date' => 'Order Date',
			    'user_phone' => 'Phone',
			    'order_id' => 'Order ID',
			    'order_status' => 'Status',
			    'order_items' => 'Items',
		    );
	    }


        return $columns;
    }

    /**
     * Registered columns to support sorting
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'display_name'  => array('display_name',false),
            'order_date'    => array('order_date',false),
            'user_phone'  => array('user_phone',false),
            'user_email'  => array('user_email',false),
            'order_id'  => array('order_id',false),
            'order_status'  => array('order_status',false),
            'order_items'  => array('order_items',false),
        );
        return $sortable_columns;
    }

    protected function get_table_classes() {
		return array( 'widefat', 'striped', $this->_args['plural'] );
	}

    /**
     * This checks for sorting input and sorts the data in the array accordingly.
     *
     * @param $a
     * @param $b
     * @return mixed
     */
    private function usort_reorder($a,$b){
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title';
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
        $result = strcmp($a[$orderby], $b[$orderby]);
        return ($order==='asc') ? $result : -$result;
    }

    public function prepare_items($ord) {

        $per_page = 50;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $data = $ord;

        usort($data, array($this,'usort_reorder'));

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }
}