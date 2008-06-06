<?php
/**
 * 编辑链接
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入父类 */
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Metas.php';

/**
 * 编辑链接组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class EditLinkWidget extends MetasWidget
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

        if(NULL != TypechoRequest::getParameter('mid'))
        {
            /** 更新模式 */
            $meta = $this->db->fetchRow($this->selectSql
            ->where('`mid` = ?', TypechoRequest::getParameter('mid'))
            ->where('`type` = ?', 'link')->limit(1));
            
            if(!$meta)
            {
                throw new TypechoWidgetException(_t('链接不存在'), TypechoException::NOTFOUND);
            }

            if($cookieMeta = TypechoRequest::getCookie('type'))
            {
                $meta = array_merge($meta, $cookieMeta);
            }
            
            $meta['do'] = 'update';
            $this->push($meta);
        }
        else
        {
            $meta = TypechoRequest::getCookie('link');
            $meta['type'] = 'link';
            $meta['do'] = 'insert';
            $meta['slug'] = 'http://';
            $this->push($meta);
        }
        
        TypechoRequest::deleteCookie('link');
    }
}
