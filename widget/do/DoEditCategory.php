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

/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

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
class DoEditCategoryWidget extends DoPostWidget
{
    public function insertCategory()
    {
        /** 取出最大的排序 */
        $sort = $this->db->fetchObject($this->db->sql()->select('table.metas', 'COUNT(`sort`) AS `maxSort`')
        ->where('type = ?', 'category'))->maxSort + 1;
    
        /** 插入数据 */
        $this->db->query($this->db->sql()->insert('table.metas')
        ->rows(TypechoRequest::getParametersFrom('name', 'slug', 'description'))
        ->row('type', "'category'")
        ->row('sort', $sort));
        
        /** 提示信息 */
        Typecho::widget('Notice')->set(_t("'%s'已经被增加", TypechoRequest::getParameter('name')), NULL, 'success');
        
        /** 转向原页 */
        $this->goBack();
    }

    public function render()
    {
        Typecho::widget('Access')->pass('editor');
        TypechoRequest::bindParameter(array('do' => 'insert'), array($this, 'insertCategory'));
        TypechoRequest::bindParameter(array('do' => 'update'), array($this, 'updateCategory'));
        TypechoRequest::bindParameter(array('do' => 'delete'), array($this, 'deleteCategory'));
        Typecho::redirect(Typecho::widget('Options')->adminUrl);
    }
}
