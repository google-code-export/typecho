<?php
/**
 * 描述数据提交管理
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入提交基类支持 **/
require_once 'DoPost.php';

/**
 * 描述数据提交管理组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class DoEditMetaWidget extends DoPostWidget
{
    public function insertMeta()
    {
        /** 取出最大的排序 */
        $sort = $this->db->fetchObject($this->db->sql()->select('table.metas', 'COUNT(`sort`) AS `maxSort`')
        ->where('type = ?', TypechoRequest::getParameter('type')))->maxSort + 1;
    
        $this->db->query($this->db->sql()->insert('table.metas')
        ->rows(TypechoRequest::getParametersFrom('name', 'slug', 'type', 'description'))
        ->row('sort', $sort));
        
        Typecho::widget('Notice')->set(_t("'%s'已经被增加", TypechoRequest::getParameter('name')), NULL, 'success');
        
        Typecho::redirect(Typecho::pathToUrl('manage-cat.php', Typecho::widget('Options')->adminUrl));
    }

    public function render()
    {
        Typecho::widget('Access')->pass('editor');
        TypechoRequest::bindParameter(array('do' => 'insert'), array($this, 'insertMeta'));
        TypechoRequest::bindParameter(array('do' => 'update'), array($this, 'updatePost'));
        TypechoRequest::bindParameter(array('do' => 'delete'), array($this, 'deletePost'));
        Typecho::redirect(Typecho::widget('Options')->adminUrl);
    }
}
