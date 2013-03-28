<?php
include_once 'inc/simple_browser.class.php';
include_once 'inc/simple_html_dom.php';
if($_GET['ac']=='wkcontent'){
//这里放HTML代码
$arr['html']=<<<"EOT"
				<div class="jsvip">
					<div id="notLoggedIn">尚未登陆</div> 
					<div id="accountInfo">尚未登录金山会员帐号，<a href="#" class="login JQ_login">[立即登录]</a></div>
					<div id="webSiteLinks"><a href="http://vip.ijinshan.com/" target="_blank">金山会员官网首页</a><a href="http://vip.ijinshan.com/activity" target="_blank">会员活动</a></div>
					
					<!--<div class="setPanel">
						<label><input type="checkbox" checked="checked" /><span>自动领取所有金米</span></label>
					</div>-->
					
					<div class="getJinMi" id="JQ_jm1">
						<h1>首页签到赚金米</h1>
						<div class="jmState">
							<div class="jmSign"><button class="sendBtn">立即签到赚金米</button></div>
						</div>
					</div>
					
					<div class="getJinMi" id="JQ_jm2">
						<h1>游戏盒子签到赚金米</h1>
						<div class="jmState">
							<div class="jmSign"><button class="sendBtn">立即签到赚金米</button></div>
						</div>
					</div>
					
					<div class="vippage">
						<iframe src="http://vip.ijinshan.com/" frameborder="0" scrolling="yes" width="100%" height="100%"></iframe>
					</div>
					
					<!--<div class="getJinMi" id="JQ_jm1">
						<h1>首页签到赚金米</h1>
						<div class="jmState" data-state="done">
							<div class="jmSign"><button class="sendBtn">立即签到赚金米</button></div>
							<!--<div class="jmScore"><p>20金米</p><p>今日已签到</p></div>-->
						<!--</div>
					</div>
					
					<div class="getJinMi" id="JQ_jm2" data-state="done">
						<h1>游戏盒子签到赚金米</h1>
						<div class="jmState">
							<div class="jmScore">
								<div class="signedScore">+20 金米</div>
								<div class="signedText"><span>今日已签到</span></div>
							</div>
						</div>
					</div>
					
					<div class="getJinMi">
						<h1>游戏盒子签到赚金米</h1>
						<div class="jmState" data-state="done">
							<div class="signing"><span>正在签到中...</span></div>
						</div>
					</div>-->
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
			url:"works.php?ac=dowork&wid=28&wac=getAccountInfo",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				//
			},
			success:function(data,textStatus){
				if(data.accountInfo){
					if(typeof closemsgid!= "undefined"){
						callback(data.accountInfo,closemsgid)
					}else{
						callback(data.accountInfo);
					}
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">获取金山会员账户信息出错！</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">获取金山会员账户信息出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
							
	function setAccountInfo(accountInfo,closemsgid){
		$("#accountInfo").html('登陆帐户：<span><a href="#" class="JQ_username" title="查看金山会员账户信息">'+accountInfo.username+'</a></span><a href="#" class="logout JQ_logout">[退出]</a>');
		if(typeof closemsgid!="undefined"){
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
			dialogWindow.show('<b>查看金山会员账户信息</b>',foundHtml(accountInfo));
			return false;
		});
		$("#accountInfo a.JQ_logout").click(function(e){
			logout();
			return false;
		});
	}
	
	function loadverifycode(t){
		
		if($("#jsVIPloginForm .veriflyCode-img img").attr('src')==""){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=28&wac=retrieveRandomImg",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.result==true){
						$("#jsVIPloginForm .veriflyCode-img").css('display','block');
						$("#jsVIPloginForm .veriflyCode-img img").attr('src',data.imgSrc);
						$("#jsVIPloginForm .veriflyCode-img img").click(function(e){
								loadverifycode(true);
						});
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">获取金山会员验证码时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">获取金山会员验证码时出错！</span></p>','time':2200,'callback':null,'level':21});
				},
				complete: function(){
					//
				}
			});
		}else if(t==true){
			$.ajax({
				type:"GET",
				url:"works.php?ac=dowork&wid=28&wac=retrieveRandomImg",
				dataType:"json",
				timeout:15000,
				beforeSend: function(){
					//
				},
				success:function(data,textStatus){
					if(data.result==true){
						$("#jsVIPloginForm .veriflyCode-img img").attr('src',data.imgSrc);
						$("#jsVIPloginForm input[name='verifycode']").focus();
					}else{
						msgWindow.show({'html':'<p><span class="prompt-error">获取金山会员验证码时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
					}
				},
				error:function(){
					msgWindow.show({'html':'<p><span class="prompt-error">获取金山会员验证码时出错！</span></p>','time':2200,'callback':null,'level':21});
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
			url:"works.php?ac=dowork&wid=28&wac=checkStatus",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				this.closemsgid=msgWindow.show({'html':'<p><span class="prompt-loading">检查金山会员帐号登陆状态中...</span></p>','time':8000,'callback':null,'level':10});
			},
			success:function(data,textStatus){
				if(data.state==true){
					getAccountInfo(setAccountInfo,this.closemsgid);
					check();
				}else if(data.state==false){
					msgWindow.show({'html':'<p><span class="prompt-error">金山会员帐号尚未登陆，请登陆！</span></p>','time':1000,'callback':login,'level':21});
					$("#accountInfo a.JQ_login").click(function(e){
						login();
						return false;
					});
					$("#notLoggedIn").show().next().nextAll().hide();
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">检查金山会员帐号登陆状态出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">检查金山会员帐号登陆状态出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		})
	}
	
	function logout(){
		$.ajax({
			type:"GET",
			url:"works.php?ac=dowork&wid=28&wac=logout",
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				msgWindow.show({'html':'<p><span class="prompt-loading">正在退出金山会员帐号...</span></p>','time':8000,'callback':null,'level':10});
			},
			success:function(data,textStatus){
				if(data.result==true){
					msgWindow.show({'html':'<p><span class="prompt-complete">退出金山会员帐号成功！'+data.describe+'</span></p>','time':2200,'callback':null,'level':21});
					$("#notLoggedIn").show().next().nextAll().hide();
					$("#accountInfo").html('尚未登录金山会员帐号，<a href="#" class="login JQ_login">[立即登录]</a>');
					clearInterval(timer);
					$("#accountInfo a.JQ_login").click(function(e){
						login();
						return false;
					});
				}else{
					msgWindow.show({'html':'<p><span class="prompt-error">退出金山会员帐号时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">退出金山会员帐号时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	function login(){
		dialogWindow.show("金山会员账户登录",'<div class="login"><form method="post" id="jsVIPloginForm"><ul class="login-form"><li><div class="login-form-l">用户名:</div><div class="login-form-r"><input type="text" maxlength="25" name="username" /></div></li><li><div class="login-form-l">密码:</div><div class="login-form-r"><input type="password" maxlength="15" name="password" /></div></li><li><div class="login-form-l">验证码:</div><div class="login-form-r verifyCode"><input type="text" maxlength="6" name="verifycode" /><div class="veriflyCode-img"><img src="" title="点击更换验证码" alt="点击更换验证码" /></div></div></li><li class="login-form-btn"><input type="submit" value="登 陆" /></li></ul></form></div>');
		function callback(r){
			$("#jsVIPloginForm input[name='username']").val(r.username);
			$("#jsVIPloginForm input[name='password']").val(r.password);
		}
		getAccountInfo(callback);
		
		$("#jsVIPloginForm").submit(function(e){
			username=$("#jsVIPloginForm input[name='username']").val();
			password=$("#jsVIPloginForm input[name='password']").val();
			vcode=$("#jsVIPloginForm input[name='verifycode']").val();
			if(username!=""&&password!=""){
				$.ajax({
					type:"GET",
					url:"works.php?ac=dowork&wid=28&wac=login&uname="+username+"&pwd="+password+"&vcode="+vcode,
					dataType:"json",
					timeout:15000,
					beforeSend: function(){
						msgWindow.show({'html':'<p><span class="prompt-loading">金山会员帐号登陆登录中...</span></p>','time':8000,'callback':null,'level':10});
					},
					success:function(data,textStatus){
						if(data.result==true){
							msgWindow.show({'html':'<p><span class="prompt-complete">登录金山会员帐号成功！帐号: '+data.username+'</span></p>','time':2200,'callback':null,'level':21});
							firstDo=false;
							checkStatus();
							init();
							dialogWindow.colse();
						}else if(data.result==false){
							msgWindow.show({'html':'<p><span class="prompt-error">登录金山会员失败，请检查 帐号、密码、验证码 是否均输入正确！</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
						}else{
							msgWindow.show({'html':'<p><span class="prompt-error">登录金山会员时出错！（程序错误代码：'+data.errorInfo.no+' ['+data.errorInfo.text+']）</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
						}
					},
					error:function(){
						msgWindow.show({'html':'<p><span class="prompt-error"登录金山会员时出错！</span></p>','time':2200,'callback':function(){loadverifycode(true);},'level':21});
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
		
		$("#jsVIPloginForm input[name='verifycode']").focus(function(e){
			loadverifycode();
		});
	}
	
	function checkSignState(id){
		var url;
		if(id==1){
			url="http://127.0.0.1/myHomePage2012/works.php?ac=dowork&wid=28&wac=opurl&urlid=0";
		}else if(id==2){
			url="http://127.0.0.1/myHomePage2012/works.php?ac=dowork&wid=28&wac=opurl&urlid=1";
		}
		$.ajax({
			type:"GET",
			url:url,
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				//
			},
			success:function(data,textStatus){
				if(data.today==1){	//今日已完成签到
					$("#JQ_jm"+id+" .jmState").html('<div class="jmScore signed"><div class="signedText"><span>今日已签到</span></div></div>').attr("data-state","done");
				}else{
					$("#JQ_jm"+id+" .jmState").html('<div class="jmSign"><button class="sendBtn">立即签到赚金米</button></div>');
					$("#JQ_jm"+id+" .jmState button").click(function(e){
						doSign(id);
					});
				}
			},
			error:function(){
				msgWindow.show({'html':'<p><span class="prompt-error">获取(id:'+id+')今日签到状态时出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				//
			}
		});
	}
	
	function doSign(id){
		var url;
		if(id==1){
			url="http://127.0.0.1/myHomePage2012/works.php?ac=dowork&wid=28&wac=opurl&urlid=2";
		}else if(id==2){
			url="http://127.0.0.1/myHomePage2012/works.php?ac=dowork&wid=28&wac=opurl&urlid=3";
		}
		$.ajax({
			type:"GET",
			url:url,
			dataType:"json",
			timeout:15000,
			beforeSend: function(){
				$("#JQ_jm"+id+" .jmState").html('<div class="signing"><span>正在签到中...</span></div>');
			},
			success:function(data,textStatus){
				if(typeof data.addscore !="undefined"){
					$("#JQ_jm"+id+" .jmState").html('<div class="jmScore"><div class="signedScore">+'+data.addscore+' 金米</div><div class="signedText"><span>今日已签到</span></div></div>').attr("data-state","done");
				}else if(data.error_code=="30001"){
					$("#JQ_jm"+id+" .jmState").html('<div class="jmScore signed"><div class="signedText"><span>今日已签到过啦</span></div></div>').attr("data-state","done");
				}else{
					$("#JQ_jm"+id+" .jmState").html('<div class="jmSign"><button class="sendBtn">立即签到赚金米</button></div>');
					$("#JQ_jm"+id+" .jmState button").click(function(e){
						doSign(id);
					});
					msgWindow.show({'html':'<p><span class="prompt-error">签到时(id:'+id+')出错！</span></p>','time':2200,'callback':null,'level':21});
				}
			},
			error:function(){
				$("#JQ_jm"+id+" .jmState").html('<div class="jmSign"><button class="sendBtn">立即签到赚金米</button></div>');
				$("#JQ_jm"+id+" .jmState button").click(function(e){
					doSign(id);
				});
				msgWindow.show({'html':'<p><span class="prompt-error">签到时(id:'+id+')出错！</span></p>','time':2200,'callback':null,'level':21});
			},
			complete: function(){
				workFinish();
			}
		});
	}
	
	function workFinish(){
		if($("#JQ_jm1 .jmState").attr("data-state")=="done" && $("#JQ_jm2 .jmState").attr("data-state")=="done"){
			completeWork(28);
		}
	}
	
	
	function init(){	//初始化
		//为各按钮绑定事件
		$("#notLoggedIn").hide().next().nextAll().show();
		
		checkSignState(1);
		checkSignState(2);
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
	
	$urlList[0]=array('url'=>'http://vip.ijinshan.com/signin/index','method'=>'POST');	//获取首页的当月签到情况，返回为JSON数组
	$urlList[1]=array('url'=>'http://vip.ijinshan.com/signin/pagesign_check','method'=>'POST','postValueArr'=>array('taskname'=>'signin_webgame'));	//获取游戏盒子的当月签到情况，返回为JSON数组
	$urlList[2]=array('url'=>'http://vip.ijinshan.com/signin/sign','method'=>'POST');	//首页的签到，直接返回
	$urlList[3]=array('url'=>'http://vip.ijinshan.com/signin/pagesign_sign','method'=>'POST','postValueArr'=>array('taskname'=>'signin_webgame'));	//游戏盒子的签到，直接返回
	
	$SE=new browser(28);
	
	function checkToday($arr){
		$n=count($arr);
		if($n>0){
			for($i=0;$i<$n;$i++){
				if($arr[$i]==date('Y-m-d')){
					return true;
				}
			}
		}
		return false;
	}
	if(!$SE->getErrorInfo()){	//如果未发现错误信息
		
		if($_GET['wac']=='opurl'&&$_GET['urlid']>=0&&isset($urlList[$_GET['urlid']])){	//如果是进行URL操作，则
			$tempArr=$urlList[$_GET['urlid']];
			if($_GET['urlid']==0){
				$tempArr['postValueArr']['year']=date('Y');
				$tempArr['postValueArr']['month']=date('m');
			}
			if($_GET['urlid']==1){
				$tempArr['postValueArr']['year']=date('Y');
				$tempArr['postValueArr']['month']=date('m');
			}
			if($tempArr['method']=='GET'){
				//
			}elseif($tempArr['method']=='POST'){
				$result=$SE->openUrl($tempArr['url'],"POST",$tempArr['postValueArr']);
			}
			if($result['info']['http_code']==200){
				if($_GET['urlid']==0||$_GET['urlid']==1){
					$return['currentMonth']=json_decode($result['body']);
					if(checkToday($return['currentMonth'])){
						$return['today']=1;
					}else{
						$return['today']=0;
					}
				}elseif($_GET['urlid']==2||$_GET['urlid']==3){
					$return=json_decode($result['body']);
				}
			}else{
				if($errorInfo['no']==null){
					$errorInfo['no']=100;
					$errorInfo['text']=$result['info']['http_code'];
				}
			}
		}elseif($_GET['wac']=='login'){	//如果是登陆
			if($_GET['uname']!=""&&$_GET['pwd']!=""){
				$result=$SE->openUrl("https://login.ijinshan.com/glt?_lt=".time(),"GET");
				if($result['info']['http_code']>=200 && count($SE->getAllCookies())>=1){
					$postArr=array('user'=>$_GET['uname'],'pwd'=>md5($_GET['pwd']),'cc'=>$_GET['vcode'],'service'=>'http://vip.ijinshan.com/account/login?from=http://vip.ijinshan.com/','rm'=>'0','cn'=>'f0f4e7fd7740f614524080819687f484');
					$result=$SE->openUrl("https://login.ijinshan.com/login","POST",$postArr);

					if($result['info']['http_code']>=200){
						$tmpJSON=json_decode($result['body']);
						$result=$SE->openUrl($tmpJSON->url,"GET");
						if($tmpJSON->code==1 && $result['info']['http_code']==302 && $result['info']['redirect_url']=='http://vip.ijinshan.com/account/login?from=http://vip.ijinshan.com/'){	//满足全部条件，则说明登录成功了
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
					$errorInfo['text']='获取登录用cookie时失败';
				}
			}else{
				$errorInfo['no']=400;
				$errorInfo['text']='用户名、密码均不能为空';
			}
		}elseif($_GET['wac']=='logout'){	//如果是退出
			$result=$SE->openUrl("http://vip.ijinshan.com/account/logout","GET");
			if($result['info']['http_code']==302){
				$result=$SE->openUrl($result['info']['redirect_url'],"GET");
				if($result['info']['http_code']==302){	//退出成功
					$return['result']=true;
					$return['describe']='已成功退出金山会员帐号 '.$SE->getUserName();
				}else{
					$errorInfo['no']=300;
					$return['result']=FALSE;	//退出失败
				}
			}else{
				$errorInfo['no']=300;
				$return['result']=FALSE;	//退出失败
			}
		}elseif($_GET['wac']=='isNeedAuthCode'){	//检查是否需要输入验证码
			$result=$SE->openUrl("https://login.ijinshan.com/?service=http%3A%2F%2Fvip.ijinshan.com%2Faccount%2Flogin%3Ffrom%3Dhttp%3A%2F%2Fvip.ijinshan.com%2F&lp=https://login.ijinshan.com/gmlogin.html&if=1","GET");
			if($result['info']['http_code']==302){
				if($result['info']['redirect_url']=='https://login.ijinshan.com/gmlogin.html?service=http%3A%2F%2Fvip.ijinshan.com%2Faccount%2Flogin%3Ffrom%3Dhttp%3A%2F%2Fvip.ijinshan.com%2F'){
					$return['needAuthCode']=0;	//不需要
				}else{
					$return['needAuthCode']=1;	//需要
				}
			}elseif($result['info']['http_code']==200){
				$return['needAuthCode']=0;	//已经登陆了，当然不需要啦
			}else{
				$errorInfo['no']=100;
				$errorInfo['text']="确定是否需要验证码时出错，http状态码：".$result['info']['http_code'];
				$return['result']=FALSE;	//退出失败
			}
		}elseif($_GET['wac']=='retrieveRandomImg'){	//如果是获取验证码图片
			$result=$SE->openUrl("https://login.ijinshan.com/imgCode?_dc=".time()."&cn=f0f4e7fd7740f614524080819687f484","GET");
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
			$userPassword=$SE->getUserPassword();
			if($cookiesArr!=FALSE){
				$return['accountInfo']=array("username"=>$userName,"password"=>$userPassword,"cookiesList"=>$cookiesArr);
			}else{
				$return['accountInfo']=array("username"=>$userName,"password"=>$userPassword,"cookiesList"=>null);
			}
		}elseif($_GET['wac']=='checkStatus'){	//检查登陆状态
			$result=$SE->openUrl("http://vip.ijinshan.com/my","GET");
			if($result['info']['http_code']==200){	//连接成功
				$tempHtml=str_get_html($result['body']);
				$tmp2=$tempHtml->find("#account",0);
				$tmp=$tmp2->getAttribute ( "data-passport" );
				if(!empty($tmp)){
					$return['state']=true;	//是登录状态
				}else{
					$return['state']=FALSE;	//已不是登陆状态
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
	
	
	$errorInfo['no']!=null?$return['errorInfo']=$errorInfo:1;
	//print_r($result);
	//统一输出json返回数据
	echo json_encode($return);
}
?>