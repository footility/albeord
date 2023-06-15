<?
include("core-config.php");

if (!$PRIVILEGI["ordini"]) redirect_to("home.php");

if ($_REQUEST["camera_submit"]){

	if ($_REQUEST["camera"]>0 & strlen($_REQUEST["datiordine"])>5){

			$IDOrdine = multi_nextval($dbh, "ordini");

			foreach (explode("|", $_REQUEST["datiordine"]) as $stringa){

				list($ida, $articolo, $quantita, $prezzo) = explode('@', $stringa);
				for ($i=0; $i<intval($quantita); $i++){
					multi_query($dbh, "INSERT INTO ordini (idordine, idutente, ora, camera, articolo, prezzo) VALUES ($IDOrdine, ".$_COOKIE["albeordlogged"].", ".time()." , '".$_REQUEST["camera"]."', '$articolo', $prezzo)");
				}

			}

	}
if (!$IDOrdine){
	print_r($_REQUEST);
	$IDOrdine=0;
}

?>



<? head_page() ?>
<? top_menu() ?><br>
<? if ($_REQUEST["stampa"]==1) stampawbs($dbh, $IDOrdine, 1); ?>
		<?
			$ordine=multi_single_query($dbh, "SELECT * FROM ordini WHERE idordine=$IDOrdine LIMIT 1",ALL);
			echo "<table cellspacing='0' cellpadding='3' width='50%' bgcolor='#ffffff' align='center'>
							<tr><td colspan='2'><b>Utente:</b> ".$ordine["idutente"]."</td><td colspan='2'><b>Data ordine:</b> ".date("d-m-Y H:i",$ordine["ora"])."</td></tr>
							<tr><td colspan='4' bgcolor='#cccccc' align='center'>Riepilogo ordine camera: <b>".$ordine["camera"]."</b></td></tr>
							<tr style='font-weight:bold'><td style='border-bottom:1px solid #000000'>Quantita</td><td style='border-bottom:1px solid #000000' colspan='2'>Articolo</td><td  style='border-bottom:1px solid #000000' align='right'>Prezzo</td></tr>";

			$res      = multi_query($dbh, "SELECT * FROM ordini WHERE idordine=$IDOrdine");
			$Articoli = array();
			for ($i=0; $i<multi_num_rows($res); $i++){
				$data=multi_fetch_array($res, $i);
				$id=md5($data["articolo"]);
				$Articoli[$id]["articolo"]    = $data["articolo"];
				$Articoli[$id]["quantita"]++;
				$Articoli[$id]["prezzo"]  = $data["prezzo"];
			}
			$totale=0;
			foreach ($Articoli as $data){
				$totale+=($data["prezzo"]*$data["quantita"]);
				echo "<tr><td>".$data["quantita"]."</td><td colspan='2' nowrap>".$data["articolo"]."</td><td align='right'>".show_prezzo($data["prezzo"]*$data["quantita"])." &euro;</td></tr>";
			}
			echo "<tr><td bgcolor='#cccccc' colspan='3' align='right'>Totale</td><td align='right' bgcolor='#cccccc'><b>".show_prezzo($totale)." &euro;</b></td></tr>";
			echo "</table>";
		?>
</body>
</html>
<? } ?>
