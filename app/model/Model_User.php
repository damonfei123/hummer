<?php
namespace App\model;

use App\system\model\Model_Base;

class Model_User extends Model_Base{

    protected $_validator = array(
        array('name','require','姓名不能为空'),
        array('name','unique','姓名已经存在', self::MODEL_INSERT),
        array('name', 'string', 'max' => 100, 'min' => 4, array(
            'min' => '长度最小为{rule}',
            'max' => '长度过长',
            'string' => '类型不对'
        )),
        array('age','require','请填写年龄'),
        array('age','int', 'min' => 10, 'max' => 100, array(
            'max' => '年龄不得超过{rule}',
            'min' => '年龄不得小于{rule}',
            'int' => '年龄必须为数字, {value}'
        )),
    );

    protected $_auto = array(
        array('age','md5', 'function'),
        array('age','getAge', 'callback'),
    );

    protected $_map = array(
        'n' => 'name'
    );

    public function getAge()
    {
        return 10;
    }

    public function findDamon()
    {
        return $this->where(array('name' => 'damon'))->findMulti();
    }
}
