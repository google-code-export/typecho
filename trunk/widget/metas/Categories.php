<?php
/**
 * 分类输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 分类输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class CategoriesWidget extends TypechoWidget
{
    public function render()
    {
        $db = TypechoDb::get();
        
        $db->fetchAll($db->sql()->select);
    }
}
