<?php
require("class.php");

$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : "";
if($request !== 'xmlhttprequest')
	exit;

if(isset($_POST['room'])){
	$room = $_POST['room'];
	$sql = "UPDATE desk SET status = 0 WHERE room = {$room}"

	try {
		$db = new ConnectDB();
		$prepare = $db->get($sql);
		$result = $prepare->execute();
	} catch ( PDOException $e ){
		$prepare->rollback();
		echo 'Error:'.$e->getmessage();
	}
} else {
	return FALSE;
}
