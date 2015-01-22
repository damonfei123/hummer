<?php
namespace App\System\controller;

use Hummer\Bundle\Framework\Controller\C_Web;

class Web_Base extends C_Web {

    public function __before__()
    {
    }

    public function ajaxReturn($iStatus, $sRetMsg='', $aData=array())
    {
        return json_encode(array(
            'status'    => $iStatus,
            'info'      => $sRetMsg
        ));
    }
}
