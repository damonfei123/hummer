<?php
namespace Hummer\Framework;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class C_Cli extends C_Base{

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

    public function getTplPath($aArgv, $sTemplate='', $sTpl)
    {
        $sRoute     = Helper::TrimInValidURI(strtolower($aArgv[1]), '..', '.');
        $aURLPATH   = explode('.', $sRoute);
        $sTplFile   = Helper::TOOP($sTemplate, $sTemplate, array_pop($aURLPATH));
        $sTplFile   = $sTplFile == '' ? 'default' : $sTplFile;
        $sTplFile   = sprintf('%s.%s', Helper::ReplaceLineToUpper($sTplFile), $sTpl);
        return sprintf('%s%s%s',join('/', $aURLPATH),'/', $sTplFile);
    }
}
