var nicEditors = {
	allTextAreas : function(nicOptions) {
		var textareas = document.getElementsByTagName("textarea");
		var editors = new Array();
		for(var i=0;i<textareas.length;i++) {
			editors.push(new nicEditor(nicOptions).panelInstance(textareas[i]));
		}
		return editors;
	}
};

var nicEditor = bkClass.extend({
	nicInstances : [],
	nicPanel : null,
	selectedInstance : null,
	
	construct : function(o) {
		var opt = bkClass.extend(nicEditorConfig);
		opt = (o) ? opt.extend(o) : opt;
		this.options = new opt();
		
		bkLib.addEvent(document.body,'mousedown', this.selectCheck.closureListener(this) );
	},
	
	selectCheck : function(e,t) {
		var found = false;
		do{
			if(t.className && t.className.indexOf('nicEdit') != -1) {
				return false;
			}
		} while(t = t.parentNode);
		this.fireEvent('noInstanceSelect',t);
		this.selectedInstance = null;
		return false;
	},
	
	panelInstance : function(e) {
		if(typeof(e) == "string") { e = $N(e); }
		var panelElm = document.createElement('DIV');
		e.parentNode.insertBefore(panelElm,e);
		panelWidth = e.width || e.clientWidth;
		panelElm.style.width = panelWidth+'px';
		this.setPanel(panelElm);
		return this.addInstance(e);	
	},
	
	findInstance : function(e) {
		if(typeof(e) == "string") { e = $N(e); }
		for(i=0;i<this.nicInstances.length;i++) {
			if(e == this.nicInstances[i].elm) {
				return this.nicInstances[i];
			}
		}
	},
	
	addInstance : function(e,o) {
		if(typeof(e) == "string") { e = $N(e); }
		if(e.contentEditable || !!window.opera) {
			this.nicInstances.push(new nicEditorInstance(e,o,this));
		} else {
			this.nicInstances.push(new nicEditorIFrameInstance(e,o,this));
		}
		return this;
	},
	
	instancesByClassName : function(className, o) {
		var editorAreas = bkLib.getElementsByClassName(className);
		var editors = new Array();
		for(var i=0;i<editorAreas.length;i++) {
			this.addInstance(editorAreas[i],o);
		}
	},
	
	nicCommand : function(cmd,args) {	
		if(this.selectedInstance) {
			this.selectedInstance.nicCommand(cmd,args);
		}
	},
	
	setPanel : function(e) {
		if(typeof(e) == "string") { e = $N(e); }
		this.nicPanel = new nicEditorPanel(e,this.options,this);
		return this;
	}
	
		
});
nicEditor = nicEditor.extend(bkEvent);

