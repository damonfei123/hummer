<?php
namespace App\controller\web\test;

use App\system\controller\Web_Base;
use Hummer\Component\Page\Page;

class C_Test extends Web_Base{

    public function __before__()
    {
        $this->HttpResponse->noCache();
        $this->HttpResponse->charset();
    }

    public function actionDefault()
    {
        $Redis = Redis();
        $Redis->set('xx','xxxx');
        $Page = new Page($this->HttpRequest, 1);
        echo $Page->getPage(
                DB()->getUser()
                    ->select('u2.id,u2.name')
                    ->left('user u2 on user.id = u2.id')
                    ->where(array('u2.id BETWEEN' => array(1,30))),
                $aList);

        foreach ($aList as $data) {
            echo $data;
        }
    }
}
