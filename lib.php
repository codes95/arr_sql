<?php

/*
select('name, fio, phone')->from($my_arr)->where( function($row){
	LIKE($row['name'], '%dfg-%') AND 
});
*/


function get_keys($arr, $key_arr){
	$response = Array();
	
	foreach($key_arr as $key){
		$response[] = $arr[$key];
	}
	
	return $response;
}

function multi_array_to_column($arr, $column, $one_dimensional_array=true){
	$response = Array();
	
	foreach($arr as $item){
		
		if($one_dimensional_array){
			$response[] = $item[$column];
		}else{
			$response[] = Array( $column => $item[$column]);
		}		
	}
	
	return $response;
	
	//return ($one_dimensional_array)? array_column($arr, $column) : array_column($arr, $column);
	
}





function array_group_by(array $array, $key)
{
	if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
		trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
		return null;
	}
	$func = (!is_string($key) && is_callable($key) ? $key : null);
	$_key = $key;
	// Load the new array, splitting by the target key
	$grouped = [];
	foreach ($array as $value) {
		$key = null;
		if (is_callable($func)) {
			$key = call_user_func($func, $value);
		} elseif (is_object($value) && property_exists($value, $_key)) {
			$key = $value->{$_key};
		} elseif (isset($value[$_key])) {
			$key = $value[$_key];
		}
		if ($key === null) {
			continue;
		}
		$grouped[$key][] = $value;
	}
	// Recursively build a nested grouping if more parameters are supplied
	// Each grouped array value is grouped according to the next sequential key
	if (func_num_args() > 2) {
		$args = func_get_args();
		foreach ($grouped as $key => $value) {
			$params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
			$grouped[$key] = call_user_func_array('array_group_by', $params);
		}
	}
	return $grouped;
}



class arr_sql{
    
	
	public $arr;
	public $column_arr;
	public $column_list;
	public $sorting_directions ='ASC';
    
	function __construct(){
       $this->arr = null;
    }

    function SELECT($column_list){ 
		$this->column_list = str_replace(" ", '', $column_list);
		$this->column_arr = explode(',', $this->column_list); 
        return $this; 
    }

	
    function FROM($arr){
        $this->arr = $arr;
		
		foreach($this->arr as $index=>$row){
			$this->arr[$index] = ($this->column_list=='*')? $row : $this->delete_keys_except($row, $this->column_arr);
		}
	
        return $this;
    }

	
    function WHERE($callback){
        
		foreach($this->arr as $index=>$row){
			$res = $callback($row);
			if(!$res) unset($this->arr[$index]);
		}
		
		return $this;
    }
	

	function ORDER_BY($code_str){
		
		$res_arr = [];	 
		$comands = explode(',', $code_str); 
		foreach($comands as $comand){			
			
			if(preg_match("/(\D{1,}) (ASC|DESC)/i", $comand, $matches)) {
				list($all_str, $column, $sort_directions) = $matches;
				$res_arr[$column] = strtoupper($sort_directions);				
			}else{
				$res_arr[$comand] = 'ASC';
			}
		}
		
		
		//https://wp-kama.ru/question/php-usort-sortirovka-massiva-po-dvum-polyam
		 
		usort($this->arr, function($a, $b) use ($res_arr){
			// поля по которым сортировать
			//$res_arr = array( 'laps'=>'DESC', 'time_ms'=>'ASC' );

			$res = 0;
			foreach( $res_arr as $k=>$v ){
				if( $a[$k] == $b[$k] ) continue;

				if( is_numeric($a[$k]) ){
					$res = ( $a[$k] < $b[$k] ) ? -1 : 1; 
				}else{
					$res = strnatcmp($a[$k], $b[$k]);
				}  
				
				if( $v=='DESC' ) $res= -$res;
				break;
			}

			return $res;
		});




		
		/*foreach()
			(explode('DESC', $c[0])[1]!='') $this->sorting_directions;
			
		}*/
		/*
		
		if($sorting_directions!=null) $this->sorting_directions = $sorting_directions;
		
		
		usort($this->arr, function($a, $b) use ($column_name){
  
			if($a[$column_name] == $b[$column_name]) return 0;
			
			if($this->sorting_directions=='ASC'){
				return $a[$column_name] < $b[$column_name]? -1 : 1;
			}else{
				return $a[$column_name] < $b[$column_name]? 1 : -1;
			}
			
		});*/
		
		return $this;  
	}
	

	
	
	
	
	
	
	function GROUP_BY($column_list){
		// https://gist.github.com/mcaskill/baaee44487653e1afc0d

		$get_arg_arr = function($column_list){
			
			$column_list = str_replace(' ', '', $column_list);
			$column_arr = explode(',', $column_list);
			
			$arr1 = array($this->arr);
			$arr2 = $column_arr;
			return array_merge($arr1, $arr2);	
			
		};
		
		
		$this->arr = call_user_func_array('array_group_by', $get_arg_arr($column_list));
		
		return $this;
	}

	
	function fetchall(){
		return $this->arr;
	}
	


	
	
	private function delete_keys_except($arr, $column_arr){

		foreach($arr as $key=>$item){
			if( !in_array($key, $column_arr) ) unset($arr[$key]);
		}
		
		return $arr;	
	}
	
}



/*$arr_sql = new arr_sql(); 
$res2 =  $arr_sql->SELECT('name, fio, phone')->FROM($arr)*/





?>