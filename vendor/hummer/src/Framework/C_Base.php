<?php
namespace Hummer\Framework;

use Hummer\Component\Context\Context;

class C_Base{

    protected $Context;
    protected $Config;
    protected $bCalledDisplay = false;

    public function __construct($sTpl='html')
    {
        $this->Context      =  Context::getInst();
        $this->sTpl         = $sTpl;
        $this->Config       = $this->Context->Config;
        $this->Log          = $this->Context->Log;
        $this->template     = $this->Context->Template;
    }

    public function assign($mKey, $mValue=null)
    {
        $aAssign = is_array($mKey) ? $mKey : array($mKey => $mValue);
        foreach ($aAssign as $sKey => $mV) {
            $this->template->assign($sKey, $mV);
        }
    }

    public function __destruct()
    {
    }
}
