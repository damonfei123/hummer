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

    public static function changeIndex(array $aArr, $sKey='id')
    {
        $aRetArr = array();
        foreach ($aArr as $Arr) {
            $aRetArr[$Arr[$sKey]] = $Arr;
        }
        return $aRetArr;
    }

    public static function changeIndexToKVMap(array $aArr, $sKey, $sVal)
    {
        $aRetArr = array();
        foreach ($aArr as $Arr) {
            $aRetArr[$Arr[$sKey]] = $Arr[$sVal];
        }
        return $aRetArr;
    }

    /**
     *  Get Array Data By Smarty Way
     **/
    public static function getBySmarty($mArr, $sKey = '', $sSepetator='.')
    {
        while ($sKey &&
               is_array($mArr) &&
               false !== ($iPos=strpos($sKey, $sSepetator))
        ){
            $sTK  = substr($sKey, 0, $iPos);
            $mArr = Arr::get($mArr, $sTK, null);
            $sKey = substr($sKey, $iPos + 1);
        }
        return Helper::TOOP(is_array($mArr), Arr::get($mArr, $sKey, null), $mArr);
    }
}
