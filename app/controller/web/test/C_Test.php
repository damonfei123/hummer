<?php
namespace App\controller\web\test;

use App\system\controller\Web_Base;
use Hummer\Component\Util\Page\Page;
use Hummer\Component\Util\File\File;

class C_Test extends Web_Base{

    public function __before__()
    {
        $this->HttpResponse->noCache();
        $this->HttpResponse->charset();
    }

    public function actionDefault()
    {
        echo DB()->get('data d')->find();
        echo DB()->get('user')->find();
        foreach(DB()->get('user u')
            ->select('u.*')
            ->join('data d on u.id = d.id')
            ->findMulti() as $M
        ){
            echo $M->id . '=>' . $M->name;
            echo "<br/>";
        }
        #添加
        //echo DB()->getData()->data(array('age' => 12))->save();
        //echo DB()->getData()->add(array('age' => 12));

        #删除
        //DB()->getData()->where(array('id BETWEEN' => array(7,9)))->delete();
        //DB()->getData()->delete(2);

        #事务transaction
        /*
        $PDO = DB()->getData()->begin();
        $iOne = DB()->get('user')->data(array('name' => 'damon'))->save();
        $iTwo = DB()->getData()->data(array('age' => 12))->save();
        if ($iOne && $iTwo) {
            $PDO->commit();
        }else{
            $PDO->rollback();
        }
        */
        /*
        #查询
        DB()->get('user')->find();
        DB()->get('user u')->select('id')->find();
        DB()->getUser()->where(array('id BETWEEN' => array(1,12)))->findMulti();
        DB()->getUser()->where(array('id BETWEEN' => array(1,12)))->findCustom();
        DB()->getUser()->where(array('id in' => array(1,2)))->findMulti();
        DB()->getUser('u')->left('user u2 on u.id = u2.id')->findMulti();

        foreach(DB()->getUser()->where(array('name like' => '%d%'))->findMulti() as $D){
            echo $D;
        };

        pr(DB()->getUser()->where(array('id BETWEEN' => array(1,4)))->explain());

        DB()->getUser()->where("name = 'damon'")->find();
        DB()->getUser()->where("id = 5")->find();

        $Datas = DB()->get('user u')
            ->group('u2.id')
            ->left('user u2 on u.id = u2.id')
            ->having('u2.id >= 6')
            ->findMulti();
        foreach ($Datas as $Data) {
            echo $Data->id . '=>' . $Data->name . '<br />';
        }
        */

        #exists
        $Exists = clone DB()->getData()->where('user.id = data.id');
        $Exists = DB()->getUser()
            ->where(array('id between' => array(3,10)))
            ->exists($Exists)
            ->findMulti();
        foreach ($Exists as $E) {
            echo $E->id;
            echo "<br />";
        }


        //同一Model调用多次方法，如分页需要用到  ---start
        //one
        $User = DB()->getUser()->where(array('id between' => array(1,6)));
        $User->enableMulti();
        $User->findCount();
        $User->findMulti();
        $User->disableMulti();
        DB()->getUser()->findMulti();

        //two
        $User = DB()->getUser()->where(array('id between' => array(1,6)));
        $User2 = clone $User;
        $User->findCount();
        $User2->findMulti();
        DB()->getUser()->findMulti();

        //Session
        $Session = CTX()->Session;
        $Session->set('name', 'damon');
        echo $Session->get('name');

        //Redis
        $Redis = Redis();
        $Redis->set('xx','xxxx');

        //Page
        $Page = new Page(1);
        echo $Page->getPage(
                DB()->getUser()
                    ->select('u2.id,u2.name')
                    ->left('user u2 on user.id = u2.id')
                    ->where(array('u2.id BETWEEN' => array(1,30))),
                $aList);

        foreach ($aList as $data) {
            echo $data;
        }

        //echo $this->fetch('show');
        //echo $this->fetch('/test/test/show');

        $this->display('show');
        //$this->display('/test/test/show');
        //$this->display();         //test/test/default
        //$this->display(null);     //disable template
    }

    public function actionDefault_POST()
    {
        $File = new File(
            'file',
            '/home/zhangyinfei/project/test/data/file/',
            array('ext' => 'image,txt')
        );
        //上传
        pr($File->upload());
    }
}
