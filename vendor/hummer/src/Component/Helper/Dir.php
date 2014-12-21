<?php
namespace Hummer\Component\Helper;

class Dir{

    public static function makeDir($sDirName, $sPerm=0777)
    {
        if (is_dir($sDirName) || is_file($sDirName)) {
            return true;
        }
        if (@mkdir($sDirName, $sPerm, true)) {
            return true;
        }
        return false;
    }
}
