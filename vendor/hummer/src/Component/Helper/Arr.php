<?php
namespace Hummer\Component\Helper;

class Arr{

    public static function get($aData, $sKey, $sDefault = null)
    {
        return isset($aData[$sKey]) ? $aData[$sKey] : $sDefault;
    }
}
