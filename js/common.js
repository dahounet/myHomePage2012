msgWindow={
	timer:null,	//定时器指针
	interval:null,	//循环定时器指针
	msgidValue:0,
	target:{t1:$("#promptBox"),t2:$("#promptBox > .box")},
	show:function(o){
			if(o.time==null){
				o.time=1200;	//默认每条消息的显示时长
			}else if(o.time>16000){
				o.time=16000;	//强制每条消息的显示时长最长为16秒
			}
			
			function control(_this){
				var timer=null;
				if(_this.index==-1){		//如果是第一条消息，则需要显示消息并同时打开窗口
					_this.chgcode(++_this.index);
					_this.timer=setTimeout(function(){control(_this);},_this.code[_this.index++].time+400);
					_this.openwin(_this);
					
					_this.interval=setInterval(function(){
							if(((_this.code[_this.index]!=null)&&_this.code[_this.index-1].level<_this.code[_this.index].level)||_this.code[_this.index-1].del==1){	//如果当前消息的后面已有新消息且那条新消息的级别比当前消息的级别高，或者当前消息已被提前删除，则立即取消当前消息的显示
								//console.log(_this.code[_this.index-1].msgid+' A 发现关闭自己情况，立即关闭自己');
								clearTimeout(_this.timer);
								clearInterval(_this.interval);
								control(_this);
							}
						},100);
				}else if(_this.index==_this.code.length){	//如果已无需要显示的消息，则关闭窗口
					clearInterval(_this.interval);
					_this.index=-1;
					_this.code=[];
					_this.closewin(_this);
				}else{	//如果既不是第一条消息，也还有需要显示的消息，则仅改变消息内容
					clearInterval(_this.interval);
					_this.chgcode(_this.index);
					_this.timer=setTimeout(function(){control(_this);},_this.code[_this.index++].time);
					
					_this.interval=setInterval(function(){
							if(((_this.code[_this.index]!=null)&&_this.code[_this.index-1].level<_this.code[_this.index].level)||_this.code[_this.index-1].del==1){	//如果当前消息的后面已有新消息且那条新消息的级别比当前消息的级别高，或者当前消息已被提前删除，则立即取消当前消息的显示
								//console.log(_this.code[_this.index-1].msgid+' B 发现关闭自己情况，立即关闭自己');
								clearTimeout(_this.timer);
								clearInterval(_this.interval);
								control(_this);
							}
						},100);
				}
			}
			//{'html':html,'time':time,'callback':callback,'level':21}
			this.msgidValue%=200;
			if(this.index==-1){	//如果添加这条新消息前，还没有消息被添加，则需要开始执行窗口控制流程
				msgid=this.code[this.code.push(o)-1].msgid=this.msgidValue++;
				control(this);
				//console.log('新加入:(第一) msgid '+msgid);
				return msgid;
			}else{	//如果已有消息，仅追加这条新消息
				msgid=this.code[this.code.push(o)-1].msgid=this.msgidValue++;
				//console.log('新加入: msgid '+msgid);
				return msgid;
			}
			
		},
	close:function(msgid){	//关闭消息或窗口
			/*if(?????){	//如果是强制关闭全部窗口，则
				clearInterval(this.interval);
				clearTimeout(this.timer);
				this.index=-1;
				this.code=[];
				this.closewin(this);
				//console.log("强制关闭窗口");*/
			if(msgid>=0){//如果是提前关闭指定消息，则
				//console.log("提前关闭 "+msgid);
				var i=0;
				while(this.code[i]!=null){
					if(this.code[i].msgid==msgid){
						this.code[i].del=1;
					}
					i++;
				}
			}
		},
	code:[],	//消息组
	index:-1,	//消息索引号
	setpos:function(){
		var x=($(window).width()-this.target.t2.width())/2+'px';
		var y=($(window).height()-this.target.t2.height())/2+'px';
		this.target.t2.css('left',x);
		this.target.t2.css('top',y);
	},
	openwin:function(_this){
		this.target.t1.css("display","block");
		this.setpos();
		$(window).bind("resize",eventFunc_msgWindow=function(){_this.setpos();});
		setTimeout(function(){_this.target.t1.css("opacity","1");},0);
	},
	closewin:function(_this){
		this.target.t1.css("opacity","0");
		setTimeout(function(){_this.target.t1.css("display","none");_this.target.t2.empty();},400);
		$(window).unbind("resize",eventFunc_msgWindow);
	},
	chgcode:function(index){
		this.target.t2.html(this.code[index].html);
		if(this.code[index].callback){
			(this.code[index].callback)();
		}
		this.setpos();
	}
};

