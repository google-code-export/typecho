<?php

/**
 * 当前登录用户
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_User extends Typecho_Widget
{
    /**
     * 用户
     *
     * @access private
     * @var array
     */
    private $_user;
    
    /**
     * 是否已经登录
     * 
     * @access private
     * @var boolean
     */
    private $_hasLogin = NULL;
    
    /**
     * 数据库对象
     * 
     * @access protected
     * @var Typecho_Db
     */
    protected $db;
    
    /**
     * 准备函数
     * 
     * @access public
     * @return void
     */
    public function prepare()
    {
        /** 初始化数据库 */
        $this->db = Typecho_Db::get();
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        if($this->hasLogin())
        {
            $rows = $this->db->fetchAll($this->db->select()
            ->from('table.options')->where('user = ?', $this->_user['uid']));

            $this->push($this->_user);

            foreach($rows as $row)
            {
                $this->widget('Widget_Options')->__set($row['name'], $row['value']);
            }

            //更新最后活动时间
            $this->db->query($this->db
            ->update('table.users')
            ->rows(array('activated' => $this->widget('Widget_Options')->gmtTime))
            ->where('uid = ?', $this->_user['uid']));
        }
    }

    /**
     * 用户登录函数
     *
     * @access public
     * @param integer $uid 用户id
     * @param string $password 用户密码
     * @param string $authCode 认证码
     * @param integer $expire 过期时间
     * @return void
     */
    public function login($uid, $password, $authCode, $expire = 0)
    {
        /** 保存登录信息,对密码采用sha1和md5双重加密 */
        $this->response->setCookie('uid', $uid, $expire, $this->widget('Widget_Options')->siteUrl);
        $this->response->setCookie('password', sha1($password), $expire, $this->widget('Widget_Options')->siteUrl);
        $this->response->setCookie('authCode', $authCode, $expire, $this->widget('Widget_Options')->siteUrl);
        
        if($this->db->fetchObject($this->db->select()
                ->from('table.users')
                ->where('uid = ?', $uid)
                ->limit(1))->activated > 0)
        {
            //更新最后登录时间以及验证码
            $this->db->query($this->db
            ->update('table.users')
            ->expression('logged', 'activated')
            ->rows(array('authCode' => $authCode))
            ->where('uid = ?', $uid));
        }
        else
        {
            //第一次登录
            $this->db->query($this->db
            ->update('table.users')
            ->rows(array('authCode' => $authCode, 'logged' => $this->widget('Widget_Options')->gmtTime))
            ->where('uid = ?', $uid));
        }
    }
    
    /**
     * 用户登出函数
     * 
     * @access public
     * @return void
     */
    public function logout()
    {
        $this->response->deleteCookie('uid', $this->widget('Widget_Options')->siteUrl);
        $this->response->deleteCookie('password', $this->widget('Widget_Options')->siteUrl);
        $this->response->deleteCookie('authCode', $this->widget('Widget_Options')->siteUrl);
        $this->response->deleteCookie('protect_password', $this->widget('Widget_Options')->siteUrl);
    }
    
    /**
     * 判断用户是否已经登录
     *
     * @access public
     * @return void
     */
    public function hasLogin()
    {
        if(NULL !== $this->_hasLogin)
        {
            return $this->_hasLogin;
        }
        else
        {
            if(NULL !== $this->request->getCookie('uid') && NULL !== $this->request->getCookie('password'))
            {
                /** 验证登陆 */
                $user = $this->db->fetchRow($this->db->select()->from('table.users')
                ->where('uid = ?', $this->request->getCookie('uid'))
                ->limit(1));

                if($user && sha1($user['password']) == $this->request->getCookie('password')
                && $user['authCode'] == $this->request->getCookie('authCode'))
                {
                    $this->_user = $user;
                    return ($this->_hasLogin = true);
                }
                
                $this->logout();
            }
            
            return ($this->_hasLogin = false);
        }
    }
    
    /**
     * 判断用户权限
     *
     * @access public
     * @param string $group 用户组
     * @param boolean $return 是否为返回模式
     * @return boolean
     * @throws TypechoWidgetException
     */
    public function pass($group, $return = false)
    {
        if($this->hasLogin())
        {
            if(array_key_exists($group, $this->groups) && $this->groups[$this->group] <= $this->groups[$group])
            {
                return true;
            }
        }
        else
        {
            if($return)
            {
                return false;
            }
            else
            {
                $this->response->redirect(Typecho_Common::pathToUrl('/login.php', $this->widget('Widget_Options')->adminUrl)
                . '?referer=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), false);
            }
        }

        if($return)
        {
            return false;
        }
        else
        {
            throw new Typecho_Widget_Exception(_t('禁止访问'), Typecho_Exception::FORBIDDEN);
        }
    }
}
