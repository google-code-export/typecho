<?php
/**
 * 评论归档
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 评论归档组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Archive extends Widget_Abstract_Comments
{
    /**
     * 重载内容获取
     * 
     * @access protected
     * @return void
     */
    protected function getParentContent()
    {
        return $this->parameter->parentContent;
    }

    /**
     * 重载准备函数
     * 
     * @access public
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $this->parameter->setDefault('desc=0');
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->db->fetchAll($this->select()->where('table.comments.status = ?', 'approved')
        ->where('table.comments.cid = ?', $this->parameter->cid)
        ->order('table.comments.created', $this->parameter->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