var nicEditorInstance = bkClass.extend({
	nicEditor : null,
	elm : null,
	initalContent : null,
	isSelected : false,
	
	construct : function(e,options,nicEditor) {
		this.nicEditor = nicEditor;
		this.elm = e;
		this.options = options || {};

		newX = e.width || e.clientWidth;
		newY = e.height || e.clientHeight;
		if(e.nodeName == "TEXTAREA") {
			e.style.display = 'none';
				
			var editorContain = document.createElement('DIV');
			var editorElm = document.createElement('DIV');
			editorElm.innerHTML = e.value;
			
			bkLib.setStyle(editorContain,{width: (newX)+'px', border : '1px solid #ccc', borderTop : 0, overflow : 'hidden'});
			bkLib.setStyle(editorElm,{width : (newX-8)+'px', margin: '4px', minHeight : (newY-8)+'px'});
			
			var nav = navigator.appVersion;
			if(nav.indexOf("MSIE") != -1 && !((typeof document.body.style.maxHeight != "undefined") && document.compatMode == "CSS1Compat")) { // Set the height on all IE except IE7+ in standards mode
					editorElm.style.height = newY+'px';
			}
			
			editorContain.appendChild(editorElm);
			e.parentNode.insertBefore(editorContain,e);
			this.elm = editorElm;
			this.copyElm = e;
			
			var formElements = document.getElementsByTagName("FORM");
			for(var i=0;i<formElements.length;i++) {
				bkLib.addEvent(formElements[i],'submit',this.saveContent.closure(this));
			}
		}
		this.initialHeight = newY-8;
		this.nicEditor.addEvent('noInstanceSelect',this.unselected.closure(this));
		
		this.init();
		this.unselected();
	},
	
	init : function() {
		this.elm.setAttribute('contentEditable','true');	
		this.initialContent = this.getContent();
		if(this.initialContent == "") {
			this.setContent('<br />');
		}
		this.elm.className = 'nicEdit';
		bkLib.addEvent(this.elm,'mousedown',this.mouseDown.closureListener(this));
	},
	
	getSel : function() {
		return (window.getSelection) ? window.getSelection() : document.selection;	
	},
	
	getRng : function() {
		var s = this.getSel();
		if(!s) { return null; }
		return (s.rangeCount > 0) ? s.getRangeAt(0) : s.createRange();
	},
	
	selRng : function(rng,s) {
		if(window.getSelection) {
			s.removeAllRanges();
			s.addRange(rng);
		} else {
			rng.select();
		}
	},
	
	saveRng : function() {
		this.savedRange = this.getRng();
		this.savedSel = this.getSel();
	},
	
	restoreRng : function() {
		if(this.savedRange) {
			this.selRng(this.savedRange,this.savedSel);
		}
	},
	
	mouseDown : function(e,t) {
		if(this.nicEditor.selectedInstance != this) {
			this.nicEditor.fireEvent('noInstanceSelect',t);
		}	
		this.nicEditor.selectedInstance = this;	
		this.nicEditor.fireEvent('instanceSelect',t);
		this.selected();
	},
	
	selected : function() {
		this.isSelected = true;
		bkLib.setStyle(this.elm,{className : 'nicEdit nicEdit-instanceSelect'});	
		if(this.toolTip) {
			this.toolTip.remove();
			this.toolTip = null;
		}
	},
	
	unselected : function() {
		this.isSelected = false;
		bkLib.setStyle(this.elm,{className : 'nicEdit nicEdit-noInstanceSelect'});	
		
		if(!this.toolTip && (this.nicEditor.options.toolTipOn || this.options.toolTipOn)) {
			this.addTooltip();
			this.toolTip.setContent((this.options.toolTipText) ? this.options.toolTipText : this.nicEditor.options.toolTipText);
		}
	},
	
	addTooltip : function() {
		this.toolTip = new nicEditorTooltip(this.elm,this.nicEditor,this.getTipStyle());
	},
	
	saveContent : function() {
		if(this.copyElm) {
			this.copyElm.value = this.getContent();
		}	
	},
	
	getContent : function() {
		return this.elm.innerHTML;
	},
	
	setContent : function(newContent) {
			this.elm.innerHTML = newContent;	
	},
	
	getTipStyle : function() {
		return {padding : '3px', backgroundColor : '#ffffc9', fontSize : '12px', border : '1px solid #ccc', className : 'nicEdit-instanceTip'}
	},
	
	getDoc : function() {
		return document.defaultView;
	},
	
	nicCommand : function(cmd,args) {
		document.execCommand(cmd,false,args);
	}		
});

