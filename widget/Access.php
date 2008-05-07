<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 用户权限管理
 *
 * @package Widget
 */
class AccessWidget extends TypechoWidget
{
    /**
     * 用户组
     *
     * @access private
     * @var array
     */
    private $_group;

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
     * 重载父类构造函数,初始化用户组
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_group = array(
            'administrator' => 0,
            'editor'		=> 1,
            'contributor'	=> 2,
            'subscriber'	=> 3,
            'visitor'		=> 4
        );
    }

    /**
     * 入口函数
     *
     * @access public
     * @return void
     */
    public function render()
    {
        if($this->hasLogin())
        {
            $db = TypechoDb::get();
            
            $rows = $db->fetchAll($db->sql()
            ->select('table.options')
            ->where('`user` = ?', $this->_user['uid']));
            $this->push($this->_user);

            foreach($rows as $row)
            {
                Typecho::widget('Options')->__set($row['name'], $row['value']);
            }

            //更新最后活动时间
            $db->query($db->sql()
            ->update('table.users')
            ->rows(array('activated' => Typecho::widget('Options')->gmtTime))
            ->where('`uid` = ?', $this->_user['uid']));
        }
    }
    
    /**
     * 获取用户数据
     * 
     * @access public
     * @param string $type 数据类型
     * @param string $return 是否返回,如果为true此函数将返回字段值,反之则直接输出,默认为false
     * @return string
     */
    public function count($type, $return = false)
    {
        $return = NULL;
        if($this->hasLogin())
        {
            $db = TypechoDb::get();
            switch($type)
            {
                case 'post':
                    $return = $db->fetchObject($db->sql()
                    ->select('table.contents', 'COUNT(`cid`) AS `num`')
                    ->where('`author` = ?', $this->uid)
                    ->where('`type` = ?', 'post'))->num;
                    break;
                case 'page':
                    $return = $db->fetchObject($db->sql()
                    ->select('table.contents', 'COUNT(`cid`) AS `num`')
                    ->where('`author` = ?', $this->uid)
                    ->where('`type` = ?', 'page'))->num;
                    break;
                case 'comment':
                    $return = $db->fetchObject($db->sql()
                    ->select('table.comments', 'COUNT(DISTINCT `coid`) AS `num`')
                    ->join('table.contents', 'table.comments.`cid` = table.contents.`cid`')
                    ->where('table.contents.`author` = ?', $this->uid)
                    ->where('table.contents.`type` = ?', 'post'))->num;
                    break;
                case 'approved_comment':
                    $return = $db->fetchObject($db->sql()
                    ->select('table.comments', 'COUNT(DISTINCT `coid`) AS `num`')
                    ->join('table.contents', 'table.comments.`cid` = table.contents.`cid`')
                    ->where('table.contents.`author` = ?', $this->uid)
                    ->where('table.comments.`status` = ?', 'approved')
                    ->where('table.contents.`type` = ?', 'post'))->num;
                    break;
                case 'spam_comment':
                    $return = $db->fetchObject($db->sql()
                    ->select('table.comments', 'COUNT(DISTINCT `coid`) AS `num`')
                    ->join('table.contents', 'table.comments.`cid` = table.contents.`cid`')
                    ->where('table.contents.`author` = ?', $this->uid)
                    ->where('table.comments.`status` = ?', 'spam')
                    ->where('table.contents.`type` = ?', 'post'))->num;
                    break;
                case 'waiting_comment':
                    $return = $db->fetchObject($db->sql()
                    ->select('table.comments', 'COUNT(DISTINCT `coid`) AS `num`')
                    ->join('table.contents', 'table.comments.`cid` = table.contents.`cid`')
                    ->where('table.contents.`author` = ?', $this->uid)
                    ->where('table.comments.`status` = ?', 'waiting')
                    ->where('table.contents.`type` = ?', 'post'))->num;
                    break;
                default:
                    break;
            }
        }
        
        if($return)
        {
            return $return;
        }
        
        echo $return;
    }

    /**
     * 用户登录函数
     *
     * @access public
     * @param array $user 用户
     * @return void
     */
    public function login($uid, $password, $authCode, $expire = 0)
    {
        /** 保存登录信息,对密码采用sha1和md5双重加密 */
        TypechoRequest::setCookie('uid', $uid, $expire, Typecho::widget('Options')->siteUrl);
        TypechoRequest::setCookie('password', sha1($password), $expire, Typecho::widget('Options')->siteUrl);
        TypechoRequest::setCookie('authCode', $authCode, $expire, Typecho::widget('Options')->siteUrl);
        $db = TypechoDb::get();

        //更新最后登录时间以及验证码
        $db->query($db->sql()
        ->update('table.users')
        ->row('logged', '`activated`')
        ->rows(array('authCode' => $authCode))
        ->where('`uid` = ?', $uid));
    }
    
    /**
     * 用户登出函数
     * 
     * @access public
     * @return void
     */
    public function logout()
    {
        TypechoRequest::deleteCookie('uid', Typecho::widget('Options')->siteUrl);
        TypechoRequest::deleteCookie('password', Typecho::widget('Options')->siteUrl);
        TypechoRequest::deleteCookie('authCode', Typecho::widget('Options')->siteUrl);
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
            if(NULL !== TypechoRequest::getCookie('uid') && NULL !== TypechoRequest::getCookie('password'))
            {
                $db = TypechoDb::get();
                
                /** 验证登陆 */
                $user = $db->fetchRow($db->sql()
                ->select('table.users')
                ->where('`uid` = ?', TypechoRequest::getCookie('uid'))
                ->limit(1));
                
                if($user && sha1($user['password']) == TypechoRequest::getCookie('password')
                && $user['authCode'] == TypechoRequest::getCookie('authCode'))
                {
                    $this->_user = $user;
                    return ($this->_hasLogin = true);
                }
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
            if(array_key_exists($group, $this->_group) && $this->_group[$this->group] <= $this->_group[$group])
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
                Typecho::redirect(Typecho::widget('Options')->siteUrl . '/admin/login.php'
                . '?referer=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), false);
            }
        }

        if($return)
        {
            return false;
        }
        else
        {
            throw new TypechoWidgetException(_t('禁止访问'), TypechoException::FORBIDDEN);
        }
    }
}