dialogWindow={
	callback:null,
	target:{t1:$("#dialogBox"),t2:$("#dialogBox .box"),t3:$("#dialogBox .tit"),t4:$("#dialogBox .con"),t5:$("#dialogBox .closeBtn")},
	show:function(title,html,callback){
		this.target.t4.html(html);
		if(title){
			this.target.t3.html("<span>"+title+"</span>");
		}else{
			this.target.t3.html("<span>对话框</span>");
		}
		this.openwin(this);
		if(callback){
			this.callback=callback;
		}
	},
	colse:function(){
		this.closewin(this);
		if(this.callback){
			(this.callback)();
			this.callback=null;
		}
	},
	setpos:function(){
		var x=($(window).width()-this.target.t2.width())/2+'px';
		var y=($(window).height()-this.target.t2.height())/2+'px';
		this.target.t2.css('left',x);
		this.target.t2.css('top',y);
	},
	openwin:function(_this){
		this.target.t1.css("display","block");
		this.setpos();
		$(window).bind("resize",eventFunc_dialogWindow=function(){_this.setpos();});
		$(this.target.t5).bind("click",function(){_this.colse();});
		setTimeout(function(){_this.target.t1.css("opacity","1");},0);
	},
	closewin:function(_this){
		this.target.t1.css("opacity","0");
		setTimeout(function(){_this.target.t1.css("display","none");_this.target.t3.empty();_this.target.t4.empty();},400);
		$(window).unbind("resize",eventFunc_dialogWindow);
	}
};

function changeLeftList(t){
	var a=document.getElementById("ltList");
	if(a.className=="lt"){
		a.className="lt c";t.title="左伸";
	}else{
		a.className="lt",t.title="右缩";
	}
}
function changeRightList(t){
	var a=document.getElementById("ltList");
	if(a.className=="lt"){
		a.className="lt c";t.title="左伸";
	}else{
		a.className="lt",t.title="右缩";
	}
}
function promptControl(action,html,time,callback){
	var t1=$("#promptBox");
	var t2=$("#promptBox > .box");
	function setpos(){
		var x=($(window).width()-t2.width())/2+'px';
		var y=($(window).height()-t2.height())/2+'px';
		t2.css('left',x);
		t2.css('top',y);
	}
	if(action=='show'){
		t2.html(html);
		t1.css("display","block");
		setpos();
		$(window).bind("resize",f1=function(){
			var x=($(window).width()-t2.width())/2+'px';
			var y=($(window).height()-t2.height())/2+'px';
			t2.css('left',x);
			t2.css('top',y);
		});
		setTimeout(function(){t1.css("opacity","1");},0);
	}else if(action=='hide'){
		if(time==null){
			time=1000;
		}
		setTimeout(function(){t1.css("opacity","0");setTimeout(function(){t1.css("display","none");t2.empty();},400)},time);
		$(window).unbind("resize",f1);
	}else if(action=='chgbox'){
		t2.html(html);
		setpos();
	}
	if(callback) (callback)();
}

