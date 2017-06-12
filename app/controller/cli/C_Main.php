<?php
namespace App\controller\cli;

use App\opt\TecLoginLog;
use App\helper\IP;
use Hummer\Component\Helper\Arr;
use App\system\controller\Cli_Base;

class C_Main extends Cli_Base{

    public function __before__()
    {
    }

    public function actionDefault()
    {
        pr(M('t_test')->where(array('id' => 1))->find());
    }

}
