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
            $aArgs   = (array)$aArgs;
            $sModel  = sprintf('%s %s', $sModel, array_shift($aArgs));
            return $this->get($sModel);
        }
        return false;
    }

    private static $_aCURD;
    private static $_aModel;
    /**
     *  @param $sModelName  string Model
     *      ex: user | user u
     **/
    public function get($sModelName, array $aArgs=null)
    {
        $sModelName = str_replace(' ', '|', trim($sModelName));
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
        #init Table
        self::$_aModel[$sModelName]->setTable($sModelName);
        return self::$_aModel[$sModelName];
    }

    public static function getRealModel($sModelName)
    {
        if (false !== ($iPos=strpos($sModelName, '|'))) {
            $sRealModel = substr($sModelName, 0, $iPos);
        }else{
            $sRealModel = $sModelName;
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
