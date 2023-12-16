<?php

defined("ABSPATH" || exit());

class TKT_Front_Department_Manager{
    private  $wpdb;
    private  $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkt_departments';
    }

    public function get_parent_department()
    {
        return $this->wpdb->get_results("SELECT * FROM ". $this->table . " WHERE parent = 0 ORDER BY postion");
    }

    public function get_child_department($parent_id)
    {
        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM ". $this->table . " WHERE parent = %d ORDER BY postion", $parent_id));
    }

    public function get_department($id)
    {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE ID = %d", $id));
    }




}