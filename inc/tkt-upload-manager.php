<?php

defined("ABSPATH" || exit());

class  TKT_Upload_Manager{
    
    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function upload()
    {
        add_filter('upload_dir', [$this, 'custom_upload_dir']);

        if (! function_exists('wp_handle_upload')){
            require_once(ABSPATH. 'wp-admin/includes/file.php');
        }

        $upload_overrides = array('test_form' => false);
        $upload_file = wp_handle_upload($this->file, $upload_overrides);

        if ($upload_file && !isset($upload_file['error'])){
            return ['success' => true, 'url' => $upload_file['url']];
        }

        return ['success' => false, 'message' => $upload_file['error']];
    }

    public function custom_upload_dir($parms)
    {
        $year = date('Y', time());
        $month = date('m', time());
        $custom_dir = '/tkt-uploads' . '/' . $year . '/' . $month;

        $parms['subdir'] = $custom_dir;
        $parms['path'] = $parms['basedir'] . $custom_dir;
        $parms['url'] = $parms['baseurl'] . $custom_dir;

        return $parms;
    }

}
