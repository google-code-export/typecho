<?php

/** Typecho_Config */
require_once 'Typecho/Config.php';

interface Typecho_Config_Able
{
    /**
     * 设置配置
     * 
     * @access public
     * @param Typecho_Config $config 配置对象
     * @return void
     */
    public static function setConfig(Typecho_Config $config);
    
    /**
     * 获取配置
     * 
     * @access public
     * @return Typecho_Config
     */
    public static function getConfig();
}
