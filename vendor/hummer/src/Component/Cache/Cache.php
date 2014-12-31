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
namespace Hummer\Component\Cache;

use Hummer\Component\Helper\Suger;

class Cache{

    /**
     *  @var $Cache Obj
     **/
    protected $Cache;

    public function __construct()
    {
        $aArgs = func_get_args();
        $this->Cache = Suger::createObjAdaptor(__NAMESPACE__, $aArgs, 'Cache_');
    }


    public function __call($sMethod, $aArgs)
    {
        return call_user_func_array(array($this->Cache, $sMethod), $aArgs);
    }
}
