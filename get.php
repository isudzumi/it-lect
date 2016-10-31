<?php
require_once("class.php");

function fetchAll(){
	$db = new ConnectDB();
	if(!$db) exit;
	$sql = "SELECT room, desk, status FROM desk";
	$prepare = $db->get($sql);
	$prepare->execute();
	$result = $prepare->fetchAll(PDO::FETCH_ASSOC);
	return $result;
}

function sortAry($result, &$statAry){
	//$resultに格納された配列を、教室番号と机番号をキーにした2次元配列に書き換える
	for($i = 0; $i < count($result); $i++){
		if($i != 0 && $result[$i]['room'] == $result[$i-1]['room']){
			$statAry[$result[$i]['room']][$result[$i]['desk']] = $result[$i]['status'];
		} else {
			$statAry += [$result[$i]['room'] => [$result[$i]['desk'] => $result[$i]['status']]];
		}
	}
	return $statAry;
}


$result = fetchAll();
$statAry=[];
sortAry($result, $statAry);
