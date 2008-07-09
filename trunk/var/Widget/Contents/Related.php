<?php
/**
 * 相关内容
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 相关内容组件(根据标签关联)
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Related extends Widget_Abstract_Contents
{
    /**
     * 构造函数,初始化数据
     * 
     * @access public
     * @param integer $cid 需要关联的内容id
     * @param string $type 内容类型
     * @param array $tags 标签列表
     * @param integer $limit 输出行数
     * @return void
     */
    public function __construct($cid, $type, array $tags, $limit = 5)
    {
        parent::__construct();
    
        if($tags)
        {
            $tagsGroup = implode(',', Typecho_API::arrayFlatten($tags, 'mid'));
            $this->db->fetchAll($this->select()->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` in (' . $tagsGroup . ')')
            ->where('table.contents.`cid` <> ?', $cid)
            ->where('table.contents.`password` IS NULL')
            ->where('table.contents.`created` < ?', $this->options->gmtTime)
            ->where('table.contents.`type` = ?', $type)
            ->order('table.contents.`created`', Typecho_Db::SORT_DESC)
            ->group('table.contents.`cid`')->limit($limit), array($this, 'push'));
        }
    }
}
