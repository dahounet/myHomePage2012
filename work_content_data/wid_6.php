<?php
include_once 'inc/simple_browser.class.php';
include_once 'inc/simple_html_dom.php';
if($_GET['ac']=='wkcontent'){
//这里放HTML代码
$arr['html']=<<<"EOT"
				<div class="pceggs">
					<div id="notLoggedIn">尚未登陆</div>		
					<div id="accountInfo">尚未登录PC蛋蛋帐号，<a href="#" class="login JQ_login">[立即登录]</a></div>
					<div class="answerPanel">
						<div class="wrapper set" id="JQ_autoAnswerPanel">
							<label><input type="checkbox" checked="checked" /><span>启用自动答题</span></label>
							<div class="info">自动答题提示信息</div>
							</div>
						<div class="wrapper" id="JQ_subjectPanel">
					</div>
				</div>
EOT;

//这里放JS代码
$arr['js']=<<<'EOT'
(function(){
	var firstDo=true;
	var timer=false;
	var autoAnswer=true;
	function getAccountInfo(callback,closemsgid){
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=6&wac=getAccountInfo",
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
					msgWindow.show({'html':'<p><span class="prompt-error">获取PC蛋蛋账户信息出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">获取PC蛋蛋账户信息出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
							
	function setAccountInfo(accountInfo,closemsgid){
		$("#accountInfo").html('登陆帐户：<span><a href="#" class="JQ_username" title="查看PC蛋蛋账户信息">'+accountInfo.username+'</a></span><a href="#" class="logout JQ_logout">[退出]</a>');
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
			dialogWindow.show('<b>查看PC蛋蛋账户信息</b>',foundHtml(accountInfo));
			return false;
		});
		$("#accountInfo a.JQ_logout").click(function(e){
			logout();
			return false;
		});
	}
	
	function loadverifycode(t){
		if($("#PCeggsloginForm .veriflyCode-img img").attr('src')==""){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=6&wac=retrieveRandomImg",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null){
						$("#PCeggsloginForm .veriflyCode-img").css('display','block');
						$("#PCeggsloginForm .veriflyCode-img img").attr('src',data.imgSrc);
						$("#PCeggsloginForm .veriflyCode-img img").click(function(e){
								loadverifycode(true);
						});
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">获取PC蛋蛋验证码时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">获取PC蛋蛋验证码时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}else if(t==true){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=6&wac=retrieveRandomImg",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null){
						$("#PCeggsloginForm .veriflyCode-img img").attr('src',data.imgSrc);
						$("#PCeggsloginForm input[name='verifycode']").focus();
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">获取PC蛋蛋验证码时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">获取PC蛋蛋验证码时出错！</span></p>','time':2200,'callback':null,'level':21});
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
			url:"works.php?ac=dowork&wid=6&wac=checkStatus",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				this.closemsgid=msgWindow.show({'html':'<p><span class="prompt-loading">检查PC蛋蛋帐号登陆状态中...</span></p>','time':8000,'callback':null,'level':10});
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null){
					if(data.result==true){
						getAccountInfo(setAccountInfo,this.closemsgid);
						check();
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">PC蛋蛋帐号尚未登陆，请登陆！</span></p>','time':1000,'callback':login,'level':21});
						$("#accountInfo a.JQ_login").click(function(e){
							login();
							return false;
						});
						$("#notLoggedIn").show().next().nextAll().hide();
					}
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">检查PC蛋蛋帐号登陆状态出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">检查PC蛋蛋帐号登陆状态出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		})
	}
	
	function logout(){
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=6&wac=logout",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				msgWindow.show({'html':'<p><span class="prompt-loading">正在退出PC蛋蛋帐号...</span></p>','time':8000,'callback':null,'level':10});
			},
			success:function(data,textStatus){
				if(data.result===true){
					msgWindow.show({'html':'<p><span class="prompt-complete">退出PC蛋蛋帐号成功！'+data.describe+'</span></p>','time':2200,'callback':null,'level':21});
					$("#notLoggedIn").show().next().nextAll().hide();
					$("#accountInfo").html('尚未登录PC蛋蛋帐号，<a href="#" class="login JQ_login">[立即登录]</a>');
					clearTimeout(timer);
					$("#accountInfo a.JQ_login").click(function(e){
						login();
						return false;
					});
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">退出PC蛋蛋帐号时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">退出PC蛋蛋帐号时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	function login(){
		dialogWindow.show("PC蛋蛋账户登录",'<div class="login"><form method="post" id="PCeggsloginForm"><ul class="login-form"><li><div class="login-form-l">用户名:</div><div class="login-form-r"><input type="text" maxlength="25" name="username" /></div></li><li><div class="login-form-l">密码:</div><div class="login-form-r"><input type="password" maxlength="15" name="password" /></div></li><li><div class="login-form-l">验证码:</div><div class="login-form-r verifyCode"><input type="text" maxlength="6" name="verifycode" /><div class="veriflyCode-img"><img src="" title="点击更换验证码" alt="点击更换验证码" /></div></div></li><li class="login-form-btn"><input type="submit" value="登 陆" /></li></ul></form></div>');
		getAccountInfo(callback);
		function callback(r){
			$("#PCeggsloginForm input[name='username']").val(r.username);
		}
		
		$("#PCeggsloginForm").submit(function(e){
			username=$("#PCeggsloginForm input[name='username']").val();
			password=$("#PCeggsloginForm input[name='password']").val();
			vcode=$("#PCeggsloginForm input[name='verifycode']").val();
			if(username!=""&&password!=""){
				$.ajax({
					type:"GET",
					url:"works.php?ac=dowork&wid=6&wac=login&uname="+username+"&pwd="+password+"&vcode="+vcode,
					dataType:"json",
					timeout:15000,
					beforeSend: function(){
						msgWindow.show({'html':'<p><span class="prompt-loading">PC蛋蛋帐号登陆登录中...</span></p>','time':8000,'callback':null,'level':10});
					},
					success:function(data,textStatus){
						if(data.errorInfo.no==null){
							if(data.result===true){
								msgWindow.show({'html':'<p><span class="prompt-complete">登录PC蛋蛋帐号成功！帐号: '+data.username+'</span></p>','time':2200,'callback':null,'level':21});
								checkStatus();
								init();
								dialogWindow.colse();
							}else{
								msgWindow.show({'html':'<p><span class="prompt-error">登录PC蛋蛋失败，请检查 帐号、密码、验证码 是否均输入正确！</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
							}
						}else{
							msgWindow.show({'html':'<p><span class="prompt-error">登录PC蛋蛋时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
						}
					},
					error:function(){
						msgWindow.show({'html':'<p><span class="prompt-error"登录PC蛋蛋时出错！</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
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
		
		$("#PCeggsloginForm input[name='verifycode']").focus(function(e){
			loadverifycode();
		});
	}
	
	function loadASubject(adid,frombtn){
		if(!frombtn){
			var frombtn=false;
		}else{
			var frombtn=true;
		}
		if(!adid){
			var url='works.php?ac=dowork&wid=6&wac=getq';
		}else{
			var url='works.php?ac=dowork&wid=6&wac=getq&adid='+adid;
		}
		
		function foundHtml(result){
			var html="";
			
			var formdata=result.formdata;
			var temp='<form name="postdata" style="display:none;">';
			for ( var p in formdata ){
				if(p!='btnt'){
					temp+='<input name="'+p+'" type="text" value="'+formdata[p]+'" />';
				}else{
					temp+='<input name="'+p+'" type="text" value="提 交" />';
				}
			}
			temp+='</form>';
			html+=temp;
			
			temp='<h1>问题： '+result.question.text;
			if(result.question.multiple==true){
				temp+='<span class="multiple">多选</span>';
			}
			temp+='<span style="display:none;" id="JQ_subjectOriginalText">'+result.question.originalText+'</span><span class="quesInfo">本组第 '+result.question.num+' 题，共 '+result.question.sum+' 题，共奖励 '+result.question.award+' 金蛋。</span></h1>';
			html+=temp;
			
			temp='<div class="choose"><ul>';
			for(var i=0;i<result.option.length;i++){
				temp+='<li><label><input type="checkbox" value="'+result.option[i].optid+'" /><img src="'+result.option[i].opturl+'" alt="" /></label></li>';
			}
			temp+='</ul><div class="c"><button>提&nbsp;&nbsp;交</button><a href="'+result.lookforurl+'" target="_blank">[寻找答案]</a></div></div>';
			html+=temp;
			return html;
		}
		$.ajax({
			type:"GET",
			url:url,
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				$("#JQ_subjectPanel .ts button").attr('disabled','true').addClass('processing').html('<span>正在获取答题题目...</span>');
			},
			success:function(data,textStatus){
				if(data.errorInfo.no==null&&data.result!=false){
					if(data.result=='finished'){
						$("#JQ_subjectPanel").html('<div class="finished">今日问题已全部回答完成！</div>');
					}else{
						$("#JQ_subjectPanel").attr('data-adid',data.result.adid);
						$("#JQ_subjectPanel").html(foundHtml(data.result));
						$("#JQ_subjectPanel .c button").click(function(e){
							answer();
						});
						if(autoAnswer==true){
							autoAnswerControl('get',function(){answer();});
						}
					}
				}else{
					$("#JQ_subjectPanel .ts button").removeAttr('disabled').removeClass('processing').html('重新获取答题题目');
					msgWindow.show({'html':'<p><span class="prompt-error">获取答题题目时出错！</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				$("#JQ_subjectPanel .ts button").removeAttr('disabled').removeClass('processing').html('重新获取答题题目');
				msgWindow.show({'html':'<p><span class="prompt-error">获取答题题目时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	
	function answer(){
		var count=0;
		for(var i=0;i<4;i++){
			var f=$("#JQ_subjectPanel .choose ul input[type=checkbox]").eq(i);
			if(f.attr("checked")){
				count++;
				$("#JQ_subjectPanel form[name=postdata] input[name=input_ANSWER"+(i+1)+"]").val(f.val());
			}else{
				$("#JQ_subjectPanel form[name=postdata] input[name=input_ANSWER"+(i+1)+"]").val('');
			}
		}
		if(count==0){
			msgWindow.show({'html':'<p><span class="prompt-error">请至少勾选一项答案！</span></p>','time':1400,'callback':null,'level':10});
			return false;
		}
		function createQueryStr(){
			var temp=new Array();
			var target=$("#JQ_subjectPanel form[name=postdata] input");
			var num=(target.toArray()).length;
			for(var i=0;i<num;i++){
				temp.push(target.eq(i).attr('name')+'='+encodeURIComponent(target.eq(i).val()));
			}
			temp.push('adid='+$("#JQ_subjectPanel").attr('data-adid'));
			return temp.join('&');
		}
		function submit(){
			$.ajax({
				type:"POST",
				url:'works.php?ac=dowork&wid=6&wac=answer',
				data:createQueryStr(),
				dataType:"json",
				timeout:15000,
				
				beforeSend: function(){
					$("#JQ_subjectPanel .choose div.c button").attr('disabled','true').addClass('processing').html('<span>正在提交...</span>');
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result!=false){
						if(data.nextstep==0){
							$("#JQ_subjectPanel").html('<div class="finished">今日问题已全部回答完成！</div>');
						}else if(data.nextstep==1){
							$("#JQ_subjectPanel").html('<div class="ts"><div class="t">本题回答正确！</div><button>继续回答本组下一题</button></div>');
							$("#JQ_subjectPanel .ts button").click(function(e){
								loadASubject(data.adid);
							});
							if(autoAnswer==true){
								loadASubject(data.adid);
							}
						}else if(data.nextstep==2){
							$("#JQ_subjectPanel").html('<div class="ts"><div class="t">本题回答正确！本组题目已回答完毕，已获得金蛋奖励。</div><button>继续回答下一组题目</button></div>');
							$("#JQ_subjectPanel .ts button").click(function(e){
								loadASubject();
							});
							if(autoAnswer==true){
								loadASubject();
							}
						}else if(data.nextstep==3){
							$("#JQ_subjectPanel").html('<div class="ts"><div class="t">本题回答错误！</div><button>更换题目，重新回答</button></div>');
							$("#JQ_subjectPanel .ts button").click(function(e){
								loadASubject(data.adid);
							});
							if(autoAnswer==true){
								autoAnswerControl('correct',function(){loadASubject(data.adid);});
							}
						}
					}else{
						$("#JQ_subjectPanel .choose div.c button").removeAttr('disabled').removeClass('processing').html('重新提交');
						msgWindow.show({'html':'<p><span class="prompt-error">提交答案时出错！</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					$("#JQ_subjectPanel .choose div.c button").removeAttr('disabled').removeClass('processing').html('重新提交');
					msgWindow.show({'html':'<p><span class="prompt-error">提交答案时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}
		if(autoAnswer==true){
			autoAnswerControl('update',function(){submit();});
		}else{
			submit();
		}
		
	}
	
	function autoAnswerControl(f,callback){
		if(f=='correct'){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=6&wac=autoanswer&correct=true",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					$("#JQ_autoAnswerPanel .info").html('正在准备清除题库中的错误答案...');
				},
				success:function(data,textStatus){
					if(data.errorInfo.no==null&&data.result==true){
						$("#JQ_autoAnswerPanel .info").html('错误答案已清除。(id='+data.id+')');
						callback();
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">清除错误答案时出错！</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">清除错误答案时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}else{
			function getOption(ck){
				var temp=new Array();
				for(var i=0;i<4;i++){
					var f=$("#JQ_subjectPanel .choose ul input[type=checkbox]").eq(i);
					if(ck==true){
						if(f.attr("checked")){
							temp.push(f.val());
						}
					}else{
						temp.push(f.val());
					}
				}
				return temp.join('|');
			}
			function setOption(r){
				for(var i=0;i<r.length;i++){
					$("#JQ_subjectPanel .choose ul input[value="+r[i]+"]").attr('checked','true');
				}
			}
			if(f=='get'){
				var text=$("#JQ_subjectOriginalText").text();
				var option=getOption();
				$.ajax({
					type:"GET",
					url:"works.php?ac=dowork&wid=6&wac=autoanswer&text="+text+"&option="+option,
					dataType:"json",
					timeout:15000,
					beforeSend: function(){
						$("#JQ_autoAnswerPanel .info").html('正在尝试获取本题答案...');
					},
					success:function(data,textStatus){
						if(data.errorInfo.no==null&&data.result!=false){
							if(data.action=='getOK'){
								$("#JQ_autoAnswerPanel .info").html('已从题库中获取到本题答案。(id='+data.id+') 3秒后自动提交...');
								setOption(data.result);
								timer=setTimeout(function(){if($("#workContent").attr('currentworkid')==6){if(autoAnswer==true){callback();}}},3000);
							}else if(data.action=='noAnswer'){
								$("#JQ_autoAnswerPanel .info").html('题库中本题无答案，请手动回答本题。(id='+data.id+')');
							}else if(data.action=='addnew'){
								$("#JQ_autoAnswerPanel .info").html('题库中未找到相符题目，已自动添加至题库，请手动回答本题。');
							}
						}else{
							msgWindow.show({'html':'<p><span class="prompt-error">尝试获取本题答案时出错！</span></p>','time':2200,'callback':null,'level':21});
						}
					},
					error:function(){
						msgWindow.show({'html':'<p><span class="prompt-error">尝试获取本题答案时出错！</span></p>','time':2200,'callback':null,'level':21});
					},
					complete: function(){
						//
					}
				});
			}else if(f=='update'){
				var text=$("#JQ_subjectOriginalText").text();
				var option=getOption();
				var selectedOption=getOption(true);
				$.ajax({
					type:"GET",
					url:"works.php?ac=dowork&wid=6&wac=autoanswer&text="+text+"&option="+option+"&selectedOption="+selectedOption,
					dataType:"json",
					timeout:15000,
					beforeSend: function(){
						$("#JQ_autoAnswerPanel .info").html('正在准备更新题目...');
					},
					success:function(data,textStatus){
						if(data.errorInfo.no==null&&data.result==true&&data.action=='update'){
							$("#JQ_autoAnswerPanel .info").html('指定题目已成功更新。(id='+data.id+')');
							callback();
						}else{
							msgWindow.show({'html':'<p><span class="prompt-error">更新题目时出错！</span></p>','time':2200,'callback':null,'level':21});
						}
					},
					error:function(){
						msgWindow.show({'html':'<p><span class="prompt-error">更新题目时出错！</span></p>','time':2200,'callback':null,'level':21});
					},
					complete: function(){
						//
					}
				});
			}
		}
	}
	function init(){	//初始化
		//为各按钮绑定事件
		$("#notLoggedIn").hide().next().nextAll().show();
		
		$("#JQ_autoAnswerPanel input").click(function(e){
			if($("#JQ_autoAnswerPanel input").attr('checked')){
				autoAnswer=true;
				$("#JQ_autoAnswerPanel .info").text('自动答题提示信息').show('fast');
				if($("#JQ_subjectOriginalText").html()){	//如果当前是答题界面，则开始自动答题
					autoAnswerControl('get',function(){answer();});
				}
			}else{
				autoAnswer=false;
				$("#JQ_autoAnswerPanel .info").empty().hide('fast');
			}
		});
		
		//执行函数
		loadASubject();
	}
	
	
	
	//入口
	checkStatus();
})();
EOT;
}

if($_GET['ac']=='dowork'){	//逻辑处理代码写这里
	
	function getData($html){	//获取答题所需信息数据
		$html=str_get_html($html);
		
		$formData=array();	//表单数据数组
		foreach ($html->find('form[name=form1] input[name]') as $item){
			$formData[$item->name]=trim($item->value);
		}
		
		$temp1=$html->find('table',0)->find('table',1);
		
		$qTmp=$temp1->find('tr',0);
		$questionInfo=array();	//题目文字及题目数量信息数组
		$questionInfo['num']=trim($qTmp->find('span',1)->innertext);
		$questionInfo['text']=$questionInfo['originalText']=trim($qTmp->find('span',2)->innertext);
		$questionInfo['sum']=trim($qTmp->find('span',4)->innertext);
		$questionInfo['award']=trim($qTmp->find('span',6)->innertext);
		if (strstr ( $questionInfo['text'], '（多选）' )) {
			$questionInfo['text']=str_replace("（多选）", "", $questionInfo['text']);
			$questionInfo['multiple']=true;
		}else{
			$questionInfo['multiple']=false;
		}
		
		$optionInfo=array();	//选项信息数组
		$i=0;
		foreach ($temp1->find('input[type=checkbox]') as $item){
			$optionInfo[$i++]['optid']=$item->value;
		}
		$i=0;
		$SE=new browser(6);
		foreach ($temp1->find('iframe') as $item){
			$result=$SE->openUrl('http://qm.pceggs.com/web/'.$item->src,"GET");
			
			if($result['info']['http_code']==200){
				if(!empty($result['body'])){
					$optionInfo[$i++]['opturl']='data:image/png;base64,'.$result['body'];
				}
			}
		}
		
		$lookforUrl=$html->find('table',0)->find('table',2)->find('a',0)->href;
		
		return array('adid'=>$formData['input_MID'],'formdata'=>$formData,'question'=>$questionInfo,'option'=>$optionInfo,'lookforurl'=>$lookforUrl);
	}
	
	$return=array();
	
	$errorInfo['no']=null;
	$errorInfo['text']=null;
	//no='null':无错误（默认），no=0:未知错误，no=100:打开URL失败，no=200:读取配置文件失败，no=300:退出失败，no=400:登陆失败，no=500:传入的参数不符合要求
	
	$SE=new browser(6);
	if(!$SE->getErrorInfo()){	//如果未发现错误信息
		if($_GET['wac']=='getq'){	//如果是获取题目内容
			if(!empty($_GET['adid'])){	//如果是获取本组题目内容
				$adid=$_GET['adid'];
			}else{	//如果是获取新一组的题目内容
				$adid=0;
			}
			
			$result=$SE->openUrl("http://qm.pceggs.com/Web/ADQuestion.aspx?ADID=".$adid,"GET");
			
			if($result['info']['http_code']>=200){
				if($result['info']['http_code']==200){
					if($result['info']['size_download']<200&&strstr($result['body'], 'ADFinish.aspx')){	//若成立，则说明当天所有题目已经回答完成
						$return['result']='finished';
						$item->complete($item->generate_complete_key());	//自动完成当前事务
					}else{	//否则则说明尚未回答完成，获取题目数据信息
						$return['result']=getData($result['body']);
					}
				}else{
					$return['result']=FALSE;
				}
			}else{
				$errorInfo['no']=100;
				$errorInfo['text']='连接题目页面失败，adid='.$adid;
			}
		}elseif($_GET['wac']=='answer'){	//如果是提交答案
			if(count($_POST)>0){
				$result=$SE->openUrl('http://qm.pceggs.com/Web/ADQuestion.aspx',"POST",$_POST,"http://qm.pceggs.com/");
				//$result=$SE->openUrl('http://127.0.0.1/myHomePage2012/test.php',"POST",$_POST,"http://qm.pceggs.com/");
				
				//$return['debug']=implode('\n</br>',$result);
				if($result['info']['http_code']>=200){
					if($result['info']['http_code']==302){
						if(stristr($result['info']['redirect_url'], '/web/ADQuestionR.aspx')){	//题目回答正确，继续回答本组剩余题目
							$return['result']=true;
							$return['nextstep']='1';
							$return['adid']=$_POST['adid'];
						}elseif(stristr($result['info']['redirect_url'], '/web/ADQuestionG.aspx?type=9')){	//题目回答正确，本组题目已全部回答完成，继续回答下一组的题目
							$return['result']=true;
							$return['nextstep']='2';
						}elseif(stristr($result['info']['redirect_url'], '/web/ADQuestionW.aspx?type=2')){	//题目回答错误，需要重新回答
							$return['result']=true;
							$return['nextstep']='3';
							$return['adid']=$_POST['adid'];
						}elseif(stristr($result['info']['redirect_url'], '/web/ADQuestionG.aspx?type=5')){	//题目回答正确，且今日所有题目回答完毕
							$return['result']=true;
							$return['nextstep']='0';
							$item->complete($item->generate_complete_key());	//自动完成当前事务
						}else{	//否则为异常情况
							$return['result']=FALSE;
						}
					}else{
						$return['result']=FALSE;
					}
				}else{
					$errorInfo['no']=100;
					$errorInfo['text']='连接题目页面失败，adid='.$adid;
				}
			}else{
				$errorInfo['no']=500;
			}
		}elseif($_GET['wac']=='autoanswer'){	//如果是自动答题
			function formatData($str){	//格式化选项字符串（排序）
				$tempArr = explode ( "|", $str );
				sort ( $tempArr );
				return implode ( "|", $tempArr );
			}
			if(isset($_GET['text'])&&isset($_GET['option'])){	//如果是获取题目的答案或提交答案
				$text=mysql_real_escape_string($_GET['text']);
				if(!isset($_GET['selectedOption'])){	//如果没有传入被选中的选项，则说明是在试图获取题目的答案
					$optionStr=formatData(mysql_real_escape_string($_GET['option']));
					$result = $db->query ( "SELECT * FROM `pceggs_wenda` WHERE `question` ='$text' AND `option`='$optionStr'" );
					if ($result->num_rows > 0) {	//若成功找到了相符的题目
						$tmpArr=$result->fetch_array();
						$return['id']=$tmpArr['id'];
						if($tmpArr['answer']==null){	//如果答案为空
							$return['action']='noAnswer';
							$return['result']=true;
						}else{	//如果答案不为空，则返回答案
							$return['action']='getOK';
							$return['result']=explode('|', $tmpArr['answer']);
						}
					}else{	//若没有找到，则将其添加入题目库
						$return['action']='addnew';
						$result = $db->query ( "insert into pceggs_wenda(`question`,`option`,`addtime`)values('$text','$optionStr',NOW())" );
						if ($result) {
							$return['result']=true;
						} else {
							$return['result']=false;
						}
					}
				}else{	//如果传入了被选中的选项，则说明是在更新题目的答案
					$return['action']='update';
					
					$optionStr=formatData(mysql_real_escape_string($_GET['option']));
					$selectedStr=formatData(mysql_real_escape_string($_GET['selectedOption']));
					
					$result = $db->query ( "SELECT `id` FROM `pceggs_wenda` WHERE `question` ='$text' AND `option`='$optionStr'" );
					if ($result->num_rows > 0) {
						$tmpArr=$result->fetch_array();
						$result = $db->query ( "update `pceggs_wenda` set `answer`='$selectedStr' WHERE `id` =".$tmpArr['id'] );
						if ($result) {
							$_SESSION ["id"] = $tmpArr['id'];
							$return['id']=$tmpArr['id'];
							$return['result']=true;
						} else {
							$return['result']=false;
						}
					}else{
						$return['result']=false;
					}
				}
			}elseif ($_GET['correct']){	//如果是改正错误答案
				$return['id']=$_SESSION ["id"];
				if($_SESSION ["id"] !=null){
					$result = $db->query ( "update `pceggs_wenda` set `answer`=NULL WHERE `id` = '{$_SESSION ['id']}'" );
					if ($result&&$db->affected_rows == 1) {
						$_SESSION ["id"] =null;
						$return['result']=true;
					} else {
						$return['result']=false;
					}
				}
			}else{
				$errorInfo['no']=500;
			}
		}elseif($_GET['wac']=='login'){	//如果是登陆
			/*步骤：
			 * 1.先打开登陆页面，获取表单的1个值
			 * 2.将刚才获取到的表单的1个值以及账户名、密码、验证码等一起POST到登陆页面
			 * 4.若返回值为302重定向，则表明登陆成功
			 * */			
			
			if($_GET['uname']!=""&&$_GET['pwd']!=""){
				$postArr=array('txt_UserName'=>$_GET['uname'],'txt_UserName'=>$_GET['uname'],'txt_PWD'=>$_GET['pwd'],'txt_VerifyCode'=>$_GET['vcode'],'LoginWay'=>'1','Login_Submit.x'=>'39','Login_Submit.y'=>'14','FromUrl'=>'http%3A%2F%2Fwww.pceggs.com%2Findex.aspx','SMONEY'=>'ABC');
				
				$result0=$SE->openUrl("http://www.pceggs.com/nologin.aspx","GET");
				if($result0['info']['http_code']>=200){
					$tmp1=str_get_html($result0['body']);
					$postArr['__VIEWSTATE']=$tmp1->find("#__VIEWSTATE",0)->value;
					
					$result=$SE->openUrl("http://www.pceggs.com/nologin.aspx","POST",$postArr);
					
					if($result['info']['http_code']>=200){
						if($result['info']['http_code']==200||$result['info']['redirect_url'] == 'http://www.pceggs.com/http%3A%2F%2Fwww.pceggs.com%2Findex.aspx'){	//如果登陆成功，则
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
					$errorInfo['no']=100;
					$errorInfo['text']='连接登陆页面获取参数值失败';
				}
			}else{
				$errorInfo['no']=400;
				$errorInfo['text']='用户名、密码均不能为空';
			}
		}elseif($_GET['wac']=='logout'){	//如果是退出
			$result=$SE->openUrl("http://www.pceggs.com/Logout.aspx","GET");
			if($result['info']['http_code']==302){	//退出成功
				$return['result']=true;
				$return['describe']='已成功退出PC蛋蛋帐号 '.$SE->getUserName();
			}else{
				$errorInfo['no']=300;	//退出失败
				$return['result']=FALSE;
			}
		}elseif($_GET['wac']=='retrieveRandomImg'){	//如果是获取验证码图片
			$result=$SE->openUrl("http://www.pceggs.com/VerifyCode_Login.aspx","GET");
			if($result['info']['http_code']==200){	//连接成功
				if(!empty($result['body'])){
					$return['result']=true;
					$return['imgSrc']='data:image/gif;base64,'.$result['body'];
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
			$result=$SE->openUrl("http://www.pceggs.com/myaccount/myeggs.aspx?id=1","GET");
			$result=$SE->openUrl("http://www.pceggs.com/myaccount/myeggs.aspx?id=1","GET");
			if($result['info']['http_code']>=200){	//连接成功
				if($result['info']['http_code']<300){
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