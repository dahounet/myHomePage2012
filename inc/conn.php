﻿<?php
function db_connect(){
	@$db=new mysqli('127.0.0.1','root','root','dahou_work');
	@$db->query("set names utf8");
	
	if(mysqli_connect_error()){
	    die('数据库连接出错！');
	}else{
		return $db;
	}
}
$db=db_connect();
?>