<?php
namespace App\System\controller;

use Hummer\Bundle\Framework\Controller\C_Cli;

class Cli_Base extends C_Cli{

    public static function getFile($sKey)
    {
        return unserialize(file_get_contents('/home/zhangyinfei/project/test/data/nohup_id/'.$sKey));
    }

    public static function sendFile($sFile, $sContent, $bAppend=true)
    {
        file_put_contents(
            '/tmp/yinfei/'.$sFile,
            $sContent . PHP_EOL,
            $bAppend ? FILE_APPEND : ''
        );
    }

    public function __before__()
    {
    }
}
