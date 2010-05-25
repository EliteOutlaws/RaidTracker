<?
	$GLOBALS[cfg][db_name]	= 'eternal_raids';
	$GLOBALS[cfg][db_host]	= 'localhost';
	$GLOBALS[cfg][db_user]	= 'www-rw';
	$GLOBALS[cfg][db_pass]	= 'pass';

	db_connect();

	#################################################################

	function db_connect() {
		$GLOBALS[cfg][db_conn] = @mysql_connect($GLOBALS[cfg][db_host], $GLOBALS[cfg][db_user], $GLOBALS[cfg][db_pass]);

		if ($GLOBALS[cfg][db_conn]) {
			@mysql_select_db($GLOBALS[cfg][db_name], $GLOBALS[cfg][db_conn]);	
			return 1;
		}

		return 0;
	}

	#################################################################

	function db_query($qstring) {

		if ($GLOBALS[HTTP_GET_VARS][debugsql] || $_GET[debugsql]){
			echo "QUERY: ".HtmlSpecialChars($qstring)."<br />\n";
		}

		$result = mysql_query($qstring, $GLOBALS[cfg][db_conn]);

		if (!$result) {
			echo "<p>".db_error()."</p>";
		} else {
			return $result;
		}
	}

	#################################################################

	function db_insertid() {
		return mysql_insert_id($GLOBALS[cfg][db_conn]);
	}

	#################################################################

	function db_error() {
		return mysql_error($GLOBALS[cfg][db_conn]);
	}

	#################################################################

	function db_insert($tbl, $hash){
		$fields = array_keys($hash);
		db_query("INSERT INTO $tbl (`".implode('`,`',$fields)."`) VALUES ('".implode("','",$hash)."')");
		return db_insertid();
	}

	function db_insert_on_dupe($tbl, $hash, $hash2){
		$fields = array_keys($hash);

		$bits = array();
		foreach(array_keys($hash2) as $k){
			$bits[] = "`$k`='$hash2[$k]'";
		}

		db_query("INSERT INTO $tbl (`".implode('`,`',$fields)."`) VALUES ('".implode("','",$hash)."') ON DUPLICATE KEY UPDATE ".implode(', ',$bits));
		return db_insertid();
	}

	#################################################################

	function db_update($tbl, $hash, $where){
		$bits = array();
		foreach(array_keys($hash) as $k){
			$bits[] = "`$k`='$hash[$k]'";
		}
		db_query("UPDATE $tbl SET ".implode(', ',$bits)." WHERE $where");
	}

	#################################################################

	function db_num_rows($qhandle) {
		if ($qhandle) {
			return mysql_numrows($qhandle);
		} else {
			echo "no result set found";
			return 0;
		}
	}

	#################################################################

	function db_fetch_array($qhandle) {
		return mysql_fetch_array($qhandle);
	}

	#################################################################

	function db_fetch_hash($qhandle){
		return mysql_fetch_array($qhandle, MYSQL_ASSOC);
	}

	function db_fetch_list($qhandle){
		return mysql_fetch_array($qhandle, MYSQL_NUM);
	}

	#################################################################

	function db_escape_like($string){
		return str_replace(array('%','_'), array('\\%','\\_'), $string);
	}

	#################################################################

	function db_escape_rlike($string){
		return preg_replace("/([.\[\]*^\$()])/", '\\\$1', $string);
	}

	#################################################################

	function db_fetch_one($sql) {
		return db_fetch_array(db_query($sql));
	}

	#################################################################

	function db_fetch_all($sql) {
		$out = array();
		$result = db_query($sql);
		while($row = db_fetch_array($result)){
			$out[] = $row;
		}
		return $out;
	}

	#################################################################
?>