<?php
namespace Hummer\Component\Log;

use Hummer\Component\Helper\Dir;

class Writer_File implements IWriter {

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
        $sContentFormat=null
    ) {
        $this->sFileFormat= $sFileFormat;
        $this->sContentFormat = is_null($sContentFormat) ?
            '[{iLevel}] : {sTime} : {sGUID} : {sContent}' :
            $sContentFormat;
    }

    protected $aLog;

    public function acceptData($aRow)
    {
        if (!$this->bEnable) {
            return;
        }
        $sLogMsg = str_replace(
            array('{iLevel}', '{sTime}', '{sGUID}', '{sContent}'),
            array(Logger::getLogNameByLevelID($aRow['iLevel']), $aRow['sTime'], $aRow['sGUID'], $aRow['sMessage']),
            $this->sContentFormat
        ) . PHP_EOL;

        $sFilePath = str_replace(
            array('{level}', '{date}', '{month}'),
            array(Logger::getLogNameByLevelID($aRow['iLevel']), date('Y-m-d'), date('Ym')),
            $this->sFileFormat
        );
        if(!Dir::makeDir(dirname($sFilePath))){
            throw new \RuntimeException('[Log] : Make Dir Error');
        }
        file_put_contents($sFilePath , $sLogMsg, FILE_APPEND);
    }
}
