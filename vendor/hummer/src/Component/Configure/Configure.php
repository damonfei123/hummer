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
namespace Hummer\Component\Configure;

use Hummer\Component\Helper\Suger;
use Hummer\Component\Context\Context;

class Configure{

    public static function factory()
    {
        return Suger::createObjAdaptor(__NAMESPACE__, func_get_args());
    }

    public static function parseRecursion($mResult, $CFG)
    {
        if (is_string($mResult) && $mResult) {
            switch ($mResult[0]) {
                case '@':
                    $mResult = $CFG->get(substr($mResult, 1));
                    break;
                case '\\':
                    $mResult = substr($mResult, 1);
                    break;
                case ':':
                    $sModel  = substr($mResult, 1);
                    $mResult = Context::getInst()->$sModel;
                    break;
            }
        }elseif(is_array($mResult)){
            foreach ($mResult as $mK => $mV) {
                $mResult[$mK] = self::parseRecursion($mV, $CFG);
            }
        }
        return $mResult;
    }
}
