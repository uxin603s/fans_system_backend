<?php
include_once __DIR__."/../include.php";

$where_list=[
	["field"=>"status","type"=>0,"value"=>0],
	["field"=>"status","type"=>0,"value"=>1],
];

$page=0;
$failId=[];

while(true){
	echo "start{$page}\n";
	$limit=[
		"page"=>$page,
		"count"=>10,
	];
	$arg=[
		"where_list"=>$where_list,
		"limit"=>$limit,
		"not_count_flag"=>true,
	];
	
	$result=FansList::getList($arg);
	if($result['status']){
		$ids=array_column($result['list'],'fb_id');
		
		$result=FansList::getOnline(compact(["ids"]));
		
		if($result['status']){
			FansList::updateOnlineSuccess($result['list']);
		}
		if($result['http_code']==200){
			$failId=array_merge($result['failIds'],$failId);
		}
	}else{
		break;
	}
	
	echo "http_code:{$result['http_code']}\n";
	echo "成功:".count($result['list'])."\n";
	echo "失敗:".count($failId)."\n";
	
	// usleep(1000000);
	echo "-------------\n";
	++$page;
}

echo "失敗".count($failId)."\n";
// exit;
foreach($failId as $id){
	$ids=[$id];
	
	$result=FansList::getOnline(compact(["ids"]));
	echo "start{$page}";
	echo "http_code:{$result['http_code']}\n";
	echo "成功:".count($result['list'])."\n";
	echo "失敗:".count($failId)."\n";
	
	// usleep(1000000);
	echo "-------------\n";
	if(!$result['status']){
		if($result['http_code']==200){
			$arg=[
				"update"=>[
					"status"=>2,
				],
				"where"=>[
					"fb_id"=>$id,
				],
			];
			FansList::update($arg);
			
		}else if($result['http_code']==400){
			var_dump($result['http_response_header']);
			$arg=[
				"update"=>[
					"status"=>0,
				],
				"where"=>[
					"fb_id"=>$id,
				],
			];
			FansList::update($arg);
		}
		++$page;
	}	
}
