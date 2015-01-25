<?php
return array(
    'http' => array(
        '#index.php|dev.php#' => array(
            array('Hummer\Component\Route\Mode', 'Http_Page'),
            'App\\controller\\web\\',
            'C_',
            'action'
        ),
        '#api.php#' => array(
            array('Hummer\Component\Route\Mode', 'Http_Page'),
            'App\\controller\\api\\',
            'C_',
            ''
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
