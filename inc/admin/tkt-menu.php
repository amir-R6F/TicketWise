<?php

defined("ABSPATH" || exit());

class TKT_Menu extends Base_Menu
{

    public $tickets_object = NULL;

    private $wpdb;
    private $table;
    private $reply_table;
    private $ticket_id;

    public function __construct()
    {
        $this->ticket_id = isset($_GET['id']) ? $_GET['id'] : null;
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'tkt_tickets';
        $this->reply_table = $wpdb->prefix . 'tkt_replies';

        $this->page_title = 'تیکت a پشتیبانی';
        $this->menu_title = 'تیکت پشتیبانی';
        $this->menu_slug = 'ticket-plugin';

        $this->has_sub_menu = true;
        $this->sub_items = [
            "settings" => [
                'page_title' => 'تنظیمات',
                'menu_title' => 'تنظیمات',
                'menu_slug' => 'tkt-settings',
                'callback' => 'tickets_page',
                'load' => [
                    'status' => false,
                ]

            ],
            "tickets" => [
                'page_title' => 'لیست تیکت ها',
                'menu_title' => 'لیست تیکت ها',
                'menu_slug' => 'tkt-tickets',
                'callback' => 'tickets_page',
                'load' => [
                    'status' => true,
                    'callback' => 'tickets_screen_option'
                ]
            ],
            "departments" => [
                'page_title' => 'لیست دپارتمان ها',
                'menu_title' => 'لیست دپارتمان ها',
                'menu_slug' => 'tkt-departments',
                'callback' => 'departments_page',
                'load' => [
                    'status' => false,
                ]
            ],
            "new-ticket" => [
                'page_title' => 'ارسال تیکت',
                'menu_title' => 'ارسال تیکت',
                'menu_slug' => 'tkt-new-ticket',
                'callback' => 'new_ticket_page',
                'load' => [
                    'status' => false,
                ]
            ],
            "edit-ticket" => [
                'page_title' => 'ویرایش تیکت',
                'menu_title' => 'ویرایش تیکت',
                'menu_slug' => 'tkt-edit-ticket',
                'callback' => 'edit_ticket_page',
                'load' => [
                    'status' => false,
                ]
            ],
        ];

        parent::__construct();
    }

    public function page()
    {
        echo 'page title';
    }

    public function new_ticket_page()
    {
        $is_edit = false;

        if (isset($_POST['publish'])) {
            if (!isset($_POST['ticket_nonce']) && !wp_verify_nonce($_POST['ticket_nonce'], 'ticket_security')) {
                echo "not verify";
            }

            $current_user = get_current_user_id();
            $data = $_POST;

            $ids = $this->create_ticket($current_user, $data);


            if (count($ids)) {
                foreach ($ids as $id) {
                    TKT_Flash_Message::add_message('تیکت با موفقیت ارسال شد' . ' ' . ' :شماره تیکت' . $id);
                }
            }

        }

        include TKT_VIEW_PATH . "admin/ticket/new.php";
    }

    public function create_ticket($creator_id, $data)
    {
        $ids = [];
        if ($data['user-id'] && count($data['user-id'])) {

            foreach ($data['user-id'] as $user_id) {
                $insert = $this->wpdb->insert(
                    $this->table,
                    [
                        'title' => sanitize_text_field($data['title']),
                        'body' => stripslashes_deep($data['tkt-content']),
                        'status' => $data['status'],
                        'priority' => $data['priority'],
                        'creator_id' => $creator_id,
                        'user_id' => $user_id,
                        'from_admin' => 1,
                        'department_id' => $data['department-id'],
                        'file' => isset($data['file']) ? sanitize_text_field($data['file']) : null
                    ],
                    ['%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s']
                );

                if ($insert) {
                    $ids[] = $this->wpdb->insert_id;
                }
            }
        }
        return $ids;
    }

