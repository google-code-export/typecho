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

/** 载入提交基类 */
require_once 'DoPost.php';

/**
 * 用户登录
 *
 * @category Widget
 */
class LoginWidget extends DoPostWidget
{
    /**
     * 开始用户登录
     * 
     * @access public
     * @return void
     */
    public function render()
    {
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
            widget('Notice')->set($e->getMessages());
            Typecho::redirect(Typecho::pathToUrl('login.php', widget('Options')->adminURL)
            . (NULL === ($referer = TypechoRequest::getParameter('referer')) ? 
            NULL : '?referer=' . urlencode($referer)));
        }
        
        /** 开始验证用户 **/
        $user = $this->db->fetchRow($this->db->sql()
        ->select('table.users')
        ->where('`name` = ?', TypechoRequest::getParameter('name'))
        ->limit(1));
        
        if($user && $user['password'] == md5(TypechoRequest::getParameter('password')))
        {
            widget('Access')->login($user);
        }
        else
        {
            widget('Notice')->set(_t('无法找到匹配的用户'));
            Typecho::redirect(Typecho::pathToUrl('login.php', widget('Options')->adminURL)
            . (NULL === ($referer = TypechoRequest::getParameter('referer')) ? 
            NULL : '?referer=' . urlencode($referer)));
        }
        
        if(NULL !== ($referer = TypechoRequest::getParameter('referer')))
        {
            Typecho::redirect($referer);
        }
        else
        {
            Typecho::redirect(Typecho::pathToUrl('index.php', widget('Options')->adminURL));
        }
    }
}
