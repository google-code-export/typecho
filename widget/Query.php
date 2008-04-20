<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 强大的Query组件
 *
 * @package Widget
 */
class QueryWidget extends TypechoWidget
{
    /**
     * 入口函数,执行查询
     *
     * @access public
     * @param string $query 查询的字符串
     * @return void
     */
    public function render($query)
    {
        //初始化数据库对象
        $db = TypechoDb::get();

        //初始化查询对象
        $sql = $db->sql();

        //解析查询字符串
        parse_str($query, $params);

        //获取数量限制
        if(empty($params['from']))
        {
            throw new TypechoWidgetException(_t('没有任何数据源'));
        }

        //选定需要查询的数据表
        $sql->select($params['from']);
        unset($params['from']);

        //获取排序
        if(!empty($params['order']))
        {
            $sql->order($params['order'], empty($params['order']) ? NULL : $params['sort']);
            unset($params['order']);
            if(!empty($params['sort']))
            {
                unset($params['sort']);
            }
        }

        //获取条件
        foreach($params as $key => $val)
        {
            if(is_array($val))
            {
                $cond = implode(' OR ', array_fill(0, count($val), '`' . $key . '` = ?'));
                array_unshift($val, $cond);
                call_user_func_array(array(&$sql, 'where'), $val);
            }
            else
            {
                $sql->where('`' . $key . '` = ?', $val);
            }
        }

        $db->fetchAll($sql, array($this, 'push'));
    }
}
