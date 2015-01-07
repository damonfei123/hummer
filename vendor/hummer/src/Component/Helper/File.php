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

class File{

    /**
     *  Read File Data
     **/
    public static function get($sFilePath)
    {
        return self::Exists($sFilePath) ? file_get_contents($sFilePath) : null;
    }

    /**
     *  Send Data To File
     **/
    public static function send($sFile, $sContent, $bAppend=true)
    {
        return file_put_contents($sFile, $sContent, $bAppend ? FILE_APPEND : '');
    }

    /**
     *  Read File To Arr
     **/
    public static function getFileToArr(
        $sFilePath,
        $iLenth=1024,
        $aCallable=array('\\Hummer\\Component\\Helper\\File', 'Handle')
    ) {
        if (self::Exists($sFilePath)) {
            $aRet = array();
            $File = fopen($sFilePath, 'r');
            while (!feof($File)) $aRet[] = call_user_func($aCallable, fgets($File, $iLenth));
            fclose($File);
            return $aRet;
        }
        return null;
    }

    /**
     *  Get File Data By Character To Arr
     **/
    public static function getCToArr($sFilePath)
    {
        if (self::Exists($sFilePath)) {
            $aRet = array();
            $File = fopen($sFilePath, 'r');
            while (!feof($File)) $aRet[] = fgetc($File);
            fclose($File);
            return $aRet;
        }
        return null;
    }

    /**
     *  Check If File Exists
     **/
    public static function Exists($sFilePath)
    {
        return file_exists($sFilePath);
    }

    public static function Handle($mValue)
    {
        return trim($mValue);
    }
}
