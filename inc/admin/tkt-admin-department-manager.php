<?php

defined("ABSPATH" || exit());

class TKT_Admin_Department_Manager{
    private  $wpdb;
    private  $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkt_departments';
    }


    public function page()
    {
        $answerAble = new TKT_Answerable_Manager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['add_department_nonce'])){
                if(!isset($_POST['add_department_nonce']) && wp_verify_nonce($_POST['add_department_nonce'], 'add_department')){
                    exit('sorry, your nonce did not verify');
                }

                $insert = $this->insert($_POST);


                if ($insert){
                    if ($_POST['answerable']){
                        foreach ($_POST['answerable'] as $user){
                            $answerAble->insert(['department_id' => $insert, 'user_id' => $user]);
                        }
                    }

                    TKT_Flash_Message::add_message('با موفقیت ایجاد شد');
                }

            }

            if (isset($_POST['update_department_nonce'])){
                if(!isset($_POST['update_department_nonce']) && wp_verify_nonce($_POST['update_department_nonce'], 'update_department')){
                    exit('sorry, your nonce did not verify');
                }

                $update = $this->update($_POST);

                if ($update){
                    TKT_Flash_Message::add_message('بروزرسانی موفقیت امیز بود.');
                }

                $answerAble->delete($_POST['department_id']);
                if ($_POST['answerable']){
                    foreach ($_POST['answerable'] as $user){
                        $answerAble->insert(['department_id' => $_POST['department_id'], 'user_id' => $user]);
                    }
                }


            }

            $departments = $this->get_departments();
            include TKT_VIEW_PATH . 'admin/department/main.php';

        }else{

            if(isset($_GET['action']) && $_GET['action'] == 'delete'){
                if(isset($_GET['delete_department_nonce']) && wp_verify_nonce($_GET['delete_department_nonce'], 'delete_department')){

                    $this->delete($_GET['id']);

                    $answerAble->delete($_GET['id']);

                    TKT_Flash_Message::add_message('دپارتمان با موفقیت حذف شد.');

                    $departments = $this->get_departments();
                    include TKT_VIEW_PATH . 'admin/department/main.php';

                }
            }elseif(isset($_GET['action']) && $_GET['action'] == 'edit'){

                $departments = $this->get_departments();
                $department = $this->get_department($_GET['id']);
                $answerable = $answerAble->get_by_department($department->ID);

                include  TKT_VIEW_PATH . 'admin/department/edit.php';

            } else{
                $departments = $this->get_departments();
                include TKT_VIEW_PATH . 'admin/department/main.php';
            }


        }


    }

    private function get_departments()
    {
        return $this->wpdb->get_results("SELECT * FROM " . $this->table . " ORDER BY postion");
    }

    private function get_department($id)
    {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE ID = %d", $id));
    }

    public function delete($id)
    {
        $this->wpdb->delete($this->table, ['ID' => $id], ['%d']);
    }

    private function insert($data)
    {
        $data = [
            'name' => sanitize_text_field($data['name']),
            'parent' => $data['parent'] ? intval($data['parent']) : 0,
            'postion' => $data['position'] ? intval($data['position']) : 1,
            'description' => $data['description'] ? sanitize_text_field($data['description']) : null,
        ];
        $data_format = ['%s', '%d', '%d', '%s'];

        $insert = $this->wpdb->insert($this->table, $data, $data_format);

        return $insert ? $this->wpdb->insert_id : null;
    }

    public function update($data)
    {
        $upData = [
            'name' => sanitize_text_field($data['name']),
            'parent' => $data['parent'] ? intval($data['parent']) : 0,
            'postion' => $data['position'] ? intval($data['position']) : 1,
            'description' => $data['description'] ? sanitize_text_field($data['description']) : null,
        ];

        $where = ['ID' => $data['department_id']];

        $data_format = ['%s', '%d', '%d', '%s'];
        $where_format = ['%d'];

        return $this->wpdb->update($this->table, $upData, $where, $data_format, $where_format);
    }

    public function get_parent_department()
    {
        return $this->wpdb->get_results("SELECT * FROM ". $this->table . " WHERE parent = 0 ORDER BY postion");
    }

    public function get_child_department($parent_id)
    {
        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM ". $this->table . " WHERE parent = %d ORDER BY postion", $parent_id));
    }
}