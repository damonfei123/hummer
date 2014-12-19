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
        $mConfig = null;
        $sModule = str_replace('.', '/', $sModule);
        if(file_exists($sFilePath = $sBaseDir . '/' . $sModule . '.php')){
            $mConfig = require($sFilePath);
        }
        return $mConfig;
    }
}
