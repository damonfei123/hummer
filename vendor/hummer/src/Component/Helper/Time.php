<?php
namespace Hummer\Component\Helper;

class Time {
    public static function time($iSec=null, $sFormat='Y-m-d H:i:s')
    {
        return date($sFormat, $iSec === null ? time() : $iSec);
    }
}
