<?php
include_once __DIR__."/include.php";

UserSystemHelp::login(function($data){
	$config=json_decode(file_get_contents(__DIR__."/config.json"),1);
	$data['tag_url']=$config['tag']['url'];
	UserSystemHelp::success($data);
});