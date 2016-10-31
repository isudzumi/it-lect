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
