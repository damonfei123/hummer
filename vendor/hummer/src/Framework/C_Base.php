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
        $aAssign = is_array($mKey) ? $mKey : array($mKey => $mValue);
        foreach ($aAssign as $sKey => $mV) {
            $this->template->assign($sKey, $mV);
        }
    }

    public function memLimit($iMem=-1)
    {
        ini_set('memory_limit', sprintf("%s",$iMem));
    }

    public function timeLimit($iTime=0)
    {
        set_time_limit($iTime);
    }

    public function __destruct()
    {
    }
}
