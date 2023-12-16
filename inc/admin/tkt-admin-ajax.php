<?php
defined("ABSPATH" || exit());

class TKT_Admin_Ajax{

    public function __construct()
    {
        add_action('wp_ajax_tkt_search_users', [$this, 'search_users']);
    }

    public function search_users()
    {
        $term = $_POST['term'];
        if (!$term){
            wp_send_json_error();
        }

        $args = ['search' => '*' . esc_attr($term) . '*', 'search_columns' => ['user_login', 'user_email', 'user_nicname']];

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();

        $result = [];
        if (!empty($users)){
            foreach ($users as $user){
                $user_login = $user->user_login;
                $user_id = $user->ID;
                $result[] = [$user_id, $user_login];
            }
        }

        $this->make_response($result);
    }

    public function make_response($res)
    {
        if (is_array($res)){
            wp_send_json($res);
        }else{
            echo $res;
        }

        wp_die();
    }
}
