<?php
namespace App\opt;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Filesystem\File;
use Hummer\Component\Filesystem\Dir;

class TecLoginLog {

    public function analyze()
    {
        $Cache     = CTX()->CacheFile;
        $sCacheKey = '__TecLoginLog_analyze__';
        if (!($aData=$Cache->fetch($sCacheKey))) {
            $aData = $this->parseFile();
            $Cache->store($sCacheKey, $aData);
        }
        return $aData;
    }

    public function parseFile()
    {
        $sFilePath = '/tmp/yinfei/login_ip.txt';
        $File      = fopen($sFilePath,'r');
        $aLogin    = array();
        while (!feof($File)){
            $sLine = fgets($File, 1024);
            $aLine = explode(':', $sLine);
            if (!isset($aLine[6]) || !isset($aLine[8])) { continue; }
            $iID   = (int)$aLine[6];//用户ID
            $sIP   = trim((string)$aLine[8]);//用户登陆IP
            if (!isset($aLogin[$iID][$sIP])) {
                $aLogin[$iID][$sIP] = 1;
            }else{
                $aLogin[$iID][$sIP] += 1;
            }
        }
        return $aLogin;
    }

    public function getAutoPC($aLogIP)
    {
        if ($aLogIP) {
            arsort($aLogIP);
            $aLogIP = array_flip($aLogIP);
            $sMaxIP    = array_shift($aLogIP);//最大次数
            $sLatestIP = array_pop($aLogIP);//最近的IP
            if ($sMaxIP == $sLatestIP) {
                return $sLatestIP;
            }
            return $sMaxIP;
        }
        return null;
    }
}
