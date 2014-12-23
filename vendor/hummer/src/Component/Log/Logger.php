<?php
namespace Hummer\Component\Log;

use Hummer\Component\Helper\Suger;
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Time;

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

    public function debug($sMessage)
    {
        $this->log(self::LEVEL_DEBUG, $sMessage);
    }
    public function warn($sMessage)
    {
        $this->log(self::LEVEL_WARN, $sMessage);
    }
    public function info($sMessage)
    {
        $this->log(self::LEVEL_INFO, $sMessage);
    }
    public function notice($sMessage)
    {
        $this->log(self::LEVEL_NOTICE, $sMessage);
    }
    public function error($sMessage)
    {
        $this->log(self::LEVEL_ERROR, $sMessage);
    }
    public function fatal($sMessage)
    {
        $this->log(self::LEVEL_FATEAL, $sMessage);
    }

    public function log($iLevel, $sMessage)
    {
        if (!($iLevel & $this->iLogLevel) || empty($this->aWriter)) {
            return;
        }
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
    }
}
