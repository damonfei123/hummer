<?php
namespace App\System\controller;

use Hummer\Framework\C_Web;

class Web_Base extends C_Web {
    public function __before__()
    {
        echo 'before by extends<br />';
    }
}
