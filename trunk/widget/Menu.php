<?php
/**
 * Typecho Blog Platform * * @author     qining * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org) * @license    GNU General Public License 2.0 * @version    $Id$ */
/**
 * 后台菜单显示 *  * @package Widget */class Menu extends TypechoWidget
{
    public function render()    {        $menu = array(array(_t('状态面板'), '/admin/dashbord.php', -1),                      array(_t('概要'), '/admin/dashbord.php', 0),                      array(_t('创建'), '/admin/edit.php', -1));                      
        $hookName = TypechoWidgetHook::name(__FILE__);        TypechoWidgetHook::call($hookName, &$menu);    }}
