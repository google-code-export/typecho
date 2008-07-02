/*
 * You can add any Javascript here.
 */

$(document).ready(function() {
	$("table.latest tr").mouseover(function() {  
		 $(this).addClass("over"); }).mouseout(function() { 
			 $(this).removeClass("over"); });
	$("table.latest tr:even").addClass("alt");
	$("table.setting tr:odd").addClass("alt");
    
    $("table.latest tr:first th:first input").click(function() {
        if(true == $(this).attr('checked'))
        {
            $("table.latest tr td input").each(function() {
                $(this).attr('checked', true);
            }
            )
        }
        else
        {
            $("table.latest tr td input").each(function() {
                $(this).attr('checked', false);
            }
            )
        }
    }
    );

	$(":text").addClass("text");
	$(":password").addClass("password");
	$(":submit").addClass("submit");
	$(":button").addClass("button");
    
    $(".latest .publish").corner("5px");
    $(".latest .unpublish").corner("5px");
    $(".latest .spam").corner("5px");
    $(".latest .waiting").corner("5px");
    $(".latest .approved").corner("5px");
    $(".latest .activated").corner("5px");
    $(".latest .deactivated").corner("5px");
    $(".latest .config").corner("5px");
    
    var idPointer = 1;
    
    /** 替换按钮样式 */
    $("input[@type=button],input[@type=submit]").each(function(){
        e = $(this);
        id = e.attr('id');
        if(null == id)
        {
            id = 'typecho-input-' + idPointer;
            e.attr('id', id);
        }
        
        button = new YAHOO.widget.Button(id);
        
        if('undefined' != typeof(this.onclick) && null != this.onclick)
        {
            button.on("click", this.onclick);
        }
        
        idPointer ++;
    });
    
    /** 替换下拉框样式 */
    $("select").each(function(){
        e = $(this);
        
        var menus = [];
        var ilabel = $('option:first', e).html();
        $('option', e).each(function(){
            imenu = {text: $(this).html(), value: $(this).val()};
            menus.push(imenu);
        });

        box = document.createElement('span');
        $(box).insertAfter(e);

        var button = new YAHOO.widget.Button({type: 'menu',
                                            label: ilabel,
                                            name: e.attr('name'),
	                                        menu: menus,
                                            container: box});
        e.remove();
        idPointer ++;
    });
});
