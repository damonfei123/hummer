<?php
namespace Hummer;

use Hummer\Framework\Bootstrap;
use Hummer\Component\Configure\Configure;

define('ROOT_DIR',       dirname(dirname(__DIR__)));
define('APP_DIR',        ROOT_DIR . '/app/');
define('FW_DIR',         ROOT_DIR . '/vendor/');
define('HM_DIR',         FW_DIR   . '/hummer/src/');
define('VIEW_DIR',       APP_DIR  . '/view/');
define('MODEL_DIR',      APP_DIR  . '/model/');
define('CONFIG_DIR',     APP_DIR  . '/config/');
define('CONTROLLER_DIR', APP_DIR  . '/controller/');

require ROOT_DIR . '/vendor/autoload.php';

Bootstrap::setHandle();
$B = new Bootstrap(
    Configure::factory(
        '@PHP',
        CONFIG_DIR . 'development',
        CONFIG_DIR . 'publish'
    ),
    'development'
);
require '__init__.php';
/*
$aUser = DB()->get('user u')
    ->where(array('parent_id' => 40464))
    ->join('tec_daily_data on u.id = tec_daily_data.user_id','L')
    ->select('u.name')
    ->limit(2)
    ->group('u.id')
    ->find();
*/
//$aUser = $Factory->getUser()->where(152023)->delete();
/*
$User = DB()->getUser()
    ->select('id,name')
    ->findMulti();

foreach ($User as $user) {
    echo $user->name . "\n";
}
*/
//$User = RDB()->getUser()->findCustom(array('name like' => '%xx%'));
/*
$User = RDB()->getUser()->select('name')
    ->findMulti(array(-1 => 'or','id between' => array(1,2), array('id' => 3)));
var_export($User);
*/
/*
$User = RDB()->getUser()->explain(1);
var_export($User);
*/
//RDB()->getUser()->save(array('name' => 'save'));
/*
$User = RDB()->getUser()->find(1);
var_export($User);
*/
/*
$User = RDB()->getUser()
    ->select('id,name')
    ->findMulti(array('id between' => array(1,2)));

foreach ($User as $id => $Data) {
    //$Data->name = 'haha_'.$Data->id;
    //$Data->update();
    //var_export($Data);
    echo $Data;
}
*/
//$User->name = 'xxxxxxx';
//echo $User['name'];
//$User->update();
//$User = RDB()->getUser()->data(array('name' => 'wwwi'))->update(1);
//$User = RDB()->getUser()->exec('insert into user(name) values (?)', array('xxx'));
//$User = RDB()->getUser()->find(1);
//var_export($User);
