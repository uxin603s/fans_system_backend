<?php
class FansList{
	use CRUD;
	public static $table="fans_list";
	public static $filter_field_arr=["id","fb_id","name","status","comment","fan_count","last_post_time_int"];
	
	
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
		return compact(["status","list","arg","failIds","http_code","http_response_header"]);
	}
	public static function updateOnlineSuccess($list){
		foreach($list as $item){
			$last_post_time_int=strtotime($item['posts']['data'][0]['created_time']);
			
			$arg=[
				"update"=>[
					"last_post_time_int"=>$last_post_time_int,
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

	public static function test(){
		
		$com="";
		$com.="/usr/bin/curl -is  --interface '209.95.33.122' ";
		$com.=" 'https://m.facebook.com/login.php' ";
		$com.=" -H 'accept-language: zh-TW,zh;q=0.8,en-US;q=0.6,en;q=0.4,ja;q=0.2,en-AU;q=0.2' ";
		$com.=" -H 'upgrade-insecure-requests: 1' ";
		$com.=" -H 'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36' ";
		$com.=" -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' "; 
		$com.=" -H 'authority: m.facebook.com' ";
		
		ob_start();
		system($com);
		$content=ob_get_clean();
		
		$cookie=[];
		if(preg_match_all("/Set-Cookie:([\w\W]+?);/",$content,$match)){
			$cookie=[];
			foreach($match[1] as $val){
				$explode=explode("=",trim($val));
				$cookie[]=$explode[0]."=".$explode[1];
			}
		}
		
		
		$cookie_str=implode("; ",$cookie);
		$html = str_get_html($content);
		$url=html_entity_decode($html->find('form',0)->action);
		
		$input=$html->find('form>input[type=hidden]');
		$post=[];
		foreach($input as $item){
			$post[]=$item->name."=".$item->value;
		}
		
		// var_dump($url,$post);
		// exit;
		$FBAccount=json_decode(file_get_contents(__DIR__."/config/FBAccount.json"),1);

		$post[]=$FBAccount['email'];
		$post[]=$FBAccount['pass'];
		$post[]="login=登入";
		
		$post_str=implode("&",$post);
		// var_dump($cookie_str);
		// var_dump($post_str);
		// exit;
		// sleep(2);
		
		$com="/usr/bin/curl -is  --interface '209.95.33.122' ";//
		$com.="  '{$url}' ";//
		$com.=" -H 'origin: https://m.facebook.com' ";
		$com.=" -H 'accept-language: zh-TW,zh;q=0.8,en-US;q=0.6,en;q=0.4,ja;q=0.2,en-AU;q=0.2' ";
		$com.=" -H 'upgrade-insecure-requests: 1' ";
		$com.=" -H 'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36' ";
		$com.=" -H 'content-type: application/x-www-form-urlencoded' ";
		$com.=" -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' ";
		$com.=" -H 'authority: m.facebook.com' ";
		
		$com.=" -H 'cookie: {$cookie_str}' ";
		$com.=" -H 'referer: https://m.facebook.com/login.php' ";
		$com.=" --data '{$post_str}' ";
		
		var_dump($com);
		exit;
		
		ob_start();
		system($com);
		$content=ob_get_clean();
		
		var_dump($content);
		exit;
		
	}
	public static function get(){
$content="
Set-Cookie: noscript=1; path=/; domain=.facebook.com
Set-Cookie: reg_fb_ref=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=-1481623338; path=/; domain=.facebook.com; httponly
Set-Cookie: fr=0n9aw7yYjiq5dZDBM.AWWi4zN-MXDfhDqbJnSgs9FZJa4.BYT8cQ.OV.AAA.0.0.BYT8cr.AWW_JtOy; expires=Mon, 13-Mar-2017 10:02:19 GMT; Max-Age=7776000; path=/; domain=.facebook.com; httponly
Set-Cookie: sb=K8dPWPZYmdevgUr-Hppae3i3; expires=Thu, 13-Dec-2018 10:02:19 GMT; Max-Age=63072000; path=/; domain=.facebook.com; secure; httponly
Set-Cookie: reg_fb_gate=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=-1481623338; path=/; domain=.facebook.com; httponly
Set-Cookie: m_ts=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=-1481623338; path=/; domain=.facebook.com
Set-Cookie: c_user=100014416278321; expires=Mon, 13-Mar-2017 10:02:19 GMT; Max-Age=7776000; path=/; domain=.facebook.com; secure
Set-Cookie: xs=35%3AToiGv_euX-Clpg%3A2%3A1481623339%3A-1; expires=Mon, 13-Mar-2017 10:02:19 GMT; Max-Age=7776000; path=/; domain=.facebook.com; secure; httponly
Set-Cookie: csm=2; expires=Mon, 13-Mar-2017 10:02:19 GMT; Max-Age=7776000; path=/; domain=.facebook.com
Set-Cookie: s=Aa7QmPOAtLdzgRYn.BYT8cr; expires=Mon, 13-Mar-2017 10:02:19 GMT; Max-Age=7776000; path=/; domain=.facebook.com; secure; httponly
Set-Cookie: m_user=0%3A0%3A0%3A0%3Av_1%2Cajax_0%2Cwidth_0%2Cpxr_0%2Cgps_0%3A1481623339%3A2; expires=Mon, 13-Mar-2017 10:02:19 GMT; Max-Age=7776000; path=/; domain=.facebook.com; httponly
Set-Cookie: lu=Rwnzx3K9XYmaRtQlj5g3-IMw; expires=Thu, 13-Dec-2018 10:02:19 GMT; Max-Age=63072000; path=/; domain=.facebook.com; secure; httponly
";
$cookie=[];
if(preg_match_all("/Set-Cookie:([\w\W]+?);/",$content,$match)){
	$cookie=[];
	foreach($match[1] as $val){
		$explode=explode("=",trim($val));
		$cookie[]=$explode[0]."=".$explode[1];
	}
}
$cookie_str=implode(";",$cookie);
$com='';
$com.="/usr/bin/curl -isL  --interface '209.95.33.122' ";//
$com.=" 'https://developers.facebook.com/tools/explorer/145634995501895/' ";
// $com.=" 'https://www.facebook.com/v2.8/dialog/oauth?response_type=token&display=popup&client_id=145634995501895&redirect_uri=https%3A%2F%2Fdevelopers.facebook.com%2Ftools%2Fexplorer%2Fcallback%3Fmethod%3DGET%26path%3Dme%253Ffields%253Did%252Cname%26version%3Dv2.8&scope=publish_actions%2Cmanage_pages%2Cpublish_pages' ";
$com.=" -H 'accept-language: zh-TW,zh;q=0.8,en-US;q=0.6,en;q=0.4,ja;q=0.2,en-AU;q=0.2' ";
$com.=" -H 'upgrade-insecure-requests: 1' ";
$com.=" -H 'user-agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36' ";
$com.=" -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' ";
$com.=" -H 'cache-control: max-age=0' ";
$com.=" -H 'authority: developers.facebook.com' ";
$com.=" -H 'cookie:  {$cookie_str}' ";
$com.=" -H 'referer: https://developers.facebook.com/tools/explorer/145634995501895/'";
ob_start();
system($com);
$content=ob_get_clean();

// var_dump($content);
if(preg_match_all("/accessToken\":\"([\w\W]+?)\"/",$content,$match)){
	var_dump($match[1][0]);
}
	}
}