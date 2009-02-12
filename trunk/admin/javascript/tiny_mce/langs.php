<?php
include '../../common.php';
Typecho_Response::setContentType('text/javascript');
?>

tinyMCE.addI18n({en:{
common:{
edit_confirm:"Do you want to use the WYSIWYG mode for this textarea?",
apply:"Apply",
insert:"Insert",
update:"Update",
cancel:"Cancel",
close:"Close",
browse:"Browse",
class_name:"Class",
not_set:"-- Not set --",
clipboard_msg:"Copy/Cut/Paste is not available in Mozilla and Firefox.\nDo you want more information about this issue?",
clipboard_no_support:"Currently not supported by your browser, use keyboard shortcuts instead.",
popup_blocked:"Sorry, but we have noticed that your popup-blocker has disabled a window that provides application functionality. You will need to disable popup blocking on this site in order to fully utilize this tool.",
invalid_data:"Error: Invalid values entered, these are marked in red.",
more_colors:"More colors"
},
contextmenu:{
align:"Alignment",
left:"Left",
center:"Center",
right:"Right",
full:"Full"
},
insertdatetime:{
date_fmt:"%Y-%m-%d",
time_fmt:"%H:%M:%S",
insertdate_desc:"Insert date",
inserttime_desc:"Insert time",
months_long:"January,February,March,April,May,June,July,August,September,October,November,December",
months_short:"Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec",
day_long:"Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday",
day_short:"Sun,Mon,Tue,Wed,Thu,Fri,Sat,Sun"
},
print:{
print_desc:"Print"
},
preview:{
preview_desc:"Preview"
},
directionality:{
ltr_desc:"Direction left to right",
rtl_desc:"Direction right to left"
},
layer:{
insertlayer_desc:"Insert new layer",
forward_desc:"Move forward",
backward_desc:"Move backward",
absolute_desc:"Toggle absolute positioning",
content:"New layer..."
},
save:{
save_desc:"Save",
cancel_desc:"Cancel all changes"
},
nonbreaking:{
nonbreaking_desc:"Insert non-breaking space character"
},
iespell:{
iespell_desc:"Run spell checking",
download:"ieSpell not detected. Do you want to install it now?"
},
advhr:{
advhr_desc:"Horizontal rule"
},
emotions:{
emotions_desc:"Emotions"
},
searchreplace:{
search_desc:"Find",
replace_desc:"Find/Replace"
},
advimage:{
image_desc:"Insert/edit image"
},
advlink:{
link_desc:"Insert/edit link"
},
xhtmlxtras:{
cite_desc:"Citation",
abbr_desc:"Abbreviation",
acronym_desc:"Acronym",
del_desc:"Deletion",
ins_desc:"Insertion",
attribs_desc:"Insert/Edit Attributes"
},
style:{
desc:"Edit CSS Style"
},
paste:{
paste_text_desc:"Paste as Plain Text",
paste_word_desc:"Paste from Word",
selectall_desc:"Select All"
},
paste_dlg:{
text_title:"Use CTRL+V on your keyboard to paste the text into the window.",
text_linebreaks:"Keep linebreaks",
word_title:"Use CTRL+V on your keyboard to paste the text into the window."
},
table:{
desc:"Inserts a new table",
row_before_desc:"Insert row before",
row_after_desc:"Insert row after",
delete_row_desc:"Delete row",
col_before_desc:"Insert column before",
col_after_desc:"Insert column after",
delete_col_desc:"Remove column",
split_cells_desc:"Split merged table cells",
merge_cells_desc:"Merge table cells",
row_desc:"Table row properties",
cell_desc:"Table cell properties",
props_desc:"Table properties",
paste_row_before_desc:"Paste table row before",
paste_row_after_desc:"Paste table row after",
cut_row_desc:"Cut table row",
copy_row_desc:"Copy table row",
del:"Delete table",
row:"Row",
col:"Column",
cell:"Cell"
},
autosave:{
unload_msg:"The changes you made will be lost if you navigate away from this page."
},
fullscreen:{
desc:"Toggle fullscreen mode"
},
media:{
desc:"Insert / edit embedded media",
edit:"Edit embedded media"
},
fullpage:{
desc:"Document properties"
},
template:{
desc:"Insert predefined template content"
},
visualchars:{
desc:"Visual control characters on/off."
},
spellchecker:{
desc:"Toggle spellchecker",
menu:"Spellchecker settings",
ignore_word:"Ignore word",
ignore_words:"Ignore all",
langs:"Languages",
wait:"Please wait...",
sug:"Suggestions",
no_sug:"No suggestions",
no_mpell:"No misspellings found."
},
pagebreak:{
desc:"Insert page break."
}}});

