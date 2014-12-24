<?php
namespace App\controller\web\test;

use App\system\controller\Web_Base;
use Hummer\Component\Page\Page;

class C_Test extends Web_Base{

    public function actionDefault()
    {
        //DB()->getUser2()->limit(100)->group('parent_id')->findMulti();
        $Page = new Page($this->HttpRequest, 10);
        echo $Page->getPage(DB()->get('user u2')->select('u.*')->join('user u on u2.id = u.id'), $aList);

        foreach ($aList as $key) {
            echo $key;
            echo "<br />";
        }
    }
}
