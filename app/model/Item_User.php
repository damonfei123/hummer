<?php
namespace App\model;

use App\system\model\Item_Base;

class Item_User extends Item_Base {

    public function isDamon()
    {
        return $this->name == 'damon';
    }
}
