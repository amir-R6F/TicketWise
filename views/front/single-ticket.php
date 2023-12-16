<?php

$ticket_id = $_GET['ticket-id'];

$ticket_manager = new TKT_Ticket_Manager();
$ticket = $ticket_manager->get_ticket($ticket_id);

$user_data = get_userdata($ticket->creator_id);

$department_managre = new TKT_Front_Department_Manager();

$reply_manager = new TKT_Reply_Manager($ticket_id);
$replies = $reply_manager->get_replies();

?>


<div class="tkt-wrap tkt-view-ticket tkt-show-sibebar">
    <header class="tkt-panel-header tkt-clearfix">
        <h4>مشاهده تیکت</h4>

        <a href="<?php echo TKT_Ticket_Url::all(); ?>" class="tkt-all-tickets tkt-btn tkt-btn-primary tkt-btn-small">همه تیکت ها</a>

    </header>

    <div class="tkt-ticket-title">
        <h4>
            <span><?php echo esc_html($ticket->title) ?></span>
            <span class="tkt-ticket-id">شناسه تیکت: <?php echo $ticket->ID ?></span>
        </h4>


        <a href="<?php echo TKT_Ticket_Url::all(); ?>" class="tkt-all-tickets">
            <img src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>left-chevron.svg" width="14" height="14" alt="chevron">
        </a>

    </div>
    <div class="tkt-row">
        <div class="tkt-main-content tkt-col-12 tkt-col-lg-8">
            <div class="tkt-ticket tkt-start-ticket tkt-from-admin">
                <div class="tkt-info">
                    <span class="tkt-creator"><?php echo $user_data->display_name ?></span>
                    <span class="tkt-date" dir="ltr"><?php echo $ticket->create_date ?></span>
                </div>
                <div class="tkt-ticket-content">

                    <?php echo nl2br($ticket->body) ?>


                    <?php if ($ticket->file) : ?>

                        <div class="tkt-ath-file">

                            <a href="<?php echo $ticket->file ?>" class="tkt-clearfix" title="دانلود" target="_blank">
                                <span class="tkt-icon"><img src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>diamond.svg" width="24" height="24" alt="diamond"></span>
                                <span class="tkt-file-name"><?php echo tkt_get_file_name($ticket->file) ?></span>
                                <img class="tkt-icon-download" src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>download.svg" width="18" height="18" alt="download">
                            </a>

                        </div>

                    <?php endif; ?>

                </div>
            </div>
            <div class="tkt-replies-title"><span>پاسخ ها</span></div>

            <?php include(TKT_VIEW_PATH . 'front/replies.php'); ?>


            <form id="tkt-submit-ticket-reply" enctype="multipart/form-data">

                <input type="hidden" id="tkt-ticket-id" name="ticket-id" value="<?= $ticket->ID ?>">

                <div class="tkt-row">
                    <div class="tkt-content-wrapper tkt-col-12">
                        <div class="tkt-form-group">
                            <label class="tkt-form-label" for="tkt-content">ارسال پاسخ جدید</label>

                            <textarea class="tkt-form-control" id="tkt-content" name="body" rows="7"></textarea>

                        </div>
                    </div>


                    <div class="tkt-upload-wrapper tkt-col-12">
                        <div class="tkt-form-group">

                            <div class="tkt-upload">
                                <label for="tkt-file" class="tkt-btn tkt-btn-secondary">فایل پیوست</label>
                                <input type="file" id="tkt-file" name="file">
                                <div class="tkt-file-name"></div>
                            </div>

                        </div>
                    </div>

                    <div class="tkt-col-12">
                        <div class="tkt-form-group">

                            <label>
                                <input type="checkbox" id="tkt-status" class="tkt-checkbox" name="status" value="closed">
                                بستن تیکت
                            </label>

                        </div>
                    </div>
                    <div class="tkt-col-12">
                        <button type="submit" class="tkt-submit tkt-btn tkt-btn-success">
                            <img src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>send.png" class="tkt-send" width="23" height="23" alt="send">
                            ارسال پاسخ
                            <img src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>oval.svg" class="tkt-loader" width="28" height="28" alt="loader">
                        </button>
                    </div>
                </div>
            </form>

        </div>
        <aside class="tkt-sidabar tkt-col-12 tkt-col-lg-4">


            <div class="tkt-widget">
                <div class="tkt-ticket-department">
                    <span class="tkt-icon"></span>
                    <div class="tkt-ticket-department-holder">
                        <span>دپارتمان: </span>
                        <?php
                        $department = $department_managre->get_department($ticket->department_id);
                        ?>
                        <span class="tkt-department"><?php echo $department->name ?></span>
                    </div>
                </div>
            </div>
            <div class="tkt-widget-status tkt-widget">

                <?= get_status_html($ticket->status) ?>

                <div class="tkt-creator">
                    <span><?php echo $user_data->display_name ?></span>
                </div>

                <hr class="custom">

                <div class="tkt-ticket-priority">
                    <strong>اولویت: </strong>
                    <span class="tkt-priority">
                        <?php
                        switch($ticket->priority){
                            case 'low':
                                echo 'کم';
                                break;
                            case 'medium':
                                echo 'متوسط';
                                break;
                            case 'high':
                                echo 'زیاد';
                                break;
                        }

                        ?>
                    </span>
                </div>


                <hr class="custom">

                <div class="tkt-time">
                    <img src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>clock.svg" width="18" height="18" alt="clock">
                    <div>
                        <div class="tkt-reply-date">
                            <span>بروز شده:</span>
                        </div>
                        <div class="tkt-date" dir="ltr"><?php echo $ticket->reply_date ?></div>
                    </div>
                </div>

            </div>


        </aside>
    </div>
</div>