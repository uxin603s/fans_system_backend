<?php
include_once __DIR__."/../include.php";
function update_online_after_process($arg){
	$result=FansList::getList($arg);
	if($result['status']){
		$ids=array_column($result['list'],'fb_id');
		ob_start();
		$result=FansList::update_online(compact(["ids"]));
		$error_log=ob_get_clean();
		if($error_log){
			error_log($error_log);
		}
		if($result['status']){
			foreach($result['list'] as $item){
				$arg=[
					"update"=>[
						"fan_count"=>$item['fan_count'],
						"name"=>$item['name'],
					],
					"where"=>[
						"fb_id"=>$item['id'],
					],
				];
				FansList::update($arg);
			}
		}
		return $result['failIds'];
	}else{
		return false;
	}
}
$where_list=[
	["field"=>"status","type"=>0,"value"=>0],
	["field"=>"status","type"=>0,"value"=>1],
];

$page=0;
$failId=[];
while(true){
	$limit=[
		"page"=>$page,
		"count"=>50,
	];
	$arg=[
		"where_list"=>$where_list,
		"limit"=>$limit,
		"not_count_flag"=>true,
	];
	if($new_failId=update_online_after_process($arg)){
		$failId=array_merge($new_failId,$failId);
		
		
	}else{
		break;
	}
	echo ++$page."\n";
	
}
echo count($failId);
foreach($failId as $id){
	$ids=[$id];
	ob_start();
	$result=FansList::update_online(compact(["ids"]));
	$error_log=ob_get_clean();
	if($error_log){
		error_log($error_log);
	}
	if(!$result['status']){
		
		$arg=[
			"update"=>[
				"status"=>2,
			],
			"where"=>[
				"fb_id"=>$id,
			],
		];
		FansList::update($arg);
		echo ++$page."\n";
	}
	
	
	
}
