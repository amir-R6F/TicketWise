<?php


if (class_exists('CSF')) {

    //
    // Set a unique slug-like ID
    $prefix = 'tkt_settings';

    CSF::createOptions($prefix, array(
        'menu_title' => 'تیکت پشتیبانی',
        'menu_slug' => 'tkt-settings',
        'framework_title' => 'تنظیمات تیکت پشتیبانی ',
        'menu_hidden' => true
    ));

    // Import & Export
    CSF::createSection($prefix, array(
        'title' => 'عمومی',
        'fields' => array(
            array(
                'id' => 'new-ticket-alert',
                'type' => 'switcher',
                'title' => 'پیعام صفحه ارسال تیکت',
                'label' => 'آیا فعال باشد؟',
                'default' => true
            ),
            array(
                'id' => 'new-ticket-alert-text',
                'type' => 'textarea',
                'title' => 'متن پیغام',
                'default' => 'متن پیش فرض',
                'dependency' => array('new-ticket-alert', '==', 'true')
            ),
        )
    ));

    CSF::createSection($prefix, array(
        'title' => 'استایل',
        'fields' => array(
            array(
                'id' => 'faqs',
                'type' => 'repeater',
                'title' => 'سوال جدید',
                'fields' => array(
                    array(
                        'id' => 'faq-title',
                        'type' => 'text',
                        'title' => 'سوال عنوان',
                    ),
                    array(
                        'id' => 'faq-body',
                        'type' => 'textarea',
                        'title' => 'سوال توضیح',
                    ),

                )
            ))
    ));

    CSF::createSection($prefix, array(
        'title'  => 'وضعیت ها',
        'fields' => array(

            array(
                'id'    => 'open-color',
                'type'  => 'color',
                'title' => 'رنگ وضعیت باز',
                'default' => '#d43306'
            ),


            array(
                'id'    => 'answered-color',
                'type'  => 'color',
                'title' => 'رنگ وضعیت پاسخ داده شده',
                'default' => '#13ba5e'
            ),


            array(
                'id'    => 'closed-color',
                'type'  => 'color',
                'title' => 'رنگ وضعیت بسته شده',
                'default' => '#f28507'
            ),


            array(
                'id'    => 'finished-color',
                'type'  => 'color',
                'title' => 'رنگ وضعیت بسته شده',
                'default' => '#141414'
            ),

            array(
                'id'    => 'trash-color',
                'type'  => 'color',
                'title' => 'رنگ وضعیت زباله دان',
                'default' => '#141414'
            ),


            array(
                'id'     => 'statuses',
                'type'   => 'repeater',
                'title'  => 'وضعیت جدید',
                'fields' => array(

                    array(
                        'id'    => 'status-title',
                        'type'  => 'text',
                        'title' => 'عنوان'
                    ),

                    array(
                        'id'    => 'status-slug',
                        'type'  => 'text',
                        'title' => 'نامک'
                    ),

                    array(
                        'id'    => 'status-color',
                        'type'  => 'color',
                        'title' => 'رنگ',
                    ),

                ),
            ),

        )
    ));

}

