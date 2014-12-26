<?php
namespace Hummer\Framework;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class C_Web extends C_Base{

    public function __construct() {
        parent::__construct();
        $this->HttpRequest  = $this->Context->HttpRequest;
        $this->HttpResponse = $this->Context->HttpResponse;
    }

    public function display($sTemplate='')
    {
        $this->bCalledDisplay = true;
        if (!is_null($sTemplate)) {
            $this->template->display($this->getTplPath($this->HttpRequest, $sTemplate,$this->sTpl));
        }
    }

    public static function getTplPath($REQ, $sTemplate=null, $sTpl)
    {
        if ($sTemplate === '') {
            $sURL       = Helper::TrimInValidURI(Arr::get(parse_url($REQ->getRequestURI()),'path',''));
            $aURLPATH   = explode('/', strtolower(substr($sURL,1)));
            $sTplFile   = array_pop($aURLPATH);
            $sTplFile   = $sTplFile == '' ? 'default' : $sTplFile;
            $sTplFile   = sprintf('%s.%s', Helper::ReplaceLineToUpper($sTplFile), $sTpl);
            $sTemplate  = sprintf('%s%s%s',join('/', $aURLPATH),'/', $sTplFile);
        }
        return $sTemplate;
    }

    protected $bEnableTpl = true;
    public function disableTpl()
    {
        $this->bEnableTpl = false;
    }


    public function __destruct()
    {
        if ($this->bEnableTpl && !$this->bCalledDisplay) {
            $this->display();
        }
    }
}
