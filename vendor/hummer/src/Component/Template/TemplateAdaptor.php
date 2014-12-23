<?php
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
