<?php
namespace Hummer\Framework;

use Hummer\Component\RDS\Factory;
use Hummer\Component\Context\Context;

class Bootstrap{

    private $DB;

    public function __construct(
        $Configure,
        $sEnv = null
    ) {
        Context::makeInst();
        $this->Context = Context::getInst();
        $aRegisterMap = array(
            'Config'    => $Configure,
            'sEnv'      => $sEnv,
            'sRunMode'  => strtolower(PHP_SAPI) === 'cli' ? 'cli' : 'http'
        );

        $this->Context->registerMulti($aRegisterMap);
    }

    public static function setHandle(
        $mCBErrorHandle=array('Hummer\\Framework\\Bootstrap', 'handleError'),
        $iErrType = null
    ) {
        set_error_handler(
            $mCBErrorHandle,
            $iErrType === null ? (E_ALL | E_STRICT) : (int)$iErrType
        );
    }

    public static function handleError($iErrNum, $sErrStr, $sErrFile, $iErrLine, $sErrContext)
    {
        echo $iErrNum . ':' . $sErrStr . "\nIn File[$sErrFile]:Line[$iErrLine]\n";
    }
}
