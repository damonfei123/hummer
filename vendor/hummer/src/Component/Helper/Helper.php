<?php
namespace Hummer\Component\Helper;

class Helper{

    public static function TrimEnd($sV, $sSeperator='/', $sBoth='')
    {
        $sFun = $sBoth.'trim';
        $sV   = $sFun($sV, $sSeperator) . $sSeperator;
        return $sV === $sSeperator ? '' : $sV;
    }

}
