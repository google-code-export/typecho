<?php

/**
 * 当前登录用户
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_User extends Widget_Abstract_Users
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
     * 用户组
     *
     * @access public
     * @var array
     */
    public $groups = array(
            'administrator' => 0,
            'editor'		=> 1,
            'contributor'	=> 2,
            'subscriber'	=> 3,
            'visitor'		=> 4
            );
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 初始化数据库 */
        $this->db = Typecho_Db::get();
        $this->options = $this->widget('Widget_Options');
    }

    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        if ($this->hasLogin()) {
            $rows = $this->db->fetchAll($this->db->select()
            ->from('table.options')->where('user = ?', $this->_user['uid']));

            $this->push($this->_user);

            foreach ($rows as $row) {
                $this->options->__set($row['name'], $row['value']);
            }

            //更新最后活动时间
            $this->db->query($this->db
            ->update('table.users')
            ->rows(array('activated' => $this->options->gmtTime))
            ->where('uid = ?', $this->_user['uid']));
        }
    }

    /**
     * 用户登录函数
     *
     * @access public
     * @param integer $uid 用户id
     * @param integer $expire 过期时间
     * @return void
     */
    public function login($uid, $expire = 0)
    {
        $authCode = sha1(Typecho_Common::randString(20));
        $this->response->setCookie('__typecho_uid', $uid, $expire, $this->options->siteUrl);
        $this->response->setCookie('__typecho_authCode', Typecho_Common::hash($authCode),
        $expire, $this->options->siteUrl);

        $this->_hasLogin = true;

		//更新最后登录时间以及验证码
		$this->db->query($this->db
				->update('table.users')
				->expression('logged', 'activated')
				->rows(array('authCode' => $authCode))
				->where('uid = ?', $uid));
	}
    
    /**
     * 用户登出函数
     * 
     * @access public
     * @return void
     */
    public function logout()
    {
        $this->response->deleteCookie('__typecho_uid', $this->options->siteUrl);
        $this->response->deleteCookie('__typecho_authCode', $this->options->siteUrl);
    }
    
    /**
     * 判断用户是否已经登录
     *
     * @access public
     * @return void
     */
    public function hasLogin()
    {
        if (NULL !== $this->_hasLogin) {
            return $this->_hasLogin;
        } else {
            if (NULL !== $this->request->getCookie('__typecho_uid')) {
                /** 验证登陆 */
                $user = $this->db->fetchRow($this->db->select()->from('table.users')
                ->where('uid = ?', intval($this->request->getCookie('__typecho_uid')))
                ->limit(1));

                if ($user && Typecho_Common::hashValidate($user['authCode'], $this->request->getCookie('__typecho_authCode'))) {
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
        if ($this->hasLogin()) {
            if (array_key_exists($group, $this->groups) && $this->groups[$this->group] <= $this->groups[$group]) {
                return true;
            }
        } else {
            if ($return) {
                return false;
            } else {
                $this->response->redirect($this->options->loginUrl
                . '?referer=' . urlencode($this->request->uri()), false);
            }
        }

        if ($return) {
            return false;
        } else {
            throw new Typecho_Widget_Exception(_t('禁止访问'), 403);
        }
    }
}
