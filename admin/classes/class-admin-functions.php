<?php

class wpnat_admin_functions
{
    public static $instance = null;

    public static function get_instance()
    {
        if(self::$instance !== null ) {
            return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }

}

wpnat_admin_functions::get_instance();
