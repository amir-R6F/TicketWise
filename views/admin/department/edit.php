<div class="tkt-departments wrap nosubsub">

    <h1 class="wp-heading-inline">ویرایش دپارتمان</h1>

    <hr class="wp-header-end">
    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">

                    <?php

                    TKT_Flash_Message::show_message(); ?>

                    <form id="tkt-add-department" method="post">

                        <?php wp_nonce_field('update_department', 'update_department_nonce', false); ?>

                        <input type="hidden" name="department_id" value="<?php echo esc_attr($department->ID)  ?>">

                        <div class="form-field">
                            <label for="department-name">عنوان</label>
                            <input type="text" name="name" id="department-name" value="<?php echo esc_attr($department->name) ?>">
                        </div>
                        <div class="form-field term-parent-wrap">
                            <label for="department-parent">والد</label>
                            <select name="parent" id="department-parent">
                                <option value="0">بدون والد</option>

                                <?php if (count($departments)) : ?>
                                    <?php foreach ($departments as $item) : ?>

                                        <?php
                                        if ($item->parent || $item->ID == $department->ID) {
                                            continue;
                                        }
                                        ?>

                                        <option <?php echo $department->parent == $item->ID ? 'selected' : '' ?> value="<?php echo esc_attr($item->ID); ?>"><?php echo esc_html($item->name); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>


                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-answerable">کاربران پاسخگو</label>
                            <select id="department-answerable" name="answerable[]" multiple>

                                <?php
                                if (count($answerable)) {
                                    foreach ($answerable as $user_id) {
                                        $user_data = get_userdata($user_id);
                                        echo '<option value="' . $user_id . '" selected>' . $user_data->user_login . '</option>';
                                    }
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-position">موقعیت</label>
                            <input type="number" class="small-text" name="position" id="department-position" value="<?php echo esc_attr($department->postion) ?>">
                        </div>
                        <div class="form-field">
                            <label for="department-description">توضیح کوتاه</label>
                            <textarea name="description" id="department-description" rows="5" cols="40"><?php echo esc_textarea($department->description) ?></textarea>
                        </div>
                        <p class="submit">
                            <input type="submit" name="submit" class="button button-primary" value="ویرایش">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>