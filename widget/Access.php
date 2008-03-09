<?php

class Access extends TypechoWidget
{
    private $_group;
    private $_user;

    public function __construct()
    {
        parent::__construct();
        $this->_group = array(
            'administrator' => 0,
            'editor'		=> 1,
            'contributor'	=> 2,
            'subscriber'	=> 3,
            'visitor'		=> 4
        );
    }

    public function render()
    {
        session_start();
        
        if($this->hasLogin())
        {
            $db = TypechoDb::get();
            $rows = $db->fetchAll($db->sql()
            ->select('table.options')
            ->where('user = ?', $_SESSION['uid']), array($this, 'push'));
            
            foreach($rows as $row)
            {
                $this->registry('Options')->set($row['name'], $row['value']);
            }
            
            //更新最后活动时间
            $db-query($db->sql()
            ->update('table.user')
            ->rows(array('activated' => $this->registry('Options')->gmt_time))
            ->where('uid = ?', $uid));
        }
    }
    
    public function user($name)
    {
        if($this->hasLogin())
        {
            if(in_array($name, array('uid', 'group', 'name')))
            {
                return $_SESSION[$name];
            }
            else
            {
                if(empty($this->_user))
                {
                    $db = TypechoDb::get();
                    $this->_user = $db->fetchRow($db->sql()
                    ->select('table.user')
                    ->where('uid = ?', $_SESSION['uid']));
                }
                
                return isset($this->_user[$name]) ? $this->_user[$name] : NULL;
            }
        }
        else
        {
            return NULL;
        }
    }
    
    public function login($uid, $name, $group)
    {
        $_SESSION['uid'] = $uid;
        $_SESSION['name'] = $name;
        $_SESSION['group'] = $group;
        
        $db = TypechoDb::get();
        
        //更新最后登录时间
        $db-query($db->sql()
        ->update('table.user')
        ->row('logged', 'activated')
        ->where('uid = ?', $uid));
    }
    
    public function logout()
    {
        session_unset();
    }
    
    public function hasLogin()
    {
        return isset($_SESSION['uid']);
    }
    
    public function pass($group)
    {
        if($this->hasLogin() && array_key_exists($group, $this->_group)
        && $this->_group[$_SESSION['group']] <= $this->_group[$group])
        {
            return true;
        }
        
        throw new TypechoWidgetException(_t('禁止访问'), __TYPECHO_EXCEPTION_403__);
    }
}
