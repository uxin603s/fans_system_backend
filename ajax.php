<?php
include_once __DIR__."/github/DB/DB.php";

include_once __DIR__."/github/MysqlCompact/MysqlCompact.php";

include_once __DIR__."/github/MysqlCompact/CRUD.php";

include_once __DIR__."/FansList.php";

$DBConfig=json_decode(file_get_contents(__DIR__."/config/DB.json"),1);

DB::$config=$DBConfig;
DB::query("SET NAMES utf8");





session_start();
$_SESSION['id']=0;
session_write_close();
if(isset($argv[1])){
	array_shift($argv);
	$_REQUEST['func_name']=array_shift($argv);
	$_REQUEST['arg']=$argv;
}
if(isset($_REQUEST['func_name'])){
	$func_name=$_REQUEST['func_name'];
	$arg=empty($_REQUEST['arg'])?[]:$_REQUEST['arg'];
	echo @json_encode(call_user_func($func_name,$arg),JSON_NUMERIC_CHECK);
}
DB::$connect=null;