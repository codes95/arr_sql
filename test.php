<?php
## localhost/09.05.19/test.php

include 'lib.php'; 



$arr = Array(
	
	Array(
		'name'=> 'Петя',
		'phone'=> '380541323421',	
		'age'=> 23,
		'salary'=> 600
	),
	
	Array(
		'name'=> 'Олег',
		'phone'=> '380741324721',
		'age'=> 29,
		'salary'=> 300
	),

	Array(
		'name'=> 'Саша',
		'phone'=> '380641323265',
		'age'=> 22,
		'salary'=> 400
	),
	
	Array(
		'name'=> 'Саша',
		'phone'=> '380541323904',
		'age'=> 31,
		'salary'=> 700
	),

	Array(
		'name'=> 'Ігор',
		'phone'=> '380975326952',
		'age'=> 27,
		'salary'=> 1100
	)	
	
);


 
$arr_sql = new arr_sql(); 

/*
$res = $arr_sql->SELECT('name, age')->FROM($arr)->fetchall();
var_dump($res);
*/

/*
$res = $arr_sql->SELECT('name, age')->FROM($arr)->WHERE(function($columns){
	return ( $columns['age'] <= 22)? true : false;	
})->fetchall();
*/



$res = $arr_sql->SELECT('name, age, salary')->FROM($arr)->WHERE(function($columns){
	return ( $columns['age'] <= 35)? true : false;	
})->ORDER_BY('name DESC')->fetchall(); 

var_dump($res);


$res = $arr_sql->SELECT('name, age, salary')->FROM($arr)->WHERE(function($columns){
	return ( $columns['age'] <= 35)? true : false;	
})->ORDER_BY('name')->GROUP_BY('name')->fetchall();  //->GROUP_BY('name')->fetchall(); 



var_dump($res);


?>