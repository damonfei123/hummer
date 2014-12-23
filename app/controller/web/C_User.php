<?php
namespace App\controller\web;

use App\system\controller\Web_Base;

class C_User extends Web_Base{

    public function actionDefault()
    {

        $User = RDB()->getUser()->findMulti();
        foreach ($User as $user) {
            echo $user->id;
            echo $user->name;
        }
        $this->assign(array('LastName' => 'Damon'));
        $this->display();
    }

    public function __after__()
    {
        echo "<br />after<br />";
    }
}
