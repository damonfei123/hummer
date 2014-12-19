<?php
namespace Hummer\Component\Configure;

use Hummer\Component\Helper\Suger;

class Configure{

    public static function factory()
    {
        return Suger::createObjAdaptor(__NAMESPACE__, func_get_args());
    }

    public static function parseRecursion($mResult, $CFG)
    {
        if (is_string($mResult) && $mResult) {
            switch ($mResult[0]) {
                case '@':
                    $mResult = $CFG->get(substr($mResult, 1));
                    break;
            }
        }elseif(is_array($mResult)){
            foreach ($mResult as $mK => $mV) {
                $mResult[$mK] = self::parseRecursion($mV, $CFG);
            }
        }
        return $mResult;
    }
}
