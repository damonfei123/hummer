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

interface ICache{

    /**
     *  Store Data
     *  @param $sKey
     *  @param $mVal    Support except resource type
     *  @param $iExpire Expire
     *  @return boolean
     **/
    public function store($sKey, $mVal, $iExpire = null);

    /**
     *  Get Data
     *  @param $sKey
     *  @param $bGC Auto delete
     *  @return null | val
     **/
    public function fetch($sKey, $bGC);

    /**
     *  delete Data
     *  @param $sKey
     *  @return boolean
     **/
    public function delete($sKey);

}
