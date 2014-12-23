<?php
namespace Hummer;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

spl_autoload_register(__NAMESPACE__.'\Autoload::autoload');

define('TM_DIR', HM_DIR.'/Component/Template');

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
                require_once $sFile;
                goto END;
            };
        }

        END:
    }

    public static function getAutoSmarty($class, $sBaseDir)
    {
        if (file_exists($sFilePath=($sBaseDir.$class.'.php'))) {
            include $sFilePath;
            goto END;
        }

        $_class = strtolower($class);
        static $_classes = array(
            'smarty_config_source'               => true,
            'smarty_config_compiled'             => true,
            'smarty_security'                    => true,
            'smarty_cacheresource'               => true,
            'smarty_cacheresource_custom'        => true,
            'smarty_cacheresource_keyvaluestore' => true,
            'smarty_resource'                    => true,
            'smarty_resource_custom'             => true,
            'smarty_resource_uncompiled'         => true,
            'smarty_resource_recompiled'         => true,
        );

        if (!strncmp($_class, 'smarty_internal_', 16) || isset($_classes[$_class])) {
            $sIncFile = $sBaseDir . '/sysplugins/' . $_class . '.php';
            if (file_exists($sIncFile)) {
                include_once($sIncFile);
            }
            goto END;
        }

        END:
    }
}

