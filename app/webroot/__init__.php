<?php
/**
 *  Register Event
 **/
\Hummer\Util\HttpCall\Event_Register::register_All(true);
\Hummer\Component\NoSQL\Redis\Event_Register::register_All(true);
\Hummer\Component\NoSQL\Memcache\Event_Register::register_All(true);

/**
 *  所有模块挂在Context下面，使用CTX方法可以获取module.php里定义的模块
 **/
function CTX(){
    return end($GLOBALS['__SELF__CONTEXT']);
}

/**
 *  快速打印Log的方式
 **/
function L($sMsg,$sLevel = 'info')
{
    call_user_func(array(CTX()->Log, $sLevel), $sMsg);
}

/**
 *  快速打印错误日志
 **/
function Err($sMsg)
{
    call_user_func(array(CTX()->Log, warn), $sMsg);
}

/**
 *  获取DB(PDO)
 **/
function DB() {
    return CTX()->RDB;
}

/**
 *  数据库基本模型
 **/
function M($sTable, $sDB='') {
    return DB()->get($sTable, $sDB);
}
/**
 *  用户自定义模型
 **/
function D($sModel, $sDB='')
{
    $sFunc = sprintf('%s%s', 'get', $sModel);
    return DB()->$sFunc(null, $sDB);
}

/**
 *  获取Config模块
 **/
function CFG($sKey = null)
{
    $Config = CTX()->Config;
    return $sKey ? $Config->get($sKey) : $Config;
}

function Redis()
{
    return CTX()->Redis;
}

function Lock()
{
    return CTX()->Lock;
}

function View()
{
    return CTX()->Template;
}

/**
 *  获取Cookie
 **/
function C($sCookie=null) {
    return CTX()->HttpRequest->getC($sCookie);
}

/**
 *  获取$_POST
 **/
function P($mKeyOrKeys=null){
    return CTX()->HttpRequest->getP($mKeyOrKeys);
}

/**
 *  获取$_GET
 **/
function G($mKeyOrKeys=null) {
    return CTX()->HttpRequest->getG($mKeyOrKeys);
}

/**
 *  获取$_GET + $_POST
 **/
function GP($mKeyOrKeys=null){
    return CTX()->HttpRequest->getGP($mKeyOrKeys);
}

/**
 *  获取$_SERVER
 **/
function SRV($mKeyOrKeys=null)
{
    return CTX()->HttpRequest->getSRV($mKeyOrKeys);
}

/**
 *  页面跳转
 **/
function go($sUrl=null)
{
    $C = CTX();
    if (is_null($sUrl)) {
        $sReferer = $C->HttpRequest->getHeader('Referer');
        $sUrl     = is_null($sReferer) ? '/' : $sReferer;
    }
    $C->HttpResponse->setHeaderRedirect($sUrl);
}

/**
 *  判断DB查询的是否为空
 **/
function MEmpty($Model)
{
    return CTX()->RDB->isModelDataEmpty($Model);
}

/**
 *  测试打印用的方法
 **/
function pr($mVar) {
    if (php_sapi_name() != 'cli') { echo "<pre>"; }
    if (is_object($mVar)) {
        echo $mVar;
    }else{
        var_export($mVar);
    }
}
