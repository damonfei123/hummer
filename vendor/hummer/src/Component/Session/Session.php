<?php
namespace Hummer\Component\Session;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class Session{
    public function __construct()
    {
        session_start();
    }

    public function set($sVarName, $mV)
    {
        $_SESSION[$sVarName] = Helper::TOOP(is_array($mV), json_encode($mV), $mV);
    }
    public function get($sVarName)
    {
        return Arr::get($_SESSION, $sVarName, null);
    }
    public function del($sVarName)
    {
        if (isset($_SESSION[$sVarName])) {
            unset($_SESSION[$sVarName]);
        }
    }
    public function __destruct()
    {
        session_destroy();
    }
}
