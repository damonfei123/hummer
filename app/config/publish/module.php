<?php
use Hummer\Component\Log\Logger;

return array(
    array(
        'module' => 'Log',
        'class'  => 'Hummer\\Component\\Log\\Logger',
        'params' => array(
            array(
                'WebDeBug' => array('\@WebPage'),
                'File'     => array('\@File','/tmp/Hummer_Log_{date}/hummer_{level}_{date}.log')
            ),
            Logger::LEVEL_ALL
        ),
    ),
    array(
        'module' => 'RDB',
        'class'  => 'Hummer\\Component\\RDS\\Factory',
        'params' => array(
            '@database',
            '@model',
            'App\\model',
            'Hummer\\Component\\RDS\\Model\\Model',
            \Hummer\Component\RDS\AopPDO::$aAopPreExecCost
        ),
    ),
    array(
        'module' => 'Template',
        'class'  => 'Hummer\\Component\\Template\\TemplateAdaptor',
    ),
);
