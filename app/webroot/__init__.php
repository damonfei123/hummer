<?php
function CTX(){
    return end($GLOBALS['__SELF__CONTEXT']);
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

function C($sCookie) {
    return CTX()->HttpRequest->getC($sCookie);
}
function P($mKeyOrKeys){
    return CTX()->HttpRequest->getP($mKeyOrKeys);
}
function G($mKeyOrKeys) {
    return CTX()->HttpRequest->getG($mKeyOrKeys);
}
function GP($mKeyOrKeys){
    return CTX()->HttpRequest->getGP($mKeyOrKeys);
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
