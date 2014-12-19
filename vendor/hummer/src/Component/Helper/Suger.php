<?php
namespace Hummer\Component\Helper;

class Suger{

    public static function createObj($sClassName, array $aArgv = array())
    {
        if (empty($aArgv)) {
            return new $sClassName();
        }else{
            $Ref = new \ReflectionClass($sClassName);
            return $Ref->newInstanceArgs($aArgv);
        }
    }

    public static function createObjAdaptor(
        $sNS,
        array $aClassAndArgs,
        $sAdaptorClassPre='Adaptor_'
    ) {
        if (empty($aClassAndArgs)) {
            throw new \InvalidArgumentException('[Suger] : Error');
        }
        $sClassName = array_shift($aClassAndArgs);
        if ($sClassName[0] === '@') {
            $sClassName = $sNS . '\\' . $sAdaptorClassPre . substr($sClassName, 1);
        }
        return self::createObj($sClassName, $aClassAndArgs);
    }
}
