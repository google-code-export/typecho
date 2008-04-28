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
     * 入口函数,初始化session
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
            ->where('`user` = ?', TypechoRequest::getSession('uid')), array($this, 'push'));

            foreach($rows as $row)
            {
                widget('Options')->set($row['name'], $row['value']);
            }

            //更新最后活动时间
            $db-query($db->sql()
            ->update('table.user')
            ->rows(array('activated' => widget('Options')->gmt_time))
            ->where('`uid` = ?', $uid));
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
     * 用户登录函数,用于初始化session状态
     *
     * @access public
     * @param string $uid 用户id
     * @param string $name 用户名
     * @param string $group 用户组
     * @return void
     */
    public function login($uid, $name, $group)
    {
        TypechoRequest::setSession('uid', $uid);
        TypechoRequest::setSession('name', $name);
        TypechoRequest::setSession('group', $group);
        $db = TypechoDb::get();

        //更新最后登录时间
        $db-query($db->sql()
        ->update('table.user')
        ->row('logged', 'activated')
        ->where('`uid` = ?', $uid));
    }

    /**
     * 登出函数,销毁session
     *
     * @access public
     * @return void
     */
    public function logout()
    {
        session_unset();
    }

    /**
     * 判断用户是否已经登录
     *
     * @access public
     * @return void
     */
    public function hasLogin()
    {
        $uid = TypechoRequest::getSession('uid');
        return !empty($uid);
    }

    /**
     * 获取验证码
     *
     * @access public
     * @return string
     */
    public function authCode()
    {
        $db = TypechoDb::get();
        $user = $db->fetchRow($db->sql()
        ->select('table.user')
        ->where('`uid` = 1'));

        return md5($user['name'] . $user['password']);
    }

    /**
     * 判断用户权限
     *
     * @access public
     * @param string $group 用户组
     * @param boolean $test 是否为测试模式
     * @return boolean
     * @throws TypechoWidgetException
     */
    public function pass($group, $test = false)
    {
        if('system' == $group)
        {
            if($this->authCode() == TypechoRequest::getParameter('auth'))
            {
                return true;
            }
        }
        else
        {
            if($this->hasLogin())
            {
                if(array_key_exists($group, $this->_group) && $this->_group[TypechoRequest::getSession('group')] <= $this->_group[$group])
                {
                    return true;
                }
            }
            else
            {
                if($test)
                {
                    return false;
                }
                else
                {
                    Typecho::redirect(widget('Options')->siteURL . '/admin/login.php'
                    . '?referer=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), false);
                }
            }
        }

        if($test)
        {
            return false;
        }
        else
        {
            throw new TypechoWidgetException(_t('禁止访问'), 403);
        }
    }
}
