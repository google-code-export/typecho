<?php
/**
 * 编辑文章输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑文章组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class EditPostWidget extends TypechoWidget
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
     * 将内容压入堆栈
     * 
     * @access public
     * @param array $value 内容值
     * @return void
     */
    public function push(array $value)
    {
        /** 验证可编辑权限 */
        if(isset($value['author']))
        {
            if(!Typecho::widget('Access')->pass('editor', true))
            {
                if($value['author'] != Typecho::widget('Access')->uid)
                {
                    throw new TypechoWidgetException(_t('没有编辑权限'), TypechoException::FORBIDDEN);
                }
            }
        }
    
        return parent::push($value);
    }
    
    /**
     * 文章分类
     * 
     * @access public
     * @return array
     */
    public function categories()
    {
        if(isset($this->cid))
        {
            $categories =
            $this->db->fetchAll($this->db->sql()
            ->select('table.metas', 'table.metas.`mid`')
            ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
            ->where('table.relationships.`cid` = ?', $this->cid)
            ->where('table.metas.`type` = ?', 'category')
            ->group('table.metas.`mid`'));
            
            return Typecho::arrayFlatten($categories, 'mid');
        }
        else
        {
            return array(Typecho::widget('Options')->defaultCategory);
        }
    }

    /**
     * 入口函数
     * 
     * @access public
     * @param string $type 内容类型
     * @return void
     */
    public function render($type = 'post')
    {
        if('page' == $type)
        {
            /** 编辑以上权限 */
            Typecho::widget('Access')->pass('editor');
        }
        else
        {
            /** 贡献者以上权限 */
            Typecho::widget('Access')->pass('contributor');
        }
    
        if(NULL != TypechoRequest::getParameter('cid'))
        {
            /** 更新模式 */
            $post = $this->db->fetchRow($this->db->sql()
            ->select('table.contents')->where('`cid` = ?', TypechoRequest::getParameter('cid'))
            ->where('`type` = ? OR `type` = ?', $type, 'draft')->limit(1), array($this, 'push'));
            
            if(!$post)
            {
                throw new TypechoWidgetException(_t('内容不存在'), TypechoException::NOTFOUND);
            }
        }
        else
        {
            /** 插入模式 */
            $post = array('title'       => _t('未命名文档'),
                          'do'          => 'insert',
                          'created'     => Typecho::widget('Options')->gmtTime + Typecho::widget('Options')->timezone,
                          'allowComment'=> Typecho::widget('Options')->allowComment,
                          'allowPing'   => Typecho::widget('Options')->allowPing,
                          'allowFeed'   => Typecho::widget('Options')->allowFeed);
                          
            $this->push($post);
        }
    }
}
