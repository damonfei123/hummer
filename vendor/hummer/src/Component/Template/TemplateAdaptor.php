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
namespace Hummer\Component\Template;

require 'Smarty/Smarty.class.php';

class TemplateAdaptor extends \Smarty {

    public function __construct(
        $sTemplate   = 'templates',
        $sTemplate_c = 'templates_c',
        $sCache      = 'cache',
        $sConfig     = 'config',
        $aOption     = array()
    ) {
        parent::__construct();
        $this->setTemplateDir($sTemplate);
        $this->setCompileDir($sTemplate_c);
        $this->setCacheDir($sCache);
        $this->setConfigDir($sConfig);

        foreach ($aOption as $sK => $mV) {
            $this->$sK = $mV;
        }
    }
}
