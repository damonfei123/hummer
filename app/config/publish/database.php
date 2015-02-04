<?php
return array(
    #Mysql
    'db' => array(
        #Master
        'default' => array(
            'dsn'       => 'mysql:host=172.17.181.135;dbname=youqian',
            'username'  => 'root',
            'password'  => 'entsafe',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 30
            )
        ),
        #slave
        'slave' => array(
            'dsn'       => 'mysql:host=10.212.117.53;dbname=youqian',
            'username'  => 'dt_guest',
            'password'  => 'read!dt@guest',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET interactive_timeout=24*3600",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 30
            )
        ),
        'yq_cms' => array(
            'dsn'       => 'mysql:host=172.17.181.135;dbname=youqian_cms',
            'username'  => 'root',
            'password'  => 'entsafe',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 30
            )
        ),
        #反作弊
        'cheat' => array(
            'dsn'       => 'mysql:host=cq02-sw-dtdb01.cq02;dbname=anti_cheat',
            'username'  => 'RD_zhangyinfei',
            'password'  => 'rd_zyf_select',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 30
            )
        ),
        #反作弊从库
        'cheat_slave' => array(
            'dsn'       => 'mysql:host=10.58.185.13;dbname=anti_cheat',
            'username'  => 'RD_zhangyinfei',
            'password'  => 'rd_zyf_select',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 30
            )
        ),
        #hf
        'hf' => array(
            'dsn'       => 'mysql:host=172.17.181.135;dbname=hf',
            'username'  => 'root',
            'password'  => 'entsafe',
            'option'    => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'latin1'",
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_WARNING,
                \PDO::ATTR_TIMEOUT            => 30
            )
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
