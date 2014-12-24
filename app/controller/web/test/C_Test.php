<?php
namespace App\controller\web\test;

use App\system\controller\Web_Base;

class C_Test extends Web_Base{

    public function actionDefault()
    {
        DB()->getUser2()->limit(100)->group('parent_id')->findMulti();
    }
}
