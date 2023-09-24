<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

ob_start();
set_time_limit(0);
session_name("ALBEORD");
session_start();
define("_DB_SYSTEM_", "MYSQL");
error_reporting(E_ALL & ~E_NOTICE);

include("lib/multidatabase.php");
include("lib/function.php");

if (strstr($_SERVER['SERVER_SOFTWARE'], "Zend Core") !== false) {
    $dbh = multi_connect("localhost", "root", "root", "albeord");
} else {
    $dbh = multi_connect("localhost", "root", "root", "albeord");
}

if (!$dbh) abort("Non posso conettermi al DB: " . mysqli_error());


$GlobPrivilegi = array(
    "ordini" => "Ordini",
    "conti" => "Check Out",
    "admin" => "Amministrazione",
    "utenti" => "Utenti",
    "schede" => "Schede",
    "stats" => "Statistiche",

    "logs" => "Archivio",
);
$GlobStati = array(
    "1" => "Pagato",
    "0" => "Da Pagare",
    "2" => "Stornato",
);


//if (!$_SESSION["priv"]){
$_SESSION["priv"] = multi_single_query($dbh, "SELECT privilegi FROM utenti WHERE id=" . intval($_COOKIE["albeordlogged"]));
$_SESSION["priv"] = unserialize($_SESSION["priv"]);
//}

$PRIVILEGI = $_SESSION["priv"];
if (!$SKIP) {
    if (!is_array($PRIVILEGI) | (is_array($PRIVILEGI) & sizeof($PRIVILEGI) == 0)) {
        ?>
        <META HTTP-EQUIV=Refresh CONTENT="0;URL=index.php"><?php
        exit;
    }
    if (!$_COOKIE["albeordlogged"]) {
        redirect_to("index.php?logout=1");
    }
}
foreach ($_REQUEST as $key => $val) {
    if (!is_array($val)) {
        $_REQUEST[$key] = trim($val);
    } else {
        foreach ($val as $subkey => $subval) {

        }
    }
}


?>
