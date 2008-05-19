<?php
/**
 * 编辑页面输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑页面组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class EditPageWidget extends TypechoWidget
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
     * @return void
     */
    public function render()
    {
        /** 编辑以上权限 */
        Typecho::widget('Access')->pass('editor');
    
        if(NULL != TypechoRequest::getParameter('cid'))
        {
            /** 更新模式 */
            $page = $this->db->fetchRow($this->db->sql()
            ->select('table.contents')->where('`cid` = ?', TypechoRequest::getParameter('cid'))
            ->where('`type` = ? OR `type` = ?', 'page', 'draft')->limit(1));
            
            $page['do'] = 'update';
            Typecho::widget('Menu')->title = _t('编辑页面');
            $this->push($page);
            
            if(!$page)
            {
                throw new TypechoWidgetException(_t('页面不存在'), TypechoException::NOTFOUND);
            }
        }
        else
        {
            /** 插入模式 */
            $page = array('title'       => _t('未命名'),
                          'do'          => 'insert',
                          'created'     => Typecho::widget('Options')->gmtTime + Typecho::widget('Options')->timezone,
                          'allowComment'=> Typecho::widget('Options')->allowComment,
                          'allowPing'   => Typecho::widget('Options')->allowPing,
                          'allowFeed'   => Typecho::widget('Options')->allowFeed);
                          
            $this->push($page);
        }
    }
}
