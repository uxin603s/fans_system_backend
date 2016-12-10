<?php
include_once __DIR__."/../include.php";
$where_list=[
	["field"=>"status","type"=>0,"value"=>0],
	["field"=>"status","type"=>0,"value"=>1],
];

$page=0;
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
	$result=FansList::getList($arg);
	// var_dump($result['status']);
	if($result['status']){
		// $result['list']
		// var_dump(array_keys());
		$ids=array_column($result['list'],'fb_id');
		$result=FansList::update_online(compact(["ids"]));
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
			foreach($result['failIds'] as $id){
				$arg=[
					"update"=>[
						"status"=>2,
					],
					"where"=>[
						"fb_id"=>$id,
					],
				];
				FansList::update($arg);
			}
		}
		echo ++$page."\n";
		// usleep(100000);
	}else{
		break;
	}
	
	// var_dump(array_keys($result));
}
// var_dump($result['status']);
// 


