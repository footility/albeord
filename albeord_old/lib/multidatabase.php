<?php
function multi_connect($host, $user, $pwd, $db){

	if (_DB_SYSTEM_=="PGSQL"){
		return pg_connect("host=$host dbname=$db user=$user password=$pwd");
	}elseif(_DB_SYSTEM_=="MYSQL"){
		$conn = mysqli_connect($host, $user, $pwd);
		mysqli_select_db($conn, $db);
		return $conn;
	}
}
function multi_query($conn, $query){
	if (!$conn) return false;

	$GLOBALS["QUERY_CNT"]++;

	if (_DB_SYSTEM_=="PGSQL"){
		return pg_query($conn, $query);
	}elseif(_DB_SYSTEM_=="MYSQL"){
		$result = mysqli_query($conn,$query);
		if (!$result & $GLOBALS["_NOERR"]!=1){
		 echo "\n mysqli_QUERY <b>ERRORE:</b> ".mysqli_error($conn)."\n <b>QUERY:</b> $query\n";
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
		return mysqli_num_rows($result);
	}
}

function multi_fetch_array($result, $rs=0, $typ="BOTH"){
	if (_DB_SYSTEM_=="PGSQL"){
		return pg_fetch_array($result, $rs);
	}elseif(_DB_SYSTEM_=="MYSQL"){
		mysqli_data_seek($result,$rs);
		if ($typ=="NUM")  return mysqli_fetch_row($result);
		if ($typ=="BOTH") return mysqli_fetch_array($result);
		if ($typ=="ASSOC") return mysqli_fetch_assoc($result);
		return mysqli_fetch_array($result);
	}
}

function multi_nextval($conn, $seq){
	if (_DB_SYSTEM_=="PGSQL"){
		$res = pg_query($conn, "SELECT nextval('$seq')");
		$ar  = pg_fetch_array($res,0);
		return $ar[0];
	}elseif(_DB_SYSTEM_=="MYSQL"){
		mysqli_query("START TRANSACTION");
		mysqli_query("UPDATE sequence SET value=value+1 WHERE name='$seq'", $conn);
		$res    = mysqli_query("SELECT value FROM sequence WHERE name='$seq'", $conn);
		$lastID = mysqli_fetch_array($res);
		$lastID = $lastID["value"];
		mysqli_query("COMMIT");
		return $lastID;
	}
}

?>
