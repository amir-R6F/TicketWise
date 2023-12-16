<?php

class TKT_WC_Dashboard
{
    public function __construct()
    {
        add_filter("woocommerce_account_menu_items", [$this, 'tickets_account_menu']);
        add_action('init', [$this, 'add_tickets_endpoint']);
        add_action('woocommerce_account_tickets_endpoint', [$this, 'tickets_endpoint_page']);
    }

    public function tickets_account_menu($items)
    {
        // 1 sulotion
        $items = array_slice($items, 0, 1, true) + array("tickets" => "تیکت پشتیبانی") + array_slice($items, 1, count($items) - 1, true);

        // 2
//        $logout = NULL;
//        if (isset($items['customer-logout'])){
//            $logout = $items['customer-logout'];
//        }
//        unset($items['customer-logout']);
//        $items['tickets'] = 'تیکت پشتیبانی';
//        if ($logout){
//            $items['customer-logout'] = $logout;
//        }

//        $items['tickets'] = 'تیکت پشتیبانی';
        return $items;
    }

    public function add_tickets_endpoint()
    {
        add_rewrite_endpoint('tickets', EP_PAGES);
        flush_rewrite_rules();
    }

    public function tickets_endpoint_page()
    {
        include_once  $this->get_view();
    }

    public function get_view()
    {
        if (isset($_GET['action'])){
            if ($_GET['action'] == 'new'){
                return TKT_VIEW_PATH . 'front/new-tickets.php';
            }

            if ($_GET['action'] == 'single' && $_GET['ticket-id']){
                return TKT_VIEW_PATH . 'front/single-ticket.php';
            }
        }else{
            return TKT_VIEW_PATH . 'front/tickets.php';
        }


    }
}
