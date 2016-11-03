<?php
require("class.php");

@session_start();

$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : "";
if($request !== 'xmlhttprequest')
	exit;

$post = $_POST['room'];
if(isset($post)) {
	$sql = "UPDATE desk SET status = 0 WHERE room = {$post}";
	try {
		$db = new ConnectDB();
		$prepare = $db->get($sql);
		$result = $prepare->execute();
		echo "success";
	} catch ( PDOException $e ){
		$prepare->rollback();
		echo 'Error:'.$e->getmessage();
	}
} else {
	echo "empty";
}
