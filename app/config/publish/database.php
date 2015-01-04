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
        'slave' => array(
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
        'backup' => array(
            'dsn'       => 'mysql:host=10.212.117.53;dbname=youqian',
            'username'  => 'dt_guest',
            'password'  => 'read!dt@guest',
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
    ),

    #Memcache
    'memcache' => array(
        'host' => '172.17.181.135',
        'port' => 11211,
    ),
);
