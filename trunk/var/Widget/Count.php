<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 同样强大的Count组件
 *
 * @package Widget
 */
class Widget_Count extends Typecho_Widget
{
    /**
     * 入口函数,执行查询
     *
     * @access public
     * @param string $query 查询的字符串
     * @return void
     */
    public function __construct($query)
    {
        //初始化数据库对象
        $db = Typecho_Db::get();

        //初始化查询对象
        $sql = $db->sql();

        //解析查询字符串
        parse_str($query, $params);

        //获取数据源
        if(empty($params['from']))
        {
            throw new Typecho_Widget_Exception(_t('没有任何数据源'));
        }

        //获取计算主键
        if(empty($params['count']))
        {
            throw new Typecho_Widget_Exception(_t('没有任何可计算的主键'));
        }

        //选定需要查询的数据表
        $sql->select($params['from'], 'COUNT(`' . $params['count'] . '`) AS num');
        unset($params['from']);
        unset($params['count']);

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
