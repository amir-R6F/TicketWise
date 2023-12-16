<?php

$department_manager = new TKT_Front_Department_Manager();
$parent_departments = $department_manager->get_parent_department();

?>


<div class="tkt-wrap tkt-submit-ticket">

    <header class="tkt-panel-header tkt-clearfix">
        <h4>ایجاد تیکت جدید</h4>

        <a href="<?php echo TKT_Ticket_Url::all(); ?>" class="tkt-all-tickets tkt-btn tkt-btn-primary tkt-btn-small">همه تیکت ها</a>

    </header>


    <?php if (tkt_settings('new-ticket-alert')) : ?>
        <div class="tkt-help-text"><?php echo tkt_settings('new-ticket-alert-text') ?></div>
    <?php endif; ?>


    <ul class="tkt-fags">

        <?php foreach (tkt_settings('faqs') as $faq) : ?>

            <li>
                <h5>
                    <?php echo esc_html($faq['faq-title']); ?>
                </h5>
                <p><?php echo nl2br($faq['faq-body']); ?></p>
            </li>


        <?php endforeach ?>

    </ul>


    <form id="tkt-submit-ticket" class="" enctype="multipart/form-data">

        <div class="tkt-row">

            <?php if (isset($parent_departments) && count($parent_departments) > 0) : ?>

                <div class="tkt-parent-department-wrapper tkt-col-12 tkt-col-lg-6">
                    <div class="tkt-form-group">
                        <label class="tkt-form-label" for="tkt-parent-department">دپارتمان را انتخاب کنید</label>
                        <select class="tkt-parent-department tkt-custom-select" id="tkt-parent-department">
                            <option value="">یک مورد را انتخاب نمایید</option>

                            <?php foreach ($parent_departments as $item) : ?>
                                <option value="<?php echo esc_attr($item->ID) ?>"><?php echo esc_html($item->name) ?></option>
                            <?php endforeach; ?>


                        </select>
                    </div>
                </div>


                <div class="tkt-child-department-wrapper tkt-col-12 tkt-col-lg-6">
                    <div class="tkt-form-group">

                        <label class="tkt-form-label" for="tkt-child-department">نوع تیکت را انتخاب نمایید</label>
                        <select class="tkt-child-department tkt-child-department-0 tkt-custom-select" id="tkt-child-department" name="child-department">
                            <option value="">ابتدا دپارتمان را انتخاب نمایید</option>
                        </select>

                        <?php foreach ($parent_departments as $parent_department) :
                            $childs = $department_manager->get_child_department($parent_department->ID);
                            ?>

                            <select class="tkt-child-department tkt-child-department-<?php echo esc_attr($parent_department->ID) ?> tkt-custom-select" id="tkt-child-department" name="child-department">
                                <option value="">عنوان تیکت را انتخاب نمایید</option>
                                <?php foreach ($childs as $child) : ?>

                                    <option value="<?php echo esc_attr($child->ID) ?>"><?php echo esc_html($child->name) ?></option>

                                <?php endforeach; ?>

                            </select>

                            <?php foreach ($childs as $child) : ?>
                            <?php if ($child->description != '') : ?>
                                <div class="tkt-description-wrapper tkt-description-wrapper-<?php echo $child->ID ?>">
                                    <div class="tkt-form-group">
                                        <div class="tkt-description"><?php echo esc_html($child->description) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>

                        <?php endforeach; ?>

                        <div class="tkt-child-department tkt-child-department-23 tkt-alert tkt-alert-danger">
                            <p>هیچ دپارتمان فرزندی یافت نشد</p>
                        </div>


                    </div>
                </div>




            <?php else : ?>

                <div class="tkt-alert tkt-alert-danger tkt-col-12">
                    <p>
                        هیچ دپارتمانی یافت نشد. ابتدا دپارتمان ایجاد نمایید
                    </p>
                </div>

            <?php endif; ?>


        </div>


        <div class="tkt-row">
            <div class="tkt-title-wrapper tkt-col-12">
                <div class="tkt-form-group">
                    <label class="tkt-form-label" for="tkt-title">عنوان تیکت</label>
                    <input type="text" class="tkt-form-control" id="tkt-title" name="title">
                </div>
            </div>

            <div class="tkt-priority-wrapper tkt-col-12">
                <div class="tkt-form-group">
                    <label class="tkt-form-label" for="tkt-priority">اهمیت تیکت را انتخاب نمایید</label>
                    <select class="tkt-custom-select" id="tkt-priority" name="priority">
                        <option value="low">کم</option>
                        <option value="medium" selected>متوسط</option>
                        <option value="high">زیاد</option>
                    </select>
                </div>
            </div>


            <!-- <div class="tkt-name-wrapper tkt-col-12">
                <div class="tkt-form-group">
                    <label class="tkt-form-label" for="tkt-user-name">نام</label>
                    <input type="text" class="tkt-form-control" id="tkt-user-name" name="user-name">
                </div>
            </div>


            <div class="tkt-email-wrapper tkt-col-12">
                <div class="tkt-form-group">
                    <label class="tkt-form-label" for="tkt-user-email">ایمیل</label>
                    <input type="email" class="tkt-form-control" id="tkt-user-email" name="user-email">
                </div>
            </div>


            <div class="tkt-phone-wrapper tkt-col-12">
                <div class="tkt-form-group">
                    <label class="tkt-form-label" for="tkt-user-phone">شماره موبایل</label>
                    <input type="text" class="tkt-form-control" id="tkt-user-phone" name="user-phone">
                </div>
            </div> -->

        </div>
        <div class="tkt-row">


            <div class="tkt-content-wrapper tkt-col-12">
                <div class="tkt-form-group">
                    <label class="tkt-form-label" for="tkt-content">محتوا تیکت</label>

                    <textarea class="tkt-form-control" id="tkt-content" name="_body" rows="10"></textarea>

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
                <button type="submit" class="tkt-submit tkt-btn tkt-btn-success">
                    <img src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>send.png" class="tkt-send" width="23" height="23" alt="send">
                    ارسال تیکت
                    <img src="<?php echo TKT_FRONT_ASSETS . 'images/'; ?>oval.svg" class="tkt-loader" width="28" height="28" alt="loader">
                </button>
            </div>
        </div>

    </form>
</div>