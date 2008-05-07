<?php
/**
 * 聚合生成
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入聚合库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Feed.php';

/**
 * 聚合生成组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class FeedWidget extends TypechoWidget
{
    public function render()
    {
        $feedQuery = TypechoRoute::getParameter('feed');
        $feedType = TypechoFeed::RSS2;
        $link = TypechoRoute::parse('feed', array('feed' => $feedQuery), Typecho::widget('Options')->index);
        
        /** 过滤路径 */
        if(0 === strpos($feedQuery, '/rss/') || '/rss' == $feedQuery)
        {
            /** 如果是RSS1标准 */
            $feedQuery = substr($feedQuery, 4);
            $feedType = TypechoFeed::RSS1;
        }
        else if(0 === strpos($feedQuery, '/atom/') || '/atom' == $feedQuery)
        {
            /** 如果是ATOM标准 */
            $feedQuery = substr($feedQuery, 5);
            $feedType = TypechoFeed::ATOM;
        }
        
        /** 解析路径 */
        if(false !== ($value = TypechoRoute::match(TypechoConfig::get('Route'), $feedQuery, $current, $matches)) &&
        (in_array($current, array('index', 'post', 'category'))))
        {
            list($pattern, $file, $values, $format) = $value;
            $parameters = array();
            
            /** 解析参数列表 */
            if(1 < count($matches) && !empty($values))
            {
                unset($matches[0]);
                $parameters = array_combine($values, $matches);
            }

            /** 获取聚合内容 */
            switch($current)
            {
                case 'post':
                    Typecho::widget('feed.SinglePostFeed', $feedType, $parameters, $link);
                    break;
                case 'category':
                    Typecho::widget('feed.CategoryFeed', $feedType, $parameters, $link);
                    break;
                case 'index':
                default:
                    Typecho::widget('feed.PostsFeed', $feedType, $parameters, $link);
                    break;
            }
            
            return;
        }
        
        throw new TypechoWidgetException(_t('聚合页不存在'), TypechoException::NOTFOUND);
    }
}
