<?php
/*************************************************************************************

   +-----------------------------------------------------------------------------+
   | Hummer [ Make Code Beauty And Web Easy ]                                    |
   +-----------------------------------------------------------------------------+
   | Copyright (c) 2014 https://github.com/damonfei123 All rights reserved.      |
   +-----------------------------------------------------------------------------+
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )                     |
   +-----------------------------------------------------------------------------+
   | Author: Damon <zhangyinfei313com@163.com>                                   |
   +-----------------------------------------------------------------------------+

**************************************************************************************/
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
