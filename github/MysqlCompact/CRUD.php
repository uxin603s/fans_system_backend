<?php
trait CRUD{
	public static function filter_field($list){
		foreach($list as $field=>$value){
			if(isset($value['field'])){
				if(!in_array($value['field'],self::$filter_field_arr)){
					unset($list[$field]);
				}
			}else{
				if(is_numeric($field)){
					if(!in_array($value,self::$filter_field_arr)){
						unset($list[$field]);
					}
				}else{
					if(!in_array($field,self::$filter_field_arr)){
						unset($list[$field]);
					}
				}
			}
		}
		return $list;
	}
	public static function getList($arg){
		$bind_data=[];
		$select_str="*";
		if($arg['where_list']){
			$arg['select_list']=self::filter_field($arg['select_list']);
			if(is_array($arg['select_list']) && count($arg['select_list'])){
				$select_str=implode(",",$arg['select_list']);
			}
		}
		
		$where_str=MysqlCompact::where(self::filter_field($arg['where_list']),$bind_data);
		$orderBy_str=MysqlCompact::order(self::filter_field($arg['order_list']));
		$group_str=MysqlCompact::group(self::filter_field($arg['group_list']),$bind_data);
		
		$limit_str=MysqlCompact::limit($arg['limit']);
		
		$sql="select {$select_str} from ".self::$table." 
		{$where_str} {$orderBy_str} {$group_str} {$limit_str}";
		
		if($tmp=DB::select($sql,$bind_data)){
			$status=true;
			$list=$tmp;
			
			
		}else{
			$status=false;
		}
		if($limit_str){
			$count_sql="select count(*) count from ".self::$table." 
			{$where_str} {$orderBy_str} {$group_str} ";
			if($tmp=DB::select($count_sql,$bind_data)){
				$total_count=$tmp[0]['count'];
				$total_page=ceil($total_count/$arg['limit']['count']);
			}
		}
		return compact(['status','list','sql','bind_data','total_page','total_count']);
	}
	
	public static function insert($insert){
		$insert=self::filter_field($insert);
		if($id=DB::insert($insert,self::$table)){
			if(is_numeric($id)){
				$insert['id']=$id;
			}
			$status=true;
			$message="新增成功";
		}else{
			$status=false;
			$message="新增失敗";
		}
		return compact(['status','message','insert']);
	}
	public static function update($arg){
		$arg['update']=self::filter_field($arg['update']);
		$arg['where']=self::filter_field($arg['where']);
		//欄位案權限 再過濾一次
		if(DB::update($arg['update'],$arg['where'],self::$table)){
			$status=true;
			$message="修改成功";
		}else{
			$status=false;
			$message="修改失敗";
		}
		return compact(['status','message','arg','update','where']);
	}
	public static function delete($where){
		$where=self::filter_field($where);
		if(DB::delete($where,self::$table)){
			$status=true;
			$message="刪除成功";
		}else{
			$status=false;
			$message="刪除失敗";
		}
		return compact(['status','message']);
	}
}
