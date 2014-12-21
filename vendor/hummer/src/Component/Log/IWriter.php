<?php
namespace Hummer\Component\Log;

interface IWriter{
    public function acceptData($aRow);
}
