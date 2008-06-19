<?php
/**
 * 过滤器插件适配器
 * 
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** Typecho_Plugin_Adapter */
require_once 'Typecho/Plugin/Adapter.php';

/**
 * 过滤器插件适配器
 * 
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Plugin_Adapter_Filter extends Typecho_Plugin_Adapter
{
    /**
     * 回调处理函数
     * 
     * @access public
     * @param string $component 元件名称
     * @param string $args 参数
     * @return mixed
     */
    public function __call($component, $args)
    {
        $result = current($args);
    
        if(isset($this->callback[$component]))
        {
            foreach($this->callback[$component] as $callback)
            {
                $result = call_user_func($callback, $result);
            }
        }
        
        return $result;
    }
}
