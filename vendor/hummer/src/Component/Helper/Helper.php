<?php
namespace Hummer\Component\Helper;

class Helper{

    public static function TrimEnd($sV, $sSeperator='/', $sBoth='')
    {
        $sFun = $sBoth.'trim';
        $sV   = $sFun($sV, $sSeperator) . $sSeperator;
        return $sV === $sSeperator ? '' : $sV;
    }

    public static function TrimInValidURI($sURI)
    {
        while (strpos($sURI, '//')) {
            $sURI = str_replace('//','/', $sURI);
        }
        return $sURI;
    }

    /**
     *  ReplaceLineToUpper(get_name)
     *  @return getName
     **/
    public static function ReplaceLineToUpper($sVar='', $sSeperator='_')
    {
        if (false === strpos($sVar, $sSeperator)) {
            return $sVar;
        }
        $aVar = explode($sSeperator, $sVar);
        for ($i = 1; $i < count($aVar); $i++) {
            $aVar[$i] = ucfirst(strtolower($aVar[$i]));
        }
        return implode('',$aVar);
    }
}
