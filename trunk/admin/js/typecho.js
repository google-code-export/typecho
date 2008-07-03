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
        
        function onMenuItemClick(p_oEven) {
            alert('ddd');
            button.set("label", p_oItem.cfg.getProperty("text"));
        }
        
        var ilabel = $('option:first', e).html();
        var ivalue = $('option:first', e).val();
        var selected = false;
        
        $('option', e).each(function(){
            /** 设置选定值 */
            if(true == $(this).attr('selected'))
            {
                ilabel = $(this).html();
                ivalue = $(this).val();
            }
        });

        box = $(document.createElement('input'));
        box.attr('id', 'typecho-button-' + idPointer);
        box.attr('type', 'button');
        box.attr('value', ilabel);
        e.after(box);
        
        var hidden = $(document.createElement('input'));
        hidden.attr('name', e.attr('name'));
        hidden.attr('type', 'hidden');
        hidden.attr('value', ivalue);
        e.after(hidden);
        
        var button = new YAHOO.widget.Button('typecho-button-' + idPointer, 
                                            {type: 'menu', menu: this});
        
        button._menu.subscribe("click", function (p_sType, p_aArgs)
        {
            var oMenuItem = p_aArgs[1];
            if (oMenuItem)
            {
                button.set('label', oMenuItem.cfg.getProperty("text"));
                hidden.remove();
            }
        });
        
        idPointer ++;
    });
});