var nicEditorIFrameInstance = nicEditorInstance.extend({
	elm : null,
	
	init : function() {
		this.elmFrame = document.createElement('iframe');
        this.elmFrame.setAttribute('frameBorder','0');
        this.elmFrame.setAttribute('allowTransparency','true');
        this.elmFrame.setAttribute('scrolling','no');

       	bkLib.setStyle(this.elmFrame,{width: '100%', overflow : 'hidden', className : 'nicEdit-frame'});
       	if(this.copyElm) { this.elmFrame.style.width = (this.elm.offsetWidth-4)+'px'; }
		
		this.initialFontSize = bkLib.getStyle(this.elm,'font-size');
		this.initialFontFamily = bkLib.getStyle(this.elm,'font-family');
		this.initialFontWeight = bkLib.getStyle(this.elm,'font-weight');
		this.initialFontColor = bkLib.getStyle(this.elm,'color');
		this.initialContent = this.elm.innerHTML;
		if(this.initialContent == "") {
			this.initialContent = '<br />';
		}
		if(!this.copyElm) {
       		this.initialHeight = 0;
       	}
       	
		this.elm.innerHTML = '';
       	this.elm.appendChild(this.elmFrame);
       	
       	this.initFrame();
       	this.heightUpdate();
       	bkLib.addEvent(this.elmFrame.contentWindow.document,'mousedown',this.mouseDown.closureListener(this));
       	bkLib.addEvent(this.elmFrame.contentWindow.document,'keyup',this.heightUpdate.closureListener(this));
	},
	
	initFrame : function() {
		this.frameDoc = this.elmFrame.contentWindow.document;
        this.frameDoc.open();
        this.frameDoc.write('<html><head></head><body id="nicEditContent" style="margin: 0 !important; background-color: transparent !important;">');
        this.frameDoc.write(this.initialContent);
        this.frameDoc.write('</body></html>');
        this.frameDoc.close();
        
        this.frameDoc.designMode = "on";
        
        this.frameContent = this.elmFrame.contentWindow.document.body;
        
        bkLib.setStyle(this.frameContent,{fontSize : this.initialFontSize, fontFamily : this.initialFontFamily, fontWeight : this.initialFontWeight, color : this.initialFontColor});
	},
	
	getContent : function() {
        return this.frameContent.innerHTML;
	},
	
	addTooltip : function(e) {
		this.toolTip = new nicEditorTooltip(this.elmFrame,this.nicEditor,this.getTipStyle());
	},
	
	setContent : function(c) {
		this.frameContent.innerHTML = c;	
	},
	
	getDoc : function() {
		return this.elmFrame.contentWindow.document.defaultView;
	},
	
	getSel : function() {
		return (this.elmFrame.contentWindow) ? this.elmFrame.contentWindow.getSelection() : this.frameDoc.selection;
	},
	
	heightUpdate : function() {
			this.elmFrame.style.height = (this.frameContent.offsetHeight < this.initialHeight) ? this.initialHeight+'px' : this.frameContent.offsetHeight+'px';
    },
    
    nicCommand : function(cmd,args) {
		this.frameDoc.execCommand(cmd,false,args);
		setTimeout(this.heightUpdate.closure(this),100);
	}

	
});

var nicEditorPanel = bkClass.extend({
	panelId : null,
	panelButtons : [],
	panelElm : null,
	elm : null,
	
	construct : function(e,options,nicEditor) {
		this.elm = e;
		this.options = options;
		this.nicEditor = nicEditor;
		
		this.panelElm = document.createElement('div');
		this.panelContain = document.createElement('div');
		
		bkLib.setStyle(this.panelContain,{width : '100%', border : '1px solid #cccccc', backgroundColor : '#efefef', className : 'nicEdit-panelContain'});
		bkLib.setStyle(this.panelElm,{margin : '2px', overflow : 'hidden', className : 'nicEdit-panel'});
		
		this.panelButtons = new Array();

		if(this.options.fullPanel) {
				for(b in this.options.buttons) {
					this.addButton(this.options.buttons[b]);
				}
		} else {
			for(var i=0;i<this.options.buttonList.length;i++) {
					this.addButton(this.options.buttons[this.options.buttonList[i]]);
			}
		}
		
		this.panelContain.appendChild(this.panelElm);
		this.elm.appendChild(this.panelContain);
		bkLib.unselectAble(this.elm);
	},
	
	addButton : function(button) {
			var type = (button['type']) ? eval(button['type']) : nicEditorButton;
			this.panelButtons.push(new type(this.panelElm,button,this.nicEditor));
	}
	
});

