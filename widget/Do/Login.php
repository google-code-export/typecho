<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

/**
 * 用户登录
 *
 * @category Widget
 */
class LoginWidget extends TypechoWidget
{
    /**
     * 开始用户登录
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $db = TypechoDb::get();
    
        /** 如果已经登录 */
        if(Typecho::widget('Access')->hasLogin())
        {
            /** 直接返回 */
            Typecho::redirect(Typecho::widget('Options')->index);
        }
        
        /** 初始化验证类 */
        $validator = new TypechoValidation();
        $validator->addRule('name', 'required', _t('请输入用户名'));
        $validator->addRule('password', 'required', _t('请输入密码'));
        
        /** 截获验证异常 */
        try
        {
            $validator->run(TypechoRequest::getParametersFrom('name', 'password'));
        }
        catch(TypechoValidationException $e)
        {
            /** 设置提示信息 */
            Typecho::widget('Notice')->set($e->getMessages());
            $this->goBack();
        }
        
        /** 开始验证用户 **/
        $user = $db->fetchRow($db->sql()
        ->select('table.users')
        ->where('`name` = ?', TypechoRequest::getParameter('name'))
        ->limit(1));
        
        /** 比对密码 */
        if($user && $user['password'] == md5(TypechoRequest::getParameter('password')))
        {
            Typecho::widget('Access')->login($user['uid'], $user['password'], sha1(Typecho::randString(20)),
            1 == TypechoRequest::getParameter('remember') ? Typecho::widget('Options')->gmtTime + Typecho::widget('Options')->timezone : 0);
        }
        else
        {
            Typecho::widget('Notice')->set(_t('无法找到匹配的用户'), NULL, 'error');
            Typecho::redirect(Typecho::pathToUrl('login.php', Typecho::widget('Options')->adminUrl)
            . (NULL === ($referer = TypechoRequest::getParameter('referer')) ? 
            NULL : '?referer=' . urlencode($referer)));
        }
        
        /** 跳转验证后地址 */
        if(NULL !== ($referer = TypechoRequest::getParameter('referer')))
        {
            Typecho::redirect($referer);
        }
        else
        {
            Typecho::redirect(Typecho::pathToUrl('index.php', Typecho::widget('Options')->adminUrl));
        }
    }
}
