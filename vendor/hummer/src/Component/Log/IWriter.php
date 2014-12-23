<?php
namespace Hummer\Component\Log;

interface IWriter{
    public function acceptData($aRow);
    public function setGUID($sGUID);
}
