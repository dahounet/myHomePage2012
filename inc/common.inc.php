<?php
class RunTime//页面执行时间类
{
    private $starttime;//页面开始执行时间
    private $stoptime;//页面结束执行时间
    private $spendtime;//页面执行花费时间
    function getmicrotime()//获取返回当前微秒数的浮点数
    {
        list($usec,$sec)=explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
    function start()//页面开始执行函数，返回开始页面执行的时间
    {
        $this->starttime=$this->getmicrotime();
    }
    function end()//显示页面执行的时间
    {
        $this->stoptime=$this->getmicrotime();
        $this->spendtime=$this->stoptime-$this->starttime;
        //return round($this->spendtime,10);
    }
    function display()
    {
        //$this->end();
        echo "<p>运行时间：".round($this->spendtime,10)."秒</p>";
    }
}

$timer=new RunTime();
$timer->start();

include_once 'inc/conn.php';
date_default_timezone_set('Asia/Shanghai');
session_start();
?>