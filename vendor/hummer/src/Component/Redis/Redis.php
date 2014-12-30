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
namespace Hummer\Component\Redis;

use Hummer\Component\Helper\Helper;
use Hummer\Component\Event\Event;

class Redis{

    /**
     *  @var $Instance
     **/
    protected $Instance = null;

    /**
     *  @var $Redis
     **/
    protected $Redis    = null;

    /**
     *  @var $aConfig Redis Config
     **/
    protected $aConfig;


    function __construct(
        array $aConfig = array(),
        $bEvent = true
    ) {
        Event_Register::register_All($bEvent);
        $this->aConfig  = $aConfig;
    }

    public function getInstance()
    {
        if (!$this->Redis) {
            $this->Redis = new \Redis();
            call_user_func_array(
                array(
                    $this->Redis,
                    Helper::TOOP(isset($this->aConfig['pconnect']),'pconnect','pconnect')
                ),
                $this->aConfig['server']
            );
        }
        return $this->Redis;
    }

    public function __call($sMethod, $aArgs=array())
    {
        Event::call(Event_Register::E_ALL_BEFORE, $sMethod, $aArgs);
        $mResult = call_user_func_array(array($this->getInstance(),$sMethod), $aArgs);
        Event::call(Event_Register::E_ALL_AFTER, $mResult, $sMethod, $aArgs);
    }
}
