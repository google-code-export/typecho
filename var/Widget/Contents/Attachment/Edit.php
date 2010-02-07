<?php
/**
 * 编辑文章
 *
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑文章组件
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Attachment_Edit extends Widget_Contents_Post_Edit implements Widget_Interface_Do
{
    /**
     * 获取页面偏移的URL Query
     *
     * @access protected
     * @param integer $cid 附件id
     * @param string $status 状态
     * @return string
     */
    protected function getPageOffsetQuery($cid, $status = NULL)
    {
        return 'page=' . $this->getPageOffset('cid', $cid, 'attachment', $status,
        $this->user->pass('editor', true) ? 0 : $this->user->uid);
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 必须为贡献者以上权限 */
        $this->user->pass('contributor');

        /** 获取文章内容 */
        if ((isset($this->request->cid) && 'delete' != $this->request->do
         && 'insert' != $this->request->do) || 'update' == $this->request->do) {
            $this->db->fetchRow($this->select()
            ->where('table.contents.type = ?', 'attachment')
            ->where('table.contents.cid = ?', $this->request->filter('int')->cid)
            ->limit(1), array($this, 'push'));

            if (!$this->have()) {
                throw new Typecho_Widget_Exception(_t('附件不存在'), 404);
            } else if ($this->have() && !$this->allow('edit')) {
                throw new Typecho_Widget_Exception(_t('没有编辑权限'), 403);
            }
        }
    }

    /**
     * 判断附件名转换到缩略名后是否合法
     *
     * @access public
     * @param string $name 附件名
     * @return boolean
     */
    public function nameToSlug($name)
    {
        if (empty($this->request->slug)) {
            $slug = Typecho_Common::slugName($name);
            if (empty($slug) || !$this->slugExists($name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 判断附件缩略名是否存在
     *
     * @access public
     * @param string $slug 缩略名
     * @return boolean
     */
    public function slugExists($slug)
    {
        $select = $this->db->select()
        ->from('table.contents')
        ->where('type = ?', 'attachment')
        ->where('slug = ?', Typecho_Common::slugName($slug))
        ->limit(1);

        if ($this->request->cid) {
            $select->where('cid <> ?', $this->request->cid);
        }

        $attachment = $this->db->fetchRow($select);
        return $attachment ? false : true;
    }

    /**
     * 生成表单
     *
     * @access public
     * @param string $action 表单动作
     * @return Typecho_Widget_Helper_Form_Element
     */
    public function form($action = NULL)
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/action/contents-attachment-edit', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);

        /** 附件名称 */
        $name = new Typecho_Widget_Helper_Form_Element_Text('name', NULL, $this->title, _t('标题*'));
        $form->addInput($name);

        /** 附件缩略名 */
        $slug = new Typecho_Widget_Helper_Form_Element_Text('slug', NULL, $this->slug, _t('缩略名'),
        _t('附件缩略名用于创建友好的链接形式,建议使用字母,数字,下划线和横杠.'));
        $form->addInput($slug);

        /** 附件描述 */
        $description =  new Typecho_Widget_Helper_Form_Element_Textarea('description', NULL, $this->attachment->description,
        _t('描述'), _t('此文字用于描述附件,在有的主题中它会被显示.'));
        $form->addInput($description);

        /** 分类动作 */
        $do = new Typecho_Widget_Helper_Form_Element_Hidden('do', NULL, 'update');
        $form->addInput($do);

        /** 分类主键 */
        $cid = new Typecho_Widget_Helper_Form_Element_Hidden('cid', NULL, $this->cid);
        $form->addInput($cid);

        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit(NULL, NULL, _t('提交修改'));
        $delete = new Typecho_Widget_Helper_Layout('a', array('href' =>
        Typecho_Common::url('/action/contents-attachment-edit?do=delete&cid=' . $this->cid, $this->options->index),
        'class' => 'operate-delete',
        'lang'  => _t('你确认删除附件 %s 吗?', $this->attachment->name)));
        $submit->container($delete->html(_t('删除附件')));
        $form->addItem($submit);

        $name->addRule('required', _t('必须填写附件标题'));
        $name->addRule(array($this, 'nameToSlug'), _t('附件标题无法被转换为缩略名'));
        $slug->addRule(array($this, 'slugExists'), _t('缩略名已经存在'));

        return $form;
    }

    /**
     * 更新附件
     *
     * @access public
     * @return void
     */
    public function updateAttachment()
    {
        if ($this->form('update')->validate()) {
            $this->response->goBack();
        }

        /** 取出数据 */
        $input = $this->request->from('name', 'slug', 'description');
        $input['slug'] = Typecho_Common::slugName(empty($input['slug']) ? $input['name'] : $input['slug']);

        $attachment['title'] = $input['name'];
        $attachment['slug'] = $input['slug'];

        $content = unserialize($this->attachment->__toString());
        $content['description'] = $input['description'];

        $attachment['text'] = serialize($content);
        $cid = $this->request->filter('int')->cid;

        /** 更新数据 */
        $updateRows = $this->update($attachment, $this->db->sql()->where('cid = ?', $cid));

        if ($updateRows > 0) {

            $this->db->fetchRow($this->select()
                ->where('table.contents.type = ?', 'attachment')
                ->where('table.contents.cid = ?', $cid)
                ->limit(1), array($this, 'push'));

            /** 设置高亮 */
            $this->widget('Widget_Notice')->highlight($this->theId);

            /** 提示信息 */
            $this->widget('Widget_Notice')->set('publish' == $this->status ?
            _t('附件 <a href="%s">%s</a> 已经被更新', $this->permalink, $this->title) :
            _t('未归档附件 %s 已经被更新', $this->title), NULL, 'success');

        }

        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('manage-medias.php?' .
        $this->getPageOffsetQuery($cid, $this->status), $this->options->adminUrl));
    }

    /**
     * 删除文章
     *
     * @access public
     * @return void
     */
    public function deleteAttachment()
    {
        $cid = $this->request->filter('int')->cid;
        $deleteCount = 0;
        $status = 'publish';

        if ($cid) {
            /** 格式化文章主键 */
            $posts = is_array($cid) ? $cid : array($cid);
            foreach ($posts as $post) {

                $condition = $this->db->sql()->where('cid = ?', $post);
                $row = $this->db->fetchRow($this->select()
                ->where('table.contents.type = ?', 'attachment')
                ->where('table.contents.cid = ?', $post)
                ->limit(1), array($this, 'push'));

                if ($this->isWriteable($condition) && $this->delete($condition)) {
                    /** 删除文件 */
                    Widget_Upload::deleteHandle($row);

                    /** 删除评论 */
                    $this->db->query($this->db->delete('table.comments')
                    ->where('cid = ?', $post));

                    $status = $this->status;

                    $deleteCount ++;
                }

                unset($condition);
            }
        }

        if ($this->request->isAjax()) {
            $this->response->throwJson($deleteCount > 0 ? array('code' => 200, 'message' => _t('附件已经被删除'))
            : array('code' => 500, 'message' => _t('没有附件被删除')));
        } else {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($deleteCount > 0 ? _t('附件已经被删除') : _t('没有附件被删除'), NULL,
            $deleteCount > 0 ? 'success' : 'notice');

            /** 返回原网页 */
            $this->response->redirect(Typecho_Common::url('manage-medias.php', $this->options->adminUrl));
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
        $this->on($this->request->is('do=delete'))->deleteAttachment();
        $this->on($this->request->is('do=update'))->updateAttachment();
        $this->response->redirect($this->options->adminUrl);
    }
}
