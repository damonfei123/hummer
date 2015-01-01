<?php
return array(
    'ActionInfo' => array(
        'table'       => 'action_info',
        'db'          => 'default',
        'pk'          => 'auto_id',
        'model_class' => 'Model_ActionInfo',
        'item_class'  => 'Item_ActionInfo'
    ),
    'User' => array(
        'model_class' => 'Model_User'
    ),
    'User2' => array(
        'table'       => 'user',
        'db'          => 'youqian',
        'item_class'  => 'Item_User',
        'model_class' => 'Model_User'
    ),
);
