<?php
TypechoPlugin::instance(__TYPECHO_ADMIN_DIR__ . '/common.php')
->register(TypechoPlugin::HOOK, 'admin', 'test');

function test()
{
    echo 'hello world';
}
