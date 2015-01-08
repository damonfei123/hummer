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

class Helper{

    public static function TrimEnd($sV, $sSeperator='/', $sBoth='')
    {
        $sFun = $sBoth.'trim';
        $sV   = $sFun($sV, $sSeperator) . $sSeperator;
        return $sV === $sSeperator ? '' : $sV;
    }

    public function lang($sVar, $FLang='UTF-8', $TLang='GBK')
    {
        return iconv($sVar, $FLang, $TLang);
    }

    public static function TrimInValidURI($sURI, $sInvalid = '//', $sReplace='/')
    {
        while (strpos($sURI, $sInvalid)) {
            $sURI = str_replace($sInvalid,$sReplace, $sURI);
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

    public static function Mem()
    {
        $size = memory_get_peak_usage(true);
        $unit = array('B','KB','MB','GB','TB','PB');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     *  @param Three Order Operator
     **/
    public static function TOOP($b, $first=null, $second=null)
    {
        return ($b) ? $first : $second;
    }
}
