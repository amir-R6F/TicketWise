<?php
defined("ABSPATH" || exit());

class TKT_Flash_Message
{

    const ERROR = 1;
    const SUCCESS = 2;
    const WARNING = 3;
    const INFO = 4;

    public static function add_message($message, $type = self::SUCCESS)
    {

        if (isset($_SESSION['tkt']['messages'])) {
            $_SESSION['tkt']['messages'] = [];
        }
        $_SESSION['tkt']['messages'][] = ['body' => $message, 'type' => $type];
    }

    public static function show_message()
    {
        if (isset($_SESSION['tkt']['messages']) && !empty($_SESSION['tkt']['messages'])) {
            foreach ($_SESSION['tkt']['messages'] as $message) {
                echo '<div class="notice is-dismissible'. self::get_type($message['type']) .'">';
                echo "<p>";
                echo $message['body'];
                echo "</p>";
                echo "</div >";
            }

            self::remove_session();
        }
    }

    public static function get_type($type)
    {
        switch ($type){
            case 1: return 'notice-error'; break;
            case 2: return 'notice-success'; break;
            case 3: return 'notice-warning'; break;
            case 4: return 'notice-info'; break;
        }
    }

    public static function remove_session()
    {
        $_SESSION['tkt']['messages'] = [];
    }
}