var nicEditorButton = bkClass.extend({
	isDisabled : false,
	isHover : false,
	isActive : false,
	
	construct : function(e,options,nicEditor) {
		this.elm = e;
		this.options = options;
		this.nicEditor = nicEditor;
		this.buttonContain = document.createElement('div');
		this.buttonBorder = document.createElement('div');
		this.buttonElm = document.createElement('div');

		bkLib.setStyle(this.buttonElm,{backgroundImage : "url('"+this.nicEditor.options.iconsPath+"')", width : '18px', height : '18px', backgroundPosition : ((this.options.tile-1)*-18)+'px 0px', cursor : 'pointer', className : 'nicEdit-button'});
		bkLib.setStyle(this.buttonBorder,{border : '1px solid #efefef', width: '18px', height: '18px', backgroundColor : '#efefef'})
		bkLib.setStyle(this.buttonContain,{'float' : 'left', overflow: 'hidden', width : '20px', height : '20px', className : 'nicEdit-buttonContain'});
		
		bkLib.addEvent(this.buttonElm,'mouseover', this.hoverOn.closure(this));
		bkLib.addEvent(this.buttonElm,'mouseout',this.hoverOff.closure(this));
		bkLib.addEvent(this.buttonElm,'click',this.mouseClick.closure(this));
		
		if(!window.opera) {
			this.buttonElm.onmousedown = bkLib.cancelEvent;
			this.buttonElm.onclick = bkLib.cancelEvent;
		}
		
		this.nicEditor.addEvent('instanceSelect', this.enable.closure(this));
		this.nicEditor.addEvent('noInstanceSelect', this.disable.closure(this));			
		
		this.disable();
		
		this.buttonBorder.appendChild(this.buttonElm);
		this.buttonContain.appendChild(this.buttonBorder);
		this.elm.appendChild(this.buttonContain);
		
		this.init();
	},
	
	init : function() {  },
	
	hideButton : function() {
		this.buttonContain.style.display = 'none';	
	},
	
	updateState : function() {
		if(this.isDisabled) { this.setBg(); }
		else if(this.isHover) { this.setBg('hover'); }
		else if(this.isActive) { this.setBg('active'); }
		else { this.setBg(); }
	},
	
	setBg : function(state) {	
		if(state == "hover") {
			bkLib.setStyle(this.buttonBorder,{border : '1px solid #666', backgroundColor : '#ddd', className : 'nicEdit-buttonContain-hover'});	
		} else if(state == "active") {
			bkLib.setStyle(this.buttonBorder,{border : '1px solid #666', backgroundColor : '#ccc', className : 'nicEdit-buttonContain-active'});
		} else {
			bkLib.setStyle(this.buttonBorder,{border : '1px solid #efefef', backgroundColor : '#efefef', className : 'nicEdit-buttonContain-normal'});
		}
	},
	
	checkNodes : function(e) {
		var elm = e;
		do {
			if(this.options.tags && bkLib.inArray(this.options.tags,elm.nodeName)) {
				this.activate();
				return true;
			}
		} while(elm = elm.parentNode);
		var elm = e;
		if(this.options.css) {
			for(itm in this.options.css) {
				if(bkLib.getStyle(elm,itm,this.nicEditor.selectedInstance.getDoc()) == this.options.css[itm]) {
					this.activate();
					return true;
				}
			}
		}
		this.deactivate();
		return false;
	},
	
	activate : function() {
		if(!this.isDisabled) {
			this.isActive = true;
			this.updateState();	
		}
	},
	
	deactivate : function() {
		this.isActive = false;
		this.updateState();	
	},
	
	enable : function(t) {
		this.isDisabled = false;
		bkLib.setStyle(this.buttonContain,{'opacity' : 1, className : 'nicEdit-buttonContain-enabled'});
		this.updateState();
		this.checkNodes(t);
	},
	
	disable : function(t) {		
		this.isDisabled = true;
		bkLib.setStyle(this.buttonContain,{'opacity' : 0.6, className : 'nicEdit-buttonContain-disabled'});
		this.updateState();	
		this.removePane();
	},
	
	toggleActive : function() {
		(this.isActive) ? this.deactivate() : this.activate();	
	},
	
	hoverOn : function() {
		if(!this.isDisabled) {
			this.isHover = true;
			this.updateState();
			this.toolTimer = setTimeout(this.addTooltip.closure(this),500);
		}
	}, 
	
	addTooltip : function() {
		if(this.isHover && !this.buttonPane) {
			this.toolTip = new nicEditorPane(this.buttonContain,this.nicEditor,{margin : '4px', padding : '3px', backgroundColor : '#ffffc9', fontSize : '12px', border : '1px solid #ccc', className : 'nicEdit-tooltip'});
			this.toolTip.setContent(this.options.name);
		}
	},
	
	removeTooltip : function() {
		if(this.toolTimer) {
			clearTimeout(this.toolTimer);
		}
		if(this.toolTip) {
			this.toolTip.remove();
			this.toolTip = null;
		}
	},
	
	getPaneStyle : function() {
		return {width : '275px', fontSize : '12px', padding : '4px', textAlign: 'left', border : '1px solid #ccc', backgroundColor : '#fff', className : 'nicEdit-buttonPane'};
	},
	
	removePane : function() {
		if(this.buttonPane) {
			this.buttonPane.remove();
			this.buttonPane = null;
			return true;
		}
	},
	
	hoverOff : function() {
		this.isHover = false;
		this.updateState();
		this.removeTooltip();
	},
	
	mouseClick : function() {
		if(this.options.command) {
			this.nicEditor.nicCommand(this.options.command,this.options.commandArgs);
			if(!this.options.noActive) {
				this.toggleActive();
			}
		} else {
			(this.buttonPane) ? this.removePane() : this.addPane();
		}
	}
	
});

