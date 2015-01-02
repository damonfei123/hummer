<?php
namespace App\model;

use App\system\model\Model_Base;

class Model_User extends Model_Base{

    public function findDamon()
    {
        return $this->where(array('name' => 'damon'))->findMulti();
    }
}
