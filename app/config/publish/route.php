<?php
return array(
    'http' => array(
        array(
            array('Hummer\Component\Route\Mode', 'Http_Page'),
            'App\\controller\\web\\',
            'C_',
            'action'
        )
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
