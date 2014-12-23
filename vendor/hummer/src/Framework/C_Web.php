<?php
namespace Hummer\Framework;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class C_Web extends C_Base{

    public function __construct() {
        parent::__construct();

        $this->template = $this->Context->Template;
    }

    public function assign($mKey, $mValue=null)
    {
        $aAssign = is_array($mKey) ? $mKey : array($mKey => $mValue);
        foreach ($aAssign as $sKey => $mV) {
            $this->template->assign($sKey, $mV);
        }
    }

    protected $bCalledDisplay = false;

    public function display($sTemplate=null)
    {
        $this->bCalledDisplay = true;
        return $this->template->display($this->getTplPath($this->HttpRequest, $sTemplate));
    }

    public static function getTplPath($REQ, $sTemplate=null)
    {
        if (is_null($sTemplate)) {
            $sURL       = Helper::TrimInValidURI(Arr::get(parse_url($REQ->getRequestURI()),'path',''));
            $aURLPATH   = explode('/', strtolower(substr($sURL,1)));
            $sTplFile   = array_pop($aURLPATH);
            $sTplFile   = $sTplFile == '' ? 'default' : $sTplFile;
            $sTplFile   = sprintf('%s.tpl', Helper::ReplaceLineToUpper($sTplFile));
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
