<?php

/*
Plugin Name: Pticket
Plugin URI: http://wordpress.org/plugins/r6f
Description: amir plugin for fun.
Author: amir arbabi
Version: 1.0.0
Author URI: http://r6f.ir
*/




defined("ABSPATH" || exit());



class Core{

    private  static  $_instance = null;
    const MINI_PHP_VER = '7.2';

    public static function instance()
    {
        if (is_null(self::$_instance)){
            self::$_instance = new self();
        }
    }


    public function __construct()
    {

        if (version_compare(PHP_VERSION, self::MINI_PHP_VER, '<')){
            do_action('admin_notices', [$this, 'admin_php_notice']);
            return;
        }

        $this->constans();
        $this->init();
    }


    public function constans()
    {

        if (!function_exists('get_plugin_data')){
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        define("TKT_BASE_FILE", __FILE__);
        define("TKT_PATH", trailingslashit(plugin_dir_path(TKT_BASE_FILE)));
        define("TKT_URL", trailingslashit(plugin_dir_url(TKT_BASE_FILE)));
        define("TKT_ADMIN_ASSETS", trailingslashit(TKT_URL . 'assets/admin'));
        define("TKT_FRONT_ASSETS", trailingslashit(TKT_URL . 'assets/front'));
        define("TKT_INC_PATH", trailingslashit(TKT_PATH . 'inc'));
        define("TKT_VIEW_PATH", trailingslashit(TKT_PATH . 'views'));

        $tkt_plugin_data = get_plugin_data(TKT_BASE_FILE);
        define('TKT_VER', $tkt_plugin_data['Version']);
    }

    public function init()
    {

        require_once TKT_PATH . 'vendor/autoload.php';

        require_once TKT_INC_PATH . 'admin/codestar/codestar-framework.php';

        require_once TKT_INC_PATH . 'admin/tkt-settings.php';

        require_once TKT_INC_PATH . 'functions.php';



        register_activation_hook(TKT_BASE_FILE, [$this, 'active']);
        register_deactivation_hook(TKT_BASE_FILE, [$this, 'deactive']);

        if (is_admin()){
            new TKT_Menu();
            new TKT_Admin_Ajax();
        }else{
            new TKT_WC_Dashboard();
            new TKT_Front_Department_Manager();
        }
        new TKT_Front_Ajax();

        new TKT_Assets();
    }

    public function active()
    {

        TKT_DB::create_table();
    }

    public function deactive()
    {

    }

    public function admin_php_notice()
    {
        ?>
        <div class="notice notice-warning">
            <p>نیازمند Php نسخه بالاتر است افزونه تیکت پشتیبان</p>
        </div>
        <?php
    }
}

Core::instance();
