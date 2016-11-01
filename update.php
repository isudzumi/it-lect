<?php

require("class.php");

function getAll($db){
	$sql = 'SELECT room, desk, status FROM desk';
	try {
		$prepare = $db->get($sql);
		$result  = $prepare->execute();
		return $result;

	} catch (PDOException $e){
		echo 'Error:'.$e->getMessage();
	}
}

$db = new ConnectDB();
if(!$db) exit;

$result = getAll($db);
$temp   = $result;

//60秒ごとにデータベースに接続して配列を比較する
while( $result === $temp ){
	sleep(60);
	$result = getAll($db);
}

//statusが変わっている席の配列を取り出す
$diff = array_diff_assoc($result, $temp);

$statAry = [];
$statAry = sortAry($diff, $statAry);
json_encode($statAry);
