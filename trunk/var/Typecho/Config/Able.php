<?php

require_once 'Typecho/Config.php';

interface Typecho_Config_Able
{
    public static function setConfig(Typecho_Config $config);
    
    public static function getConfig();
}
