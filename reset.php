<?php
require("class.php");

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
