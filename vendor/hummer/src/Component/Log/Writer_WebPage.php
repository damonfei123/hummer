<?php
namespace Hummer\Component\Log;

class Writer_WebPage implements IWriter{

    protected $aLog;

    public function acceptData($aRow)
    {
        $this->aLog[] = $aRow;
    }

    public function __destruct()
    {
    }
}
