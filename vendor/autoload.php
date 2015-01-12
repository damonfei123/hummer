<?php
/*************************************************************************************

   +-----------------------------------------------------------------------------+
   | Hummer [ Make Code Beauty And Web Easy ]                                    |
   +-----------------------------------------------------------------------------+
   | Copyright (c) 2014 https://github.com/damonfei123 All rights reserved.      |
   +-----------------------------------------------------------------------------+
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )                     |
   +-----------------------------------------------------------------------------+
   | Author: Damon <zhangyinfei313com@163.com>                                   |
   +-----------------------------------------------------------------------------+

**************************************************************************************/
namespace Hummer;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

spl_autoload_register(__NAMESPACE__.'\Autoload::autoload');

!defined('APP_DIR') && trigger_error('[Autoload] : APP_DIR Not Defined');
!defined('HM_DIR') && trigger_error('[Autoload]  : HM_DIR Not Defined');

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
}
