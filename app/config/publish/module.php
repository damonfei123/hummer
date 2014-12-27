<?php
use Hummer\Component\Log\Logger;
use Hummer\Framework\Bootstrap;

return array(
    array(
        'module' => 'Log',
        'class'  => 'Hummer\\Component\\Log\\Logger',
        'params' => array(
            array(
                'WebDeBug' => array('\@WebPage'),
                'File'     => array(
                    '\@File',
                    '/tmp/Hummer_http_Log_{date}/hummer_{level}_{date}.log'
                )
            ),
            Logger::LEVEL_ALL
        ),
        'run_mode' => Bootstrap::S_RUN_HTTP
    ),
    array(
        'module' => 'Log',
        'class'  => 'Hummer\\Component\\Log\\Logger',
        'params' => array(
            array(
                'File'   => array('\@File','/tmp/Hummer_cli_Log_{date}/hummer_{level}_{date}.log'),
                'STDIO'  => array('\@STDIO')
            ),
            Logger::LEVEL_ALL
        ),
        'run_mode' => Bootstrap::S_RUN_CLI
    ),
    array(
        'module' => 'Template',
        'class'  => 'Hummer\\Component\\Template\\TemplateAdaptor',
        'params' => array(
            VIEW_DIR,
            '/home/zhangyinfei/project/test/data/templates_c',
            '/home/zhangyinfei/project/test/data/cache',
            '/home/zhangyinfei/project/test/data/config',
            'options' => array(
                'force_compile'    => true,
                'caching'          => true,
                'cache_lifetime'   => 120,
            )
        ),
    ),
    array(
        'module' => 'RDB',
        'class'  => 'Hummer\\Component\\RDS\\Factory',
        'params' => array(
            '@database.db',
            '@model',
            'App\\model',
            'Hummer\\Component\\RDS\\Model\\Model',
            \Hummer\Component\RDS\AopPDO::$aAopPreExecCost
        ),
    ),
    array(
        'module' => 'Redis',
        'class'  => 'Hummer\\Component\\Redis\\Redis',
        'params' => array('@database.redis'),
    ),
    array(
        'module' => 'Session',
        'class'  => 'Hummer\\Component\\Session\\Session',
        'params' => array(),
    ),
);
