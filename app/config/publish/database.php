<?php
return array(
    #Mysql
    'db' => array(
        #Master
        'default' => array(
            'dsn'       => 'mysql:host=172.17.181.135;dbname=damon',
            'username'  => 'root',
            'password'  => 'entsafe',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 3
            ),
            'aAopCallBack' => \Hummer\Component\RDS\AopPDO::$aAopPreExecCost
        ),
        #slave
        'default_slave' => array(
            'dsn'       => 'mysql:host=172.17.181.135;dbname=youqian',
            'username'  => 'root',
            'password'  => 'entsafe',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 3
            ),
            'aAopCallBack' => \Hummer\Component\RDS\AopPDO::$aAopPreExecCost
        ),
        'youqian' => array(
            'dsn'       => 'mysql:host=172.17.181.135;dbname=youqian',
            'username'  => 'root',
            'password'  => 'entsafe',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 3
            ),
            'aAopCallBack' => \Hummer\Component\RDS\AopPDO::$aAopPreExecCost
        ),
    ),

    #Redis
    'redis' => array(
        'pconnect'  => true,
        'server'    => array('127.0.0.1', 6379)
    )
);
