<?php

function tkt_settings($key = '')
{

    $options = get_option('tkt_settings');

    return isset($options[$key]) ? $options[$key] : null;
}

function tkt_format_date($timestamp)
{
    return jdate($timestamp)->format("Y-m-d H:i");
}

function tkt_get_status()
{

    $open_color = tkt_settings('open-color');
    $closed_color = tkt_settings('closed-color');
    $answered_color = tkt_settings('answered-color');
    $finished_color = tkt_settings('finished-color');

    $statuses = tkt_settings('statuses');

    $status_array = [
        [
            'slug' => 'open',
            'name' => 'باز',
            'color' => $open_color
        ],
        [
            'slug' => 'answerd',
            'name' => 'پاسخ داده شده',
            'color' => $answered_color
        ],
        [
            'slug' => 'closed',
            'name' => 'بسته شده',
            'color' => $closed_color
        ],
        [
            'slug' => 'finished',
            'name' => 'پایان یافته',
            'color' => $finished_color
        ]
    ];

    if(is_array($statuses)){
        foreach($statuses as $status){
            $status_array[] = [
                'slug' => $status['status-slug'],
                'name' =>  $status['status-title'],
                'color' =>  $status['status-color']
            ];
        }
    }

    if (is_admin()) {
        $trash_color = tkt_settings('trash-color');

        $status_array[] = [
            'slug' => 'trash',
            'name' => 'زباله دان',
            'color' =>  $trash_color
        ];
    }

    return $status_array;
}

function tkt_get_status_color($status)
{

    $ststuses = tkt_get_status();
    foreach ($ststuses as $item) {
        if ($status == $item['slug']) {
            return $item['color'];
        }
    }
}

function tkt_get_status_name($status)
{

    $ststuses = tkt_get_status();
    foreach ($ststuses as $item) {
        if ($status == $item['slug']) {
            return $item['name'];
        }
    }
}

function tkt_get_file_name($url)
{
    $path = parse_url($url, PHP_URL_PATH);
    return basename($path);
}

function get_status_html($status)
{
    $status_name = tkt_get_status_name($status);
    $status_color = tkt_get_status_color($status);

    $style = is_admin() && !wp_doing_ajax() ? 'style="background:' . $status_color . '"' : '';

    return '<div class="tkt-status"' . $style . '>
    <span class="tkt-status-name"> ' . $status_name . ' </span>
    <span class="tkt-status-color" style="background:' . $status_color . ';">
    </span>
    </div>';
}

function get_department_html($department_id)
{
    $department_manager = new TKT_Front_Department_Manager();
    $department = $department_manager->get_department($department_id);
    return '<span>' . esc_html($department->name) . '</span>';
}

function get_priority_name($priority){
    switch ($priority){
        case 'low':
            return "کم";
            break;
        case 'medium':
            return "متوسط";
            break;
        case 'high':
            return "زیاد";
            break;
    }
}
