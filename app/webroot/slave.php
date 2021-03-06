<?php
namespace Hummer;

//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

use Hummer\Bundle\Framework\Bootstrap;
use Hummer\Component\Configure\Configure;
use Hummer\Component\Route\RouteErrorException;

//header('Access-Control-Allow-Origin: *');

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
        CONFIG_DIR . 'pre',
        CONFIG_DIR . 'publish'
    ),
    'development'
);
Bootstrap::setDefaultErrorPage();
require '__init__.php';

$B->run();
/*
try{
    $B->run();
}catch(\SmartyException $E){
    CTX()->Log->fatal($E->getMessage());
}
*/
//$data = xhprof_disable();
//pr($data);
