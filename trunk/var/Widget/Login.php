<?php
/**
 * 登录动作
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 登录组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Login extends Widget_Abstract_Users implements Widget_Interface_DoWidget
{
    /**
     * 开始用户登录
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        /** 如果已经登录 */
        if(Typecho_API::factory('Widget_Users_Current')->hasLogin())
        {
            /** 直接返回 */
            Typecho_API::redirect(Typecho_API::factory('Widget_Abstract_Options')->index);
        }
        
        /** 初始化验证类 */
        $validator = new Typecho_Validate();
        $validator->addRule('name', 'required', _t('请输入用户名'));
        $validator->addRule('password', 'required', _t('请输入密码'));
        
        /** 截获验证异常 */
        try
        {
            $validator->run(Typecho_Request::getParametersFrom('name', 'password'));
        }
        catch(Typecho_Validate_Exception $e)
        {
            /** 设置提示信息 */
            Typecho_API::factory('Widget_Notice')->set($e->getMessages());
            Typecho_API::goBack();
        }
        
        /** 开始验证用户 **/
        $user = $this->db->fetchRow($this->select()
        ->where('`name` = ?', Typecho_Request::getParameter('name'))
        ->limit(1));
        
        /** 比对密码 */
        if($user && $user['password'] == md5(Typecho_Request::getParameter('password')))
        {
            Typecho_API::factory('Widget_Users_Current')->login($user['uid'], $user['password'], sha1(Typecho_API::randString(20)),
            1 == Typecho_Request::getParameter('remember') ? Typecho_API::factory('Widget_Abstract_Options')->gmtTime + Typecho_API::factory('Widget_Abstract_Options')->timezone + 30*24*3600 : 0);
        }
        else
        {
            Typecho_API::factory('Widget_Notice')->set(_t('无法找到匹配的用户'), NULL, 'error');
            Typecho_API::redirect(Typecho_API::pathToUrl('login.php', Typecho_API::factory('Widget_Abstract_Options')->adminUrl)
            . (NULL === ($referer = Typecho_Request::getParameter('referer')) ? 
            NULL : '?referer=' . urlencode($referer)));
        }
        
        /** 跳转验证后地址 */
        if(NULL != ($referer = Typecho_Request::getParameter('referer')))
        {
            Typecho_API::redirect($referer);
        }
        else
        {
            Typecho_API::redirect(Typecho_API::pathToUrl('index.php', Typecho_API::factory('Widget_Abstract_Options')->adminUrl));
        }
    }
}
