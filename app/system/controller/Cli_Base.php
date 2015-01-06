<?php
namespace App\System\controller;

use Hummer\Framework\C_Cli;

class Cli_Base extends C_Cli{

    public static function getFile($sKey)
    {
        return unserialize(file_get_contents('/home/zhangyinfei/project/test/data/nohup_id/'.$sKey));
    }

    public static function sendFile($sFile, $sContent, $bAppend=true)
    {
        file_put_contents(
            '/home/zhangyinfei/project/test/data/nohup_id/'.$sFile,
            $sContent,
            $bAppend ? FILE_APPEND : ''
        );
    }

    public function __before__()
    {
    }
}
