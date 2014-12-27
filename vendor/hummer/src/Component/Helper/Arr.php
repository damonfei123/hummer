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
namespace Hummer\Component\Helper;

class Arr{

    public static function get($aData, $sKey, $sDefault = null)
    {
        return isset($aData[$sKey]) ? $aData[$sKey] : $sDefault;
    }

    public function changeIndex(array $aArr, $sKey='id')
    {
        $aRetArr = array();
        foreach ($aArr as $Arr) {
            $aRetArr[$Arr[$sKey]] = $Arr;
        }
        return $aRetArr;
    }

    public function changeIndexToKVMap(array $aArr, $sKey, $sVal)
    {
        $aRetArr = array();
        foreach ($aArr as $Arr) {
            $aRetArr[$Arr[$sKey]] = $Arr[$sVal];
        }
        return $aRetArr;
    }
}
