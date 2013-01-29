<?php
//sleep(20);
header('Content-Type:text/html; charset=utf-8');
include 'inc/common.inc.php';
include 'inc/works_list.func.php';
include_once 'inc/works_list.class.php';
/*$item=new itemWork();
$rt=$item->delete(259);
if($rt){
	echo "成功删除";
}else{
	echo "删除失败";
}/*
$modi=new itemWork();
//if($modi->modify(array('title'=>'！！改掉标题啦！','describe'=>'【被改掉的描述】','stopdate'=>NULL,'data'=>'abc.php'),41)){
if($modi->modify(array('describe'=>'【被改掉的描述】周期性任务001','intervaltime'=>200,'stopdate'=>NULL,'data'=>'0001cc.php','status'=>'0'),5)){
	echo "修改成功";
}else{
	echo "修改失败啦";
}

$r=$item->create('哈哈哈【！这是标题！】', '1', '111', '2012-09-22 22:40:00', 1, 'new.data000.php');
if($r){
	echo "成功";
}else{
	echo '失败';
}
//$content=$item->get_info();
/*$c=$item->get_content();
$log=$item->get_log();
if($c){
	print_r($c);
}else{
	print_r('11111');
}
if($log){
	print_r($log);
}else{
	print_r('无数据日志');
}
/*
//print_r($content);
while ($e=each($content)){
	echo '<p>'.$e['key'].':________'.$e['value'].'</p>';
}*//*
$item=new itemWork(6);
echo $item->finish();*/
//print_r($list->get_list());
/*echo '<p>今日待办数：'.$list->get_wait_do_num().'<p/>';

$timer->end();$timer->display();*/
$ac=$_GET['ac'];
if($ac=='wkslist'){
	$list=new worksListToday();
	$md5=md5('{"list":'.json_encode($list->get_list()).',"waitdonum":'.$list->get_wait_do_num().'}');
	if($_GET['md5']==$md5){
		echo '{"status":"latest"}';
	}else{
		echo '{"list":'.json_encode($list->get_list()).',"waitdonum":'.$list->get_wait_do_num().',"md5":"'.$md5.'"}';
	}
}else if($ac=='wkmodi'){
	
}else if ($ac=='wkdelete'){
	
}else if($ac=='wklog'){
	
}else if($ac=='wkinfo'){
	$item=new itemWork((int)$_GET['wid']);
	if(count($item->get_info())){
		echo json_encode($item->get_info());
	}
}else if($ac=='wkcontent'){
	$arr=array('type'=>null,'html'=>null,'js'=>null);
	$item=new itemWork((int)$_GET['wid']);
	$content=$item->get_content();
	$arr['type']=$content['ctype'];
	if($content['ctype']==1){
		include_once 'work_content_data/'.$content['data'];
		//$arr['html']='<strong>测试数据：【'.$content['data'].'】</strong>';
	}else if($content['ctype']==2){
		$dataC2arr=unserialize($content['data']);
		$arr['html']='<iframe src="'.$dataC2arr['url'].'" width="100%" height="100%" scrolling="yes" frameborder="0"></iframe>';
		$arr['js']=$dataC2arr['js'];
	}
	echo json_encode($arr);
}else if($ac=='wkcomplete'){
	$item=new itemWork((int)$_GET['wid']);
	$workInfo=$item->get_info();
	if($_GET['key']!=null){
		$result=$item->complete($_GET['key']);
	}else{
		$result=$item->complete();
	}
	if($result=='succeed'){
		echo json_encode(array('wid'=>$workInfo['wid'],'title'=>$workInfo['title'],'result'=>'succeed','msg'=>''));
	}elseif($result=='finished'){
		echo json_encode(array('wid'=>$workInfo['wid'],'title'=>$workInfo['title'],'result'=>'finished','msg'=>'此事务当前已经完成过了，无需重复完成。'));
	}elseif($result=='incorrect_key'){
		echo json_encode(array('wid'=>$workInfo['wid'],'title'=>$workInfo['title'],'result'=>'incorrect_key','msg'=>'参数不正确'));
	}else{
		echo json_encode(array('wid'=>$workInfo['wid'],'title'=>$workInfo['title'],'result'=>'error','msg'=>'有错误发生'));
	}
}else if($ac=='wkcreate'){
	
}else if($ac=='dowork'){
	$item=new itemWork((int)$_GET['wid']);
	$content=$item->get_content();
	if($content['ctype']==1){
		include_once 'work_content_data/'.$content['data'];
	}
}
?>