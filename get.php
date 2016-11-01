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

$result = fetchAll();
$statAry=[];
sortAry($result, $statAry);
