<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 内容基类
 *
 * @package Widget
 */
class Widget_Abstract_Contents extends Widget_Abstract
{
    /**
     * 将tags取出
     * 
     * @access protected
     * @return array
     */
    protected function ___tags()
    {
        return $this->db->fetchAll($this->db
        ->select()->from('table.metas')
        ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
        ->where('table.relationships.cid = ?', $this->cid)
        ->where('table.metas.type = ?', 'tag'), array($this->widget('Widget_Abstract_Metas'), 'filter'));
    }
    
    /**
     * 文章作者
     * 
     * @access protected
     * @return string
     */
    protected function ___author()
    {
        return $this->db->fetchObject($this->db->select()->from('table.users')
        ->where('uid = ?', $this->authorId));
    }
    
    /**
     * 获取词义化日期
     * 
     * @access protected
     * @return void
     */
    protected function ___dateWord()
    {
        return Typecho_I18n::dateWord($this->created + $this->options->timezone, $this->options->gmtTime + $this->options->timezone);
    }
    
    /**
     * 获取文章摘要
     * 
     * @access protected
     * @return string
     */
    protected function ___excerpt()
    {
        return Typecho_Common::subStr(Typecho_Common::stripTags($this->text), 0, 100, '...');
    }

    /**
     * 获取查询对象
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.contents.authorId',
        'table.contents.modified', 'table.contents.type', 'table.contents.status', 'table.contents.text', 'table.contents.commentsNum', 'table.contents.meta',
        'table.contents.template', 'table.contents.password', 'table.contents.allowComment', 'table.contents.allowPing', 'table.contents.allowFeed')
        ->from('table.contents');
    }
    
    /**
     * 插入内容
     * 
     * @access public
     * @param array $content 内容数组
     * @return integer
     */
    public function insert(array $content)
    {
        /** 构建插入结构 */
        $insertStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'created'       =>  empty($content['created']) ? $this->options->gmtTime : $content['created'],
            'modified'      =>  $this->options->gmtTime,
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'meta'          =>  empty($content['meta']) ? intval($content['meta']) : 0,
            'authorId'      =>  isset($content['authorId']) ? $content['authorId'] : $this->user->uid,
            'template'      =>  empty($content['template']) ? NULL : $content['template'],
            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
            'status'        =>  empty($content['status']) ? 'publish' : $content['status'],
            'password'      =>  empty($content['password']) ? NULL : $content['password'],
            'commentsNum'   =>  0,
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 1 : 0,
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 1 : 0,
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 1 : 0,
        );
        
        /** 首先插入部分数据 */
        $insertId = $this->db->query($this->db->insert('table.contents')->rows($insertStruct));
        /** 更新缩略名 */
        $slug = Typecho_Common::slugName(empty($content['slug']) ? NULL : $content['slug'], $insertId);
        $this->db->query($this->db->update('table.contents')
        ->rows(array('slug' => $slug))
        ->where('cid = ?', $insertId));

