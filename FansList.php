<?php
class FansList{
	use CRUD;
	public static $table="fans_list";
	public static $filter_field_arr=["id","fb_id","name","status","comment","fan_count"];
	
	
	public static function update_online($arg){
		$FB=json_decode(file_get_contents(__DIR__."/config/FB.json"),1);
		$url="";
		$url.="https://graph.facebook.com?fields=fan_count,name,id";
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
			$list=json_decode(file_get_contents($url),1);
		}
		
		if(count($list)){
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
		return compact(["status","list","arg","failIds"]);
	}
}