var nicEditorSelect = bkClass.extend({
	isDisabled : false,
	
	construct : function(e,options,nicEditor) {
		this.elm = e;
		this.options = options;
		this.nicEditor = nicEditor;	
		
		this.selectContain = document.createElement('DIV');
		this.selectItems = document.createElement('DIV');
		this.selectControl = document.createElement('DIV');
		this.selectTxt = document.createElement('DIV');
		this.selectOptions = new Array();

		bkLib.setStyle(this.selectContain,{overflow : 'hidden', width: '90px', height : '20px', 'float' : 'left', cursor : 'pointer', margin : '0 2px', className : 'nicEdit-selectContain'});
		bkLib.setStyle(this.selectItems,{overflow : 'hidden', border: '1px solid #ccc', paddingLeft : '3px', height : '18px', backgroundColor : '#fff'});
		bkLib.setStyle(this.selectControl,{backgroundImage : "url('"+this.nicEditor.options.iconsPath+"')", backgroundPosition : (17*-18)+'px 0px', 'float' : 'right', height: '16px', width : '16px', className : 'nicEdit-selectControl'});
		bkLib.setStyle(this.selectTxt,{'float' : 'left', width : '66px', overflow: 'hidden', height : '16px', marginTop : '1px', fontFamily : 'arial', fontSize : '12px', className : 'nicEdit-selectTxt'});
		
		if(!window.opera) {
			this.selectContain.onmousedown = this.selectControl.onmousedown = this.selectTxt.onmousedown = bkLib.cancelEvent;
		}
		
		this.selectItems.appendChild(this.selectTxt);
		this.selectItems.appendChild(this.selectControl);
		
		this.selectContain.appendChild(this.selectItems);
		this.elm.appendChild(this.selectContain);
		
		bkLib.addEvent(this.selectContain,'click',this.togglePane.closure(this));	
		this.nicEditor.addEvent('instanceSelect', this.enable.closure(this));
		this.nicEditor.addEvent('noInstanceSelect', this.disable.closure(this));	
		
		this.disable();
		this.init();
		
		bkLib.unselectAble(this.elm);
	},
	
	disable : function() {
		this.isDisabled = true;
		this.removePane();
		bkLib.setStyle(this.selectContain,{opacity : 0.6});
	},
	
	enable : function(t) {
		this.isDisabled = false;
		bkLib.setStyle(this.selectContain,{opacity : 1});
	},
	
	setDisplay : function(txt) {
		this.selectTxt.innerHTML = txt;
	},
	
	togglePane : function() {
		if(!this.isDisabled) {
			(this.selectPane) ? this.removePane() : this.showPane();
		}
	},
	
	showPane : function() {
		this.selectPane = new nicEditorPane(this.selectContain,this.nicEditor,this.getPaneStyle());
		for(var i=0;i<this.selectOptions.length;i++) {
			var optionKey = this.selectOptions[i][0];
			var optionValue = this.selectOptions[i][1];
			
			var itm = document.createElement('div');
			bkLib.setStyle(itm,{overflow : 'hidden', width: '88px', textAlign : 'left', padding : '0 4px', cursor : 'pointer', borderBottom : '1px solid #ccc'});
			itm.id = optionKey;
			itm.innerHTML = optionValue;
			itm.onclick = this.onSelect.closure(this,optionKey);
			itm.onmouseover = this.optionOver.closure(this,itm);
			itm.onmouseout = this.optionOut.closure(this,itm);
			if(!window.opera) {
				itm.onmousedown = bkLib.cancelEvent;
			}
			this.selectPane.append(itm);
			bkLib.unselectAble(itm);
		}
	},
	
	removePane : function() {
		if(this.selectPane) {
			this.selectPane.remove();
			this.selectPane = null;
		}	
	},
	
	getPaneStyle : function() {
		return {width : '88px', overflow : 'hidden', fontSize : '12px', borderLeft : '1px solid #ccc', borderRight : '1px solid #ccc', backgroundColor : '#fff', className : 'nicEdit-selectPane'};
	},
	
	optionOver : function(opt) {
		bkLib.setStyle(opt,{backgroundColor : '#ccc'});			
	},
	
	optionOut : function(opt) {
		bkLib.setStyle(opt,{backgroundColor : '#fff'});	
	},
	
	
	add : function(k,v) {
		this.selectOptions.push(new Array(k,v));	
	},
	
	onSelect : function(elm) {
		this.nicEditor.nicCommand(this.options.command,elm);
		this.removePane();	
	}
});

