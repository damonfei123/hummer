<?php
namespace Hummer\Component\Configure;

class Adaptor_PHP {

    private $sBaseDir;
    private $sDefaultDir;

    public function __construct($sBaseDir, $sDefaultDir)
    {
        $this->sBaseDir    = $sBaseDir;
        $this->sDefaultDir = $sDefaultDir;
    }

    private $aConfig=array();

    public function get($sConfigName)
    {
        $mBaseConfig    = self::_get($sConfigName, $this->sBaseDir);
        $mDefaultConfig = self::_get($sConfigName, $this->sDefaultDir);
        return $mBaseConfig === null ? $mDefaultConfig : $mBaseConfig;
    }

    public static function _get($sModule, $sBaseDir)
    {
        $mConfig   = null;
        $iPos      = strpos($sModule, '.');
        $sFileName = $sModule;
        if (false !== $iPos) {
            $sFileName = substr($sModule, 0, $iPos);
        }
        if(file_exists($sFilePath = $sBaseDir . '/' . $sFileName. '.php')){
            $mConfig = require($sFilePath);
        }
        if (false === $iPos) {
            return $mConfig;
        }
        $sKey = substr($sModule, $iPos+1);
        $sKey = str_replace('.',"']['", $sKey);
        return $mConfig["$sKey"];
    }
}
