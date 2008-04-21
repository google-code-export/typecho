function bkClass() { }
bkClass.prototype.construct = function() {};
bkClass.extend = function(def) {
  var classDef = function() {
      if (arguments[0] !== bkClass) { this.construct.apply(this, arguments); }
  };
  var proto = new this(bkClass);
  var superClass = this.prototype;
  for (var n in def) {
      var item = def[n];                      
      if (item instanceof Function) item.$ = superClass;
      proto[n] = item;
  }
  classDef.prototype = proto;
  classDef.extend = this.extend;      
  return classDef;
};

Function.prototype.closure = function() {
  var __method = this, args = bkLib.toArray(arguments), obj = args.shift();
  return function() { return __method.apply(obj,args.concat(bkLib.toArray(arguments))); };
}

Function.prototype.closureListener = function() {
  var __method = this, args = bkLib.toArray(arguments), object = args.shift(); 
  return function(e) { 
  		e = e || window.event;
  		if(e.target) { var target = e.target; } else { var target =  e.srcElement };
  		return __method.apply(object, [e,target].concat(args) ); 
	};
}

function $N(itm) {
	return document.getElementById(itm);	
}

var bkLib = {
	getStyle : function( element, cssRule, d ) {
		var doc = (!d) ? document.defaultView : d; 
		return (doc && doc.getComputedStyle) ? doc.getComputedStyle( element, '' ).getPropertyValue(cssRule) : element.currentStyle[ cssRule ];
	},
	
	setStyle : function(element, st) {
		var elmStyle = element.style;
		for(itm in st) {
			switch(itm) {
				case 'float':
					elmStyle['cssFloat'] = elmStyle['styleFloat'] = st[itm];
					break;
				case 'opacity':
					elmStyle.opacity = st[itm];
					elmStyle.filter = "alpha(opacity=" + Math.round(st[itm]*100) + ")"; 
					break;
				case 'className':
					element.className = st[itm];
					break;
				default:
					if(document.compatMode || itm != "cursor") { // Nasty Workaround for IE 5.5
						elmStyle[itm] = st[itm];
					}		
			}
		}
	},
	
	cancelEvent : function(e) {
		e = e || window.event;
		if(e.preventDefault && e.stopPropagation) {
			e.preventDefault();
			e.stopPropagation();
		}
		return false;
	},
	
	domLoad : [],
	domLoaded : function() {
		if (arguments.callee.done) return;
		arguments.callee.done = true;
		for (i = 0;i < bkLib.domLoad.length;i++) bkLib.domLoad[i]();
	},
	onDomLoaded : function(fireThis) {
		this.domLoad.push(fireThis);
		if (document.addEventListener) {
			document.addEventListener("DOMContentLoaded", bkLib.domLoaded, null);
		}
		/*@cc_on @*/
		/*@if (@_win32)
			var proto = "src='javascript:void(0)'";
			if (location.protocol == "https:") proto = "src=//0";
			document.write("<scr"+"ipt id=__ie_onload defer " + proto + "><\/scr"+"ipt>");
			var script = document.getElementById("__ie_onload");
			script.onreadystatechange = function() {
			    if (this.readyState == "complete") {
			        bkLib.domLoaded();
			    }
			};
		/*@end @*/
	    window.onload = bkLib.domLoaded;
	},
	
	addEvent : function(obj, type, fn) {
		(obj.addEventListener) ? obj.addEventListener( type, fn, false ) : obj.attachEvent("on"+type, fn);	
	},
	
	elmPos : function(obj) {
		var curleft = curtop = 0;
		var objHeight = obj.offsetHeight;
		if (obj.offsetParent) {
			curleft = obj.offsetLeft
			curtop = obj.offsetTop
			while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft
				curtop += obj.offsetTop
			}
		}
		return [curleft,curtop+objHeight];
	},
	
	mousePos : function(e) {
		return [e.pageX||e.clientX+document.body.scrollLeft+document.documentElement.scrollLeft,e.pageY||e.clientY+document.body.scrollTop+document.documentElement.scrollTop];
	},
	
	getElementsByClassName : function(classname) {
		if(document.getElementsByClassName) {
			return document.getElementsByClassName(classname);
		}
	    var a = [];
	    var re = new RegExp('\\b' + classname + '\\b');
	    var els = document.getElementsByTagName("*");
	    for(var i=0,j=els.length; i<j; i++) {
	        if(re.test(els[i].className))a.push(els[i]);
	    }
	    return a;
	},
	
	inArray : function(arr,item) {
	    for (i=0; i < arr.length; i++) {
		    if (arr[i] === item) {
		        return true;
		    }
	    }
	    return false;
	},
	
	toArray : function(iterable) {
		var length = iterable.length, results = new Array(length);
    	while (length--) results[length] = iterable[length];
    	return results;	
	},
	
	unselectAble : function(element) {
		if(element.setAttribute && element.contentEditable != true && element.nodeName != 'input' && element.nodeName != 'textarea') {
			element.setAttribute('unselectable','on');
		}
	
		for(var i=0;i<element.childNodes.length;i++) {
			bkLib.unselectAble(element.childNodes[i]);
		}
	},
	
	ajaxRequest : function(requestMethod,ajaxURL,ajaxData) {
		var ajaxRequest = (window.XMLHttpRequest) ? new window.XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
		ajaxRequest.open((!requestMethod) ? 'GET' : requestMethod, ajaxURL, true);
		if(requestMethod == "POST") {
			ajaxRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		}
		ajaxRequest.send(ajaxData);
	}
};

var bkEvent = {
	
	addEvent : function(evType, evFunc) {
		if(evFunc) {
			this.eventList = this.eventList || {};
			this.eventList[evType] = this.eventList[evType] || [];
			this.eventList[evType].push(evFunc);
		}
	},
	
	fireEvent : function(evType,evArgs) {
		if(this.eventList && this.eventList[evType]) {
			for(var i=0;i<this.eventList[evType].length;i++) {
				this.eventList[evType][i](evArgs);
			}
		}
	}	
};