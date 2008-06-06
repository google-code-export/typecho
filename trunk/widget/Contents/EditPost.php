<?php
/**
 * 编辑文章输出
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
 * 编辑文章组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class EditPostWidget extends ContentsWidget
{
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
        if(isset($value['authorId']) && !$this->haveContentPermission($value['authorId']))
        {
            throw new TypechoWidgetException(_t('没有编辑此文章的权限'), TypechoException::FORBIDDEN);
        }

        return parent::push($value);
    }

    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        /** 贡献者以上权限 */
        Typecho::widget('Access')->pass('contributor');
    
        if(NULL != TypechoRequest::getParameter('cid'))
        {
            /** 更新模式 */
            $post = $this->db->fetchRow($this->selectSql->group('table.contents.`cid`')
            ->where('table.contents.`cid` = ?', TypechoRequest::getParameter('cid'))
            ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft')
            ->limit(1));
            
            $post['do'] = 'update';
            Typecho::widget('Menu')->title = _t('编辑文章');
            $this->push($post);
            
            if(!$post)
            {
                throw new TypechoWidgetException(_t('文章不存在'), TypechoException::NOTFOUND);
            }
        }
        else
        {
            /** 插入模式 */
            $post = array('title'       => _t('未命名文档'),
                          'do'          => 'insert',
                          'cid'         => 0,
                          'type'        => 'post',
                          'created'     => $this->options->gmtTime + $this->options->timezone,
                          'allowComment'=> $this->options->allowComment,
                          'allowPing'   => $this->options->allowPing,
                          'allowFeed'   => $this->options->allowFeed);
                          
            $this->push($post);
        }
    }
}