function loadworkslist(auto){
	function bindEvent(){
		function createTip(c,e,f){
			if(f==1){
				$("#workTip").attr('wid',c);
				$("#workTip .loading").show().siblings().hide();
			}
			if(f==2){
				$("#workTip .loading").hide().siblings().show();
				var t=$("#workTip ul");
				t.empty();
				$("#workTip h1").empty().text(c.title);
				var workType=new Array(null,'每日事务','一次性事务','周期性事务');
				var li=new Array();
				li[0]='<li><span>事务类型: </span>'+workType[c.type]+'</li>';
				if(c.stopdate!=null){
					li[1]='<li><span>执行时间: </span><div class="r"><i>'+c.startdate+'</i> 至 <i>'+c.stopdate+'</i></div></li>';
				}else{
					li[1]='<li><span>执行时间: </span><div class="r"><i>'+c.startdate+'</i> 至 <i>长期</i></div></li>';
				}
				li[2]='<li><span>完成情况: </span><!--<a href="javascript:void(0);" class="l" title="点击查看详细完成情况"><em>50/55</em>，完成率<em>95%</em></a>-->(研发中...)</li>';
				li[3]='<li><span>添加时间: </span><i>'+c.addtime+'</i></li>';
				if(c.describe){
					li[4]='<li><span>详细说明: </span><div class="r">'+c.describe+'</div></li>';
				}else{
					li[4]='<li><span>详细说明: </span><div class="r">(无)</div></li>';
				}
				if($("#"+e.currentTarget.id+"").children().is("em")){
					li[5]='<li><b>当前待完成</b></li>';
				}else{
					li[5]='<li><b class="ok">已完成</b></li>';
				}
				var i=0;
				while(i<li.length){
					t.append(li[i++]);
				};
			}
			var $t2=$("#workTip"),$t3=$("#"+e.currentTarget.id+"");
			var x,y;
			x=$t3.offset().left+30;
			y=$t3.offset().top+$t3.height()+5;
			//$("#abc").text($(window).height()-y);
			if($(window).height()-y<$t2.height()+5){
				y=y-$t2.height()-40;
				$t2.children(".arrow2").css("display","block");
				$t2.children(".arrow1").css("display","none");
			}else{
				$t2.children(".arrow1").css("display","block");
				$t2.children(".arrow2").css("display","none");
			}
			//alert(e.clientX);
			//alert($t3.position().top)
			$t2.css('left',x);
			$t2.css('top',y);
		}
		function loadWorkContent(wid){
			function makeSign(wid){
				$("#workslist").attr("now_wid",wid);
				$("#wid_"+wid).attr("clicked","yes").addClass("now").siblings().attr("clicked","no").removeClass("now");
				$("#workContent").attr('currentWorkid',wid);
			}
			if($("#wid_"+wid).attr("clicked")!="yes"){
			$.ajax({
				type:"GET",
				url:"works.php?ac=wkcontent&wid="+wid,
				dataType:"json",
				timeout:12000,
				beforeSend: function(){
					this.closemsgid=msgWindow.show({'html':'<p><span class="prompt-loading">事务内容加载中...</span></p>','time':4000,'callback':null,'level':10});
				},
				success:function(data,textStatus){
					makeSign(wid);
					$("#workContent").empty().append('<li>'+data.html+'</li>');
					var o=document.createElement('script');
					o.type="text/javascript";
					o.text=data.js;
					$(o).appendTo("body");
					msgWindow.close(this.closemsgid);
					//msgWindow.show({'html':'<p><span class="prompt-complete">数据加载完成</span></p>','time':1000,'callback':null,'level':21});
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">事务内容加载出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
			}
		}
		var t=null;
		$("#workslist > li").mouseenter(function(e){
			var wid=(e.currentTarget.id).slice(4);
			t=setTimeout(function(){
					createTip(wid,e,1);
					$("#workTip").addClass('show');
					$.ajax({
						type:"GET",
						url:"works.php?ac=wkinfo&wid="+wid,
						dataType:"json",
						timeout:12000,
						success:function(data,textStatus){
							if($("#workTip").attr('wid')==wid){
								createTip(data,e,2);
							}
						}
					});
				},330);
		});
		$("#workslist > li").mouseleave(function(){
			clearTimeout(t);
			t=setTimeout(function(){$("#workTip").removeClass('show');},280);
		});
		$("#workslist > li").click(function(e){
			loadWorkContent((e.currentTarget.id).slice(4));
		});
		$("#workTip").mouseover(function() {
			clearTimeout(t);
			//q$("#workTip").addClass('show');
		});
		$("#workTip").mouseleave(function(){
			$(this).removeClass('show');
		});
	}
	
	if($("#workslist").attr("md5")!=undefined){
		var ajaxURL="works.php?ac=wkslist&md5="+$("#workslist").attr("md5");
	}else{
		var ajaxURL="works.php?ac=wkslist";
	}
	$.ajax({
		type:"GET",
		url:ajaxURL,
		dataType:"json",
		timeout:12000,
		beforeSend: function(){
			if(!auto)	this.closemsgid=msgWindow.show({'html':'<p><span class="prompt-loading">事务列表加载中...</span></p>','time':4000,'callback':null,'level':10});
		},
		success:function(data,textStatus){
			if(data.status=='latest'){	//如果当前列表数据已是最新
				if(!auto)	msgWindow.show({'html':'<p><span class="prompt-complete">事务列表已是最新</span></p>','time':600,'callback':null,'level':21});
			}else{
				
				if($("#workslist").attr("now_wid")!=null){
					var now_wid=$("#workslist").attr("now_wid");
				}
				$("#workslist").empty().attr("md5",data.md5);
				//msgWindow.show({'html':'<p><span class="prompt-complete">事务列表加载完成</span></p>','time':500,'callback':null,'level':21});
				//console.log("____-----  "+this.closemsgid);
				msgWindow.close(this.closemsgid);
				for(var i=0;i<data.list.length;i++){
					if(now_wid==data.list[i].wid){
						var c="<li "+'id="wid_'+data.list[i].wid+'" class="now">';
					}else{
						var c="<li "+'id="wid_'+data.list[i].wid+'">';
					}
					if(!data.list[i].finished){
						c+='<em class="prompt">提醒</em>';
					}
					if(data.list[i].deleteable!=0){
						c+='<span class="manager"><a href="#" title="编辑事务" class="manager-modibtn">编辑事务</a><a href="#" title="删除事务" class="manager-delbtn" onClick="doC2(this.parentNode.parentNode);">删除事务</a></span>';
					}else{
						c+='<span class="manager"><a href="#" title="编辑事务" class="manager-modibtn">编辑事务</a></span>';
					}
					$("#workslist").append(c+'<a href="#" onClick="return false;">'+data.list[i].title+'</a></li>');
				}
				$("#waitdonum").text(data.waitdonum);
				bindEvent();
			}
		},
		error:function(){
			if(!auto)	msgWindow.show({'html':'<p><span class="prompt-error">事务列表加载出错！</span></p>','time':2200,'callback':null,'level':10});	else	msgWindow.show({'html':'<p><span class="prompt-error">事务列表自动刷新出错！</span></p>','time':2200,'callback':null,'level':10});
		},
		complete: function(){
			//msgWindow.show({'html':'<p><span class="prompt-complete">AJAX完毕</span></p>','time':800,'callback':function(){/*alert("1111")*/},'level':10});
		}
	});
}
function loadWorkContent(wid){
	function makeSign(wid){
		$("#workslist").attr("now_wid",wid);
		$("#wid_"+wid).attr("clicked","yes").addClass("now").siblings().attr("clicked","no").removeClass("now");
	}
	if($("#wid_"+wid).attr("clicked")!="yes"){
	$.ajax({
		type:"GET",
		url:"works.php?ac=wkcontent&wid="+wid,
		dataType:"json",
		timeout:12000,
		beforeSend: function(){
			this.closemsgid=msgWindow.show({'html':'<p><span class="prompt-loading">事务内容加载中...</span></p>','time':4000,'callback':null,'level':10});
		},
		success:function(data,textStatus){
			makeSign(wid);
			$("#workContent").empty().append('<li>'+data.html+'</li>');
			var o=document.createElement('script');
			o.type="text/javascript";
			o.text=data.js;
			$(o).appendTo("body");
			msgWindow.close(this.closemsgid);
			//msgWindow.show({'html':'<p><span class="prompt-complete">数据加载完成</span></p>','time':1000,'callback':null,'level':21});
		},
		error:function(){
			msgWindow.show({'html':'<p><span class="prompt-error">事务内容加载出错！</span></p>','time':2200,'callback':null,'level':21});
		},
		complete: function(){
			//
		}
	});
	}
}
function refreshWorksList(){
	loadworkslist();
}
function completeWork(wid,key){
	if(key==null){
		var url='works.php?ac=wkcomplete&wid='+wid;
	}else{
		var url='works.php?ac=wkcomplete&wid='+wid+'&key='+key;
	}
	$.ajax({
		type:"GET",
		url:url,
		dataType:"json",
		timeout:12000,
		beforeSend: function(){
			msgWindow.show({'html':'<p><span class="prompt-loading">正在完成事务...</span></p>','time':8000,'callback':null,'level':10});
		},
		success:function(data,textStatus){
			if(data.result=='succeed'){
				msgWindow.show({'html':'<p><span class="prompt-complete">恭喜，您已完成了事务'+data.title+'</span></p>','time':1200,'callback':function(){setTimeout(refreshWorksList,500);},'level':21});			
			}else if(data.result=='finished'){
				msgWindow.show({'html':'<p><span class="prompt-error">'+data.msg+'</span></p>','time':1600,'callback':null,'level':21});
			}else if(data.result=='error'){
				msgWindow.show({'html':'<p><span class="prompt-error">'+data.msg+'</span></p>','time':1600,'callback':null,'level':21});
			}else if(data.result=='incorrect_key'){
				msgWindow.show({'html':'<p><span class="prompt-error">'+data.msg+'</span></p>','time':1600,'callback':null,'level':21});
			}else{
				msgWindow.show({'html':'<p><span class="prompt-error">发生了未知错误</span></p>','time':1600,'callback':null,'level':21});
			}
		},
		error:function(){
			msgWindow.show({'html':'<p><span class="prompt-error">发生了错误！</span></p>','time':2200,'callback':null,'level':21});
		},
		complete: function(){
			//
		}
	});
}