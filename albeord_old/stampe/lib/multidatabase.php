<?
function multi_connect($host, $user, $pwd, $db){

	if (_DB_SYSTEM_=="PGSQL"){
		return pg_connect("host=$host dbname=$db user=$user password=$pwd");
	}elseif(_DB_SYSTEM_=="MYSQL"){
		$conn = mysql_connect($host, $user, $pwd);
		mysql_select_db($db, $conn);
		return $conn;
	}
}
function multi_query($conn, $query){
	if (!$conn) return false;

	$GLOBALS["QUERY_CNT"]++;

	if (_DB_SYSTEM_=="PGSQL"){
		return pg_query($conn, $query);
	}elseif(_DB_SYSTEM_=="MYSQL"){
		$result = mysql_query($query, $conn);
		if (!$result & $GLOBALS["_NOERR"]!=1){
		 echo "\n MySQL_QUERY <b>ERRORE:</b> ".mysql_error($conn)."\n <b>QUERY:</b> $query\n";
		}
		return $result;
	}
}
function multi_single_query($dbh, $query, $campo='0'){
	$result = multi_query($dbh, $query);
	if (multi_num_rows($result)>0){
		$data = multi_fetch_array($result, 0);
		if ($campo=="ALL"){
			return $data;
		}else{
			return $data[$campo];
		}
	}else{
		return false;
	}
}
function multi_num_rows($result){
	if (!$result) return 0;
	if (_DB_SYSTEM_=="PGSQL"){
		return pg_num_rows($result);
	}elseif(_DB_SYSTEM_=="MYSQL"){
		return mysql_num_rows($result);
	}
}

function multi_fetch_array($result, $rs=0, $typ="BOTH"){
	if (_DB_SYSTEM_=="PGSQL"){
		return pg_fetch_array($result, $rs);
	}elseif(_DB_SYSTEM_=="MYSQL"){
		mysql_data_seek($result,$rs);
		if ($typ=="NUM")  return mysql_fetch_row($result);
		if ($typ=="BOTH") return mysql_fetch_array($result);
		if ($typ=="ASSOC") return mysql_fetch_assoc($result);
		return mysql_fetch_array($result);
	}
}

function multi_nextval($conn, $seq){
	if (_DB_SYSTEM_=="PGSQL"){
		$res = pg_query($conn, "SELECT nextval('$seq')");
		$ar  = pg_fetch_array($res,0);
		return $ar[0];
	}elseif(_DB_SYSTEM_=="MYSQL"){
		mysql_query("START TRANSACTION");
		mysql_query("UPDATE sequence SET value=value+1 WHERE name='$seq'", $conn);
		$res    = mysql_query("SELECT value FROM sequence WHERE name='$seq'", $conn);
		$lastID = mysql_fetch_array($res);
		$lastID = $lastID["value"];
		mysql_query("COMMIT");
		return $lastID;
	}
}

?>
