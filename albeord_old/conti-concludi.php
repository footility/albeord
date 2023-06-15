<?
include("core-config.php");

if (!$PRIVILEGI["conti"]) redirect_to("home.php");
if (!$_REQUEST["camera"]) redirect_to("conti-core.php");

$t = time();
$IDOperazione = multi_nextval($dbh, "operazioni");
$Stato        = intval($_REQUEST["azione"]);
$addq         = " modificatoda='".$_COOKIE["albeordlogged"]."',oraoperazione=$t,";

if(intval($_REQUEST["azione"])==3){//preventivo no pagamento!
    $Stato = 0;
    $addq  = "";
}

foreach (explode(",", $_REQUEST["articoli"]) as $ido){
	if ($ido>0) multi_query($dbh, "UPDATE ordini SET stato=$Stato, $addq  idoperazione=$IDOperazione WHERE id=".intval($ido));
}

?>


<? head_page() ?>
<? top_menu() ?><br>
<? if ($_REQUEST["stampa"]==1) 
			stampawbs($dbh, $IDOperazione , 2);
			echo "Ok";
			
			
			
			


		?>
		


	
</body>
</html>










