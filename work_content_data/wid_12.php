<?php
$a=array('url'=>'http://127.0.0.1/jztv/','js'=>'(function(){completeWork(29);})();');
echo serialize($a);

if($_GET['ac']=='wkcontent'){
	$item=new itemWork(12);
	$content=$item->get_content();
//这里放HTML代码
$arr['html']=<<<"EOT"
<iframe src="'$content[data]'" width="100%" height="100%" scrolling="yes" frameborder="0"></iframe>
EOT;

//这里放JS代码
$arr['js']=<<<'EOT'
	(function(){
		setTimeout(function(){alert('加载啦12!!!');},500);
	})();
EOT;
}

if($_GET['ac']=='dowork'){	//逻辑处理代码写这里
	echo '00000';
}
?>