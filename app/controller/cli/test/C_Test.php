<?php
namespace App\controller\cli\test;

class C_Test{

    public function actionShow($aParam)
    {
        DB()->getUser()->find(1);
    }

}
