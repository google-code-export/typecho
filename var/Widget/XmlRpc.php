<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
 */

/**
 * XmlRpc接口
 * 
 * @author qining
 * @category typecho
 * @package Widget_XmlRpc
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_XmlRpc extends Widget_Abstract_Contents implements Widget_Interface_Action_Widget
{
    /**
     * 用户结构 
     * 
     * @var mixed
     * @access private
     */
    private $_user;

    /**
     * 构造函数 
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_user = Typecho_API::factory('Widget_Users_Current');
    }

    /**
     * checkAccess  检测用户权限 
     * 
     * @param mixed $userName 用户名
     * @param mixed $password 密码
     * @access private
     * @return void
     */
    private function checkAccess($userName, $password)
    {
        /** 开始验证用户 **/
        $user = $this->db->fetchRow($this->select()
        ->where('`name` = ?', $userName)
        ->limit(1));

        if($user && $user['password'] == md5($password))
        {
            $this->_user->login($user['uid'], $user['password'], sha1(Typecho_API::randString(20)), 0);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * wpGetPage: wordpress获取页面信息的方法 
     * 
     * @param mixed $blogId 博客id
     * @param mixed $pageId 页面id
     * @param mixed $userName 用户名
     * @param mixed $password 密码
     * @access public
     * @return void
     */
    public function wpGetPage($blogId, $pageId, $userName, $password)
    {
        if($this->checkAccess($userName, $password))
        {
            return new IXR_Error(403, _t('权限禁止'));
        }
        
        
    }

    /**
     * 入口执行方法
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $methods = array(
                'wp.getPage'    =>  array($this, 'wpGetPage'),
                );

        if(isset($_GET['rsd']))
        {
            echo '<?xml version="1.0" encoding="' . $this->options->charset .'"?>'; ?>
            <rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
            <service>
            <engineName>Typecho</engineName>
            <engineLink>http://www.typecho.org/</engineLink>
            <homePageLink><?php echo $this->options->siteUrl; ?></homePageLink>
            <apis>
            <api name="WordPress" blogID="1" preferred="true" apiLink="<?php echo $this->options->xmlRpcUrl; ?>" />
            <api name="Movable Type" blogID="1" preferred="false" apiLink="<?php echo $this->options->xmlRpcUrl; ?>" />
            <api name="MetaWeblog" blogID="1" preferred="false" apiLink="<?php echo $this->options->xmlRpcUrl; ?>" />
            <api name="Blogger" blogID="1" preferred="false" apiLink="<?php echo $this->options->xmlRpcUrl; ?>" />
            </apis>
            </service>
            </rsd><?php
        }
        else
        {
            new Ixr_Server($methods);
        }
    }
}
