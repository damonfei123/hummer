<?php
namespace App\helper;

use Hummer\Util\HttpCall\HttpCall;

class IP {

    public static function IPToArea($sIP = '')
    {
        return $sIP ?
            json_decode(HttpCall::callGet('http://ip.baidu.com/ip', array('ip' => $sIP)),true) :
            array();
    }
}
