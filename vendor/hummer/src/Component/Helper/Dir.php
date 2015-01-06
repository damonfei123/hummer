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

class Dir{

    public static function makeDir($sDirName, $sPerm=0777)
    {
        if (File::Exists($sDirName)) {
            return true;
        }
        if (@mkdir($sDirName, $sPerm, true)) {
            return true;
        }
        return false;
    }

    /**
     *  Get Dir File
     *  @param $sDirName Dir Name
     *  @param $bRecursion Get Dir File By Recursion
     *  @param $aFile      Default File
     *  @return array | null
     **/
    public static function showList($sDirName, $bRecursion=false, &$aFile=array())
    {
        if (self::Check($sDirName)) {
            $Dir = opendir($sDirName);
            while ($sFileName=readdir($Dir)) {
                if (self::IsValidFileName($sFileName)) {
                    if (is_dir($sDirName . '/' . $sFileName) && $bRecursion) {
                        self::showList($sDirName . '/'. $sFileName, $bRecursion, $aFile);
                    }else{
                        array_push($aFile, $sFileName);
                    }
                }
            }
            return $aFile;
        }
        return null;
    }

    /**
     *  Check Dir
     **/

    protected static function Check($sDirName){
        return is_dir($sDirName) && is_readable($sDirName);
    }

    /**
     *  Check Valid FileName
     **/
    protected static function IsValidFileName($sFileName)
    {
        return $sFileName != '.' && $sFileName != '..';
    }
}
