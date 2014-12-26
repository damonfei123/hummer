<?php
namespace Hummer\Component\Redis;

use Hummer\Component\Event\Event;
use Hummer\Component\Context\Context;

class Event_Register{
    const E_ALL_BEFORE = 'Hummer.Component.Redis.Redis.__ALL__:before';
    const E_ALL_AFTER  = 'Hummer.Component.Redis.Redis.__ALL__:after';
    const E_REDIS_MODE = 'Hummer.Component.Redis.Event_Register:time';

    public static function register_All($bEvent=true)
    {
        if (!$bEvent) {
            return true;
        }
        Event::register(
            self::E_ALL_BEFORE,
            function($sMethodName, $aArgs=array()){
                Context::getInst()->Arr[self::E_REDIS_MODE] = microtime(true);
            }
        );
        Event::register(
            self::E_ALL_AFTER,
            function($mResult, $sMethodName, $aArgs){
                $Log   = Context::getInst()->Log;
                $Log->info(
                    '[Redis] : Time : {cost}; cmd : {cmd}; Args : {args}',
                    array(
                        'cost' => round(microtime(true) - Context::getInst()->Arr[self::E_REDIS_MODE], 6),
                        'args' => json_encode($aArgs),
                        'cmd'  => $sMethodName
                    )
                );
            }
        );
    }
}
