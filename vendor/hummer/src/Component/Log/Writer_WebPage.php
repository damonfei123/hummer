<?php
namespace Hummer\Component\Log;

class Writer_WebPage implements IWriter{

    protected $aLog;

    public function acceptData($aRow)
    {
        $this->aLog[] = $aRow;
    }

    public function setGUID($sGUID)
    {
        #GUID should be same for one request
        $this->sGUID = $sGUID;
    }

    public function __destruct()
    {
    }
}
