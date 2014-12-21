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
$B->run();
