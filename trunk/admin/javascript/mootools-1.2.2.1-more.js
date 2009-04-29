//MooTools More, <http://mootools.net/more>. Copyright (c) 2006-2009 Aaron Newton <http://clientcide.com/>, Valerio Proietti <http://mad4milk.net> & the MooTools team <http://mootools.net/developers>, MIT Style License.

MooTools.More={version:"1.2.2.1"};Fx.Scroll=new Class({Extends:Fx,options:{offset:{x:0,y:0},wheelStops:true},initialize:function(b,a){this.element=this.subject=$(b);
this.parent(a);var d=this.cancel.bind(this,false);if($type(this.element)!="element"){this.element=$(this.element.getDocument().body);}var c=this.element;
if(this.options.wheelStops){this.addEvent("start",function(){c.addEvent("mousewheel",d);},true);this.addEvent("complete",function(){c.removeEvent("mousewheel",d);
},true);}},set:function(){var a=Array.flatten(arguments);this.element.scrollTo(a[0],a[1]);},compute:function(c,b,a){return[0,1].map(function(d){return Fx.compute(c[d],b[d],a);
});},start:function(c,h){if(!this.check(c,h)){return this;}var e=this.element.getSize(),f=this.element.getScrollSize();var b=this.element.getScroll(),d={x:c,y:h};
for(var g in d){var a=f[g]-e[g];if($chk(d[g])){d[g]=($type(d[g])=="number")?d[g].limit(0,a):a;}else{d[g]=b[g];}d[g]+=this.options.offset[g];}return this.parent([b.x,b.y],[d.x,d.y]);
},toTop:function(){return this.start(false,0);},toLeft:function(){return this.start(0,false);},toRight:function(){return this.start("right",false);},toBottom:function(){return this.start(false,"bottom");
},toElement:function(b){var a=$(b).getPosition(this.element);return this.start(a.x,a.y);}});var Scroller=new Class({Implements:[Events,Options],options:{area:20,velocity:1,onChange:function(a,b){this.element.scrollTo(a,b);
},fps:50},initialize:function(b,a){this.setOptions(a);this.element=$(b);this.listener=($type(this.element)!="element")?$(this.element.getDocument().body):this.element;
this.timer=null;this.bound={attach:this.attach.bind(this),detach:this.detach.bind(this),getCoords:this.getCoords.bind(this)};},start:function(){this.listener.addEvents({mouseenter:this.bound.attach,mouseleave:this.bound.detach});
},stop:function(){this.listener.removeEvents({mouseenter:this.bound.attach,mouseleave:this.bound.detach});this.timer=$clear(this.timer);},attach:function(){this.listener.addEvent("mousemove",this.bound.getCoords);
},detach:function(){this.listener.removeEvent("mousemove",this.bound.getCoords);this.timer=$clear(this.timer);},getCoords:function(a){this.page=(this.listener.get("tag")=="body")?a.client:a.page;
if(!this.timer){this.timer=this.scroll.periodical(Math.round(1000/this.options.fps),this);}},scroll:function(){var b=this.element.getSize(),a=this.element.getScroll(),f=this.element.getOffsets(),c=this.element.getScrollSize(),e={x:0,y:0};
for(var d in this.page){if(this.page[d]<(this.options.area+f[d])&&a[d]!=0){e[d]=(this.page[d]-this.options.area-f[d])*this.options.velocity;}else{if(this.page[d]+this.options.area>(b[d]+f[d])&&a[d]+b[d]!=c[d]){e[d]=(this.page[d]-b[d]+this.options.area-f[d])*this.options.velocity;
}}}if(e.y||e.x){this.fireEvent("change",[a.x+e.x,a.y+e.y]);}}});