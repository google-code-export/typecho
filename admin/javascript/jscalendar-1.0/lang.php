<?php
include '../../common.php';
Typecho_Response::setContentType('text/javascript');
?>
// ** I18N

// Calendar ZH language
// Author: muziq, <muziq@sina.com>
// Encoding: GB2312 or GBK
// Distributed under the same terms as the calendar itself.

// full day names
Calendar._DN = new Array
("<?php _e('星期日'); ?>",
 "<?php _e('星期一'); ?>",
 "<?php _e('星期二'); ?>",
 "<?php _e('星期三'); ?>",
 "<?php _e('星期四'); ?>",
 "<?php _e('星期五'); ?>",
 "<?php _e('星期六'); ?>",
 "<?php _e('星期日'); ?>");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

// short day names
Calendar._SDN = new Array
("<?php _e('日'); ?>",
 "<?php _e('一'); ?>",
 "<?php _e('二'); ?>",
 "<?php _e('三'); ?>",
 "<?php _e('四'); ?>",
 "<?php _e('五'); ?>",
 "<?php _e('六'); ?>",
 "<?php _e('日'); ?>");

// full month names
Calendar._MN = new Array
("<?php _e('一月'); ?>",
 "<?php _e('二月'); ?>",
 "<?php _e('三月'); ?>",
 "<?php _e('四月'); ?>",
 "<?php _e('五月'); ?>",
 "<?php _e('六月'); ?>",
 "<?php _e('七月'); ?>",
 "<?php _e('八月'); ?>",
 "<?php _e('九月'); ?>",
 "<?php _e('十月'); ?>",
 "<?php _e('十一月'); ?>",
 "<?php _e('十二月'); ?>");

// short month names
Calendar._SMN = new Array
("<?php _e('一月'); ?>",
 "<?php _e('二月'); ?>",
 "<?php _e('三月'); ?>",
 "<?php _e('四月'); ?>",
 "<?php _e('五月'); ?>",
 "<?php _e('六月'); ?>",
 "<?php _e('七月'); ?>",
 "<?php _e('八月'); ?>",
 "<?php _e('九月'); ?>",
 "<?php _e('十月'); ?>",
 "<?php _e('十一月'); ?>",
 "<?php _e('十二月'); ?>");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "<?php _e('帮助'); ?>";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"<?php _e('选择日期'); ?>:\n" +
"<?php _e('- 点击 \xab, \xbb 按钮选择年份'); ?>\n" +
"<?php _e('- 点击 " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " 按钮选择月份'); ?>\n" +
"- <?php _e('长按以上按钮可从菜单中快速选择年份或月份'); ?>";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"<?php _e('选择时间'); ?>:\n" +
"- <?php _e('点击小时或分钟可使改数值加一'); ?>\n" +
"- <?php _e('按住Shift键点击小时或分钟可使改数值减一'); ?>\n" +
"- <?php _e('点击拖动鼠标可进行快速选择'); ?>";

Calendar._TT["PREV_YEAR"] = "<?php _e('上一年 (按住出菜单)'); ?>";
Calendar._TT["PREV_MONTH"] = "<?php _e('上一月 (按住出菜单)'); ?>";
Calendar._TT["GO_TODAY"] = "<?php _e('转到今日'); ?>";
Calendar._TT["NEXT_MONTH"] = "<?php _e('下一月 (按住出菜单)'); ?>";
Calendar._TT["NEXT_YEAR"] = "<?php _e('下一年 (按住出菜单)'); ?>";
Calendar._TT["SEL_DATE"] = "<?php _e('选择日期'); ?>";
Calendar._TT["DRAG_TO_MOVE"] = "<?php _e('拖动'); ?>";
Calendar._TT["PART_TODAY"] = " <?php _e('(今日)'); ?>";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "<?php _e('最左边显示%s'); ?>";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "<?php _e('0,6'); ?>";

Calendar._TT["CLOSE"] = "<?php _e('关闭'); ?>";
Calendar._TT["TODAY"] = "<?php _e('今日'); ?>";
Calendar._TT["TIME_PART"] = "<?php _e('(Shift-)点击鼠标或拖动改变值'); ?>";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "<?php _e('%Y-%m-%d'); ?>";
Calendar._TT["TT_DATE_FORMAT"] = "<?php _e('%A, %b %e日'); ?>";

Calendar._TT["WK"] = "<?php _e('周'); ?>";
Calendar._TT["TIME"] = "<?php _e('时间'); ?>:";
