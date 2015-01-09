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

class Writer_STDIO implements IWriter {

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
        $sContentFormat=null
    ) {
        $this->sContentFormat = is_null($sContentFormat) ?
            '[{iLevel}] : {sTime} : {sContent}' :
            $sContentFormat;
    }

    public function acceptData($aRow)
    {
        if ($this->bEnable) {
            $sLevelName = Logger::getLogNameByLevelID($aRow['iLevel']);
            $sLogMsg = str_replace(
                array('{sGUID}', '{iLevel}', '{sTime}', '{sContent}'),
                array($this->sGUID, $sLevelName, $aRow['sTime'], $aRow['sMessage']),
                $this->sContentFormat
            ) . PHP_EOL;

            #flush to STDIO
            fprintf(STDOUT, sprintf('%s', $sLogMsg), null);
        }
    }

    public function setGUID($sGUID)
    {
        #GUID should be same for one request
        $this->sGUID  = $sGUID;
    }
}
