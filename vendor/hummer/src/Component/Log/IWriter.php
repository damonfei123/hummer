<?php
namespace Hummer\Component\Log;

interface IWriter{

    /**
     *  AcceptData
     **/
    public function acceptData($aRow);

    /**
     * Everty Query GUID
     **/
    public function setGUID($sGUID);
}
