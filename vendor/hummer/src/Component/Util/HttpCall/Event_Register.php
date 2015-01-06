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
namespace Hummer\Component\Util\HttpCall;

use Hummer\Component\Event\Event;
use Hummer\Component\Context\Context;

class Event_Register{

    const E_ALL_BEFORE = 'Hummer.Component.HttpCall.HttpCall.__ALL__:before';
    const E_ALL_AFTER  = 'Hummer.Component.HttpCall.HttpCall.__ALL__:after';
    const E_REDIS_MODE = 'Hummer.Component.HttpCall.Event_Register:time';

    public static function register_All($bEvent=true)
    {
        if ($bEvent) {
            Event::register(
                self::E_ALL_BEFORE,
                function(){
                    Context::getInst()->Arr[self::E_REDIS_MODE] = microtime(true);
                }
            );
            Event::register(
                self::E_ALL_AFTER,
                function($sUrl, $aParam, $mResult=array()){
                    $Log   = Context::getInst()->Log;
                    $iCost = @round(microtime(true) - Context::getInst()->Arr[self::E_REDIS_MODE], 6);
                    $Log->info(
                        '[HttpCall] : Time : {cost}; url : {url}; param: {param}; Result : {result}',
                        array(
                            'cost'    => $iCost,
                            'url'     => $sUrl,
                            'param'   => json_encode($aParam),
                            'result'  => var_export($mResult, true)
                        )
                    );
                }
            );
        }
    }
}
