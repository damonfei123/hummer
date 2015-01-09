<?php
return array(
    'ActionInfo' => array(
        'db'          => 'cheat',
        'table'       => 'action_info',
        'pk'          => 'auto_id',
        'model_class' => 'Model_ActionInfo',
        'item_class'  => 'Item_ActionInfo'
    ),
    'User' => array(
        'item_class'  => 'Item_User',
        'model_class' => 'Model_User'
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
);
