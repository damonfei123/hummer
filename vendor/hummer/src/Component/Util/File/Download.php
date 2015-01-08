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
namespace Hummer\Component\Util\File;

use Hummer\Component\Helper\File;
use Hummer\Component\Helper\Helper;

class Download{

    public static function download($sFilePath, $sDownName=null)
    {
        header("Content-type:text/html;charset=utf-8");
        if (!File::Exists($sFilePath)) {
            return;
        }
        $FP        = fopen($sFilePath, 'r');
        $iFileSize = filesize($sFilePath);

        $sDownName = Helper::TOOP($sDownName !== null, $sDownName, basename($sFilePath));

        self::header(self::getFileType($sDownName), $sDownName, $iFileSize);

        $iBuffer = 1024; $iFileCount = 0;
        //OUTPUT
        while(!feof($FP) && $iFileCount < $iFileSize){
            $sFileContent = fread($FP,$iBuffer);
            $iFileCount  += $iBuffer;
            echo $sFileContent;
        }
        fclose($FP);
    }

    public static function header($sFileType, $sFileName, $iFileSize)
    {
        header('Content-type: application/octet-stream');
        header('Accept-Ranges: bytes');
        header('Accept-Length:'.$iFileSize);
        header('Content-Disposition: attachment; filename='.$sFileName);
        switch ($sFileType){
            case 'excel':
                header("Content-Transfer-Encoding: binary");
                header("Content-Type: application/msexcel");
                break;
            case 'normal':
                break;
        }
    }

    public function getFileType($sFileName)
    {
        $sFileExt = substr($sFileName, strpos($sFileName, '.') + 1);
        if (in_array($sFileExt, array('xlsx','xlsm','xltx','xltm','xlsb','xlam'))) {
            return 'excel';
        }
        return 'normal';
    }
}
