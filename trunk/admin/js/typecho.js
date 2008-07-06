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

    var idPointer = 1;
    
    /** 替换按钮样式 */
    $("input[@type=button],input[@type=submit]").each(function(){
        e = $(this);
        id = e.attr('id');
        var rel = e.attr('rel');
        
        if(null == id)
        {
            id = 'typecho-button-' + idPointer;
            e.attr('id', id);
        }
        
        button = new YAHOO.widget.Button(id);
        
        /** 增加图片效果 */
        if(null != rel)
        {
            YAHOO.util.Event.onContentReady(id, function(){
                b = $(this.firstChild.firstChild);
                
                if($.browser.msie)
                {
                    b.css({background: "url(" + rel + ") no-repeat",
                    paddingLeft: "2em",
                    backgroundPosition: ".3em .2em"});
                }
                else if($.browser.opera || $.browser.safari)
                {
                    b.css({background: "url(" + rel + ") no-repeat",
                    paddingLeft: "2em",
                    backgroundPosition: ".4em .3em"});
                }
                else
                {
                    b.css({background: "url(" + rel + ") no-repeat",
                    paddingLeft: "1.8em",
                    backgroundPosition: ".4em .3em"});
                }
            });
        }
        
        if('undefined' != typeof(this.onclick) && null != this.onclick)
        {
            button.on("click", this.onclick);
        }
        
        idPointer ++;
    });
    
    /** 替换下拉框样式 */
    $("select").each(function(){
        e = $(this);
        
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
        box.attr('id', 'typecho-select-' + idPointer);
        box.attr('type', 'button');
        box.attr('value', ilabel);
        e.after(box);
        
        var hidden = $(document.createElement('input'));
        hidden.attr('name', e.attr('name'));
        hidden.attr('type', 'hidden');
        hidden.attr('value', ivalue);
        e.after(hidden);
        
        var button = new YAHOO.widget.Button('typecho-select-' + idPointer, 
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
        var e = $(this);
        e.css("border-width", "0");
        e.css("background-color", "transparent");
        
        e1 = $(document.createElement("span"));
        e1.addClass("yui-button yui-menu-button typecho-input");
        e.before(e1);
        
        e2 = $(document.createElement("span"));
        e2.addClass("first-child typecho-input-first-child");
        e2.appendTo(e1);
        
        e.width(e.width());
        if($.browser.msie)
        {
            e.height(e.height() - 2);
        }
        if($.browser.safari)
        {
            e.height(e.height() + 1);
        }
        else
        {
            e.height(e.height());
        }
        
        e.appendTo(e2);
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
        }
        
        button = { label: $("label[@for=" + e.attr("id") + "]").html(), name: e.attr('name'),
        value: e.val(), onclick: {fn: function(e){
            $("input[@name=" + this.get('name') + "]").removeAttr("checked");
            var checkedValue = this.get('value');
            $("input[@name=" + this.get('name') + "]").each(function(){
                if(checkedValue == $(this).val())
                {
                    $(this).attr("checked", true);
                }
            });
        }}};
        if(e.attr('checked')){button.checked = true;}
        radioList[e.attr('name')].addButtons([button]);
        
        $("label[@for=" + e.attr("id") + "]").remove();
        e.hide();
    });
    
    /** 替换多选框 */
    $("input[@type=checkbox]").each(function(){
        e = $(this);

        if(!$("label[@for=" + e.attr("id") + "]").html() || "LABEL" == this.parentNode.tagName
         || "label" == this.parentNode.tagName) return;
        
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
    
    /** 修正FF2 inline-box bug */
    if($.browser.mozilla)
    {
        $(".yui-button").css({display: "-moz-inline-box"});
    }
});
