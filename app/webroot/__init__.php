<?php
function CTX(){
    return end($GLOBALS['__SELF__CONTEXT']);
}

function RDB()
{
    return CTX()->RDB;
}
