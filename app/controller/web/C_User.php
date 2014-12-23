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
        $Template = View();

        $Template->setTemplateDir(VIEW_DIR);
        $Template->setCompileDir('/home/zhangyinfei/project/test/data/templates_c');
        $Template->setCacheDir('/home/zhangyinfei/project/test/data/cache');
        $Template->setConfigDir('/home/zhangyinfei/project/test/data/config');

        //echo $Template::SMARTY_VERSION;
        $Template->force_compile = true;
        //$Template->debugging = true;
        $Template->caching = true;
        $Template->cache_lifetime = 120;

        $Template->assign("LastName", array("Doe", "Smith", "Johnson", "Case"));

        $User = RDB()->getUser()->findMulti();
        foreach ($User as $user) {
            echo $user->id;
            echo $user->name;
        }


        $Template->display('index.tpl');
    }
}
