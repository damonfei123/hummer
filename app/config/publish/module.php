<?php
use Hummer\Component\Log\LogFactory;
use Hummer\Bundle\Framework\Bootstrap;

return array(
    array(
        'module' => 'Log',
        'class'  => 'Hummer\\Component\\Log\\LogFactory',
        'params' => array(
            array(
                'WebDeBug' => array('\@WebPage'),
                'File'     => array(
                    '\@File',
                    '/tmp/Hummer_http_Log_{date}/hummer_{level}_{date}.log'
                )
            ),
            LogFactory::LEVEL_ALL
        ),
        'run_mode' => Bootstrap::S_RUN_HTTP
    ),
    array(
        'module' => 'Log',
        'class'  => 'Hummer\\Component\\Log\\LogFactory',
        'params' => array(
            array(
                'File'  => array('\@File','/tmp/Hummer_cli_Log_{date}/hummer_{level}_{date}.log'),
                'STDIO' => array('\@STDIO')
            ),
            LogFactory::LEVEL_ALL
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
                'cache_lifetime'   => 120
            )
        ),
    ),
    array(
        'module' => 'RDB',
        'class'  => 'Hummer\\Component\\RDB\\ORM\\Factory',
        'params' => array(
            '@database.db',
            '@model',
            'App\\model',
            'Hummer\\Component\\RDB\\ORM\\Model\\Model',
            \Hummer\Component\RDB\ORM\AopPDO::$aAopPreExecCost
        ),
    ),
    array(
        'module' => 'Redis',
        'class'  => 'Hummer\\Component\\NoSQL\\Redis\\Redis',
        'params' => array('@database.redis'),
    ),
    array(
        'module' => 'Session',
        'class'  => 'Hummer\\Component\\Session\\SessionFactory',
        'params' => array(
            '\@DB',
            ':RDB',
            'default',
            array(
                'db'    => 'session',
                'key'   => 'k',
                'value' => 'v',
            )
        ),
    ),
    array(
        'module' => 'CacheFile',
        'class'  => 'Hummer\\Component\\Cache\\CacheFactory',
        'params' => array(
            '\@File',
            '/tmp/damon/'
        )
    ),
    #Lock With Redis
    array(
        'module' => 'Lock',
        'class'  => 'Hummer\\Component\\Lock\\LockFactory',
        'params' => array(
            '\@Redis',
            ':Redis'
        )
    ),
    #Lock With Cache -> File
    /*
    array(
        'module' => 'Lock',
        'class'  => 'Hummer\\Component\\Lock\\LockFactory',
        'params' => array(
            '\@Cache',
            ':CacheFile'
        )
    ),
    */
);
