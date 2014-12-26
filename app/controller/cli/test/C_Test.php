<?php
namespace App\controller\cli\test;

use App\system\controller\Cli_Base;

class C_Test extends Cli_Base {

    public function actionShow($aParam)
    {
        echo $this->fetch('/test/test/show');
        return;
        $Users = DB()->getUser()->where(array('id between' => array(1,5)))->findMulti();
        foreach ($Users as $User) {
            echo $User->name;
        }
        echo $this->fetch('show');
    }
}
