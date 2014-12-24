<?php
namespace Hummer\Component\RDS;

use Hummer\Component\RDS\CURD;
use Hummer\Component\Helper\Arr;

class Factory {
    private static $_aDBConfig;
    private static $_aAopCallBack;
    private static $_aModelConf;
    private static $_sAppModelNS;
    private static $_sDefaultModelClass;
    private $_sDefaultDB = 'default';

    public function __construct(
        array $_aDBConfig,     //DB config
        array $_aModelConfig,  //Model config
        $_sAppModelNS        = '',
        $_sDefaultModelClass = 'Hummer\\Component\\RDS\\Model\\Model',
        $_aAopCallBack
    ) {
        self::$_aDBConfig          = $_aDBConfig;
        self::$_aAopCallBack       = $_aAopCallBack;
        self::$_aModelConf         = $_aModelConfig;
        self::$_sAppModelNS        = $_sAppModelNS;
        self::$_sDefaultModelClass = $_sDefaultModelClass;
    }

    public function __call($sModel, $aArgs=null)
    {
        if (($sModel = substr($sModel, 3)) !== false) {
            return $this->get($sModel, $aArgs);
        }
        return false;
    }

    private static $_aCURD;
    private static $_aModel;
    public function get($sModelName, $aArgs=null)
    {
        $sModelName = str_replace(' ', '|', $sModelName);
        $sRealModel = self::getRealModel($sModelName);
        if (!isset(self::$_aModel[$sModelName])) {
            $CURD  = $this->initCURD($sModelName);
            $aConf = Arr::get(self::$_aModelConf, $sRealModel,array());

            $sModelClassName = isset($aConf['model_class']) ?
                self::$_sAppModelNS . '\\' . $aConf['model_class'] :
                self::$_sDefaultModelClass;

            self::$_aModel[$sModelName] = new $sModelClassName(
                $sModelName,
                $CURD,
                $aConf,
                $this
            );
        }
        return self::$_aModel[$sModelName];
    }

    public static function getRealModel($sModelName)
    {
        $sRealModel = null;
        if (false !== ($iPos=strpos($sModelName, '|'))) {
            $sRealModel = substr($sModelName, 0, $iPos);
        }
        return ucfirst($sRealModel);
    }

    public function initCURD($sModelName)
    {
        $sRealModel = self::getRealModel($sModelName);
        $sModelDB = isset(self::$_aModelConf[$sRealModel]) ?
            Arr::get(self::$_aModelConf[$sRealModel], 'db', $this->_sDefaultDB) :
            $this->_sDefaultDB;

        if (!isset(self::$_aCURD[$sModelDB])) {
            if (!array_key_exists($this->_sDefaultDB, self::$_aDBConfig)) {
                throw new \InvalidArgumentException('[ FACTORY ] : NONE DB CONFIG');
            }
            $PDO = new CURD(
                self::$_aDBConfig[$sModelDB]['dsn'],
                self::$_aDBConfig[$sModelDB]['username'],
                self::$_aDBConfig[$sModelDB]['password'],
                self::$_aDBConfig[$sModelDB]['option'],
                self::$_aAopCallBack
            );
            self::$_aCURD[$sModelDB] = $PDO;
        }
        return self::$_aCURD[$sModelDB];
    }
}
