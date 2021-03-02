<?php

class Cookie
{
    public static function set($data = [], $day = 1)
    {
        if (is_array($data) && sizeof($data) > 0) {
            foreach ($data as $key => $val) {
                setcookie($key, $val, time() + (86400 * $day), '/');
            }
        }
    }

    public static function remove($data = null)
    {
        if ($data != null && isset($_COOKIE[$data])) {
            setcookie($data, null, time() - 3600);
        }
    }
}
