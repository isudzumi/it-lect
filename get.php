<?php
class ConnectDB {

	private $db;

	public function get($sql){
		$result = $this->db;
		$result = $result->prepare($sql);
		return $result;
	}

	function __construct() {
		try {
			$db = parse_url(getenv('CLEARDB_DATABASE_URL'));
			$db['dbname'] = ltrim($db['path'], '/');
			$dsn = "{$db['scheme']}:host={$db['host']};dbname={$db['dbname']};charset=utf8";
			$db = new PDO($dsn, $db['user'], $db['pass']);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db = $db;
		} catch (PDOException $e) {
			return FALSE;
		}
	}
}

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
	//$result�Ɋi�[���ꂽ�z����A�����ԍ��Ɗ��ԍ����L�[�ɂ���2�����z��ɏ���������
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
