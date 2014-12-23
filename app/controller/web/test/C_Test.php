<?php
namespace App\controller\web\test;

use App\system\controller\Web_Base;

class C_Test extends Web_Base{

    public function actionDefault()
    {
        $this->Log->info(11);
        $this->Log->error(11);
        $this->Log->fatal(11);
        $this->Log->warn(11);
        $this->Log->warn(11);
        RDB()->getUser()->findMulti();
    }
}
