<?php

defined("ABSPATH" || exit());

class TKT_Tickets_List extends WP_List_Table
{

    private $wpdb;
    private $table;
    private $statues;

    public function __construct()
    {
        parent::__construct([
            'singular' => 'ticket',
            'plural' => 'tickets',
        ]);

        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkt_tickets';
        $this->statues = tkt_get_status();
    }


    public function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'title' => 'عنوان',
            'department_id' => 'دپارتمان',
            'creator_id' => 'ایجاد کننده',
            'status' => 'وضعیت',
            'priority' => 'اهمیت',
            'create_date' => 'تاریخ ایجاد',
            'reply_date' => 'تاریخ آخرین پاسخ',
        ];

        return $columns;
    }

    public function get_tickets($params = NULL)
    {
        if ($params == NULL) {
            $params = $_GET;
        }

        $args = [];
        $sql = " WHERE 1=1";

        if (isset($params['department-id']) && $params['department-id'] !== '') {
            $sql .= " AND (department-id = %d)";
            $args[] = $params['department-id'];
        }
        if (isset($params['priority']) && $params['priority'] !== '') {
            $sql .= " AND (priority = %s)";
            $args[] = $params['priority'];
        }
        if (isset($params['creator-id']) && $params['creator-id'] !== '') {
            $sql .= " AND (creator-id = %d)";
            $args[] = $params['creator-id'];
        }
        if (isset($params['search']) && $params['search'] !== '') {
            $sql .= " AND (title LIKE '%" . $params['search'] . "%' )";
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $sql .= " AND (status = %s)";
            $args[] = $params['status'];
        }

        if (isset($params['orderby'])) {
            switch ($params['orderby']) {
                case "create_date":
                    $sql .= " ORDER BY create_date " . $params['order'];
                    break;
                case "reply_date":
                    $sql .= " ORDER BY reply_date " . $params['order'];
                    break;
                default:
                    $sql .= " ORDER BY reply_date DESC";
                    break;
            }
        }


        return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM " . $this->table . $sql, $args), ARRAY_A);
    }

    public function record_count($params = NULL)
    {
        return count($this->get_tickets($params));
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case "ID":
                return $item[$column_name];
                break;
            case "title":
                return $item[$column_name];
                break;
            case "department_id":
                return "<a href='admin.php?page=tkt-tickets&department-id=" . $item[$column_name] . "'>" . get_department_html($item[$column_name]) . "</a>";
                break;
            case "creator_id":
                return $item[$column_name];
                break;
            case "status":
                return get_status_html($item[$column_name]);
                break;
            case "priority":
                return '<a href="admin.php?page=tkt-tickets&priority=' . $item[$column_name] . '" class="tkt-priority tkt-priority=-' . $item[$column_name] . '">' . get_priority_name($item[$column_name]) . '</a>';
                break;
            case "create_date":
                return $item[$column_name];
                break;
            case "reply_date":
                return $item[$column_name];
                break;

        }
    }

    public function column_cb($item)
    {
        return sprintf("<input type='checkbox' name='id[]' value='%s'>", $item['ID']);
    }

    public function column_title($item)
    {
        $title = '<strong>' . $item['title'] . '</strong>';
        $actions = [
            'id' => sprintf("<span>" . 'ایدی' . ': %d </span>', absint($item['ID'])),
            'edit' => sprintf("<a href='?page=tkt-edit-ticket&id=%s'>" . 'ویرایش' . '</a>', absint($item['ID']))
        ];

        if (isset($_GET['status']) && $_GET['status'] == 'trash') {
            $actions['trash'] = sprintf("<a href='?page=tkt-tickets&action=delete&id=%s&_wpnonce=%s'>" . 'پاک برای همیشه' . "</a>", absint($item['ID']), wp_create_nonce('tkt_delete_ticket'));
        } else {
            $actions['trash'] = sprintf("<a href='?page=tkt-tickets&action=trash&id=%s&_wpnonce=%s'>" . 'زباله دان' . "</a>", absint($item['ID']), wp_create_nonce('tkt_trash_ticket'));
        }

        return $title . $this->row_actions($actions);
    }

    public function column_creator_id($item)
    {
        $user = get_userdata($item['creator_id']);
        $creator = '<a href="admin.php?page=tkt-tickets&creator-id=' . $item['creator_id'] . '">' . $user->display_name . '</a>';
        $actions = ['edit' => '<a href="' . get_edit_user_link($item['creator_id']) . '" target="_blank">' . 'پروفایل' . '</a>'];
        return $creator . $this->row_actions($actions);
    }

    public function get_sortable_columns()
    {
        return [
            'create_date' => ['create_date', true],
            'reply_date' => ['reply_date', true],
        ];
    }

    public function prepare_items()
    {
        $this->trash_action();

        $this->delete_action();

        $this->bulk_action();

        $this->items = $this->get_tickets();

        $per_page = $this->get_items_per_page('tickets_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items = $this->record_count();

        $this->items = array_slice($this->items, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_arg([
            'total_items' => $total_items,
            'per_page' => $per_page
        ]);
    }

    public function bulk_action()
    {
        $action = $this->current_action();
        $action = str_replace('bulk-', '', $action);
        $ids = isset($_POST['id']) ? $_POST['id'] : [];

        if (count($ids)) {
            foreach ($ids as $id) {
                if ($action == 'delete') {
                    $this->delete_ticket($id);
                    (new TKT_Reply_Manager($id))->delete_replies();

                } else {
                    $this->update_ticket_status($id, $action);
                }
            }
            TKT_Flash_Message::add_message('عملیات با موفقیت انجام شد.');
        }
    }

    public function trash_action()
    {

        if (isset($_GET['action']) && $_GET['action'] == 'trash' && isset($_GET['id']) && isset($_GET['_wpnonce'])) {

            if (!wp_verify_nonce($_GET['_wpnonce'], 'tkt_trash_ticket')) {
                wp_die('nonce fail!');
            }

            $this->update_ticket_status($_GET['id'], 'trash');
            TKT_Flash_Message::add_message('تیکت با موفقیت حذف شد');

        }
    }

    public function get_bulk_actions()
    {
        $actions = [];

        foreach ($this->statues as $status) {
            $actions['bulk-' . $status['slug']] = $status['name'];
        }

        if (isset($_GET['status']) && $_GET['status'] == 'trash') {
            unset($actions['bulk-trash']);
            $actions['bulk-delete'] = 'حذف';
        }
        return $actions;
    }

    public function delete_action()
    {
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && isset($_GET['_wpnonce'])) {

            if (!wp_verify_nonce($_GET['_wpnonce'], 'tkt_delete_ticket')) {
                wp_die('nonce fail!');
            }

            $this->delete_ticket($_GET['id']);
            TKT_Flash_Message::add_message('تیکت با موفقیت حذف شد');

        }
    }

    public function delete_ticket($id)
    {
        $this->wpdb->delete($this->table, ['id' => $id], ['%d']);
    }

    public function update_ticket_status($id, $status)
    {
        $this->wpdb->update($this->table, ['status' => $status], ['id' => $id], ['%s'], ['%d']);
    }
}
