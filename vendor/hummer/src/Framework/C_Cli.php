<?php
namespace Hummer\Framework;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class C_Cli extends C_Base{

    public function __construct()
    {
        parent::__construct();
    }

    public function fetch($sTemplate='')
    {
        $sContent = '';
        if ($sTemplate) {
            $sContent = $this->template->fetch(
                $this->getTplPath($this->Context->aArgv, $sTemplate,  $this->sTpl)
            );
        }
        return $sContent;
    }

    /**
     *  @param $aArgv      {array}
     *  @param $sTemplate  {string}
     *      ex: xx | /xx | /xx/xx/xx
     *  @param $sTpl       {string}
     *  @return {string}
     **/
    public function getTplPath($aArgv, $sTemplate='', $sTpl)
    {
        if ($sTemplate && '/' == $sTemplate[0]) {
            $sTplFile = sprintf('%s.%s', substr($sTemplate, 1), $sTpl);
        }else{
            $sRoute   = Helper::TrimInValidURI(strtolower($aArgv[1]), '..', '.');
            $aURLPATH = explode('.', $sRoute);
            $sTplFile = Helper::TOOP($sTemplate, $sTemplate, array_pop($aURLPATH));
            $sTplFile = $sTplFile == '' ? 'default' : $sTplFile;
            $sTplFile = sprintf('%s.%s', Helper::ReplaceLineToUpper($sTplFile), $sTpl);
            $sTplFile = sprintf('%s%s%s',join('/', $aURLPATH),'/', $sTplFile);
        }
        return $sTplFile;
    }
}
