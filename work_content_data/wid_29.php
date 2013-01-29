<?php
if($_GET['ac']=='wkcontent'){
	$item=new itemWork(29);
	$key=$item->generate_complete_key();
//这里放HTML代码
$arr['html']=<<<"EOT"
<strong>测试数据：【{$content['data']}】</strong>
<h2 onclick="completeWork(29,'$key');">wid:29</h2>
EOT;

//这里放JS代码
$arr['js']=<<<'EOT'
	(function(){
		/*setTimeout(function(){alert('加载啦');},500);*/
	})();
EOT;
}

if($_GET['ac']=='dowork'){	//逻辑处理代码写这里
	echo '00000';
}
?>