var nicEditorFontSizeSelect = nicEditorSelect.extend({
	selConfig : {1 : '1&nbsp;(8pt)', 2 : '2&nbsp;(10pt)', 3 : '3&nbsp;(12pt)', 4 : '4&nbsp;(14pt)', 5 : '5&nbsp;(18pt)', 6 : '6&nbsp;(24pt)'},
	init : function() {
		this.setDisplay('Font&nbsp;Size..');
		for(itm in this.selConfig) {
			this.add(itm,'<font size="'+itm+'">'+this.selConfig[itm]+'</font>');
		}		
	}
});

var nicEditorFontFamilySelect = nicEditorSelect.extend({
	selConfig : {'arial' : 'Arial','comic sans ms' : 'Comic Sans','courier new' : 'Courier New','georgia' : 'Georgia', 'helvetica' : 'Helvetica', 'impact' : 'Impact', 'times new roman' : 'Times', 'trebuchet ms' : 'Trebuchet', 'verdana' : 'Verdana'},
	
	init : function() {
		this.setDisplay('Font&nbsp;Family..');
		for(itm in this.selConfig) {
			this.add(itm,'<font face="'+itm+'">'+this.selConfig[itm]+'</font>');
		}
	}
});

var nicEditorFontFormatSelect = nicEditorSelect.extend({
		selConfig : {'p' : 'Paragraph', 'pre' : 'Pre', 'h6' : 'Heading&nbsp;6', 'h5' : 'Heading&nbsp;5', 'h4' : 'Heading&nbsp;4', 'h3' : 'Heading&nbsp;3', 'h2' : 'Heading&nbsp;2', 'h1' : 'Heading&nbsp;1'},
		
	init : function() {
		this.setDisplay('Font&nbsp;Format..');
		for(itm in this.selConfig) {
			var tag = itm.toUpperCase();
			this.add('<'+tag+'>','<'+itm+' style="padding: 0px; margin: 0px;">'+this.selConfig[itm]+'</'+tag+'>');
		}
	}
});

var nicEditorSaveButton = nicEditorButton.extend({
	onSave : null,
	
	init : function() {
		this.onSave = this.nicEditor.options.onSave;
		if(!this.onSave) {
			this.hideButton();
		}
	},
	
	mouseClick : function() {
		if(this.nicEditor.options.onSave) {
			this.nicEditor.options.onSave(this.nicEditor.nicInstances);
		}
	}	
});

var nicEditorPane = bkClass.extend({
	nicPane : null,
	options : null,
	isVisible : true,
	isOver : true,
	
	construct : function(elm,nicEditor,options) {
		this.options = options;
		this.nicEditor = nicEditor;
		this.elm = elm;
		
		var nicPanelElm = this.nicEditor.nicPanel.panelElm;
		this.nicPane = document.createElement('DIV');
		this.pos = bkLib.elmPos(elm);
		
		bkLib.setStyle(this.nicPane,{position : 'absolute', left : this.pos[0]+'px', top : this.pos[1]+'px', className : 'nicEdit-pane'});
		bkLib.setStyle(this.nicPane,options);
		
		document.body.appendChild(this.nicPane);
		
		panelPos = bkLib.elmPos(nicPanelElm);
		var xPanel = panelPos[0]+nicPanelElm.offsetWidth;	
		xPos = this.pos[0]+this.nicPane.offsetWidth;
		
		if(xPos > xPanel) {
			bkLib.setStyle(this.nicPane,{left : (this.pos[0]-(xPos-xPanel+1))+'px'});
		}	
		
		this.init();
		bkLib.unselectAble(this.nicPane);	
	},
	
	init : function() {
	
	},
	
	hide : function() {
		this.nicPane.style.display = 'none';
		this.isVisible = false;	
	},
	
	show : function() {
		if(this.isOver) {
			this.nicPane.style.display = 'block';
			this.isVisible = true;	
		}
	},
	
	remove : function() {
		if(this.nicPane) {
			document.body.removeChild(this.nicPane);
		}
	},
	
	append : function(c) {
		this.nicPane.appendChild(c);
	},
	
	setContent : function(c) {
		this.nicPane.innerHTML = c;
	}
	
});

