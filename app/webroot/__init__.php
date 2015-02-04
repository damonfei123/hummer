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
 *  获取DB(PDO)
 **/
function DB() {
    return CTX()->RDB;
}

/**
 *  获取Config模块
 **/
function CFG()
{
    return CTX()->Config;
}

function Redis()
{
    return CTX()->Redis;
}

function C($sCookie=null) {
    return CTX()->HttpRequest->getC($sCookie);
}
function P($mKeyOrKeys=null){
    return CTX()->HttpRequest->getP($mKeyOrKeys);
}
function G($mKeyOrKeys=null) {
    return CTX()->HttpRequest->getG($mKeyOrKeys);
}
function GP($mKeyOrKeys=null){
    return CTX()->HttpRequest->getGP($mKeyOrKeys);
}
function SRV($mKeyOrKeys=null)
{
    return CTX()->HttpRequest->getSRV($mKeyOrKeys);
}

function go($sUrl=null)
{
    $C = CTX();
    if (is_null($sUrl)) {
        $sReferer = $C->HttpRequest->getHeader('Referer');
        $sUrl     = is_null($sReferer) ? '/' : $sReferer;
    }
    $C->HttpResponse->setHeaderRedirect($sUrl);
}

function View()
{
    return CTX()->Template;
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