tinyMCE.addI18n('en.advanced',{
style_select:"<?php _e('样式'); ?>",
font_size:"<?php _e('字体样式'); ?>",
fontdefault:"<?php _e('字体'); ?>",
block:"<?php _e('格式'); ?>",
paragraph:"<?php _e('段落'); ?>",
div:"<?php _e('布局'); ?>",
address:"<?php _e('地址'); ?>",
pre:"Preformatted",
h1:"Heading 1",
h2:"Heading 2",
h3:"Heading 3",
h4:"Heading 4",
h5:"Heading 5",
h6:"Heading 6",
blockquote:"<?php _e('引用'); ?>",
code:"Code",
samp:"Code sample",
dt:"Definition term ",
dd:"Definition description",
bold_desc:"Bold (Ctrl+B)",
italic_desc:"Italic (Ctrl+I)",
underline_desc:"Underline (Ctrl+U)",
striketrough_desc:"Strikethrough",
justifyleft_desc:"Align left",
justifycenter_desc:"Align center",
justifyright_desc:"Align right",
justifyfull_desc:"Align full",
bullist_desc:"Unordered list",
numlist_desc:"Ordered list",
outdent_desc:"Outdent",
indent_desc:"Indent",
undo_desc:"Undo (Ctrl+Z)",
redo_desc:"Redo (Ctrl+Y)",
link_desc:"Insert/edit link",
unlink_desc:"Unlink",
image_desc:"Insert/edit image",
cleanup_desc:"Cleanup messy code",
code_desc:"Edit HTML Source",
sub_desc:"Subscript",
sup_desc:"Superscript",
hr_desc:"Insert horizontal ruler",
removeformat_desc:"Remove formatting",
custom1_desc:"Your custom description here",
forecolor_desc:"Select text color",
backcolor_desc:"Select background color",
charmap_desc:"Insert custom character",
visualaid_desc:"Toggle guidelines/invisible elements",
anchor_desc:"Insert/edit anchor",
cut_desc:"Cut",
copy_desc:"Copy",
paste_desc:"Paste",
image_props_desc:"Image properties",
newdocument_desc:"New document",
help_desc:"Help",
blockquote_desc:"<?php _e('引用'); ?>",
clipboard_msg:"Copy/Cut/Paste is not available in Mozilla and Firefox.\r\nDo you want more information about this issue?",
path:"Path",
newdocument:"Are you sure you want clear all contents?",
toolbar_focus:"Jump to tool buttons - Alt+Q, Jump to editor - Alt-Z, Jump to element path - Alt-X",
more_colors:"More colors"
});

tinyMCE.addI18n('en.advanced_dlg',{
about_title:"About TinyMCE",
about_general:"About",
about_help:"Help",
about_license:"License",
about_plugins:"Plugins",
about_plugin:"Plugin",
about_author:"Author",
about_version:"Version",
about_loaded:"Loaded plugins",
anchor_title:"Insert/edit anchor",
anchor_name:"Anchor name",
code_title:"HTML Source Editor",
code_wordwrap:"Word wrap",
colorpicker_title:"Select a color",
colorpicker_picker_tab:"Picker",
colorpicker_picker_title:"Color picker",
colorpicker_palette_tab:"Palette",
colorpicker_palette_title:"Palette colors",
colorpicker_named_tab:"Named",
colorpicker_named_title:"Named colors",
colorpicker_color:"Color:",
colorpicker_name:"Name:",
charmap_title:"Select custom character",
image_title:"<?php _e('插入/编辑图片'); ?>",
image_src:"<?php _e('图片地址'); ?>",
image_alt:"<?php _e('图片描述'); ?>",
image_list:"<?php _e('图片列表'); ?>",
image_border:"<?php _e('边框'); ?>",
image_dimensions:"<?php _e('尺寸'); ?>",
image_vspace:"Vertical space",
image_hspace:"Horizontal space",
image_align:"<?php _e('对齐'); ?>",
image_align_baseline:"<?php _e('基线对齐'); ?>",
image_align_top:"<?php _e('居上'); ?>",
image_align_middle:"<?php _e('居中'); ?>",
image_align_bottom:"<?php _e('居下'); ?>",
image_align_texttop:"<?php _e('文字上方'); ?>",
image_align_textbottom:"<?php _e('文字下方'); ?>",
image_align_left:"<?php _e('居左'); ?>",
image_align_right:"<?php _e('居右'); ?>",
link_title:"<?php _e('插入/编辑链接'); ?>",
link_url:"<?php _e('链接地址'); ?>",
link_target:"<?php _e('目标'); ?>",
link_target_same:"<?php _e('在当前窗口打开链接'); ?>",
link_target_blank:"<?php _e('在新窗口打开链接'); ?>",
link_titlefield:"<?php _e('标题'); ?>",
link_is_email:"The URL you entered seems to be an email address, do you want to add the required mailto: prefix?",
link_is_external:"The URL you entered seems to external link, do you want to add the required http:// prefix?",
link_list:"Link list"
});

