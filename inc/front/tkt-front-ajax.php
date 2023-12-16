<?php

defined("ABSPATH" || exit());

class TKT_Front_Ajax{
    public function __construct()
    {
        add_action('wp_ajax_tkt_submit_ticket', [$this, 'submit_ticket']);
        add_action('wp_ajax_nopriv_tkt_submit_ticket', [$this, 'submit_ticket']);

        add_action('wp_ajax_tkt_submit_reply', [$this, 'submit_reply']);
        add_action('wp_ajax_nopriv_tkt_submit_reply', [$this, 'submit_reply']);
    }

    public function submit_reply()
    {
        if(!wp_verify_nonce($_POST['nonce'], 'tkt_ajax')){
            wp_send_json_error();
        }

        $user_id = get_current_user_id();
        $ticket_id = $_POST['ticket-id'];

        $ticket_manager = new TKT_Ticket_Manager();
        $ticket = $ticket_manager->get_ticket($ticket_id);

        if (!$ticket_id || $ticket->status == 'finished'){
            $this->make_response(['__success' => false, 'results' => 'خطا رخ داده']);
        }

        $reply_data = ['body' => $_POST['body'], 'creator_id' => $user_id];

        if (isset($_POST['status']) && !empty($_POST['status'])){
            $status = $_POST['status'];
        }else{
            $status = 'open';
        }

        $ticket_manager->update_status($ticket_id, $status);

        // upload file
        $file = $_FILES['file'];
        if ($file){
            $uploader = new TKT_Upload_Manager($file);
            $upload_result = $uploader->upload();
        }

        if(is_array($upload_result)){
            if ($upload_result['success']){
                if (isset($upload_result['url'])){
                    $reply_data['file'] = $upload_result['url'];

                    // insert reply
                    $reply_manager = new TKT_Reply_Manager($ticket_id);
                    $insert = $reply_manager->insert($reply_data);

                    if (is_numeric($insert)){
                        $ticket_manager->update_reply_date($ticket_id);

                        $replies = $reply_manager->get_replies();
                        ob_start();
                        include(TKT_VIEW_PATH . 'front/replies.php');
                        $reply_html = ob_get_clean();

                        $this->make_response(['__success' => true, 'results' => 'پاسخ ثبت شد.', 'reply_html' => $reply_html, '__status' => get_status_html($status)]);
                    }

                }
            }else{
                $this->make_response(['__success' => false, 'results' => $upload_result['message']]);
            }
        }else{
            // insert reply
            $reply_manager = new TKT_Reply_Manager($ticket_id);
            $insert = $reply_manager->insert($reply_data);

            if (is_numeric($insert)){
                $ticket_manager->update_reply_date($ticket_id);

                $replies = $reply_manager->get_replies();
                ob_start();
                include(TKT_VIEW_PATH . 'front/replies.php');
                $reply_html = ob_get_clean();

                $this->make_response(['__success' => true, 'results' => 'پاسخ ثبت شد.', 'reply_html' => $reply_html, '__status' => get_status_html($status)]);
            }
        }

        $this->make_response(['__success' => false, 'results' => $insert]);


    }

    public function submit_ticket()
    {
        if(!wp_verify_nonce($_POST['nonce'], 'tkt_ajax')){
            wp_send_json_error();
        }

        $file = $_FILES['file'];
        if ($file){
            $uploader = new TKT_Upload_Manager($file);
            $upload_result = $uploader->upload();
        }

        $user_id = get_current_user_id();

        $ticket_data = [];
        $ticket_data['title'] = !empty($_POST['title']) ? $_POST['title'] : 'no title';
        $ticket_data['body'] = $_POST['body'];
        $ticket_data['creator_id'] = $user_id;
        $ticket_data['status'] = 'open';
        $ticket_data['priority'] = $_POST['priority'];
        $ticket_data['department_id'] = $_POST['child_department'];


        if(is_array($upload_result)){
            if ($upload_result['success']){
                if (isset($upload_result['url'])){
                    $ticket_data['file'] = $upload_result['url'];
                }

                $ticket_manager = new TKT_Ticket_Manager();
                $ticket = $ticket_manager->insert($ticket_data);

                if (isset($ticket['ticket_id'])){
                    $this->make_response(['__success' => true, 'results' => TKT_Ticket_Url::all()]);
                }
            }else{
                $this->make_response(['__success' => false, 'results' => $upload_result['message']]);
            }
        }else{
            $ticket_manager = new TKT_Ticket_Manager();
            $ticket = $ticket_manager->insert($ticket_data);

            if (isset($ticket['ticket_id'])){
                $this->make_response(['__success' => true, 'results' => TKT_Ticket_Url::all()]);
            }
        }

        $this->make_response(['__success' => false, 'results' => $ticket]);

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