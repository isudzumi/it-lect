<?php
$db = parse_url(getenv('CLEARDB_DATABASE_URL'));
$db['dbname'] = ltrim($db['path'], '/');
$dsn = "{$db['scheme']}:host={$db['host']};dbname={$db['dbname']};charset=utf8";
try {
	$db = new PDO($dsn, $db['user'], $db['pass']);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$statAry = [];			//['�����ԍ�']=>['���ԍ�']=>'status( 0 or 1 )'
	$sql = "SELECT room, desk, status FROM desk";
	$prepare = $db->prepare($sql);
	$prepare->execute();
	$result = $prepare->fetchAll(PDO::FETCH_ASSOC);

} catch (PODException $e) {
	$iserr = TRUE;
	echo "Error: ". h($e->getMessage());
}

$statAry = [];

//$result�Ɋi�[���ꂽ�z����A�����ԍ��Ɗ��ԍ����L�[�ɂ���2�����z��ɏ���������
for($i = 0; $i < count($result); $i++){
	if($i != 0 && $result[$i]['room'] == $result[$i-1]['room']){
		$statAry[$result[$i]['room']][$result[$i]['desk']] = $result[$i]['status'];
	} else {
		$statAry += [$result[$i]['room'] => [$result[$i]['desk'] => $result[$i]['status']]];
	}
}

