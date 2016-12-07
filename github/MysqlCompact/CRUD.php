<?php
trait CRUD{
	public static function filter_field($list){
		foreach($list as $field=>$value){
			if(isset($value['field'])){
				if(!in_array($value['field'],self::$filter_field_arr)){
					unset($list[$field]);
				}
			}else{
				if(!in_array($field,self::$filter_field_arr)){
					unset($list[$field]);
				}
			}
		}
		return $list;
	}
	public static function getList($arg){
		$bind_data=[];
		$where_str=MysqlCompact::where(self::filter_field($arg['where_list']),$bind_data);
		$orderBy_str=MysqlCompact::order(self::filter_field($arg['order_list']));
		$group_str=MysqlCompact::group(self::filter_field($arg['group_list']),$bind_data);
		
		$sql="select * from ".self::$table." {$where_str} {$orderBy_str} {$group_str}";
		
		if($tmp=DB::select($sql,$bind_data)){
			$status=true;
			$list=$tmp;
		}else{
			$status=false;
		}
		return compact(['status','list','sql','bind_data']);
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
