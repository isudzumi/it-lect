<?php
include("class.php");

$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : "";
if($request !== 'xmlhttprequest')
	exit;

if(isset($_POST)){

	$post = $_POST;
	$sql = "UPDATE desk SET status = {$post['status']} WHERE room = {$post['room']} AND desk = {$post['desk']}";

	try {
		$db = new ConnectDB();
		$prepare = $db->get($sql);
		$prepare->execute();

	} catch (PDOException $e) {
		$prepare->rollback();
		echo "Error: ". $e->getMessage();
	}
}

