<?php
namespace Hummer\Framework;

use Hummer\Component\Context\Context;

class C_Base{

    protected $Context;
    protected $Config;

    public function __construct()
    {
        $this->Context      =  Context::getInst();
        $this->Config       = $this->Context->Config;
        $this->Log          = $this->Context->Log;
        $this->HttpRequest  = $this->Context->HttpRequest;
        $this->HttpResponse = $this->Context->HttpResponse;
    }

    public function __destruct()
    {
    }
}
