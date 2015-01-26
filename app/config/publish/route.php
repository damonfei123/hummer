<?php
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;
use Hummer\Component\Route\CallBack;
use Hummer\Component\Http\HttpRequest;

return array(
    'http' => array(
        '#api.php#' => array(
            array('Hummer\Component\Route\Mode', 'Http_Page'),
            'App\\controller\\api\\',
            'C_',
            ''
        ),
        //自定义路由
        '#index.php#' => array(
            function(
                $REQ,
                $RES,
                $sControllerPath,
                $sControllerPre,
                $sActionPre,
                $HitMode,
                array $aDefaultCA = array('main', 'default')
            ){
                $sControllerPathPre = sprintf('%s%s%s%s%s',
                    $sControllerPath,
                    $REQ->getG('m'),
                    '\\',
                    $sControllerPre,
                    ucfirst($REQ->getG('c'))
                );
                HttpRequest::$FORGE_REQUEST_URI = sprintf('/%s/%s/%s',
                    $REQ->getG('m'), $REQ->getG('c'), $REQ->getG('a'));
                $sAction = sprintf('%s%s', $sActionPre, ucfirst($REQ->getG('a')));
                $CallBack = new CallBack();
                $CallBack->setCBObject($sControllerPathPre, $sAction);

                return $CallBack;
            },
            'App\\controller\\web\\',
            'C_',
            'action'
        ),
        array(
            array('Hummer\Component\Route\Mode', 'Http_Page'),
            'App\\controller\\web\\',
            'C_',
            'action'
        ),
    ),
    'cli' => array(
        array(
            array('Hummer\Component\Route\Mode', 'Http_Cli'),
            'App\\controller\\cli\\',
            'C_',
            'action'
        )
    ),
);
