<?php
namespace App\controller\cli\test;

use App\system\controller\Cli_Base;

class C_Test extends Cli_Base {

    public function actionShow($aParam)
    {
        /*
        $Users = DB()->getUser()->where(array('id between' => array(1,5)))->findMulti();
        $Users = DB()->getUser()->find();
        foreach ($Users as $User) {
            echo $User->name;
        }
        */
        $this->fetch('default');
    }
}
