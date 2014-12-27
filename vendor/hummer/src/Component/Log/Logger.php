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
namespace Hummer\Component\Log;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Time;
use Hummer\Component\Helper\Suger;
use Hummer\Component\Helper\Helper;

class Logger{

    const DESC_DEBUG   = 'debug';
    const DESC_INFO    = 'info';
    const DESC_WARN    = 'warn';
    const DESC_NOTICE  = 'notice';
    const DESC_ERROR   = 'error';
    const DESC_FATEAL  = 'fatal';
    const DESC_ALL     = 'all';

    const LEVEL_DEBUG   = 1;
    const LEVEL_INFO    = 2;
    const LEVEL_WARN    = 4;
    const LEVEL_NOTICE  = 8;
    const LEVEL_ERROR   = 16;
    const LEVEL_FATEAL  = 32;
    const LEVEL_ALL     = 255;

    public static $aLogInfo = array(
        self::LEVEL_DEBUG   => self::DESC_DEBUG,
        self::LEVEL_INFO    => self::DESC_INFO,
        self::LEVEL_WARN    => self::DESC_WARN,
        self::LEVEL_NOTICE  => self::DESC_NOTICE,
        self::LEVEL_ERROR   => self::DESC_ERROR,
        self::LEVEL_FATEAL  => self::DESC_FATEAL
    );
    public static function getLogNameByLevelID($iLevel)
    {
        return Arr::get(self::$aLogInfo, $iLevel, null);
    }

    public function __construct(
        array $aWriterConf,
        $iLogLevel,
        $sRequestID = null
    ) {
        foreach ($aWriterConf as $sK => $aConf) {
            $this->aWriter[$sK] = Suger::createObjAdaptor(__NAMESPACE__, $aConf, 'Writer_');
        }
        $this->iLogLevel = $iLogLevel;
        $this->sGUID     = $sRequestID ? $sRequestID : md5(uniqid(__class__,true));
    }

    public function debug($sMessage, array $aContext = array())
    {
        $this->log(self::LEVEL_DEBUG, $sMessage, $aContext);
    }
    public function warn($sMessage, array $aContext = array())
    {
        $this->log(self::LEVEL_WARN, $sMessage, $aContext);
    }
    public function info($sMessage, array $aContext = array())
    {
        $this->log(self::LEVEL_INFO, $sMessage, $aContext);
    }
    public function notice($sMessage, array $aContext = array())
    {
        $this->log(self::LEVEL_NOTICE, $sMessage, $aContext);
    }
    public function error($sMessage, array $aContext = array())
    {
        $this->log(self::LEVEL_ERROR, $sMessage, $aContext);
    }
    public function fatal($sMessage, array $aContext = array())
    {
        $this->log(self::LEVEL_FATEAL, $sMessage, $aContext);
    }

    public function log($iLevel, $sMessage, $aContext=array())
    {
        if (!($iLevel & $this->iLogLevel) || empty($this->aWriter)) {
            goto END;
        }
        $sMessage = self::interpolate($sMessage, $aContext);
        list($sUSec, $sSec) = explode(' ', microtime());
        $aRow = array(
            'sTime'    => sprintf('%s.%s',
                Time::time($sSec),
                substr($sUSec, strpos($sUSec,'.')+1, 4)
            ),
            'iLevel'   => $iLevel,
            'sMessage' => $sMessage
        );
        #display to every writer
        foreach ($this->aWriter as $Writer) {
            $Writer->setGUID($this->sGUID);
            $Writer->acceptData($aRow);
        }

        END:
    }

    public static function interpolate($sMessage, $aContext=array())
    {
        $aReplace = array();
        foreach ($aContext as $sK => $mV) {
            $aReplace['{'.$sK.'}'] = Helper::TOOP(
                is_array($mV) || is_object($mV),
                json_encode($mV),
                $mV
            );
        }
        return strtr($sMessage, $aReplace);
    }
}
