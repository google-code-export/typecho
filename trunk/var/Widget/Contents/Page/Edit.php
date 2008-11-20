<?php
/**
 * 编辑页面
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑页面组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Page_Edit extends Widget_Abstract_Contents implements Widget_Interface_Action_Widget
{
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Typecho_API::factory('Widget_Users_Current')->pass('editor');
    
        /** 获取页面内容 */
        if (Typecho_Request::getParameter('cid')) {
            $this->db->fetchRow($this->select()->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'page', 'page_draft')
            ->where('table.contents.`cid` = ?', Typecho_Request::getParameter('cid'))
            ->limit(1), array($this, 'push'));
            Typecho_API::factory('Widget_Menu')->title = _t('编辑页面');
        }
    }
    
    /**
     * 获取创建GMT时间戳
     * 
     * @access public
     * @return integer
     */
    public function getCreated()
    {
        if (!($date = Typecho_Request::getParameter('date'))) {
            $date = date('Y-m-d');
        }
        
        if (!($time = Typecho_Request::getParameter('time'))) {
            $time = date('g:i A');
        }
        
        return strtotime($date . ' ' . $time) - $this->options->timezone;
    }
    
    /**
     * 新增页面
     * 
     * @access public
     * @return void
     */
    public function insertPage()
    {
        $contents = Typecho_Request::getParametersFrom('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'meta');
        $contents['type'] = (1 == Typecho_Request::getParameter('draft')) ? 'page_draft' : 'page';
        $contents['title'] = (NULL == Typecho_Request::getParameter('title')) ? 
        _t('未命名文档') : Typecho_Request::getParameter('title');
        $contents['created'] = $this->getCreated();
    
        $insertId = $this->insert($contents);
        
        $this->db->fetchRow($this->select()->group('table.contents.`cid`')
        ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'page', 'page_draft')
        ->where('table.contents.`cid` = ?', $insertId)->limit(1), array($this, 'push'));
        
        /** 页面提示信息 */
        if ('page' == $contents['type']) {
            Typecho_API::factory('Widget_Notice')->set($insertId > 0 ? 
            _t("页面 '<a href=\"%s\">%s</a>' 已经被创建", $this->permalink, $this->title)
            : _t('页面提交失败'), NULL, $insertId > 0 ? 'success' : 'error');
        } else {
            Typecho_API::factory('Widget_Notice')->set($insertId > 0 ? 
            _t("草稿 '%s' 已经被保存", $this->title) :
            _t('草稿保存失败'), NULL, $insertId > 0 ? 'success' : 'error');
        }

        /** 跳转页面 */
        if (1 == Typecho_Request::getParameter('continue')) {
            Typecho_API::redirect(Typecho_API::pathToUrl('edit.php?cid=' . $this->cid, $this->options->adminUrl));
        } else {
            Typecho_API::redirect(Typecho_API::pathToUrl('page-list.php', $this->options->adminUrl));
        }
    }
    
    /**
     * 更新页面
     * 
     * @access public
     * @return void
     */
    public function updatePage()
    {
        $validator = new Typecho_Validate();
        $validator->addRule('cid', 'required', _t('页面不存在'));
        $validator->run(Typecho_Request::getParametersFrom('cid'));
        
        $select = $this->select()->group('table.contents.`cid`')
        ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'page', 'page_draft')
        ->where('table.contents.`cid` = ?', Typecho_Request::getParameter('cid'))
        ->limit(1);
        
        $exists = $this->db->fetchRow($select);
        
        if (!$exists) {
            throw new Typecho_Widget_Exception(_t('页面不存在'), Typecho_Exception::NOTFOUND);
        }
    
        $contents = Typecho_Request::getParametersFrom('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'meta');
        $contents['type'] = (1 == Typecho_Request::getParameter('draft')) ? 'page_draft' : 'page';
        $contents['title'] = (NULL == Typecho_Request::getParameter('title')) ? 
        _t('未命名文档') : Typecho_Request::getParameter('title');
        $contents['created'] = $this->getCreated();
    
        $updateRows = $this->update($contents, $this->db->sql()->where('`cid` = ?', Typecho_Request::getParameter('cid')));
        $this->db->fetchRow($select, array($this, 'push'));

        /** 页面提示信息 */
        if ('page' == $this->type) {
            Typecho_API::factory('Widget_Notice')->set($updateRows > 0 ? 
            _t("页面 '<a href=\"%s\">%s</a>' 已经被更新", $this->permalink, $this->title)
            : _t('页面提交失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        } else {
            Typecho_API::factory('Widget_Notice')->set($updateRows > 0 ? 
            _t("草稿 '%s' 已经被保存", $this->title) :
            _t('草稿保存失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        }

        /** 跳转页面 */
        if (1 == Typecho_Request::getParameter('continue')) {
            Typecho_API::redirect(Typecho_API::pathToUrl('edit.php?cid=' . $this->cid, $this->options->adminUrl));
        } else {
            Typecho_API::redirect(Typecho_API::pathToUrl('page-list.php', $this->options->adminUrl));
        }
    }
    
    /**
     * 删除页面
     * 
     * @access public
     * @return void
     */
    public function deletePage()
    {
        $cid = Typecho_Request::getParameter('cid');
        $deleteCount = 0;

        if ($cid) {
            /** 格式化页面主键 */
            $pages = is_array($cid) ? $cid : array($cid);
            foreach ($pages as $page) {
                if ($this->delete($this->db->sql()->where('`cid` = ?', $page))) {
                    /** 删除评论 */
                    $this->db->query($this->db->sql()->delete('table.comments')
                    ->where('`cid` = ?', $page));
                    
                    $deleteCount ++;
                }
            }
        }
        
        /** 设置提示信息 */
        Typecho_API::factory('Widget_Notice')->set($deleteCount > 0 ? _t('页面已经被删除') : _t('没有页面被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        Typecho_API::goBack();
    }
    
    /**
     * 页面排序
     * 
     * @access public
     * @return void
     */
    public function sortPage()
    {
        $pages = Typecho_Request::getParameter('sort');
        
        if ($pages && is_array($pages)) {
            foreach ($pages as $sort => $cid) {
                $this->db->query($this->db->sql()->update('table.contents')->row('meta', $sort + 1)
                ->where('`cid` = ?', $cid));
            }
        }
        
        if (!Typecho_Request::isAjax()) {
            /** 转向原页 */
            Typecho_API::redirect(Typecho_API::pathToUrl('page-list.php', $this->options->adminUrl));
        } else {
            Typecho_API::throwAjaxResponse(_t('页面排序已经完成'), $this->options->charset);
        }
    }
    
    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        Typecho_Request::bindParameter(array('do' => 'insert'), array($this, 'insertPage'));
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updatePage'));
        Typecho_Request::bindParameter(array('do' => 'delete'), array($this, 'deletePage'));
        Typecho_Request::bindParameter(array('do' => 'sort'), array($this, 'sortPage'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
