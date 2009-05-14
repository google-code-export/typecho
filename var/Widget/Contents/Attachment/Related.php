<?php
/**
 * 文章相关附件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 文章相关附件组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Attachment_Related extends Widget_Abstract_Contents
{
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->parameter->setDefault('cid=0');
        
        //如果没有cid值
        if (!$this->parameter->cid) {
            return;
        }

        /** 构建基础查询 */
        $select = $this->select()->where('table.contents.type = ?', 'attachment');
        
        //order字段在附件里代表所属文章
        $select->where('table.contents.order = ?', $this->parameter->cid);
        
        /** 提交查询 */
        $select->order('table.contents.created', Typecho_Db::SORT_DESC);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
