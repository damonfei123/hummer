<?php
return array(
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
        'run_mode' => 'cli'
    ),
);
