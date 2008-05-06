<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
 */

/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

/**
 * 执行模块
 *
 * @package Widget
 */
class DoWidget extends TypechoWidget
{
    /**
     * 排除抽象类
     * 
     * @param string $value 输入数据
     * @param boolean
     */
    public function noAbstract($value)
    {
        return ($value != 'DoPost' && $value != 'ContentsPost');
    }

    /**
     * 入口函数,初始化路由器
     *
     * @access public
     * @return void
     */
    public function render()
    {
        /** 验证路由地址 **/
        $validator = new TypechoValidation($this);
        $validator->addRule('do', 'required', _t('地址不合法'));
        $validator->addRule('do', 'alphaDash', _t('地址不合法'));
        $validator->addRule('do', 'noAbstract', _t('地址不合法'));
        $validator->run(array('do' => TypechoRoute::getParameter('do')));
        
        Typecho::widget('do.' . TypechoRoute::getParameter('do'));
    }
}
