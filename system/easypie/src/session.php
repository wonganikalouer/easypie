<?php
namespace EaseRoutes;
class session{
    /**
     * @param string $key session identification key, case sensitive
     */
    static function get($key){
        @session_start();
        return $_SESSION[$key];
    }

    static function set($key, $value){
        @session_start();
        $_SESSION[$key]= $value;
    }

    static function remove($key)
    {
        @session_start();
        unset($_SESSION[$key]);
    }
}