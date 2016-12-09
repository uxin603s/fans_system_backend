<?php
/*
$where_list=[
	[
		"field"=>"",//欄位名稱
		"type"=>"",//組合符號0:=,1:!=,2:like,3:not like
		"value"=>"",//欄位值
	]
];
*/
class MysqlCompact{
	public static $type_list=[
		"=",
		"!=",
		"like",
		"not like",
	];
	public static function where($where_list=[],&$bind_data){
		$query_str="where 1=1 ";
		
		$used_count=array_count_values(array_column($where_list,'field'));
		$or_array=[];
		$and_array=[];
		foreach($used_count as $field=>$count){
			if($count>1){
				$or_array[]=$field;
			}else{
				$and_array[]=$field;
			}
		}
		$or_where=[];
		foreach($where_list as $item){
			$field=$item['field'];
			$type=$item['type'];
			$value=$item['value'];
			$symbol=self::$type_list[$type];
			//欄位白名單
			if($symbol){
				if(in_array($field,$and_array)){
					$query_str.=" && {$field} {$symbol} ?";
					$bind_data[]=$value;
				}else if(in_array($field,$or_array)){
					$or_where[$field]['query'][]=" {$field} {$symbol} ? ";
					$or_where[$field]['value'][]=$value;
				}
			}
		}
		foreach($or_where as $field=>$item){
			$query_str.=" && (".implode(" || ",$item['query']).")";
			$bind_data=array_merge($bind_data,$item['value']);
		}
		
		return $query_str;
	}
	public static $sort_list=[
		"asc",
		"desc",
	];
	public static function order($list){
		$query_str='';
		if(is_array($list) && count($list)){
			$query_str="order by ";
			$array=[];
			foreach($list as $item){
				$field=$item['field'];
				$sort=self::$sort_list[$item['type']];
				if($sort){
					$array[]="{$field} {$sort}";
				}
			}
			$query_str.=implode(",",$array);
		}
		return $query_str;
	}
	public static function group($list,&$bind_data){
		$query_str='';
		if(is_array($list) && count($list)){
			foreach($list as $item){
				if($item['type']==0){
					if(is_array($item['value'])){
						$query_str="group by {$item['field']} having count({$item['field']})  >=  ? ";
						$bind_data[]=array_shift($item['value']);
						foreach($item['value'] as $value){
							$query_str.=" && max( CASE `{$item['field']}`  WHEN ? THEN 1 ELSE 0 END ) = 1";
							$bind_data[]=$value;
						}
					}
				}
			}
		}
		return $query_str;
	}
	public static function limit($data=[]){
		$query_str='';
		if(count($data) && is_numeric($data['page']) && is_numeric($data['count'])){
			$start=$data['page']*$data['count'];
			$count=$data['count'];
			$query_str.=" limit {$start},{$count}";
		}
		return $query_str;
	}
}