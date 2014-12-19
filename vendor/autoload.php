<?php
namespace Hummer;

spl_autoload_register(__NAMESPACE__.'\Autoload::autoload');

class Autoload{

    private static $aPsr4Map=array(
        'App'    => APP_DIR,
        'Hummer' => HM_DIR
    );

    public static function autoload($sClassName)
    {
        self::requireFile($sClassName);
    }

    public static function requireFile($sClassName)
    {
        $sFrameworkBase = substr($sClassName, 0, strpos($sClassName,'\\'));
        $sFrameworkPath = str_replace('\\','/',substr($sClassName, strpos($sClassName,'\\')));
        if (array_key_exists($sFrameworkBase, self::$aPsr4Map)) {
            if(file_exists($sFile = self::$aPsr4Map[$sFrameworkBase] . $sFrameworkPath . '.php')){
                require $sFile;
            };
        }
    }
}

