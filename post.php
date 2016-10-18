<?php
$db = parse_url(getenv('CLEARDB_DATABASE_URL'));
$db['dbname'] = ltrim($db['path'], '/');
$dsn = "{$db['scheme']}:host={$db['host']};dbname={$db['dbname']};charset=utf8";
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

function h($var)
{
    if (is_array($var)) {
	return array_map('h', $var);
    } else {
	return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}

