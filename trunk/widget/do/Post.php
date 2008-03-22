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
 * 数据提交基础类库
 * 
 * @package Post
 */
class Post extends TypechoWidget
{
    /**
     * 数据库对象
     * 
     * @access protected
     * @var TypechoDb
     */
    protected $db;
    
    /**
     * 构造函数,初始化数据库
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = TypechoDb::get();
    }

    /**
     * 返回来路
     * 
     * @access protected
     * @param string $anchor 锚点地址
     * @return void
     */
    protected function goBack($anchor = NULL)
    {
        //判断来源
        if(empty($_SERVER['HTTP_REFERER']) || 0 === strpos($_SERVER['HTTP_REFERER'], $this->registry('Options')->index))
        {
            throw new TypechoWidgetException(_t('无法返回原网页'));
        }
        
        typechoRedirect($_SERVER['HTTP_REFERER'] . $anchor, false);
    }
    
    protected function goForward($url)
    {
        typechoRedirect($this->registry('Options')->siteUrl . $url, false);
    }
    
    protected function formDataList($key)
    {
        if(!empty($_POST[$key]))
        {
            return is_array($_POST[$key]) ? array_unique($_POST[$key]) : array($_POST[$key]);
        }
        else if(!empty($_GET[$key]))
        {
            return is_array($_GET[$key]) ? array_unique($_GET[$key]) : array($_GET[$key]);
        }
        else
        {
            return array();
        }
    }

    protected function onSubmit($postData, $functionName, $method = 'GET')
    {
        $data = ('POST' == strtoupper($method)) ? $_POST : $_GET;
    
        if(is_array($postData))
        {
            $doPost = true;
            foreach($postData as $key => $val)
            {
                if(empty($data[$key]) || $data[$key] != $val)
                {
                    $doPost = false;
                }
            }
            
            if($doPost)
            {
                $this->$functionName();
            }
        }
        else if(!empty($data[$postData]))
        {
            $this->$functionName();
        }
    }
}
