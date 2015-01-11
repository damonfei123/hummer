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
use Hummer\Component\Helper\Helper;
use Hummer\Component\Context\InvalidArgumentException;

class Factory {

    /**
     *  @var $_aDBConfig All Database config
     **/
    private static $_aDBConfig;

    /**
     * @var $_aAopCallBack
     **/
    private static $_aAopCallBack;

    /**
     * @var $_aModelConf model config
     **/
    private static $_aModelConf;

    /**
     *  @var $_sAppModelNS APP namespace
     **/
    private static $_sAppModelNS;

    /**
     *  @var $_sDefaultModelClass Default Model
     **/
    private static $_sDefaultModelClass;

    /**
     *  @var $_sDefaultDB Default Database Config
     **/
    private $_sDefaultDB = 'default';

    public function __construct(
        array $aDBConfig,     //DB config
        array $aModelConfig,  //Model config
        $sAppModelNS        = '',
        $sDefaultModelClass = 'Hummer\\Component\\RDS\\Model\\Model',
        $aAopCallBack = null
    ) {
        self::$_aDBConfig          = $aDBConfig;
        self::$_aModelConf         = $aModelConfig;
        self::$_aAopCallBack       = $aAopCallBack;
        self::$_sAppModelNS        = $sAppModelNS;
        self::$_sDefaultModelClass = $sDefaultModelClass;
    }

    public function __call($sModel, $aArgs=array())
    {
        if (($sModel = substr($sModel, 3)) !== false) {
            $aArgs   = (array)$aArgs;
            $sModel  = sprintf('%s %s', $sModel, array_shift($aArgs));
            return $this->get($sModel, array_shift($aArgs));
        }
        throw new \BadMethodCallException('[ Factory ] : Err : call undefined method');
    }

    /**
     *  @var $_aCURD All CURD Object Cache
     **/
    private static $_aCURD;

    /**
     * @var $_aDB All DB Cache
     **/
    private static $_aDB;

    /**
     *  @var $_aModel Model Cache
     **/
    private static $_aModel;

    /**
     *  @param $sModelName  string Model
     *      ex: user | user u
     **/
    public function get($sModelName, $sDB = '')
    {
        $sModelName = str_replace(' ', '|', Helper::TrimInValidURI(trim($sModelName), '  ', ' '));
        $sRealModel = self::getRealModel($sModelName);
        #config
        $aConf = Arr::get(self::$_aModelConf, $sRealModel,array());
        $sDB   = Helper::TOOP($sDB, $sDB, Arr::get($aConf, 'db', $this->_sDefaultDB));
        $CURD = Helper::TOOP(
            isset(self::$_aDB[$sDB]),
            self::$_aDB[$sDB],
            $this->initCURD($sModelName, $sDB)
        );
        $_sTmpModel = sprintf('%s_%s', $sDB, $sRealModel);
        if (!isset(self::$_aModel[$_sTmpModel])) {
            $sModelClassName = isset($aConf['model_class']) ?
                self::$_sAppModelNS . '\\' . $aConf['model_class'] :
                self::$_sDefaultModelClass;

            self::$_aModel[$_sTmpModel] = new $sModelClassName(
                $sModelName,
                $CURD,
                $aConf,
                $this
            );
        }
        #init Model
        self::$_aModel[$_sTmpModel]->initModel($sModelName);
        return self::$_aModel[$_sTmpModel];
    }

    /**
     *  Parse Model, Get Real Model
     *  Ex:  user -> User | user u -> User
     **/
    public static function getRealModel($sModelName)
    {
        if (false !== ($iPos=strpos($sModelName, '|'))) {
            $sModelName = substr($sModelName, 0, $iPos);
        }
        return ucfirst($sModelName);
    }

    /**
     *  Init CURD By Deferent Database
     **/
    public function initCURD($sModelName, $sModelDB=null)
    {
        $sRealModel = self::getRealModel($sModelName);

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

    public function isModelDataEmpty($Model)
    {
        if ($Model === null ||
            (is_array($Model) && count($Model) == 0)
        ) {
            return true;
        }
        return false;
    }
}
