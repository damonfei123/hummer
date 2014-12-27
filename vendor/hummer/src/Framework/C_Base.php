<?php
namespace Hummer\Framework;

use Hummer\Component\Context\Context;
use Hummer\Component\Helper\Helper;

class C_Base{

    protected $Context;
    protected $Config;
    protected $bCalledDisplay = false;

    public function __construct($sTpl=null)
    {
        $this->Context  = Context::getInst();
        $this->Log      = $this->Context->Log;
        $this->Config   = $this->Context->Config;
        $this->template = $this->Context->Template;
        $this->sTpl     = Helper::TOOP($sTpl, $sTpl, 'html');
    }

    public function assign($mKey, $mValue=null)
    {
        $aAssign = Helper::TOOP(is_array($mKey), $mKey, array($mKey => $mValue));
        foreach ($aAssign as $sKey => $mV) {
            $this->template->assign($sKey, $mV);
        }
    }

    public function __destruct()
    {
    }
}