        return $insertId;
    }
    
    /**
     * 更新内容
     * 
     * @access public
     * @param array $content 内容数组
     * @param Typecho_Db_Query $condition 更新条件
     * @return integer
     */
    public function update(array $content, Typecho_Db_Query $condition)
    {
        /** 首先验证写入权限 */
        if (!$this->postIsWriteable(clone $condition)) {
            return false;
        }
    
        /** 构建更新结构 */
        $preUpdateStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'meta'          =>  empty($content['meta']) ? intval($content['meta']) : 0,
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'template'      =>  empty($content['template']) ? NULL : $content['template'],
            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
            'status'        =>  empty($content['status']) ? 'publish' : $content['status'],
            'password'      =>  empty($content['password']) ? NULL : $content['password'],
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 1 : 0,
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 1 : 0,
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 1 : 0,
        );
        
        $updateStruct = array();
        foreach ($content as $key => $val) {
            if (array_key_exists($key, $preUpdateStruct)) {
                $updateStruct[$key] = $preUpdateStruct[$key];
            }
        }
        
        /** 更新创建时间 */
        if (!empty($content['created'])) {
            $updateStruct['created'] = $content['created'];
        }
        
        $updateStruct['modified'] = $this->options->gmtTime;
        
        /** 首先插入部分数据 */
        $updateCondition = clone $condition;
        $updateRows = $this->db->query($condition->update('table.contents')->rows($updateStruct));
        
        /** 更新缩略名 */
        $slug = Typecho_Common::slugName(empty($content['slug']) ? NULL : $content['slug'], $cid);
        $this->db->query($updateCondition->update('table.contents')->rows(array('slug' => $slug)));

        return $updateRows;
    }
    
    /**
     * 删除内容
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->delete('table.contents'));
    }

    /**
     * 内容是否可以被修改
     * 
     * @access public
     * @param Typecho_Db_Query $condition 条件
     * @return mixed
     */
    public function postIsWriteable(Typecho_Db_Query $condition = NULL)
    {
        if (empty($condition)) {
            if ($this->have() && ($this->user->pass('editor', true) || $this->authorId == $this->user->uid)) {
                return true;
            }
        } else {
            $post = $this->db->fetchRow($condition->select('authorId')->from('table.contents')->limit(1));

            if ($post && ($this->user->pass('editor', true) || $post['authorId'] == $this->user->uid)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 按照条件计算内容数量
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select(array('COUNT(table.contents.cid)' => 'num'))->from('table.contents'))->num;
    }
    
    /**
     * 通用过滤器
     * 
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     */
    public function filter(array $value)
    {
        /** 取出所有分类 */
        $value['categories'] = $this->db->fetchAll($this->db
        ->select()->from('table.metas')
        ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
        ->where('table.relationships.cid = ?', $value['cid'])
        ->where('table.metas.type = ?', 'category')
        ->order('table.metas.sort', Typecho_Db::SORT_ASC), array($this->widget('Widget_Abstract_Metas'), 'filter'));
        
        /** 取出第一个分类作为slug条件 */
        $value['category'] = current(Typecho_Common::arrayFlatten($value['categories'], 'slug'));

        /** 生成日期 */
        $value['year'] = date('Y', $value['created'] + $this->options->timezone);
        $value['month'] = date('m', $value['created'] + $this->options->timezone);
        $value['day'] = date('d', $value['created'] + $this->options->timezone);
        
        /** 生成访问权限 */
        $value['hidden'] = false;

        /** 获取路由类型并判断此类型在路由表中是否存在 */
        $type = $value['type'];
        $routeExists = (NULL != Typecho_Router::get($type));
        
        $tmpSlug = $value['slug'];
        $value['slug'] = urlencode($value['slug']);
        
        /** 生成静态路径 */
        $linkPath = $routeExists ? Typecho_Router::url($type, $value) : '#';
        
        /** 生成反馈地址 */
        /** 评论 */
        $value['commentUrl'] = Typecho_Router::url('feedback', 
        array('type' => 'comment', 'permalink' => $linkPath), $this->options->index);
        
        /** trackback */
        $value['trackbackUrl'] = Typecho_Router::url('feedback', 
        array('type' => 'trackback', 'permalink' => $linkPath), $this->options->index);
        
        /** 生成静态链接 */
        $value['permalink'] = Typecho_Common::url($linkPath, $this->options->index);
        
        /** 生成聚合链接 */
        /** RSS 2.0 */
        $value['feedUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedUrl) : '#';
        
        /** RSS 1.0 */
        $value['feedRssUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedRssUrl) : '#';
        
        /** ATOM 1.0 */
        $value['feedAtomUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedAtomUrl) : '#';
        
        $value['slug'] = $tmpSlug;

        /** 处理密码保护流程 */
        if (!empty($value['password']) &&
        $value['password'] != $this->request->protectPassword &&
        ($value['authorId'] != $this->user->uid
        || !$this->user->pass('editor', true))) {
            $value['hidden'] = true;
        
            /** 抛出错误 */
            if ($this->request->isPost() && $this->request->isSetParameter('protectPassword')) {
                throw new Typecho_Widget_Exception(_t('对不起,您输入的密码错误'), 403);
            }
        }
        
        $value = $this->plugin(__CLASS__)->filter($value);

        /** 如果访问权限被禁止 */
        if ($value['hidden']) {
            $value['text'] = '<form class="protected" action="' . $value['permalink'] . '" method="post">' .
            '<p class="word">' . _t('请输入密码访问') . '</p>' .
            '<p><input type="password" class="text" name="protectPassword" />
            <input type="submit" class="submit" value="' . _t('提交') . '" /></p>' .
            '</form>';
            
            $value['title'] = _t('此内容被密码保护');
            $value['tags'] = array();
            $value['commentsNum'] = 0;
        }
        
        return $value;
    }

    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        $value = $this->filter($value);
        return parent::push($value);
    }

    /**
     * 输出文章发布日期
     *
     * @access public
     * @param string $format 日期格式
     * @return void
     */
    public function date($format = NULL)
    {
        echo date(empty($format) ? $this->options->postDateFormat : $format, $this->created + $this->options->timezone);
    }

    /**
     * 输出文章内容
     *
     * @access public
     * @param string $more 文章截取后缀
     * @return void
     */
    public function content($more = NULL)
    {
        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
        $contents = explode('<!--more-->', $content);
        
        list($abstract) = $contents;
        echo NULL === $more ? $content : (Typecho_Common::fixHtml($abstract) . (count($contents) > 1 ? '<p class="more"><a href="'
        . $this->permalink . '">' . $more . '</a></p>' : NULL));
    }

    /**
     * 输出文章摘要
     *
     * @access public
     * @param integer $length 摘要截取长度
     * @param string $trim 摘要后缀
     * @return void
     */
    public function excerpt($length = 100, $trim = '...')
    {
        echo Typecho_Common::subStr(Typecho_Common::stripTags($this->text), 0, $length, $trim);
    }

    /**
     * 输出文章评论数
     *
     * @access public
     * @param string $string 评论数格式化数据
     * @return void
     */
    public function commentsNum($string = 'Comments %d')
    {
        $args = func_get_args();
        $num = intval($this->commentsNum);
        
        echo sprintf(isset($args[$num]) ? $args[$num] : array_pop($args), $num);
    }

    /**
     * 获取文章权限
     *
     * @access public
     * @param string $permission 权限
     * @return unknown
     */
    public function allow()
    {
        $permissions = func_get_args();
        $allow = true;

        foreach ($permissions as $permission) {
            $permission = strtolower($permission);

            /** 对自动关闭反馈功能的支持 */
            if (('ping' == $permission || 'comment' == $permission) && $this->options->commentsPostTimeout > 0) {
                if ($this->options->gmtTime - $this->created > $this->options->commentsPostTimeout) {
                    return false;
                }
            }
            
            $allow &= ($this->row['allow' . ucfirst($permission)] == 1);
        }

        return $allow and !$this->hidden;
    }

    /**
     * 输出文章分类
     *
     * @access public
     * @param string $split 多个分类之间分隔符
     * @param boolean $link 是否输出链接
     * @param string $default 如果没有则输出
     * @return void
     */
    public function category($split = ',', $link = true, $default = NULL)
    {
        $categories = $this->categories;
        if ($categories) {
            $result = array();
            
            foreach ($categories as $category) {
                $result[] = $link ? '<a href="' . $category['permalink'] . '">'
                . $category['name'] . '</a>' : $category['name'];
            }

            echo implode($split, $result);
        } else {
            echo $default;
        }
    }

    /**
     * 输出文章标签
     *
     * @access public
     * @param string $split 多个标签之间分隔符
     * @param boolean $link 是否输出链接
     * @param string $default 如果没有则输出
     * @return void
     */
    public function tags($split = ',', $link = true, $default = NULL)
    {
        /** 取出tags */
        if ($this->tags) {
            $result = array();
            foreach ($this->tags as $tag) {
                $result[] = $link ? '<a href="' . $tag['permalink'] . '">'
                . $tag['name'] . '</a>' : $tag['name'];
            }

            echo implode($split, $result);
        } else {
            echo $default;
        }
    }
    
    /**
     * 输出当前作者
     * 
     * @access public
     * @param string $item 需要输出的项目
     * @return void
     */
    public function author($item = 'screenName')
    {
        echo $this->author->{$item};
    }
}
