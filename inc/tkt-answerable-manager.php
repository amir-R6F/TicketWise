<?php

defined("ABSPATH" || exit());

class TKT_Answerable_Manager{
    private  $wpdb;
    private  $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkt_users';
    }

    public function insert($data)
    {
        return $this->wpdb->insert(
            $this->table,
            ['department_id' => sanitize_text_field($data['department_id']), 'user_id' => sanitize_text_field($data['user_id'])],
            ['%d', '%d']
        );
    }

    public function delete($department_id)
    {
        $this->wpdb->delete($this->table, ['department_id' => $department_id], ['%d']);
    }

    public function get_by_department($department_id)
    {

//        if (intval($department_id)){
//            return [];
//        }

        return $this->wpdb->get_col($this->wpdb->prepare("SELECT user_id FROM ". $this->table . " WHERE department_id = %d", (int) $department_id));
    }
}
