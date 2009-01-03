<?php
/**
 * 插件帮手本身也是一个插件, 它将默认出现在所有的typecho发行版中.
 * 因此你可以放心使用它的功能, 以方便你的插件安装在用户的系统里.
 * 
 * @package Plugin Helper 
 * @author qining
 * @version 1.0.0
 * @link http://typecho.org
 */
class Helper implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate(){}
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 获取Widget_Options对象
     * 
     * @access public
     * @return Widget_Options
     */
    public static function options()
    {
        return Typecho_Widget::widget('Widget_Options');
    }
    
    /**
     * 增加路由
     * 
     * @access public
     * @return boolean
     */
    public static function addRoute()
    {
        
    }
    
    /**
     * 移除路由
     * 
     * @access public
     * @return boolean
     */
    public static function removeRoute()
    {
        
    }
    
    /**
     * 增加action扩展
     * 
     * @access public
     * @param string $widgetName 需要扩展的widget名称
     * @return integer
     */
    public static function addAction($widgetName)
    {
        $actionTable = unserialize(self::options()->actionTable);
        $actionTable = empty($actionTable) ? array() : $actionTable;
        $actionTable[] = $widgetName;
        $actionTable = array_unique($actionTable);
        
        $db = Typecho_Db::get();
        return Typecho_Widget::widget('Widget_Abstract_Options')->update(array('value' => serialize($actionTable))
        , $db->sql()->where('name = ?', 'actionTable'));
    }
    
    /**
     * 删除action扩展
     * 
     * @access public
     * @param unknown $widgetName
     * @return unknown
     */
    public static function removeAction($widgetName)
    {
        $actionTable = unserialize(self::options()->actionTable);
        $actionTable = empty($actionTable) ? array() : $actionTable;
        
        if (false !== ($index = array_search($widgetName, $actionTable))) {
            unset($actionTable[$index]);
            reset($actionTable);
        }
        
        $db = Typecho_Db::get();
        return Typecho_Widget::widget('Widget_Abstract_Options')->update(array('value' => serialize($actionTable))
        , $db->sql()->where('name = ?', 'actionTable'));
    }
    
    /**
     * 增加一个菜单
     * 
     * @access public
     * @param string $menuName 菜单名
     * @return integer
     */
    public static function addMenu($menuName)
    {
        $panelTable = unserialize(self::options()->panelTable);
        $panelTable['parent'] = empty($panelTable['parent']) ? array() : $panelTable['parent'];
        $panelTable['parent'][] = $menuName;
        
        $db = Typecho_Db::get();
        Typecho_Widget::widget('Widget_Abstract_Options')->update(array('value' => (self::options()->panelTable = serialize($panelTable)))
        , $db->sql()->where('name = ?', 'panelTable'));
        
        end($panelTable['parent']);
        return key($panelTable['parent']) + 10;
    }
    
    /**
     * 移除一个菜单
     * 
     * @access public
     * @param string $menuName 菜单名
     * @return integer
     */
    public static function removeMenu($menuName)
    {
        $panelTable = unserialize(self::options()->panelTable);
        $panelTable['parent'] = empty($panelTable['parent']) ? array() : $panelTable['parent'];
        
        if (false !== ($index = array_search($menuName, $panelTable['parent']))) {
            unset($panelTable['parent'][$index]);
        }
        
        $db = Typecho_Db::get();
        Typecho_Widget::widget('Widget_Abstract_Options')->update(array('value' => (self::options()->panelTable = serialize($panelTable)))
        , $db->sql()->where('name = ?', 'panelTable'));
        
        return $index + 10;
    }
    
    /**
     * 增加一个面板
     * 
     * @access public
     * @param integer $index 菜单索引
     * @param string $fileName 文件名称
     * @param string $title 面板标题
     * @param string $subTitle 面板副标题
     * @param string $level 进入权限
     * @param boolean $hidden 是否隐藏
     * @return integer
     */
    public static function addPanel($index, $fileName, $title, $subTitle, $level, $hidden = false)
    {
        $panelTable = unserialize(self::options()->panelTable);
        $panelTable['child'] = empty($panelTable['child']) ? array() : $panelTable['child'];
        $panelTable['child'][$index] = empty($panelTable['child'][$index]) ? array() : $panelTable['child'][$index];
        $fileName = urlencode(trim($fileName, '/'));
        $panelTable['child'][$index][] = array($title, $subTitle, '/admin/extending.php?panel=' . $fileName, $level, $hidden);
        
        $panelTable['file'] = empty($panelTable['file']) ? array() : $panelTable['file'];
        $panelTable['file'][] = $fileName;
        $panelTable['file'] = array_unique($panelTable['file']);
        
        $db = Typecho_Db::get();
        Typecho_Widget::widget('Widget_Abstract_Options')->update(array('value' => (self::options()->panelTable = serialize($panelTable)))
        , $db->sql()->where('name = ?', 'panelTable'));
        
        end($panelTable['child'][$index]);
        return key($panelTable['child'][$index]);
    }
    
    /**
     * 移除一个面板
     * 
     * @access public
     * @param integer $index 菜单索引
     * @param string $fileName 文件名称
     * @return integer
     */
    public static function removePanel($index, $fileName)
    {
        $panelTable = unserialize(self::options()->panelTable);
        $panelTable['child'] = empty($panelTable['child']) ? array() : $panelTable['child'];
        $panelTable['child'][$index] = empty($panelTable['child'][$index]) ? array() : $panelTable['child'][$index];
        $panelTable['file'] = empty($panelTable['file']) ? array() : $panelTable['file'];
        $fileName = urlencode(trim($fileName, '/'));
        
        if (false !== ($key = array_search($fileName, $panelTable['file']))) {
            unset($panelTable['file'][$key]);
        }
        
        foreach ($panelTable['child'][$index] as $key => $val) {
            if ($val[2] == '/admin/extending.php?panel=' . $fileName) {
                unset($panelTable['child'][$index][$key]);
                $index = $key;
                break;
            }
        }

        $db = Typecho_Db::get();
        Typecho_Widget::widget('Widget_Abstract_Options')->update(array('value' => (self::options()->panelTable = serialize($panelTable)))
        , $db->sql()->where('name = ?', 'panelTable'));
        return $index;
    }
    
    /**
     * 获取面板url
     * 
     * @access public
     * @return unknown
     */
    public static function panelUrl($fileName)
    {
        
    }
}