tinyMCE.addI18n('en.media_dlg',{
title:"Insert / edit embedded media",
general:"General",
advanced:"Advanced",
file:"File/URL",
list:"List",
size:"Dimensions",
preview:"Preview",
constrain_proportions:"Constrain proportions",
type:"Type",
id:"Id",
name:"Name",
class_name:"Class",
vspace:"V-Space",
hspace:"H-Space",
play:"Auto play",
loop:"Loop",
menu:"Show menu",
quality:"Quality",
scale:"Scale",
align:"Align",
salign:"SAlign",
wmode:"WMode",
bgcolor:"Background",
base:"Base",
flashvars:"Flashvars",
liveconnect:"SWLiveConnect",
autohref:"AutoHREF",
cache:"Cache",
hidden:"Hidden",
controller:"Controller",
kioskmode:"Kiosk mode",
playeveryframe:"Play every frame",
targetcache:"Target cache",
correction:"No correction",
enablejavascript:"Enable JavaScript",
starttime:"Start time",
endtime:"End time",
href:"Href",
qtsrcchokespeed:"Choke speed",
target:"Target",
volume:"Volume",
autostart:"Auto start",
enabled:"Enabled",
fullscreen:"Fullscreen",
invokeurls:"Invoke URLs",
mute:"Mute",
stretchtofit:"Stretch to fit",
windowlessvideo:"Windowless video",
balance:"Balance",
baseurl:"Base URL",
captioningid:"Captioning id",
currentmarker:"Current marker",
currentposition:"Current position",
defaultframe:"Default frame",
playcount:"Play count",
rate:"Rate",
uimode:"UI Mode",
flash_options:"Flash options",
qt_options:"Quicktime options",
wmp_options:"Windows media player options",
rmp_options:"Real media player options",
shockwave_options:"Shockwave options",
autogotourl:"Auto goto URL",
center:"Center",
imagestatus:"Image status",
maintainaspect:"Maintain aspect",
nojava:"No java",
prefetch:"Prefetch",
shuffle:"Shuffle",
console:"Console",
numloop:"Num loops",
controls:"Controls",
scriptcallbacks:"Script callbacks",
swstretchstyle:"Stretch style",
swstretchhalign:"Stretch H-Align",
swstretchvalign:"Stretch V-Align",
sound:"Sound",
progress:"Progress",
qtsrc:"QT Src",
qt_stream_warn:"Streamed rtsp resources should be added to the QT Src field under the advanced tab.\nYou should also add a non streamed version to the Src field..",
align_top:"Top",
align_right:"Right",
align_bottom:"Bottom",
align_left:"Left",
align_center:"Center",
align_top_left:"Top left",
align_top_right:"Top right",
align_bottom_left:"Bottom left",
align_bottom_right:"Bottom right",
flv_options:"Flash video options",
flv_scalemode:"Scale mode",
flv_buffer:"Buffer",
flv_startimage:"Start image",
flv_starttime:"Start time",
flv_defaultvolume:"Default volumne",
flv_hiddengui:"Hidden GUI",
flv_autostart:"Auto start",
flv_loop:"Loop",
flv_showscalemodes:"Show scale modes",
flv_smoothvideo:"Smooth video",
flv_jscallback:"JS Callback"
});
