<?php
class FansList{
	use CRUD;
	public static $table="fans_list";
	public static $filter_field_arr=["id","fb_id","name","status","comment"];
	
	
	public static function update_online($arg){
		$url="";
		$url.="https://graph.facebook.com?fields=fan_count,name,id";
		$url.="&access_token";
		$url.="&ids=";
		
		$ids=$arg['ids'];
		foreach($ids as $id){
			
		}
		return json_decode(file_get_contents($url));
	}
}