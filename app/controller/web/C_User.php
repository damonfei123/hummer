<?php
namespace App\controller\web;

use App\system\controller\Web_Base;

class C_User extends Web_Base{

    public function __after__()
    {
        echo "<br />after<br />";
    }

    public function actionDefault()
    {
        $this->Log->info('来个中文');
        pr(RDB()->getUser()->explain(array('name like' => '%xx%')));
        //pr(RDB()->getUser()->explain(array('id between' => array(1,4))));
        //echo RDB()->getUser()->data(array('name' => 'damon'))->save();
    }
}

