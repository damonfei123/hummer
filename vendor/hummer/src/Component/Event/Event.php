<?php
namespace Hummer\Component\Event;

use Hummer\Component\Helper\Helper;

class Event{

    protected static $aEventCBMap = array();

    public static function register($sEventName, $mCB)
    {
        self::$aEventCBMap[$sEventName][] = $mCB;
    }

    public static function call($sEventName)
    {
        $aArgs = Helper::TOOP(func_num_args() > 1, array_slice(func_get_args(), 1), array());
        if (isset(self::$aEventCBMap[$sEventName])) {
            foreach (self::$aEventCBMap[$sEventName] as $mCB) {
                call_user_func_array($mCB, $aArgs);
            }
        }
    }
}
