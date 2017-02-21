<?php
$path=__DIR__."/config.json";
if(file_exists($path)){
	$data=json_decode(file_get_contents($path),1);
}else{
	$data=[];
}
echo "請設定tag_api_url:";
$data['tag']['url']=stream_get_line(STDIN, 1024, PHP_EOL);
echo "\n";


var_dump($data);
file_put_contents(__DIR__."/config.json",json_encode($data));