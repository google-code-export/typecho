<?php

/** Typecho_Config */
require_once 'Typecho/Config.php';

interface Typecho_Config_Able
{
    /**
     * 设置配置
     * 
     * @access public
     * @param mixed $config 配置数据
     * @return void
     */
    public static function setConfig($config);
    
    /**
     * 获取配置
     * 
     * @access public
     * @return Typecho_Config
     */
    public static function getConfig();
}
