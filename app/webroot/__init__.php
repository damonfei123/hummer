<?php
function CTX(){
    return end($GLOBALS['__SELF__CONTEXT']);
}

function L($sMsg,$sLevel = 'info')
{
    call_user_func(array(CTX()->Log, $sLevel), $sMsg);
}

function DB() {
    return CTX()->RDB;
}

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

function MEmpty($Model)
{
    return CTX()->RDB->isModelDataEmpty($Model);
}

function pr($mVar) {
    if (php_sapi_name() != 'cli') { echo "<pre>"; }
    if (is_object($mVar)) {
        echo $mVar;
    }else{
        var_export($mVar);
    }
}
