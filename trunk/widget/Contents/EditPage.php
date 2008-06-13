<?php
/**
 * 编辑页面输出
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入父类 */
require_once 'widget/Abstract/Contents.php';

/**
 * 编辑页面组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class EditPageWidget extends ContentsWidget
{
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
            $page = $this->db->fetchRow($this->selectSql
            ->where('table.contents.`cid` = ?', TypechoRequest::getParameter('cid'))
            ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'page', 'page_draft')
            ->limit(1));
            
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
                          'cid'         => 0,
                          'type'        => 'page',
                          'slug'        => NULL,
                          'created'     => $this->options->gmtTime + $this->options->timezone,
                          'allowComment'=> $this->options->allowComment,
                          'allowPing'   => $this->options->allowPing,
                          'allowFeed'   => $this->options->allowFeed);
                          
            $this->push($page);
        }
    }
}
