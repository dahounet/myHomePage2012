<?php
if($_GET['ac']=='wkcontent'){
//这里放HTML代码
$arr['html']=<<<"EOT"
<strong>测试数据：【{$content['data']}】</strong>
				<div class="yy365">
					<div id="accountInfo">登陆帐户：<span>60931111</span><a href="#">退出</a></div>
					<div class="daka">
						<div class="score">今日积分<span class="no">[尚未领取]</span></div>
						<button>打卡领积分</button>
					</div>
					<div class="renqi">
						<div class="n">今日当前人气<span>200</span></div>
					</div>
					<div class="sendMsg">
						<textarea class="cntInput"></textarea>
						<button class="sendBtn">发布</button>
						<div class="set"><button>0点时自动发送1条迷你博客</button></div>
					</div>
					<div class="clr"></div>
					<div class="hudong">
						<div class="blist">
							<div class="hl">评论回复提示列表<a href="javascript:void(0);">[全部忽略]</a><a href="javascript:void(0);">刷新</a></div>
							<div class="loading">加载中...</div>
							<ul>
								<li><a href="javascript:void(0);">[查看]</a><span>{#sssssssssssssssssssssssssssssssssssssssss博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
								<li><a href="javascript:void(0);">[查看]</a><span>{#博客名称1#}</span></li>
							</ul>
						</div>
						<div class="clist">
							<div class="loading">加载中...</div>
							<h1><a href="#">{#这一条博客的名称#}afjofjsdofjsodfjosdfjsofjojsdofjofd sdofj osjf osdfjo osdfj os </a></h1>
							<div class="autoReplySet">
								<div class="t">自动回复<span class="status">{#状态#}</span></div>
								<div class="rc"><textarea></textarea></div>
								<div class="c">
									<button>开始/暂停自动回复</button>
									<span class="sp">开始点:<span>ID:66611133</span></span>
								</div>
								<div class="clr"></div>
							</div>
							<div class="clr l">cccccc</div>
							<ul>
								<li class="ok">
									<label><input type="checkbox" /><span class="f" title="ID:{#33333333#}">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">2012-05-06 12:12:33</span>
									<a href="javascript:void(0);">[单独回复]</a>
									<span class="prq">+0</span>
									<div class="cnt">{#这里是评论的内容#}</div>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
									<span class="prq">+5</span>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
									<span class="prq">+11</span>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">2012-05-06 12:12:33</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">2012-05-06 12:12:33</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">2012-05-06 12:12:33</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
								</li>
								<li title="ID:{#33333333#}">
									<label title="点击设为开始点"><input type="checkbox" /><span class="f">1楼</span></label>
									<span class="n">{#姓名#}</span>
									<span class="t">{#时间#}</span>
									<a href="javascript:void(0);">[单独回复]</a>
									<div class="cnt">{#这里是评论的内容#}</div>
								</li>
							</ul>
						</div>
						<div class="clr"></div>
					</div>
				</div>
EOT;

//这里放JS代码
$arr['js']=<<<'EOT'
	(function(){
		/*setTimeout(function(){alert('加载啦');},500);*/
		completeWork(4);
	})();
EOT;
}

if($_GET['ac']=='dowork'){	//逻辑处理代码写这里
	echo '00000';
}
?>