var nicEditorTooltip = nicEditorPane.extend({
	init : function() {
		bkLib.addEvent(this.elm,'mouseover',this.mouseOver.closureListener(this));
		bkLib.addEvent(this.elm,'mouseout',this.mouseOut.closureListener(this));
		bkLib.addEvent((this.elm.nodeName == "IFRAME") ? this.elm.contentWindow.document : this.elm,'mousemove',this.mouseMove.closureListener(this));
		this.hide();
	},
	
	mouseMove : function(e) {
		var pos = bkLib.mousePos(e);
		var isFrame = (this.elm.nodeName == "IFRAME");
		
		var xpos = isFrame ? pos[0]+this.pos[0] : pos[0];
		var ypos = isFrame ? pos[1]+this.pos[1]-this.elm.offsetHeight : pos[1];
		bkLib.setStyle(this.nicPane,{left : (xpos+10)+'px', top : (ypos+10)+'px'});
	},
	
	mouseOver : function(e,t) {
		var rt = (e.relatedTarget) ? e.relatedTarget : e.toElement;
		while(rt && rt != this.elm) {
			rt = rt.parentNode;
		}
		if(rt == this.elm) { return; }
		
		if(!this.isVisible) {
			this.toolTimer = setTimeout(this.show.closure(this),400);
		}
		this.isOver = true;
	},
	
	mouseOut : function(e,t) {
		var rt = (e.relatedTarget) ? e.relatedTarget : e.toElement;
		while(rt && rt != this.elm) {
			rt = rt.parentNode;
		}
		if(rt == this.elm) { return; }
		
		if(this.toolTimer) {
			clearTimeout(this.toolTimer);
		}
		this.hide();
		this.isOver = false;
	}
});

var nicEditorImageButton = nicEditorButton.extend({
	
	addPane : function() {
			this.removeTooltip();
			this.nicEditor.selectedInstance.saveRng();
			
			this.buttonPane = new nicEditorPane(this.buttonContain,this.nicEditor,this.getPaneStyle());
				
			var paneContent = "<form id=\"nicImageForm\" onSubmit=\"return false;\">\
            <label style=\"display: block; float: left; width: 100px\" for=\"url\">图片路径</label>\
			<input style=\"border: 1px solid #ccc; width: 150px;\" type=\"text\" name=\"url\" id=\"nicImageURL\" value=\"http://\" /><br />\
			<input type=\"submit\" style=\"border: 1px solid #ccc\" id=\"nicImageSubmit\" value=\"确定\" />\
            </div></form>";
			
        	this.buttonPane.setContent(paneContent);
			this.imgURL = $N('nicImageURL');
			this.imgURL.focus();
			
			bkLib.addEvent($N('nicImageForm'),'submit',this.paneSubmit.closure(this));
	},
	
	paneSubmit : function() {
		var si = this.nicEditor.selectedInstance;
		if(si) {
			si.restoreRng();
			var url = this.imgURL.value;
			if(url == "http://" || url == "") {
				alert("图片路径不能为空");
				return false;
			}
			this.nicEditor.nicCommand("insertImage",url);
			this.removePane();
		}
	}
});


