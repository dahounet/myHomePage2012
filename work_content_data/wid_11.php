<?php
include_once 'inc/simple_browser.class.php';
include_once 'inc/simple_html_dom.php';
if($_GET['ac']=='wkcontent'){
//这里放HTML代码
$arr['html']=<<<"EOT"
				<div class="yy365">
					<div id="notLoggedIn">尚未登陆</div> 
					<div id="accountInfo">尚未登录YY365帐号，<a href="#" class="login JQ_login">[立即登录]</a></div>
					<div id="webSiteLinks"><a href="http://www.yy365.com/" target="_blank">YY365首页</a><a href="http://www.yy365.com/info!commentInfo.do" target="_blank">评论回复提示</a><a href="http://www.pceggs.com/Gain/Gnmain.aspx" target="_blank">[积分兑换金蛋]</a></div>
					<div class="daka" id="JQ_TodayJF">
						<div class="score">今日积分<span class="no"><a href="#">[立即领取]</a></span></div>
						<div class="totalScore">当前总积分:--</div>
					</div>
					<div class="renqi" id="JQ_NowRQ">
						<div class="n">今日当前人气<span>--</span></div>
					</div>
					<div class="sendMsg" id="JQ_SendMiniBlogPanel">
						<textarea class="cntInput"></textarea>
						<button class="sendBtn">发布</button>
						<div class="autoSendset"><label><input type="checkbox" checked="checked" /><span>0点时自动发送一条迷你博客</span></label></div>
					</div>
					<div class="clr"></div>
					<div class="hudong">
						<div class="blist" id="JQ_BlogList">
							<div class="hl">评论回复提示<a href="javascript:void(0);">[全部忽略]</a><a href="javascript:void(0);">刷新</a></div>
							<div class="loading">加载中...</div>
							<ul>
							</ul>
						</div>
						<div class="clist" id="JQ_MiniBlog">
							
							<div class="loading" style="display:none;">加载中...</div>
							<div class="notLoaded">请在左侧“评论回复提示”中单击一条迷你博客的[查看]。</div>
						</div>
						<div class="clr"></div>
					</div>
				</div>
EOT;

//这里放JS代码
$arr['js']=<<<'EOT'
(function(){
	var firstDo=true;
	var timer=false;
	function getAccountInfo(callback,closemsgid){
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=11&wac=getAccountInfo",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				//
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null){
					if(callback&&closemsgid!==null){
						callback(data.result,closemsgid)
					}else{
						callback(data.result);
					}
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">获取YY365账户信息出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">获取YY365账户信息出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
							
	function setAccountInfo(accountInfo,closemsgid){
		$("#accountInfo").html('登陆帐户：<span><a href="#" class="JQ_username" title="查看YY365账户信息">'+accountInfo.username+'</a></span><a href="#" class="logout JQ_logout">[退出]</a>');
		if(closemsgid){
			msgWindow.close(closemsgid);
		}
		$("#accountInfo span a.JQ_username").click(function(e){
			function foundHtml(o){
				var temp="";
				if(o.cookiesList!=null){
					for(i=0;i<o.cookiesList.length;i++){
						if(o.cookiesList[i].expire){
							temp=temp+'<li><span class="n">'+o.cookiesList[i].name+'</span><span class="e">('+o.cookiesList[i].expire+' 到期)</span><span class="v">'+o.cookiesList[i].value+'</span></li>';
						}else{
							temp=temp+'<li><span class="n">'+o.cookiesList[i].name+'</span><span class="e">(无到期时间)</span><span class="v">'+o.cookiesList[i].value+'</span></li>';
						}
					}
				}
				temp='<div class="login accountInfo"><ul class="login-form"><li><div class="login-form-l">用户名:</div><div class="login-form-r"><input type="text" maxlength="25" disabled="disabled" value="'+o.username+'" /></div></li><li><div class="login-form-l">cookie:</div><div class="login-form-r"><ol class="cookie-list">'+temp+'</ol></div></li></ul></div>';
				return temp;
			}
			dialogWindow.show('<b>查看YY365账户信息</b>',foundHtml(accountInfo));
			return false;
		});
		$("#accountInfo a.JQ_logout").click(function(e){
			logout();
			return false;
		});
	}
	
	function loadverifycode(t){
		if($("#YY365loginForm .veriflyCode-img img").attr('src')==""){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=11&wac=retrieveRandomImg",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null){
						$("#YY365loginForm .veriflyCode-img").css('display','block');
						$("#YY365loginForm .veriflyCode-img img").attr('src',data.imgSrc);
						$("#YY365loginForm .veriflyCode-img img").click(function(e){
								loadverifycode(true);
						});
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">获取YY365验证码时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">获取YY365验证码时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}else if(t==true){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=11&wac=retrieveRandomImg",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null){
						$("#YY365loginForm .veriflyCode-img img").attr('src',data.imgSrc);
						$("#YY365loginForm input[name='verifycode']").focus();
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">获取YY365验证码时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">获取YY365验证码时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}
	}
			
	function checkStatus(){
		function check(){
			if(firstDo==true){
				init();
				firstDo=false;
			}
		}
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=11&wac=checkStatus",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				this.closemsgid=msgWindow.show({'html':'<p><span class="prompt-loading">检查YY365帐号登陆状态中...</span></p>','time':8000,'callback':null,'level':10});
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null){
					if(data.result==true){
						getAccountInfo(setAccountInfo,this.closemsgid);
						check();
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">YY365帐号尚未登陆，请登陆！</span></p>','time':1000,'callback':login,'level':21});
						$("#accountInfo a.JQ_login").click(function(e){
							login();
							return false;
						});
						$("#notLoggedIn").show().next().nextAll().hide();
					}
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">检查YY365帐号登陆状态出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">检查YY365帐号登陆状态出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		})
	}
	
	function logout(){
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=11&wac=logout",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				msgWindow.show({'html':'<p><span class="prompt-loading">正在退出YY365帐号...</span></p>','time':8000,'callback':null,'level':10});
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null){
					msgWindow.show({'html':'<p><span class="prompt-complete">退出YY365帐号成功！'+data.describe+'</span></p>','time':2200,'callback':null,'level':21});
					$("#notLoggedIn").show().next().nextAll().hide();
					$("#accountInfo").html('尚未登录YY365帐号，<a href="#" class="login JQ_login">[立即登录]</a>');
					clearInterval(timer);
					$("#accountInfo a.JQ_login").click(function(e){
						login();
						return false;
					});
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">退出YY365帐号时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">退出YY365帐号时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	function login(){
		dialogWindow.show("YY365账户登录",'<div class="login"><form method="post" id="YY365loginForm"><ul class="login-form"><li><div class="login-form-l">用户名:</div><div class="login-form-r"><input type="text" maxlength="25" name="username" /></div></li><li><div class="login-form-l">密码:</div><div class="login-form-r"><input type="password" maxlength="15" name="password" /></div></li><li><div class="login-form-l">验证码:</div><div class="login-form-r verifyCode"><input type="text" maxlength="6" name="verifycode" /><div class="veriflyCode-img"><img src="" title="点击更换验证码" alt="点击更换验证码" /></div></div></li><li class="login-form-btn"><input type="submit" value="登 陆" /></li></ul></form></div>');
		getAccountInfo(callback);
		function callback(r){
			$("#YY365loginForm input[name='username']").val(r.username);
		}
		
		$("#YY365loginForm").submit(function(e){
			username=$("#YY365loginForm input[name='username']").val();
			password=$("#YY365loginForm input[name='password']").val();
			vcode=$("#YY365loginForm input[name='verifycode']").val();
			if(username!=""&&password!=""){
				$.ajax({
					type:"GET",
					url:"works.php?ac=dowork&wid=11&wac=login&uname="+username+"&pwd="+password+"&vcode="+vcode,
					dataType:"json",
					timeout:15000,
					beforeSend: function(){
						msgWindow.show({'html':'<p><span class="prompt-loading">YY365帐号登陆登录中...</span></p>','time':8000,'callback':null,'level':10});
					},
					success:function(data,textStatus){
						if(data.errorInfo.no==null){
							if(data.result===true){
								msgWindow.show({'html':'<p><span class="prompt-complete">登录YY365帐号成功！帐号: '+data.username+'</span></p>','time':2200,'callback':null,'level':21});
								firstDo=false;
								checkStatus();
								init();
								dialogWindow.colse();
							}else{
								msgWindow.show({'html':'<p><span class="prompt-error">登录YY365失败，请检查 帐号、密码、验证码 是否均输入正确！</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
							}
						}else{
							msgWindow.show({'html':'<p><span class="prompt-error">登录YY365时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
						}
					},
					error:function(){
						msgWindow.show({'html':'<p><span class="prompt-error"登录YY365时出错！</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
					},
					complete: function(){
						//
					}
				});
			}else{
				msgWindow.show({'html':'<p><span class="prompt-error">用户名、密码均不能为空！</span></p>','time':1400,'callback':null,'level':10});
			}
			return false;
		});
		
		$("#YY365loginForm input[name='verifycode']").focus(function(e){
			loadverifycode();
		});
	}
	
	function sendMiniBlog(){
		var content=$("#JQ_SendMiniBlogPanel textarea").val();
		if(content.length>0){
			$.ajax({
				type:"POST",
				url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=0",
				data:"content="+content,
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					msgWindow.show({'html':'<p><span class="prompt-loading">正在发送迷你博客...</span></p>','time':8000,'callback':null,'level':10});
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result!=null&&data.result.state=='ok'){
						msgWindow.show({'html':'<p><span class="prompt-complete">迷你博客发送成功！</span></p>','time':800,'callback':null,'level':21});
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">迷你博客发送时出错！</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">迷你博客发送时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}else{
			msgWindow.show({'html':'<p><span class="prompt-error">发送的迷你博客内容不能为空！</span></p>','time':2200,'callback':null,'level':10});
		}
	}
	
	function creatMiniBlogContent(){
		var today=new Date();
		var h=today.getHours();
		var m=today.getMinutes();
		var s=today.getSeconds();
		var mo=today.getMonth();
		var d=today.getDate();
		var weekday=new Array(7);
		var time_list=new Array();
		time_list[0]=h+":"+m+":"+s+"啦！";
		time_list[1]="嗯~"+h+"点过"+m+"分钟"+s+"秒了   ";
		time_list[2]=h+"点啦！！ ";;
		
		var time_num=Math.round(Math.random()*2);
		weekday[0]="我来了~呵呵，今天是星期天咯 ";
		weekday[1]="哈，现在星期一啦~~";
		weekday[2]="额...星期二了哦，";
		weekday[3]="呵呵，星期三了~~";
		weekday[4]="....都到星期四啦~";
		weekday[5]="今天星期五，";
		weekday[6]="嗯~星期六，周末愉快。";
		nowweekday=weekday[today.getDay()];
		nowmonth=mo+1;
		nowdate=d;
		nowtime=time_list[time_num];
		
		var str2_list=new Array();
		str2_list[0]="我来啦，好友们都在忙些什么呢？";
		str2_list[1]="今天天气怎么样啊？";
		str2_list[2]="登陆啦~更新一下了，哈哈~";
		str2_list[3]="休息一下，来看看朋友!";
		str2_list[4]="大家说说晚上有什么安排？";
		str2_list[5]="好友们打卡领积分了没？友情提醒~嘿嘿";
		str2_list[6]="好友们有什么要和大家分享的？";
		str2_list[7]="嗯？大家今天一切顺利吧？..";
		str2_list[8]="我来更新~呵呵";
		str2_list[9]="来更新咯，嗯！ 日复一日~";
		str2_list[10]="说说看大家周末想怎么玩？";
		str2_list[11]="友情提醒~今天和好友互动了吗？";
		var num_value2=Math.round(Math.random()*11);
		$("#JQ_SendMiniBlogPanel textarea").val(nowmonth+"月"+nowdate+"号 "+nowtime+""+nowweekday+str2_list[num_value2]);
		
		if(h==0 && $("#JQ_SendMiniBlogPanel .autoSendset input[type='checkbox']").attr("checked")=="checked"){
			if($("#JQ_SendMiniBlogPanel .autoSendset input[type='checkbox']").attr("isSend")!="1"){
				setTimeout(sendMiniBlog,6000);
				msgWindow.show({'html':'<p><span class="prompt-loading">即将于6秒后自动发送一条迷你博客</span></p>','time':8000,'callback':null,'level':0});
				$("#JQ_SendMiniBlogPanel .autoSendset input[type='checkbox']").attr("isSend","1");
				$("#JQ_SendMiniBlogPanel .autoSendset label span").text($("#JQ_SendMiniBlogPanel .autoSendset label span").text()+' (已发送)');
			}
		}
	}
	
	function loadNowRQ(){
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=3",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null&&data.result!=null){
					$("#JQ_NowRQ span").text(data.result);
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">获取当前人气时出错！</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">获取当前人气时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	
	function loadJF(){
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=1",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null&&data.result!=null){
					if(data.result.error!=null){	//互动次数不够，还未打卡领积分
						$("#JQ_TodayJF span").addClass("no").html('<a href="#">[立即领取]</a>');
						$("#JQ_TodayJF span.no a").click(function(e){
							getJF();
						});
					}else if(data.result.nowintegral>=50){	//已经领取了积分
						$("#JQ_TodayJF span").removeClass("no").html(data.result.nowintegral);
						$("#JQ_TodayJF .totalScore").show().text('未兑换积分: '+data.result.inte);
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">获取当前积分时发生未知错误！</span></p>','time':2200,'callback':null,'level':21});
					}
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">获取当前积分时出错！</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">获取当前积分时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	
	function getJF(){
		if($("#JQ_TodayJF span").hasClass('no')){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=2",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					msgWindow.show({'html':'<p><span class="prompt-loading">正在领取今日积分...</span></p>','time':8000,'callback':null,'level':10});
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result!=null){
						if(data.result.error!=null){	//互动次数不够
							msgWindow.show({'html':'<p><span class="prompt-error">领取失败，今日互动次数达到3次才可领取积分。</span></p>','time':2200,'callback':null,'level':21});
						}else if(data.result.nowintegral>=50){	//成功领取了积分
							msgWindow.show({'html':'<p><span class="prompt-complete">恭喜您！已成功领取今日积分: '+data.result.nowintegral+' (目前已积累 '+data.result.inte+' 积分未兑换为金蛋) 。</span></p>','time':3200,'callback':null,'level':21});
							$("#JQ_TodayJF span").removeClass("no").html(data.result.nowintegral);
							$("#JQ_TodayJF .totalScore").show().text('未兑换积分: '+data.result.inte);
						}else{
							msgWindow.show({'html':'<p><span class="prompt-error">领取积分时发生未知错误！</span></p>','time':2200,'callback':null,'level':21});
						}
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">领取积分时出错！</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">领取积分时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}
	}
	
	function getMiniBlogList(){
		
		function generateHtml(t){
			var tempArr=new Array();
			for(var i=0;i<t.length;i++){
				if(t[i].ctype==2){
					tempArr.push('<li cid="'+t[i].cid+'" ctype="'+t[i].ctype+'"><a href="javascript:void(0);">[查看]</a><span>迷你博客《'+t[i].str+'...》</span></li>');
				}
			}
			if(t.length>0){
				return tempArr.join("\n");
			}else{
				return '<li class="noMiniBlogList">目前“评论回复提示”为空</li>';
			}
		}
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=4",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				$("#JQ_BlogList .loading").show();
				$("#JQ_BlogList ul").hide();
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null&&data.result!=null){
					var t=data.result;
					$("#JQ_BlogList .loading").hide();
					$("#JQ_BlogList ul").show().html(generateHtml(t));
					$("#JQ_BlogList ul li a").click(function(){
						showMiniBlog($(this).parent().attr('cid'));
					});
				}else{
					$("#JQ_BlogList .loading").hide();
					msgWindow.show({'html':'<p><span class="prompt-error">获取评论回复提示时出错！</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">获取评论回复提示时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	
	function delAllMiniBlogList(){
		$.ajax({
			type:"POST",
			url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=5",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				//
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null&&data.result!=null){
					if(data.result.error ==null&&data.result.state != null){
						reloadMiniBlogList();
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">忽略全部回复提示时出错！</span></p>','time':2200,'callback':null,'level':21});
					}
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">忽略全部回复提示时出错！</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">忽略全部回复提示时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	
	function reloadMiniBlogList(){
		getMiniBlogList();
	}
	
	
	/*变量初始化*/
	var autoReplySwitch=false;
	var autoReplyAllCompeleted=false;
	var autoReplyTimer=null;
	
	function getMiniBlog(mid,callback){
		function getContent(mid,callback){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=6&miniblogid="+mid,
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					$("#JQ_MiniBlog .loading").show().siblings().remove();
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result!=null){
						$("#JQ_MiniBlog").append('<h1 mid="'+mid+'"><a href="'+data.result.url+'" target="_blank">'+data.result.contentStr+'</a></h1>');
						//console.log(callback);
						if(callback){
							//console.log('1有回调');
							getCommentList(mid,callback);
						}else{
							//console.log('1无回调');
							getCommentList(mid);
						}
					}else{
						$("#JQ_MiniBlog .loading").hide().siblings().remove();
						msgWindow.show({'html':'<p><span class="prompt-error">获取迷你博客内容时出错！</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					$("#JQ_MiniBlog .loading").hide().siblings().remove();
					msgWindow.show({'html':'<p><span class="prompt-error">获取迷你博客内容时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}
		function getCommentList(mid,callback){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=7&miniblogid="+mid,
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					$("#JQ_MiniBlog .loading").show();
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result!=null){
						var repliedLevelArr=new Array();	//我已回复过了的楼层对象
						for(var i=0;i<data.result.length;i++){	//本次循环用于建立已经被我回复过了的楼层数组，以备后面使用
							var temp=data.result[i];
							if(temp.deleted==0){
								var toUserId=temp.user.id;
								if(toUserId==81051 && temp.toLevel!=0){	//如果是来自于自己的消息，且是自己回复他人的评论，则加入被我回复过了的楼层数组
									var t=temp.toLevel;
									repliedLevelArr[t]=true;
								}
							}
						}
						//console.log(repliedLevelArr);
						
						var htmlArr=new Array();
						for(var i=0;i<data.result.length;i++){	//用于生成最终的html结果数组
							var temp=data.result[i];
							if(temp.deleted==0){
								var toUserId=temp.user.id;
								var commentId=temp.id;
								var toUserName=temp.user.name;
								var toLevel=temp.level;
								var addTime=temp.addTime;
								var content=temp.content;
								if(toUserId!=81051){	//如果不是来自于自己的消息
									if(repliedLevelArr[toLevel]!=true){	//如果当前楼层还没有被我回复过，则
										htmlArr.push('<li title="ID:'+commentId+'" touserid="'+toUserId+'"  commentid="'+commentId+'" tousername="'+toUserName+'" tolevel="'+toLevel+'"><label><input type="radio" name="startPoint" value="'+commentId+'" disabled="disabled" /><span class="f">'+toLevel+'楼</span></label><span class="n">'+toUserName+'</span><span class="t">'+addTime+'</span><div class="cnt">'+content+'</div></li>');
									}else{
										htmlArr.push('<li title="ID:'+commentId+'" touserid="'+toUserId+'"  commentid="'+commentId+'" tousername="'+toUserName+'" tolevel="'+toLevel+'" replied="yes" class="replied"><label><input type="radio" name="startPoint" value="'+commentId+'" disabled="disabled" /><span class="f">'+toLevel+'楼</span></label><span class="n">'+toUserName+' (已回复)</span><span class="t">'+addTime+'</span><div class="cnt">'+content+'</div></li>');
									}
								}
							}
						}
						
						temp='<div class="autoReplySet"><div class="t">自动回复<span class="status">[未启动]</span></div><div class="rc"><textarea></textarea></div><div class="c"><button>开始自动回复</button></div><div class="clr"></div></div><div class="clr l"></div>'+'<ul>'+htmlArr.join("\n")+'</ul>'
						$("#JQ_MiniBlog").append(temp);
						$("#JQ_MiniBlog .loading").hide();
						
						$("#JQ_MiniBlog button").click(function(e){
							controlAutoReply();
						});
						
						if(callback){
							//console.log('2有回调');
							callback();
						}else{
							//console.log('2无回调');
						}
					}else{
						$("#JQ_MiniBlog .loading").hide().siblings().remove();
						msgWindow.show({'html':'<p><span class="prompt-error">获取迷你博客评论时出错！PHP错误</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					$("#JQ_MiniBlog .loading").hide().siblings().remove();
					msgWindow.show({'html':'<p><span class="prompt-error">获取迷你博客评论时出错！JQ ERROR</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}
		if(callback){
			//console.log('000有');
			getContent(mid,callback);
		}else{
			//console.log('000无');
			getContent(mid);
		}
	}
	
	function sendReply(commentid,callback){
		function replyContent(){
			var t = Array("呵呵，见到你非常开心！哈哈！",
							"终于又见到了你....",
							"今天你貌似来过了，哈哈，又来了？",
							"天天都见到你，真是勤快啊！",
							"你开心吗？祝你开心哦！",
							"见到你实在是太高兴啦！呵呵~~",
							"废话是人际关系的第一句，哈哈！！",
							"给自己加油，虽然很累，坚持就是胜利！！！[em36]不是吗？",
							"给好友加油，谢谢合作，共同进步！~",
							"祝你开心每一天哦！",
							"昨天你开心吗？",
							"嘿嘿，心情怎么样啊？",
							"今天过得不错吧？呵呵，我想...是这样的。是不？",
							"哈哈~又见到你了！",
							"祝你天天开心快乐！！",
							"好天气，好心情～",
							"天天好心情哦~！",
							"你好呀！今天高兴吗？",
							"[em13][em2]我的好友啊，你过得还好吗？",
							"好友呀[em1]一定一定要开心每一天哟！",
							"一声真挚的问候，祝你开开心心！",
							"你还好吗？我的Y友？",
							"我异常十分非常高兴地见到了你！！",
							"你天天都在吗？玩的高兴吗？",
							"你幸福吗？总之，要祝你幸福！",
							"[em9]合作愉快！祝你幸福！",
							"见到你来我十分开心！",
							"若想得到快乐，就别让自己过得无精打采~嘿嘿！",
							"祝你开心哦，一定要开心呢！",
							"我想问问：你今天高兴吗？",
							"我的Y友，祝你幸福快乐，前程锦绣。",
							"呵呵，愿所有美妙的事情都纠缠着你，愿所有的财运都笼罩着你，愿所有的福星都呵护着你，愿你：天天开心！时时舒心!刻刻安心!",
							"见到你真是一件令人兴奋的事~~哈哈！",
							"祝愿你永远开心哦！",
							"人生　因朋友而美好",
							"亲爱的朋友，你好！[em33]",
							"风清云淡的日子，我们好好过，祝你开心！",
							"兴奋地见到你~我的Y友",
							"朋友你好呀[em6]",
							"永远的朋友，永远都开心",
							"天天见好友，每一天都高兴",
							"送你这个[em10]，开心哦！呵呵",
							"[em12]见好友，开心中...",
							"[em6]抱抱，交流感情~",
							"摸一下你[em8]，呵呵！记取串串快乐回忆，让每一刹那都成为永恒！",
							"[em11]送你个虚拟礼物，这才是真正的虚拟~~[em7]",
							"又见到了你，非常开心！非常高兴！",
							"呵呵，你来我往互动加密~~",
							"哈哈，不错，支持朋友！",
							"要得到别人赞叹.就得先赞叹别人。",
							"Y友呀~时间过的好快啊！~",
							"祝你天天开心，快乐，幸福，健康！",
							"盼你伸出双手，接受我盈盈的祝福。",
							"在你未来的日子里，让幸福之花开放的灿烂芬芳。寄一份祝福给你，在这美好的日子里，愿你拥有真心的快乐！",
							"告诉你，如果你看到面前的阴影，别怕，那是因为你的背后有阳光！",
							"祝福朋友开心，快乐每一刻。",
							"祝福你永远幸福，永远开心，嘿嘿！");
			var n = Math.floor(Math.random()*t.length);
			
			return t[n];
		}
		
		$("#JQ_MiniBlog .autoReplySet textarea").val(replyContent());
		var content=$("#JQ_MiniBlog .autoReplySet textarea").val();
		var toUserId=$('#JQ_MiniBlog ul li[commentid='+commentid+']').attr('touserid');
		var commentId=$('#JQ_MiniBlog ul li[commentid='+commentid+']').attr('commentid');
		var toUserName=$('#JQ_MiniBlog ul li[commentid='+commentid+']').attr('tousername');
		var toLevel=$('#JQ_MiniBlog ul li[commentid='+commentid+']').attr('tolevel');
		var mid=$("#JQ_MiniBlog h1").attr("mid");
		
		if(content!=""){
			$.ajax({
				type:"POST",
				url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=8&miniblogid="+mid,
				data:'content='+content+'&toUserId='+toUserId+'&commentId='+commentId+'&toUserName='+toUserName+'&toLevel='+toLevel,
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result!=null){
						if(data.result.error==null&&data.result.state != null){
							var html;
							if(!data.result.score){
								html='<span class="prq">无人气</span>';
							}else{
								html='<span class="prq">+'+data.result.score+' 人气</span>';
							}
							$(html).insertBefore('#JQ_MiniBlog ul li[commentid='+commentid+'] div.cnt');
							$('#JQ_MiniBlog ul li[commentid='+commentid+']').addClass('ok').attr("replied","yes");
							$('#JQ_MiniBlog ul li[commentid='+commentid+'] input[type="radio"]').attr("disabled","disabled");
							if(callback){
								callback();
							}
						}else{
							msgWindow.show({'html':'<p><span class="prompt-error">回复迷你博客评论未成功！</span></p>','time':2200,'callback':null,'level':21});
							if(callback){
								controlAutoReply();
							}
						}
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">回复迷你博客评论时出错！</span></p>','time':2200,'callback':null,'level':21});
						if(callback){
							controlAutoReply();
						}
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">回复迷你博客评论时出错！</span></p>','time':2200,'callback':null,'level':21});
					if(callback){
						controlAutoReply();
					}
				},
				complete: function(){
					//
				}
			});
		}else{
			msgWindow.show({'html':'<p><span class="prompt-error">回复内容不能为空！</span></p>','time':2200,'callback':null,'level':21});
		}
	}
	
	function changeAutoReplyStateInfo(t){
		if(t=='start'){
			autoReplySwitch=true;
			$('#JQ_MiniBlog .status').text('[进行中]');
			$('#JQ_MiniBlog button').text('暂停自动回复');
		}else if(t=='stop'){
			autoReplySwitch=false;
			$('#JQ_MiniBlog .status').text('[已停止]');
			$('#JQ_MiniBlog button').text('开始自动回复');
		}
	}
	function autoReply(flag){
		var target='#JQ_MiniBlog ul li[replied!="yes"]';
		
		if(autoReplySwitch==true){	//如果开关为开启状态，则开始自动回复
			var commentid;
			if($(target).length>0){	//如果还未回复完，则
				//console.log('回复啦！');
				commentid=$(target).eq(0).attr("commentid");
				sendReply(commentid,function(){
						autoReplyTimer=setTimeout(function(){autoReply(1)},Math.floor(Math.random()*100));
					});
			}else{	//如果已经回复完毕，则停止自动回复
				//console.log('【回复完毕，自动停止】');
				getJF();
				loadNowRQ();
				clearTimeout(autoReplyTimer);
				autoReplyAllCompeleted=true;
				if(!flag){	//如果是第一次执行就自动停止，则说明当前博客评论中没有还需要回复的评论
					msgWindow.show({'html':'<p><span class="prompt-complete">自动回复已停止，没有发现需要回复的评论。</span></p>','time':2200,'callback':null,'level':21});
				}
				changeAutoReplyStateInfo('stop');
			}
		}else{	//若发现是关闭状态，则
			//停止执行自动回复
			getJF();
			loadNowRQ();
			//console.log('【已被停止】');
		}
	}
	
	function controlAutoReply(){
		if(autoReplySwitch==true){	//暂停自动回复
			changeAutoReplyStateInfo('stop');
		}else{	//启动自动回复
			if(autoReplyAllCompeleted==false){
				changeAutoReplyStateInfo('start');
				autoReply();
			}else{
				msgWindow.show({'html':'<p><span class="prompt-error">当前迷你博客的评论已全部回复完毕，无需再次回复！</span></p>','time':2200,'callback':null,'level':21});
			}
		}
	}
	
	function showMiniBlog(id){
		autoReplySwitch=false;
		autoReplyAllCompeleted=false;
		autoReplyTimer=null;
		getMiniBlog(id,function(){
			//console.log('准备移除'+id);
			$.ajax({
				type:"POST",
				url:"works.php?ac=dowork&wid=11&wac=opurl&urlid=9",
				data:"miniblogid="+id,
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result!=null){
						if(data.result.error==null){
							$('#JQ_BlogList ul li[cid='+id+']').remove();
							if($('#JQ_BlogList ul li').length==0){
								reloadMiniBlogList();
							}
							//console.log('移除成功'+id);
						}
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">将评论回复中的一项设为已读时出错！</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">将评论回复中的一项设为已读时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		});
	}
	
	
	function init(){	//初始化
		//为各按钮绑定事件
		$("#notLoggedIn").hide().next().nextAll().show();
		$("#JQ_SendMiniBlogPanel button.sendBtn").click(function(e){
			sendMiniBlog();
		});
		$("#JQ_BlogList a").first().click(function(e){
			delAllMiniBlogList();
			return false;
		}).next().click(function(e){
			reloadMiniBlogList();
			return false;
		});
		
		//执行函数
		timer=setInterval(function(){if($("#workContent").attr('currentworkid')==11){creatMiniBlogContent();}else{/*console.log('发现事务内容已被关闭，停止工作！');*/clearInterval(timer);}},1000);
		loadNowRQ();
		loadJF();
		getMiniBlogList();
		//getMiniBlog(38019333);
		//setTimeout(function(){sendReply(750370521)},10000);
	}
	
	
	
	//入口
	checkStatus();
})();
EOT;
}

if($_GET['ac']=='dowork'){	//逻辑处理代码写这里
	
	$return=array();
	
	$errorInfo['no']=null;
	$errorInfo['text']=null;
	//no='null':无错误（默认），no=0:未知错误，no=100:打开URL失败，no=200:读取配置文件失败，no=300:退出失败，no=400:登陆失败，no=500:传入的参数不符合要求
	
	$urlList[0]=array('url'=>'http://www.yy365.com/miniblog!saveMiniBlog.do?t='.time(),'method'=>'POST','postValueArr'=>array('_isAjaxRequest'=>'true','openType'=>3,'push'=>0));	//发送一条迷你博客，返回值无需做处理，直接返回
	$urlList[1]=array('url'=>'http://www.yy365.com/login!loginintegral.do','method'=>'POST','postValueArr'=>array('_isAjaxRequest'=>'true'));	//获得当前的积分以及打卡情况（注意：此URL也用于打卡领积分），直接返回
	$urlList[2]=array('url'=>'http://www.yy365.com/login!loginintegral.do','method'=>'POST','postValueArr'=>array('_isAjaxRequest'=>'true'));	//打卡领积分按钮，直接返回
	$urlList[3]=array('url'=>'http://www.yy365.com/popularity!todaypop.do','method'=>'GET');	//获得当前人气
	$urlList[4]=array('url'=>'http://www.yy365.com/info!commentInfo.do','method'=>'GET');	//加载评论回复
	//$urlList[5]=array('url'=>'http://127.0.0.1/myHomePage2012/test.php?sign=comment','method'=>'POST');	//忽略评论回复中的每一项，直接返回
	$urlList[5]=array('url'=>'http://www.yy365.com/info!removeAllComment.do?sign=comment','method'=>'POST');	//忽略评论回复中的每一项，直接返回
	$urlList[6]=array('url'=>'http://www.yy365.com/miniblog!showMiniBlog.do?id={%miniblogid%}&userId=81051','method'=>'GET');	//加载指定的迷你博客的内容
	$urlList[7]=array('url'=>'http://www.yy365.com/miniblog!getComment.do?id={%miniblogid%}&userId=81051&pageIndex=0&pageSize=100','method'=>'GET');	//加载指定的迷你博客的评论的列表
	$urlList[8]=array('url'=>'http://www.yy365.com/miniblog!saveComment.do?mainId={%miniblogid%}&userId=81051&'.time(),'method'=>'POST','postValueArr'=>array('_isAjaxRequest'=>'true'));	//对指定的迷你博客的一条评论进行回复，直接返回
	$urlList[9]=array('url'=>'http://www.yy365.com/info!removeComment.do','method'=>'POST','postValueArr'=>array('_isAjaxRequest'=>'true','type'=>7,'sign'=>'comment'));	//将评论回复中的一项设为已读，直接返回
	
	$SE=new browser(11);
	if(!$SE->getErrorInfo()){	//如果未发现错误信息
		
		if($_GET['wac']=='opurl'&&$_GET['urlid']>=0&&isset($urlList[$_GET['urlid']])){	//如果是进行URL操作，则
			$tempArr=$urlList[$_GET['urlid']];
			if($tempArr['method']=='GET'){
				if($_GET['urlid']==7){
					if(!empty($_GET['miniblogid'])){
						$tempArr['url']=str_replace("{%miniblogid%}", $_GET['miniblogid'],$tempArr['url']);
						$result=$SE->openUrl($tempArr['url'],"GET");
					}else{
						$errorInfo['no']=500;
					}
				}elseif($_GET['urlid']==6){
					if(!empty($_GET['miniblogid'])){
						$tempArr['url']=str_replace("{%miniblogid%}", $_GET['miniblogid'],$tempArr['url']);
						$result=$SE->openUrl($tempArr['url'],"GET");
					}else{
						$errorInfo['no']=500;
					}
				}else{
					$result=$SE->openUrl($tempArr['url'],"GET");
				}
			}elseif($tempArr['method']=='POST'){
				
				if($_GET['urlid']==0){
					$tempArr['postValueArr']['content']=$_POST['content'];
				}elseif($_GET['urlid']==8){
					if(!empty($_GET['miniblogid'])){
						$tempArr['url']=str_replace("{%miniblogid%}", $_GET['miniblogid'],$tempArr['url']);
						$tempArr['postValueArr']['content']=$_POST['content'];
						$tempArr['postValueArr']['toUserId']=$_POST['toUserId'];
						$tempArr['postValueArr']['commentId']=$_POST['commentId'];
						$tempArr['postValueArr']['toUserName']=$_POST['toUserName'];
						$tempArr['postValueArr']['toLevel']=$_POST['toLevel'];
					}else{
						$errorInfo['no']=500;
					}
				}elseif($_GET['urlid']==9){
					if(!empty($_POST['miniblogid'])){
						$tempArr['postValueArr']['id']=$_POST['miniblogid'];
					}else{
						$errorInfo['no']=500;
					}
				}
				
				if($errorInfo['no']==null){
					if(!empty($tempArr['postValueArr'])){
						$result=$SE->openUrl($tempArr['url'],"POST",$tempArr['postValueArr']);
					}else{
						$result=$SE->openUrl($tempArr['url'],"POST");
					}
				}
			}
			if($result['info']['http_code']==200){
				if($_GET['urlid']==0){
					$return['result']=json_decode($result['body']);
				}elseif($_GET['urlid']==1){
					$return['result']=json_decode($result['body']);
				}elseif($_GET['urlid']==2){
					$return['result']=json_decode($result['body']);
					if(/*!isset($return['result']->error)&&*/$return['result']->nowintegral>0){//完成了事务
						$item->complete($item->generate_complete_key());	//自动完成当前事务
					}
				}elseif($_GET['urlid']==3){
					$tmp1=str_get_html($result['body']);
					$tmp2=$tmp1->find("div.top_l strong",0);
					$return['result']=mb_substr($tmp2->innertext,6,10,"utf-8");
				}elseif($_GET['urlid']==4){
					$tmp1=str_get_html($result['body']);
					$tmp2=$tmp1->find("div.hylist_nr",0);
					$returnArr=array();
					foreach($tmp2->find('div') as $item){
						$text=$item->innertext;
						if(preg_match ( "/^\迷你博客《(.+)》有新(评论|回复)，.+\('(.+)','(.+)','.+','(.+)'\).+$/", $text, $matchArr )){
							array_push ( $returnArr, array('str'=>$matchArr[1],'cid'=>$matchArr[4],'ctype'=>$matchArr[5]) );
						}
					}
					$return['result']=$returnArr;
				}elseif($_GET['urlid']==5){
					$return['result']=json_decode($result['body']);
				}elseif($_GET['urlid']==6){
					$tmp1=str_get_html($result['body']);
					$tmp2=$tmp1->find("div.mini_nr p",0);
					$return['result']=array('contentStr'=>trim($tmp2->innertext),'url'=>$tempArr['url']);
				}elseif($_GET['urlid']==7){
					//echo '【1长度：】'.strlen($result['body']);
					/*$return['result']=json_decode(preg_replace("/<a[^>]+>(.+?)<\/a>/i","$1",$dat))->page->elements;*/
					//echo $result['body'];
					$return['result']=json_decode(preg_replace("/<a[^>]+>(.+?)<\/a>/i","$1",$result['body']))->page->elements;
					if(strlen($result['body'])>0&&$return['result']==null){	//如果获取的文本体长度大于0，而经过JSON解码之后对象内容却为空，则
						//说明JSON中有不符合JSON语法的字符，须再次对其执行深度纠错
						$return['result']=json_decode(preg_replace("/\\\[^u|r|n]/",'1', $result['body']))->page->elements;
					}
				}elseif($_GET['urlid']==8){
					$return['result']=json_decode($result['body']);
					//$return['allResult']=$result;
				}elseif($_GET['urlid']==9){
					$return['result']=json_decode($result['body']);
				}
			}else{
				if($errorInfo['no']==null){
					$errorInfo['no']=100;
					$errorInfo['text']=$result['info']['http_code'];
				}
			}
		}elseif($_GET['wac']=='login'){	//如果是登陆
			if($_GET['uname']!=""&&$_GET['pwd']!=""){
				$postArr=array('rememberMe'=>'true','ytsnsEmail'=>$_GET['uname'],'password'=>$_GET['pwd'],'loginvalidateCode'=>$_GET['vcode'],'k'=>'login_key','Submit2'=>'%E7%99%BB+%E5%BD%95');
				$result=$SE->openUrl("http://www.yy365.com/login.do","POST",$postArr);
				
				if($result['info']['http_code']>=200){
					if(($result['info']['http_code']==302&&$result['info']['redirect_url']=='http://www.yy365.com/home.do')||strpos($result['body'], 'function toLogon(){')=== false){	//如果登陆成功，则
						if($SE->saveUnamePwd($_GET['uname'], $_GET['pwd'])){
							$return['result']=true;
							$return['username']=$SE->getUserName();
						}else{
							$errorInfo['no']=400;
						}
					}else{	//如果登陆失败，则
						$return['result']=FALSE;
					}
				}else{
					$errorInfo['no']=100;
					$errorInfo['text']='连接登陆页面失败';
				}
			}else{
				$errorInfo['no']=400;
				$errorInfo['text']='用户名、密码均不能为空';
			}
		}elseif($_GET['wac']=='logout'){	//如果是退出
			$result=$SE->openUrl("http://www.yy365.com/login!logout.do","GET");
			if($result['info']['http_code']==200){	//退出成功
				//$r=$SE->deleteAllCookies();
				if($r>=0){
					$return['result']=true;
					$return['describe']='已成功退出YY365帐号 '.$SE->getUserName();
					//$return['number']=$r;
				}else{
					$errorInfo['no']=300;
				}
			}else{
				$return['result']=FALSE;	//退出失败
			}
		}elseif($_GET['wac']=='retrieveRandomImg'){	//如果是获取验证码图片
			$result=$SE->openUrl("http://www.yy365.com/login!retrieveImg.do","GET");
			if($result['info']['http_code']==200){	//连接成功
				if(!empty($result['body'])){
					$tempArr=json_decode($result['body'],true);
					$return['result']=true;
					$return['imgSrc']='http://www.yy365.com/'.$tempArr['imgSrc'];
				}else{
					$return['result']=FALSE;
				}
			}else{	//连接失败
				$errorInfo['no']=100;
				$errorInfo['text']=$result['info']['http_code'];
			}
		}elseif($_GET['wac']=='getAccountInfo'){
			$cookiesArr=$SE->getAllCookies();
			$userName=$SE->getUserName();
			if($cookiesArr!=FALSE){
				$return['result']=array("username"=>$userName,"cookiesList"=>$cookiesArr);
			}else{
				$return['result']=array("username"=>$userName,"cookiesList"=>null);
			}
		}elseif($_GET['wac']=='checkStatus'){	//检查登陆状态
			$result=$SE->openUrl("http://www.yy365.com/home!loadLeftMenu.do","GET");
			if($result['info']['http_code']==200){	//连接成功
				if(strpos($result['body'], '<!DOCTYPE')=== false){
					$return['result']=true;	//是登录状态
				}else{
					$return['result']=FALSE;	//已不是登陆状态
				}
			}else{	//连接失败
				$errorInfo['no']=100;
				$errorInfo['text']=$result['info']['http_code'];
			}
		}else{
			$errorInfo['no']=0;
		}
		
	}else{	//如果发现错误信息，则
		$errorInfo['no']=200;
		$errorInfo['text']=$SE->getErrorInfo();
	}
	
	
	$return['errorInfo']=$errorInfo;
	//print_r($result);
	//统一输出json返回数据
	echo json_encode($return);
}
?>