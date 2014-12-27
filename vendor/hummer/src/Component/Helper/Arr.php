<?php
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
