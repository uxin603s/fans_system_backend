<?php
class FansList{
	use CRUD {
		// CRUD::insert as private tmp_insert;
	}
	public static $table="fans_list";
	public static $filter_field_arr=["id","fb_id","name","status","comment","fan_count","last_post_time_int","updated_time_int"];
	public static $cache_key_field=["id","fb_id","status"];
	
	public static function getOnline($arg){
		$FB=json_decode(file_get_contents(__DIR__."/config/FB.json"),1);
		$url="";
		$url.="https://graph.facebook.com?fields=fan_count,name,id,posts";
		$url.=".limit(1){created_time}";
		$url.="&access_token={$FB['id']}|{$FB['secret']}";
		$url.="&ids=";
		
		$ids=[];
		$failIds=[];
		foreach($arg['ids'] as $id){
			if(is_numeric($id)){
				$ids[]=$id;
			}else{
				$failIds[]=$id;
			}
		}
		
		if(count($ids)){
			$url.=implode(",",$ids);
			
			ob_start();
			$list=json_decode(file_get_contents($url),1);
			$error_log=ob_get_clean();
			
			if($error_log){
				ob_start();
				var_dump($http_response_header);
				$http_head_code=ob_get_clean();
				
				error_log("\n--------------\n".$http_head_code.$error_log."\n--------------\n");
				// echo $error_log;
			}
			if(preg_match("/\d{3}/",$http_response_header[0],$match)){
				$http_code=$match[0];
			}
		}
		
		if($list && count($list)){
			foreach($ids as $id){
				if(!$list[$id]){
					$failIds[]=$id;
				}
			}
			$status=true;
		}
		else{
			$failIds=array_merge($failIds,$ids);
			$status=false;
		}
		return compact(["status","list","arg","failIds","http_code","http_response_header"]);
	}
	public static function getStruct($list,$callback){
		$result=[];
		foreach($list as $item){
			$last_post_time_int=strtotime($item['posts']['data'][0]['created_time']);
			$arg=[
				"last_post_time_int"=>$last_post_time_int,
				"fan_count"=>$item['fan_count'],
				"name"=>$item['name'],
				"fb_id"=>$item['id'],
			];
			if($callback){
				$result[]=$callback($arg);
			}
		}
		if(count($result)==1){
			return $result[0];
		}
		return $result;
	}
	public static function getFB($arg){
		$path=__DIR__."/config/FB.json";
		
		// return $arg;
		return json_decode(file_get_contents($path),1);
	}
	public static function setFB($arg){
		$path=__DIR__."/config/FB.json";
		$data=[
			'id'=>$arg['id'],
			'secret'=>$arg['secret'],
		];
		return file_put_contents($path,json_encode($data));
	}
}