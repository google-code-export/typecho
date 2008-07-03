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
    
    /** 替换输入项样式 */
    $("input[@type=text], input[@type=password]").each(function(){
        e = $(this);
        e.css("border-width", "0");
        e.css("background-color", "transparent");
        
        e1 = $(document.createElement("span"));
        e1.addClass("yui-button yui-menu-button typecho-input");
        e.before(e1);
        
        e2 = $(document.createElement("span"));
        e2.addClass("first-child typecho-input-first-child");
        e2.appendTo(e1);
        e3 = e.clone();
        e3.width(e.width());
        e3.height(e.height());
        e3.appendTo(e2);
        e.remove();
    });
    
    /** 替换多行输入项样式 */
    $("textarea").each(function(){
        e = $(this);
        if('text' == e.attr('id'))
        {
            return;
        }
        
        e.css("border-width", "0");
        e.css("background-color", "transparent");
        
        e1 = $(document.createElement("span"));
        e1.addClass("yui-button yui-menu-button typecho-input");
        e.before(e1);
        
        e2 = $(document.createElement("span"));
        e2.addClass("first-child typecho-input-first-child");
        e2.appendTo(e1);
        e3 = e.clone();
        e3.width(e.width());
        e3.height(e.height());
        e3.appendTo(e2);
        e.remove();
    });
    
    /** 替换单选框 */
    var radioList = [];
    var hiddenList = [];
    $("input[@type=radio]").each(function(){
        e = $(this);
        if(!radioList[e.attr('name')])
        {
            icontainer = document.createElement("span");
            e.before(icontainer);
            radioList[e.attr('name')] = new YAHOO.widget.ButtonGroup({
                name:  e.attr('name'),
                container: icontainer
            });
            
            hiddenList[e.attr('name')] = document.createElement("input");
            hidden = $(hiddenList[e.attr('name')]);
            hidden.attr('name', e.attr('name'));
            hidden.attr('type', 'hidden');
            e.after(hidden);
        }
        
        function onClick(p_aArgs)
        {
            $(hiddenList[this.get('name')]).val(this.get('value'));
        }
        
        button = { label: $("label[@for=" + e.attr("id") + "]").html(), name: e.attr('name'),
        value: e.val() , onclick: {fn: onClick}};
        if(e.attr('checked')){button.checked = true; hidden.val(e.val());}
        radioList[e.attr('name')].addButtons([button]);
        
        $("label[@for=" + e.attr("id") + "]").remove();
        e.remove();
    });
    
    /** 替换多选框 */
    $("input[@type=checkbox]").each(function(){
        e = $(this);
        
        if(!$("label[@for=" + e.attr("id") + "]").html()) return;
        
        icontainer = document.createElement("span");
        e.before(icontainer);
        
        var oCheckButton9 = new YAHOO.widget.Button({ 
                            type: "checkbox", 
                            label: $("label[@for=" + e.attr("id") + "]").html(), 
                            name: e.attr("name"), 
                            value: e.val(), 
                            container: icontainer, 
                            checked: e.attr("checked") });
        $("label[@for=" + e.attr("id") + "]").remove();
        e.remove();
    });
});
