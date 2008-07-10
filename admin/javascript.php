<?php
require_once 'common.php';
?>

function ajaxInsertCategory(category) {
    var icategory = category;

    $.ajax({
        type: 'POST',
        url: '<?php $options->index('/Metas/Category/Edit.do'); ?>',
        data: 'name=' + encodeURIComponent(category) + '&do=ajaxInsert',
        contentType: "application/x-www-form-urlencoded; charset=<?php $options->charset(); ?>",
        dataType: "xml",
        cache: false,
        success: function(xml){
            data = $("response", xml).text();
        
            if(!isNaN(data))
            {
                li = $(document.createElement('li'));
                li.html('<label for="category-' + data + '"><input type="checkbox" name="category[]" value="' + data +
                '" id="category-' + data + '" /> ' + icategory + '</label>');
                li.hide();
                
                $("#cat_list").append(li);
                li.fadeIn();
            }
            else
            {
                document.cookie = "form_record[name]=deleted; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/";
                document.cookie = "form_record[slug]=deleted; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/";
                document.cookie = "form_record[name]=deleted; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/";
                document.cookie = "form_record[description]=deleted; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/";
                document.cookie = "form_record[mid]=deleted; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/";
                document.cookie = "form_message[name]=deleted; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/";
                alert(data);
            }
            
            document.cookie = "form_record[do]=deleted; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/";
        }
    });
};

$("#date").datepicker({dateFormat: "yy-mm-dd"});
$("#time").clockpick({starthour: 0, endhour : 23});

tinyMCE.init({
mode : "exact",
elements : "text",
theme : "advanced",
skin : "o2k7",
plugins : "safari,inlinepopups,pagebreak",
theme_advanced_buttons1 : "bold,italic,underline,strikethrough, separator, forecolor,backcolor,fontselect,fontsizeselect,separator,hr,link,unlink,image",
theme_advanced_buttons2 : "",
theme_advanced_buttons1_add :"code,charmap,pagebreak",
theme_advanced_buttons3 : "",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,
relative_urls : false,
remove_script_host : false
});
