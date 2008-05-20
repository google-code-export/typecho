<?php
/**
 * 编辑分类
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑分类组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class EditMetaWidget extends TypechoWidget
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
        $this->db = TypechoDb::get();
    }
    
    /**
     * 入口函数
     * 
     * @access public
     * @param string $type 内容类型
     * @return void
     */
    public function render($type)
    {
        /** 编辑以上权限 */
        Typecho::widget('Access')->pass('editor');

        if(NULL != TypechoRequest::getParameter('mid'))
        {
            /** 更新模式 */
            $meta = $this->db->fetchRow($this->db->sql()
            ->select('table.metas')->where('`mid` = ?', TypechoRequest::getParameter('mid'))
            ->where('`type` = ?', $type)->limit(1));
            
            if(!$meta)
            {
                throw new TypechoWidgetException(_t('不存在'), TypechoException::NOTFOUND);
            }
            
            if($cookieMeta = TypechoRequest::getCookie($type))
            {
                $meta = array_merge($meta, $cookieMeta);
            }
            
            $meta['do'] = 'update';
            $this->push($meta);
        }
        else
        {
            $meta = TypechoRequest::getCookie($type);
            $meta['do'] = 'insert';
            $this->push($meta);
        }
        
        TypechoRequest::deleteCookie($type);
    }
}
