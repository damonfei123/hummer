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

use Hummer\Component\Helper\Dir;
use Hummer\Component\Helper\Time;

class Writer_File implements IWriter {

    protected $aData = array();
    protected $sGUID = null;

    protected $sFileFormat;
    protected $sContentFormat;
    protected $bEnable = true;

    public function setDisable()
    {
        $this->bEnable = false;
    }
    public function setEnable()
    {
        $this->bEnable = true;
    }

    public function __construct(
        $sFileFormat,
        $sContentFormat=null,
        $sMonthFormat='Ym',
        $sDateFormat='Ymd'
    ) {
        $this->sMonthFormat = $sMonthFormat;
        $this->sDateFormat  = $sDateFormat;
        $this->sFileFormat  = $sFileFormat;
        $this->sContentFormat = is_null($sContentFormat) ?
            '[{iLevel}] : {sTime} : {sContent}' :
            $sContentFormat;
    }

    public function acceptData($aRow)
    {
        if (!$this->bEnable) {
            return;
        }
        $sLevelName = Logger::getLogNameByLevelID($aRow['iLevel']);
        $sLogMsg = str_replace(
            array('{sGUID}', '{iLevel}', '{sTime}', '{sContent}'),
            array($this->sGUID, $sLevelName, $aRow['sTime'], $aRow['sMessage']),
            $this->sContentFormat
        ) . PHP_EOL;

        #Add to queue
        $this->aData[$sLevelName][] = $sLogMsg;
    }

    public function setGUID($sGUID)
    {
        #GUID should be same for one request
        $this->sGUID  = $sGUID;
    }

    /**
     *  END
     *  Flush log to file
     **/
    public function __destruct()
    {
        $sDate  = Time::time(null,$this->sDateFormat);
        $sMonth = Time::time(null,$this->sMonthFormat);
        foreach ($this->aData as $sLevelName => $aContent) {
            $sFilePath = str_replace(
                array('{level}', '{date}', '{month}'),
                array($sLevelName, $sDate, $sMonth),
                $this->sFileFormat
            );
            if(!Dir::makeDir(dirname($sFilePath))){
                throw new \RuntimeException('[Log] : Make Dir Error');
            }
            file_put_contents(
                $sFilePath ,
                sprintf('%s[%s]%s%s',PHP_EOL, $this->sGUID, PHP_EOL,implode('',$aContent)),
                FILE_APPEND|LOCK_EX
            );
        }
    }
}
