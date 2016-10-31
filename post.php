<?php

$request = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : "";
if($request !== 'xmlhttprequest')
	exit;

$db = parse_url(getenv('CLEARDB_DATABASE_URL'));
$db['dbname'] = ltrim($db['path'], '/');
$dsn = "{$db['scheme']}:host={$db['host']};dbname={$db['dbname']};charset=utf8";
if(isset($_POST)){

	$post = $_POST;

	try {
		$db = new PDO($dsn, $db['user'], $db['pass']);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "UPDATE desk SET status = {$post['status']} WHERE room = {$post['room']} AND desk = {$post['desk']}";
		$prepare = $db->prepare($sql);
		try {
			$prepare->execute();
		} catch (PDOException $e){
			$prepare->rollback();
			throw $e;
		}

	} catch (PODException $e) {
		$iserr = TRUE;
		echo "Error: ". h($e->getMessage());
	}
}

