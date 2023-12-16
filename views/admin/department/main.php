<div class="tkt-departments wrap nosubsub">

    <h1 class="wp-heading-inline">دپارتمان ها</h1>

    <hr class="wp-header-end">
    <div id="ajax-response"></div>
    <div id="col-container" class="wp-clearfix">
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2>دپارتمان جدید</h2>

                    <?php TKT_Flash_Message::show_message(); ?>

                    <form id="tkt-add-department" method="post">

                        <?php wp_nonce_field('add_department' , 'add_department_nonce' , false); ?>

                        <div class="form-field">
                            <label for="department-name">عنوان</label>
                            <input type="text" name="name" id="department-name">
                        </div>
                        <div class="form-field term-parent-wrap">
                            <label for="department-parent">والد</label>
                            <select name="parent" id="department-parent">
                                <option value="0">بدون والد</option>

                                <?php if(count($departments)): ?>
                                    <?php foreach($departments as $department): ?>

                                        <?php
                                        if($department->parent){
                                            continue;
                                        }
                                        ?>

                                        <option value="<?php echo esc_attr($department->ID); ?>"><?php echo esc_html($department->name); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>


                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-answerable">کاربران پاسخگو</label>
                            <select id="department-answerable" name="answerable[]" multiple>

                            </select>
                        </div>
                        <div class="form-field">
                            <label for="department-position">موقعیت</label>
                            <input type="number" class="small-text" name="position" id="department-position">
                        </div>
                        <div class="form-field">
                            <label for="department-description">توضیح کوتاه</label>
                            <textarea name="description" id="department-description" rows="5" cols="40"></textarea>
                        </div>
                        <p class="submit">
                            <input type="submit" name="submit" class="button button-primary" value="افزودن">
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div id="col-right">
            <div class="col-wrap">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th scope="col" class="manage-column">عنوان</th>
                        <th scope="col" class="manage-column">والد</th>
                        <th scope="col" class="manage-column">کاربران پاسخگو</th>
                        <th scope="col" class="manage-column">موقعیت</th>
                    </tr>
                    </thead>
                    <tbody id="the-list">

                    <?php if (count($departments)) : ?>
                        <?php foreach ($departments as $department) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($department->name) ?></strong>
                                    <br>
                                    <div class="row-actions">
                                        <span class="edit"><a href="<?php echo esc_url(admin_url('admin.php?page=tkt-departments&action=edit&id=' . $department->ID)) ?>">ویرایش</a> | </span>
                                        <span class="delete"><a href="<?php echo wp_nonce_url(admin_url('admin.php?page=tkt-departments&action=delete&id=' . $department->ID) , 'delete_department' , 'delete_department_nonce') ?>">حذف</a></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if($department->parent) {
                                        $parent = $this->get_department($department->parent);
                                        echo $parent ? $parent->name : '__';
                                    }else{
                                        echo '__';
                                    } ?>

                                </td>
                                <td></td>
                                <td><?php echo esc_html($department->postion); ?></td>
                            </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column">عنوان</th>
                        <th scope="col" class="manage-column">والد</th>
                        <th scope="col" class="manage-column">کاربران پاسخگو</th>
                        <th scope="col" class="manage-column">موقعیت</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>