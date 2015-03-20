<?php
namespace App\controller\web\test;

use Hummer\Util\Page\Page;
use Hummer\Util\Image\Image;
use Hummer\Util\Image\Verify;
use Hummer\Util\File\Download;
use Hummer\Util\File\FileUpload;
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Str;
use App\system\controller\Web_Base;
use Hummer\Component\Filesystem\Dir;
use Hummer\Util\Validator\Validator;
use Hummer\Component\Filesystem\File;
use Hummer\Component\Route\RouteErrorException;
use Hummer\Component\Context\InvalidClassException;

class C_Test extends Web_Base{

    public function __before__()
    {
        $this->HttpResponse->noCache();
        $this->HttpResponse->charset();
    }

    public function actionTest()
    {
        $Model = D('User');
        if ($Model->create(array('n' => 'data', 'age' => 12))) {
            pr($Model->add());
            echo $Model->getError();
        }
        //pr(CFG('id'));
        //pr(CFG('id.name'));
        //pr(D('User')->find());
        //echo 'xxxxxxxx';
        $this->display(null);
        return;
        //pr(DB()->getUser()->find()->id);
        echo CTX()->_sControllerName;
        echo '<br />';
        echo CTX()->_sActionName;
        //$this->display();//加载
        $this->display('');//不加载
        //echo $this->fetch('/test/test/default');
        echo $this->fetch(null);
        //$this->display(null);//不加载
    }

    public function actionValidator()
    {
        $validator = new Validator(
            array(
                'ip'        => '10.1.1.1',
                'url'       => 'http://www.baidu.com',
                'salary'    => '1.4234123412342e10',
                'yesOrNo'   => true,
                'age'       => 100,
                'sex'       => '0',
                'school'    => '江南大学',
                'type'      => 1,
                'isnumber'  => 112312,
                'mobile'    => 18621719751,
                'email'     => 'xxe317030876@qq.com',
                'unique'    => '1',
                'qq'        => '123123',
            ),
            array(
                array('ip','ip'),
                array('url','url'),
                array('yesOrNo','boolean'),
                array('qq',     'qq'),
                array('sex',    'require'),
                array('isnumber','number'),
                array('salary','float'),
                array('mobile', 'mobile'),
                array('email',  'email'),
                array('type',   'enum', array(1,2)),
                array('age',    'int', 'max'=>100, 'min' => 10),//int的max,min代表大小
                array('school', 'string', 'max' => 100, 'min' => 2),//string的max和min代表长度
                array('unique', 'express', MEmpty(DB()->getUser()->find(1))),
                array('isnumber','regex','#^1\d+$#'),
            ),
            array(
                'ip'  => array(
                    'ip' => '{key}:{value}不是ip'
                ),
                'url'  => array(
                    'url' => '{key}:{value}不是url'
                ),
                'salary'  => array(
                    'float' => '{key}:{value}不是float'
                ),
                'isnumber'  => array(
                    'number' => '{key}:{value}不是number'
                ),
                'qq'  => array(
                    'qq' => '{key}:{value}不是一个QQ号码'
                ),
                'unique'  => array(
                    'express' => '{key}要求唯一，但在数据库里已经存在:{value}'
                ),
                'email'  => array(
                    'email' => '{key}不是一个有效的邮箱帐号:{value}'
                ),
                'number' => array(
                    'regex' => '{key}非数字格式,{value}',
                ),
                'mobile' => array(
                    'mobile' => '手机号{value}格式不对',
                ),
                'type'   => array(
                    'enum'  => '{key}的值不对,传的值为{value}'
                ),
                'school' => array(
                    'string' => '学校必须为字符串',
                    'max'    => '{key}最大长度为{rule}, 现在长度为{value}',
                    'min'    => '{key}最小长度为{rule}, 现在长度为{value}',
                ),
                'yesOrNo' => array(
                    'boolean' => '姓名必须为boolean类型'
                ),
                'sex'   => array(
                    'require' => '{key}不得为空'
                ),
                'age'   => array(
                    'int'   => '{key}必须为整形',
                    'max'   => '{key}不得大于{rule},传的值为{value}',
                    'min'   => '{key}不得小于{rule},传的值为{value}'
                )
            )
        );
        echo $validator->validate();
    }

