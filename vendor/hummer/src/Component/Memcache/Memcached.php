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
namespace Hummer\Component\Memcache;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Event\Event;

class Memcached{

    /**
     *  @var $Instance
     **/
    protected $Instance;

    /**
     *  @var $aConfig
     **/

    public function __construct(array $aConfig = array())
    {
        Event_Register::register_All($bEvent);
        $this->aConfig = $aConfig;
    }

    public function getInstance()
    {
        if (!$this->Instance) {
            $Memcache = new Memcached();
            $Memcache->connect(
                $aConfig['host'],
                $aConfig['port'],
                Arr::get($aConfig, 'timeout', 1)
            );
        }
        return $this->Instance;
    }

    public function __call($sMethod, $aArgs=array())
    {
        Event::call(Event_Register::E_ALL_BEFORE, $sMethod, $aArgs);
        $mResult = call_user_func_array(array($this->getInstance(),$sMethod), $aArgs);
        Event::call(Event_Register::E_ALL_AFTER, $mResult, $sMethod, $aArgs);
    }
}