    public function edit_ticket_page()
    {
        $is_edit = true;
        $current_user = get_current_user_id();
        $reply_manager = new TKT_Reply_Manager($this->ticket_id);

        if (isset($_POST['publish'])) {
            if (!isset($_POST['ticket_nonce']) && !wp_verify_nonce($_POST['ticket_nonce'], 'ticket_security')) {
                echo "not verify";
            }

            $data = $_POST;
            $update = $this->update_ticket($data);


            $insert_reply = 0;
            if (isset($data['tkt-reply-content']) && !empty($data['tkt-reply-content'])) {
                $reply_data = [
                    'ticket_id' => $this->ticket_id,
                    'creator_id' => $current_user,
                    'from_admin' => 1,
                    'body' => $data['tkt-reply-content'],
                    'file' => isset($data['reply-file']) ? sanitize_text_field($data['reply-file']) : null,

                ];
                $insert_reply = $this->create_reply($reply_data);
                if ($insert_reply)
                    $this->update_reply_date();
            }

            $replies = $reply_manager->get_replies();

            $update_reply = 0;
            if (count($replies)) {
                foreach ($replies as $reply) {
                    if (isset($data['tkt-reply-content-' . $reply->ID])) {
                        if (!empty($data['tkt-reply-content-' . $reply->ID])) {
                            $reply_data = [
                                'ID' => $reply->ID,
                                'body' => stripslashes_deep($data['tkt-reply-content-' . $reply->ID]),
                                'file' => sanitize_text_field($data['reply-file-' . $reply->ID])
                            ];
                            if ($this->update_reply($reply_data)) {
                                $update_reply++;
                            }
                        } else {
                            if ($reply_manager->delete_reply($reply->ID)) {
                                $update_reply--;
                            }
                        }
                    }
                }
            }

            if ($update || $insert_reply) {
                TKT_Flash_Message::add_message('تیکت با موفقیت آپدیت شد.');
            }

        }

        $ticket = $this->get_ticket($this->ticket_id);


        $replies = $reply_manager->get_replies();


        include TKT_VIEW_PATH . "admin/ticket/new.php";
    }

    public
    function create_reply($data)
    {
        $insert = $this->wpdb->insert(
            $this->reply_table,
            $data,
            ['%d', '%d', '%d', '%s', '%s']
        );
        return $insert ? $this->wpdb->insert_id : null;
    }

    public
    function update_reply_date()
    {
        return $this->wpdb->query($this->wpdb->prepare("UPDATE " . $this->table . " SET reply_date = NOW() WHERE ID = %d", $this->ticket_id));
    }

    public
    function update_ticket($data)
    {
        return $this->wpdb->update(
            $this->table,
            [
                'title' => sanitize_text_field($data['title']),
                'body' => stripslashes_deep($data['tkt-content']),
                'creator_id' => isset($data['creator-id']) ? intval($data['creator-id']) : null,
                'user_id' => isset($data['user-id']) ? intval($data['user-id']) : null,
                'department_id' => $data['department-id'],
                'status' => $data['status'],
                'priority' => $data['priority'],
                'create_date' => sanitize_text_field($data['create-date']),
                'file' => $data['file'] ? sanitize_text_field($data['file']) : null
            ],
            ['ID' => $this->ticket_id],
            ['%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s'],
            ['%d']
        );
    }

    public
    function update_reply($data)
    {
        $this->wpdb->update(
            $this->reply_table,
            [
                'body' => $data['body'],
                'file' => $data['file'],
            ],
            ["ID" => $data->ID],
            ["%s", "%s"],
            ["%d"],
        );
    }

    public
    function get_ticket()
    {
        if (!intval($this->ticket_id)){
            return null;
        }

        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . $this->table . " WHERE ID = %d", $this->ticket_id));
    }

    public
    function tickets_screen_option()
    {
        $args = [
            'label' => 'تعداد تیکت در هر صفحه',
            'default' => 20,
            'option' => 'tickets_per_page'
        ];
        add_screen_option('per_page', $args);
        $this->tickets_object = new TKT_Tickets_List();


    }

    public
    function tickets_page()
    {
        include TKT_VIEW_PATH . "admin/ticket/main.php";
    }

    public
    function departments_page()
    {
        $dep = new TKT_Admin_Department_Manager();
        $dep->page();
    }
}
