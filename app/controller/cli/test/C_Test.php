<?php
namespace App\controller\cli\test;

use App\system\controller\Cli_Base;

class C_Test extends Cli_Base {

    public function actionShow($aParam)
    {
        foreach(DB()->get('user u')
            ->select('u.*')
            ->join('data d on u.id = d.id')
            ->findMulti() as $M
        ){
            echo $M->id;
            echo $M->name;
        }
        $Users = DB()->getUser()->where(array('id between' => array(1,5)))->findMulti();
        DB()->getData()->find();
        foreach ($Users as $User) {
            echo $User->name;
        }
        pr($aParam);
        echo $this->fetch('show');
        echo $this->fetch('/test/test/show');
    }
}
