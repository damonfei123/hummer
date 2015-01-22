<?php
return array(
    'ActionInfo' => array(
        'db'          => 'cheat_slave',
        'table'       => 'action_info',
        'pk'          => 'auto_id',
        'model_class' => 'Model_ActionInfo',
        'item_class'  => 'Item_ActionInfo'
    ),
    'User2' => array(
        'table'       => 'user',
        'db'          => 'youqian',
        'item_class'  => 'Item_User',
        'model_class' => 'Model_User'
    ),
    'Test' => array(
        'pk'          => 'name,age',
    ),
    'IPLog' => array(
        'table'       => 'ip_log',
        'db'          => 'yq_cms',
    ),







    ////////////////hf//////////////
    'Invest' => array(
        'table'   => 'hf_invest',
        'db'          => 'hf',
    ),
    'Project'   => array(
        'table'       => 'hf_project',
        'db'          => 'hf',
    ),
    'User' => array(
        'db'          => 'hf',
        'table'       => 'hf_user',
        'item_class'  => 'Item_User',
        'model_class' => 'Model_User'
    ),
    'Interest' => array(
        'table'       => 'interest',
        'db'          => 'hf',
    ),
);
