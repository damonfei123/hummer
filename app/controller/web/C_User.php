<?php
namespace App\controller\web;

use App\system\controller\Web_Base;

class C_User extends Web_Base{

    public function actionDefault()
    {
    }

    public function __after__()
    {
        echo "<br />after<br />";
    }
}
