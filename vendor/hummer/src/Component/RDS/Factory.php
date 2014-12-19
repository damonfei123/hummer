<?php
namespace Hummer\Component\RDS;

use Hummer\Component\RDS\CURD;
use Hummer\Component\Helper\Arr;

class Factory {
    private static $aDBConfig;
    private static $aAopCallBack;
    private static $aModelConf;
    private static $sAppModelNS;
    private static $sDefaultModelClass;
    private $sDefaultDB = 'default';

    public function __construct(
        array $aDBConfig,     //DB config
        array $aModelConfig,  //Model config
        $sAppModelNS        = '',
        $sDefaultModelClass = 'Hummer\\Component\\RDS\\Model\\Model',
        $aAopCallBack
    ) {
        self::$aDBConfig          = $aDBConfig;
        self::$aAopCallBack       = $aAopCallBack;
        self::$aModelConf         = $aModelConfig;
        self::$sAppModelNS        = $sAppModelNS;
        self::$sDefaultModelClass = $sDefaultModelClass;
    }

    public function __call($sModel, $aArgs=null)
    {
        if (($sModel = substr($sModel, 3)) !== false) {
            return $this->get($sModel, $aArgs);
        }
        return false;
    }

    private static $aCURD;
    private static $_aModel;
    public function get($sModelName, $aArgs=null)
    {
        if (!isset(self::$_aModel[$sModelName])) {
            $CURD = $this->initCURD($sModelName);
            $aConf = self::$aModelConf[$sModelName];

            $sModelClassName = isset($aConf['model_class']) ?
                self::$sAppModelNS . '\\' . $aConf['model_class'] :
                self::$sDefaultModelClass;

            self::$_aModel[$sModelName] = new $sModelClassName(
                $sModelName,
                $CURD,
                $aConf,
                $this
            );
        }

        return self::$_aModel[$sModelName];
    }

    public function initCURD($sModelName)
    {
        $sModelDB = isset(self::$aModelConf[$sModelName]) ?
            Arr::get(self::$aModelConf[$sModelName], 'db', $this->sDefaultDB) :
            $this->sDefaultDB;

        if (!isset(self::$aCURD[$sModelDB])) {
            if (!array_key_exists($this->sDefaultDB, self::$aDBConfig)) {
                throw new \InvalidArgumentException('[ FACTORY ] : NONE DB CONFIG');
            }
            $PDO = new CURD(
                self::$aDBConfig[$sModelDB]['dsn'],
                self::$aDBConfig[$sModelDB]['username'],
                self::$aDBConfig[$sModelDB]['password'],
                self::$aDBConfig[$sModelDB]['option'],
                self::$aAopCallBack
            );
            self::$aCURD[$sModelDB] = $PDO;
        }
        return self::$aCURD[$sModelDB];
    }
}
