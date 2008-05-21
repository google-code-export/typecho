<?php
/**
 * 描述记录输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入父类 */
require_once 'Abstract/Metas.php';

/**
 * 描述记录输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class TagsWidget extends MetasWidget
{    
    /**
     * 输出内容分页
     *
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = Typecho::pathToUrl('manage-cat.php?page={page}', $this->options->adminUrl);
        parent::pageNav($query);
    }

    /**
     * 入口函数
     * 
     * @access public
     * @param string $type 类型
     * @param string $pageSize 分页参数,为0时表示不分页
     * @return unknown
     */
    public function render($pageSize = 0)
    {
        $select = $this->selectSql->where('`type` = ?', 'tag')->order('`mid`', TypechoDb::SORT_ASC);
        $this->countSql = clone $select;
        
        $this->pageSize = 20;
        $this->currentPage = (NULL === TypechoRequest::getParameter('page')) ? 1 : TypechoRequest::getParameter('page');
        $select->page($this->currentPage, $this->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
