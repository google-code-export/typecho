<?php
/**
 * 评论设置
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 评论设置组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options_Discussion extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 输出表单结构
     * 
     * @access public
     * @return Typecho_Widget_Helper_Form
     */
    public function form()
    {
        /** 构建表格 */
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/action/options-discussion', $this->options->index),
        Typecho_Widget_Helper_Form::POST_METHOD);
        
        /** 评论日期格式 */
        $commentDateFormat = new Typecho_Widget_Helper_Form_Element_Text('commentDateFormat', NULL, $this->options->commentDateFormat,
        _t('评论日期格式'), _t('这是一个默认的格式,当你在模板中调用显示评论日期方法时, 如果没有指定日期格式, 将按照此格式输出.<br />
        具体写法请参考<a href="http://cn.php.net/manual/zh/function.date.php">PHP日期格式写法</a>.'));
        $form->addInput($commentDateFormat);
        
        /** 评论列表数目 */
        $commentsListSize = new Typecho_Widget_Helper_Form_Element_Text('commentsListSize', NULL, $this->options->commentsListSize,
        _t('评论列表数目'), _t('此数目用于指定显示在侧边拦中的评论列表数目.'));
        $commentsListSize->input->setAttribute('class', 'mini');
        $form->addInput($commentsListSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** 是否在列表中的评论者处显示其个人主页链接 */
        $commentsShowUrl = new Typecho_Widget_Helper_Form_Element_Radio('commentsShowUrl', array('0' => _t('不显示'), '1' => _t('显示')),
        $this->options->commentsShowUrl, _t('是否在列表中的评论者名称处显示其个人主页链接'),
        _t('如果你打开此选项, 当评论作者在提交评论时留下的个人主页地址, 将以链接的形式呈现出来.<br />
        在某些主题中此选项可能不会生效, 因为它可以在模板中被强行设置.'));
        $form->addInput($commentsShowUrl);
        
        /** 是否对评论者个人主页链接使用nofollow属性 */
        $commentsUrlNofollow = new Typecho_Widget_Helper_Form_Element_Radio('commentsUrlNofollow', array('0' => _t('不启用'), '1' => _t('启用')),
        $this->options->commentsUrlNofollow, _t('是否对评论者个人主页链接使用nofollow属性'),
        _t('当评论作者的个人主页地址在你的网站上呈现时, 其在搜索引擎中可能被识别为外链地址.<br />
        过多的外链地址将导致你的网站在搜索引擎中被降权, 打开此选项能帮助你解决此问题.<br />
        更多关于nofollow的信息请参考<a href="http://en.wikipedia.org/wiki/Nofollow">wikipedia上的解释</a>.'));
        $form->addInput($commentsUrlNofollow);
        
        /** 评论嵌套层数限制 */
        $commentsListSize = new Typecho_Widget_Helper_Form_Element_Text('commentsMaxNestingLevels', NULL, $this->options->commentsMaxNestingLevels,
        _t('评论嵌套层数限制'), _t('当模板中嵌套出现相关评论回复时, 此数值将用于限制嵌套评论最大的层数.<br />
        我们建议将层数限制在10以下.'));
        $commentsListSize->input->setAttribute('class', 'mini');
        $form->addInput($commentsListSize->addRule('isInteger', _t('请填入一个数字')));
        
        /** 评论审核 */
        $commentsRequireModeration = new Typecho_Widget_Helper_Form_Element_Radio('commentsRequireModeration', array('0' => _t('不启用'), '1' => _t('启用')),
        $this->options->commentsRequireModeration, _t('评论审核'),
        _t('打开此选项后,所有提交的评论,引用通告和广播将不会立即呈现, 而是被标记为待审核, 你可以在后台标记它们是否呈现.<br />
        被评论文章的作者和编辑及以上权限的用户不受此选项的约束.'));
        $form->addInput($commentsRequireModeration);
        
        /** 在文章发布一段时间后自动关闭评论和广播功能 */
        $commentsPostTimeout = new Typecho_Widget_Helper_Form_Element_Select('commentsPostTimeout', array('0' => _t('永不关闭'), '86400' => _t('一天后关闭'),
        '259200' => _t('三天后关闭'), '1296000' => _t('半个月后关闭'), '2592000' => _t('一个月后关闭'), '7776000' => _t('三个月后关闭'),
        '15552000' => _t('半年后关闭'), '31536000' => _t('一年后关闭')),
        $this->options->commentsPostTimeout, _t('在文章发布一段时间后自动关闭反馈功能'),
        _t('打开此选项后, 发布时间超过此设置文章的反馈功能将被关闭.<br />
        此选项可以帮助你抵御一部分垃圾评论, 但也有可能会让你失去一部分优秀的评论.'));
        $form->addInput($commentsPostTimeout);
        
        /** 必须填写邮箱 */
        $commentsRequireMail = new Typecho_Widget_Helper_Form_Element_Radio('commentsRequireMail', array('0' => _t('不需要'), '1' => _t('需要')),
        $this->options->commentsRequireMail, _t('必须填写邮箱'));
        $form->addInput($commentsRequireMail);
        
        /** 必须填写网址 */
        $commentsRequireURL = new Typecho_Widget_Helper_Form_Element_Radio('commentsRequireURL', array('0' => _t('不需要'), '1' => _t('需要')),
        $this->options->commentsRequireURL, _t('必须填写网址'));
        $form->addInput($commentsRequireURL);
        
        /** 允许使用的HTML标签和属性 */
        $commentsHTMLTagAllowed = new Typecho_Widget_Helper_Form_Element_Textarea('commentsHTMLTagAllowed', NULL,
        htmlspecialchars($this->options->commentsHTMLTagAllowed),
        _t('允许使用的HTML标签和属性'), _t('默认的用户评论不允许填写任何的HTML标签, 你可以在这里填写允许使用的HTML标签.<br />
        比如: &lt;a href=&quot;&quot;&gt; &lt;img src=&quot;&quot;&gt; &lt;blockquote&gt;'));
        $form->addInput($commentsHTMLTagAllowed);
        
        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit('submit', NULL, _t('保存设置'));
        $form->addItem($submit);
        
        return $form;
    }
    
    /**
     * 执行更新动作
     * 
     * @access public
     * @return void
     */
    public function updateDiscussionSettings()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }
    
        $settings = $this->request->from('commentDateFormat', 'commentsListSize', 'commentsShowUrl', 'commentsUrlNofollow',
                'commentsMaxNestingLevels', 'commentsUrlNofollow', 'commentsPostTimeout', 'commentsUniqueIpInterval', 'commentsRequireMail',
                'commentsRequireModeration', 'commentsRequireURL', 'commentsHTMLTagAllowed', 'commentsStopWords', 'commentsIpBlackList');
        foreach ($settings as $name => $value) {
            $this->update(array('value' => $value), $this->db->sql()->where('name = ?', $name));
        }

        $this->widget('Widget_Notice')->set(_t("设置已经保存"), NULL, 'success');
        $this->response->goBack();
    }

    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->on($this->request->isPost())->updateDiscussionSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