    /**
     *  验证码
     **/
    public function actionV()
    {
        $V = new Verify();
        $V->setFontSize(20);
        $V->setCodeType(2);
        $V->setCodeLen(6);
        $V->setWidth(130);
        $V->setHeight(50);
        $V->setLineCount(13);
        $V->setStringPad('++');
        $V->create();
        CTX()->Log->aWriter['WebDeBug']->setDisable();
        $this->display(null);
    }

    public function __after_actionV__()
    {
    }

    public function actionDefault()
    {
        /**
         *  File Cache
         **/
        //$Cache = CTX()->CacheFile;
        //$Cache->store('user',array(1,3), 86400);
        //pr($Cache->fetch('user'));
        //CTX()->getInst()->Log->aWriter['WebDeBug']->disable();//disable web debug
        //Download::download('/home/zhangyinfei/project/test/data/excel.xlsx');
        //Download::download('/home/zhangyinfei/project/hummer/app/webroot/index.php');
        //$Session = CTX()->Session;
        //$Session::set('name', 'yinfei_damon_fei');
        //$Session::get('name');
        //$Session::del('name');
        /*
        $User = DB()->getUser()->select('name')->findMulti();
        foreach ($User as $user) {
            echo $user;
        }
        */
        #查询
        DB()->get('user')->where(['id' => 2]);
        echo DB()->get('user')->find();

        DB()->get('user u')->find();

        DB()->get('user u')->select('id')->find();
        DB()->getUser()->where(array('id BETWEEN' => array(1,12)))->findMulti();
        DB()->getUser()->where(array('id BETWEEN' => array(1,12)))->findCustom();
        DB()->getUser()->where(array('id in' => array(1,2)))->findMulti();
        DB()->getUser('u')->left('hf_user u2 on u.id = u2.id')->findMulti();

        foreach(DB()->getUser()->where(array('name like' => '%d%'))->findMulti() as $D){
            echo 'name:' . $D;
        };

        /*
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
        echo DB()->get('data d')->find();
        echo DB()->get('user')->find();
        foreach(DB()->get('user u')
            ->select('u.*')
            //->where('u.id between 6 and 8')
            ->where(array('u.id between' => array(6,8)))
            ->join('data d on u.id = d.id')
            ->findMulti() as $M
        ){
            echo $M->id . '=>' . $M->name;
            echo "<br/>";
        }

        $Users = DB()->getUser('u', 'default_slave')->query('select count(*) as num from user');
        $Users = DB()->getUser('u', 'default_slave')
            ->query('select * from user where id = ? ', array(40464));
        //$Users = DB()->getUser('u')->exec('delete from user where id < 20');
        //$Users = DB()->getUser('u')->exec('delete from user where id < ?' ,array(21));

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

        $Items = DB()->getUser()->findMulti();
        foreach ($Items as $Item) {
            if ($Item->isDamon()) {
                echo 'Yes';
            }
        }

        $Damons = DB()->getUser()->findDamon();
        foreach ($Damons as $Damon) {
            echo sprintf('%s->%s',$Damon->auto_id, $Damon->name);
        }

        //当一张表主键为多列时，可以用下面这种形式以主键形式查找
        $M = DB()->getTest()->where(array('damon', 12))->select('age,name,detail')->find();
        DB()->getTest()->where(array('damon', 12))->select('age,name,detail')->find();
        DB()->getTest()->where(array('damon', 12))->delete();

        ///////程序读写分离,线上环境一定要注意从库不要有写权限///////////////
        //默认走配置的(主)库
        echo DB()->get('user')->find();
        //查询的可以指定slave查询,增删改可以走默认主库,实现主从分离,特别是大量的读操作
        echo DB()->get('user u', 'default_slave')
            ->where(array('id BETWEEN' => array(1,500000)))
            ->findCount();
        //或者
        echo DB()->getUser('u', 'default_slave')
            ->where(array('id BETWEEN' => array(1,500000)))
            ->findCount();


        */
        #添加
        //echo DB()->getData()->data(array('age' => rand(0,100)))->save();
        //echo DB()->getData()->add(array('age' => 12));
        /*
        //为了效率，这里采用values(?,?),(?,?),(?,?)的形式，多项数据一条sql插件
        $aBatchData = array(
            array(
                'name' => "'data2'",
                'age'  => 11
            ),
            array(
                'name' => 'data',
                'age'  => 12
            ),
            array(
                'name' => "'data9'",
                'age'  => 15
            ),
        );
        DB()->getUser()->batchSave($aBatchData, 2);
        */

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


        //同一Model调用多次方法，如分页需要用到  ---start
        //one
        /*
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


        //$this->timeLimit(1);
        //$this->memLimit();

        //Redis
        $Redis = Redis();
        $Redis->set('xx','xxxx');

        //分页类
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

        #图片处理:缩放、裁切、水印
        /*
        $image = new Image(
            "/home/zhangyinfei/project/test/data/file/1419907292.jpg",
            1,
            "300",
            "500",
            "/home/zhangyinfei/project/test/data/file/resize.jpg"
        ); //使用图片缩放功能
        */
        /*
        $image = new Image(
            "/home/zhangyinfei/project/test/data/file/1419907292.jpg",
            2,
            "0,0",
            "50,50",
            "/home/zhangyinfei/project/test/data/file/cop.jpg"
        ); //图片裁剪
        */
        /*
        $image = new Image(
            "/home/zhangyinfei/project/test/data/file/1419907292.jpg",
            3,
            "/home/zhangyinfei/project/test/data/file/cop.jpg",
            "0",
            "/home/zhangyinfei/project/test/data/file/water.jpg"
        ); //图片裁剪
        $image->outimage();
        */

        //$a = File::getFileToArr('/home/zhangyinfei/project/hummer/app/webroot/index.php');
        //$a = File::getCToArr('/home/zhangyinfei/project/hummer/app/webroot/index.php');
        //$a   = Dir::showList('/home/zhangyinfei/project/hummer/app/webroot');
        //$a   = Dir::showList('/home/zhangyinfei/project/hummer/app/webroot',true);
        //pr($a);

        /**
         *  File Cache
         **/
        //$Cache = CTX()->CacheFile;
        //$Cache->store('user',array(1,3), 86400);
        //$Cache->store('user',CTX()->Redis);//存储一天
        //var_export($Cache->fetch('user'));
        //var_export(unserialize(serialize(CTX()->Redis)));

        //multi assign
        $this->assign(array(
            'Name' => 'damon',
            'age'  => 1,
            'FirstName' => array('John', 'Mary', 'James', 'Henry')
        ));
        //assign single
        $this->assign('LastName', array('Doe', 'Smith', 'Johnson', 'Case'));
        //echo $this->fetch('show');//don't display
        //echo $this->fetch('/test/test/show');

        //控制模板
        //self::disableTpl();
        //self::enableTpl();
        //$this->display('show');
        //$this->display('/test/test/show');
        //$this->display();         //自动模板 -> 路由  test/test/default

        //$this->display();//加载
        //$this->display('');//不加载
        //$this->display(null);//不加载

        //告诉浏览器内部出错
        //$this->HttpResponse->setStatus(500);

        /**
         *  断开HTTP链接，响应完用户请求后可以做一些事后操作，比如日志
         **/
        //$this->HttpResponse->httpFastClose();
        //这里写日志，防止日志影响用户响应速度.....
    }

    public function actionDefault_POST()
    {
        //文件上传处理
        $File = new FileUpload(
            'file',
            '/home/zhangyinfei/project/test/data/file',
            array('ext' => 'image,txt', 'max' => '4M')
            //array(__NAMESPACE__.'\\C_Test','saveFileName')#自定义文件存储名称
        );
        //上传
        pr($File->upload());
    }

    public function actionAjax_POST()
    {
        echo 'Ajax Post';
    }

    /**
     *  如果上传的文件需要自定义文件名，可以直接外部定义
     **/
    public static function saveFileName($aFileInfo)
    {
        return time();
    }

    /**
     *  After
     **/
    public function __after__()
    {
        echo "<br>After...</br>";
        echo Str::sub('我是中国a人人人人', 5);
    }
}
