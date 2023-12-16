<?php
defined("ABSPATH" || exit());

class TKT_Ticket_Manager
{

    private $wpdb;
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkt_tickets';
    }

    public function insert($data)
    {
        $errors = [];
        if (!intval($data['department_id'])) {
            $errors[] = 'chose ticket type';
        }

        if (empty($data['body'])) {
            $errors[] = 'fill the content';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $this->wpdb->insert($this->table,
            [
                'title' => sanitize_text_field($data['title']),
                'body' => stripslashes_deep($data['body']),
                'creator_id' => $data['creator_id'] ? $data['creator_id'] : null,
                'user_id' => $data['user_id'] ? $data['user_id'] : null,
                'department_id' => $data['department_id'],
                'status' => $data['status'],
                'priority' => $data['priority'] ? $data['priority'] : 'medium',
                'create_date' => date("Y-m-d H:i:s"),
                'reply_date' => date("Y-m-d H:i:s"),
                'file' => $data['file'] ? $data['file'] : null,
            ],
            ['%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s',]
        );
        $insert_id = $this->wpdb->insert_id;
        return ['ticket_id' => $insert_id];
    }

    public function get_tickets($user_id, $type = null, $status = null, $orderby = null, $page_num = null)
    {
        if (!intval($user_id)) {
            return [];
        }

        // type filter
        switch ($type) {
            case 'send':
                $type_where = "creator_id = %d";
                $args[] = $user_id;
                break;


            case 'received':
                $type_where = "user_id = %d AND from_admin = 1";
                $args[] = $user_id;
                break;

            default:
                $type_where = "user_id = %d OR creator_id = %d";
                $args[] = $user_id;
                $args[] = $user_id;
                break;
        }

        // status filter
        switch ($status) {
            case 'all':
                $status_where = "";
                break;

            case NULL:
                $status_where = "";
                break;

            default:
                $status_where = "AND status = %s";
                $args[] = $status;
                break;
        }

        // orderby filter
        switch ($orderby) {
            case 'create-date':
                $orderby_sql = "ORDER BY create_date DESC";
                break;

            case 'reply-date':
                $orderby_sql = "ORDER BY reply_date DESC";
                break;

            default:
                $orderby_sql = "ORDER BY reply_date DESC";
                break;
        }

        // pagination
        $page_sql = '';
        if($page_num){
            $per_page = 1;
            $page_sql = "LIMIT %d";
            $args[] = $per_page;

            if ($page_num != 1){
                $offset = ($page_num - 1) * $per_page;
                $page_sql .= " OFFSET %d";
                $args[] = $offset;
            }
        }

        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE " . $type_where . " " . $status_where . " " . $orderby_sql . " " . $page_sql, $args));
    }

    public function tickets_count($user_id, $type, $status)
    {
        return count($this->get_tickets($user_id, $type, $status));
    }

    public function get_ticket($ticket_id)
    {
        if (!intval($ticket_id)) {
            return null;
        }

        return $this->wpdb->get_row($this->wpdb->prepare('SELECT * FROM ' . $this->table . ' WHERE ID = %d', $ticket_id));
    }

    public function update_status($ticket_id, $status)
    {
        return $this->wpdb->update($this->table,
            ['status' => $status],
            ['ID' => $ticket_id],
            ['%s'],
            ['%d'],
        );
    }

    public function update_reply_date($ticket_id)
    {
        $date = date("Y-m-d H:i:s");
        return $this->wpdb->query($this->wpdb->prepare("UPDATE ". $this->table . " SET reply_date = '". $date . "' WHERE ID = %d", $ticket_id));
    }
}