var nicEditorLinkButton = nicEditorButton.extend({
	
	addPane : function() {
			this.removeTooltip();
			this.nicEditor.selectedInstance.saveRng();
			
			this.buttonPane = new nicEditorPane(this.buttonContain,this.nicEditor,this.getPaneStyle());
			var paneContent = "<form id=\"nicLinkForm\" onSubmit=\"return false;\">\
			<label style=\"display: block; float: left; width: 100px\" for=\"url\">链接地址</label>\
			<input type=\"text\" style=\"border: 1px solid #ccc; width: 150px;\" name=\"url\" id=\"nicLinkURL\" value=\"http://\" /><br />\
			<input type=\"submit\" style=\"border: 1px solid #ccc\" value=\"确定\" /></form>";
			this.buttonPane.setContent(paneContent);
			this.linkURL = $N('nicLinkURL');
			this.linkURL.focus();
			
			bkLib.addEvent($N('nicLinkForm'),'submit',this.paneSubmit.closure(this));
	},
	
	paneSubmit : function() {
		var si = this.nicEditor.selectedInstance;
		if(si) {
			this.nicEditor.selectedInstance.restoreRng();
			var url = this.linkURL.value;
			
			if(url == "http://" || url == "") {
				alert("链接路径不能为空");
				return false;
			}
			this.nicEditor.nicCommand("createlink",url);
		}
		this.removePane();
	}
});

var nicEditorHTMLButton = nicEditorButton.extend({
	
	addPane : function() {
			this.removeTooltip();
			this.buttonPane = new nicEditorPane(this.buttonContain,this.nicEditor,{width : (this.nicEditor.nicPanel.panelElm.offsetWidth-12)+'px', textAlign: 'left', border : '1px solid #ccc', zIndex : '9999', backgroundColor : '#fff'});
			paneContent = "<form id=\"nicHTMLForm\" onSubmit=\"return false;\"><textarea id=\"nicHTMLArea\" style=\"width: 100%; text-align: left; font-size: 13px; height: 200px; border: 0; border-bottom: 1px solid #ccc;\">\
			</textarea><input type=\"submit\" style=\"margin-left: 20px; border: 1px solid #ccc\" value=\"Update HTML\" /></form>";
			this.buttonPane.setContent(paneContent);
			
			bkLib.addEvent($N('nicHTMLForm'),'submit',this.paneSubmit.closure(this));
			$N("nicHTMLArea").value = this.nicEditor.selectedInstance.getContent();
	},
	
	paneSubmit : function() {
		var si = this.nicEditor.selectedInstance;
		if(si) {
			si.restoreRng();
			si.setContent($N('nicHTMLArea').value);
		}

		this.removePane();
	}
});

var nicEditorColorButton = nicEditorButton.extend({
	
	addPane : function() {
			this.removeTooltip();
			this.buttonPane = new nicEditorPane(this.buttonContain,this.nicEditor,this.getPaneStyle());
			var colorList = {0 : '00',1 : '33',2 : '66',3 :'99',4 : 'CC',5 : 'FF'};
			
			var colorItems = document.createElement('DIV');
			bkLib.setStyle(colorItems,{width : '270px'});
			for(var r in colorList) {
				for(var b in colorList) {
					for(var g in colorList) {
						var colorSquare = document.createElement('DIV');
						var colorBorder = document.createElement('DIV');
						var colorInner = document.createElement('DIV');
						
						var colorCode = '#'+colorList[r]+colorList[g]+colorList[b];
						bkLib.setStyle(colorSquare,{'cursor' : 'pointer', 'height' : '15px', 'float' : 'left'});
						bkLib.setStyle(colorBorder,{border: '2px solid '+colorCode});
						bkLib.setStyle(colorInner,{backgroundColor : colorCode, overflow : 'hidden', width : '11px', height : '11px'});
						bkLib.addEvent(colorSquare,'click',this.colorSelect.closure(this,colorCode));
						bkLib.addEvent(colorSquare,'mouseover',this.borderOn.closure(this,colorBorder));
						bkLib.addEvent(colorSquare,'mouseout',this.borderOff.closure(this,colorBorder,colorCode));
						
						if(!window.opera) {
							colorSquare.onmousedown = bkLib.cancelEvent;
							colorInner.onmousedown = bkLib.cancelEvent;
						}
						
						colorSquare.appendChild(colorBorder);
						colorBorder.appendChild(colorInner);
						colorItems.appendChild(colorSquare);
					}	
				}	
			}
			bkLib.unselectAble(colorItems);
			this.buttonPane.append(colorItems);	
	},
	
	colorSelect : function(c) {
		this.nicEditor.nicCommand('foreColor',c);
		this.removePane();
	},
	
	borderOn : function(colorBorder) {
		bkLib.setStyle(colorBorder,{border : '2px solid #000'});
	},
	
	borderOff : function(colorBorder,colorCode) {
		bkLib.setStyle(colorBorder,{border : '2px solid '+colorCode});		
	}
});
