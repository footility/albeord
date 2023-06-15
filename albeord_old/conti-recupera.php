<?
include("core-config.php");

if (!$PRIVILEGI["conti"]) redirect_to("home.php");

head_page();

top_menu();

$ST[1]="Prenottamento e colazione";
$ST[2]="Mezza Pensione";
$ST[3]="Pensione Completa";

if(!$_SESSION["dal_stat"]){
	$_SESSION["dal_stat"]=time();
	$_SESSION["al_stat"]=strtotime("+30 days");
}

if ($_REQUEST["invia"]){
	$_SESSION["dal_stat"] = mktime(0,0,1, $_REQUEST["d_m"], $_REQUEST["d_g"],$_REQUEST["d_a"]);

	$_SESSION["al_stat"]  = mktime(23,59,59, $_REQUEST["a_m"], $_REQUEST["a_g"],$_REQUEST["a_a"]);
	if(intval($_REQUEST["d_a"])<2000){
		$_SESSION["dal_stat"]=$_SESSION["al_stat"]=0;
	}
}
$instat=1;
if($_REQUEST["stampa_singola"]){
	stampawbs($dbh, $_REQUEST["idscheda"], 10);
}
?>

<br>
<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc">
	<form method='POST' action='<?= $_SERVER["PHP_SELF"] ?>'>
	<tr bgcolor='#ffffff'>
		<td colspan="5">Data di checkout compresa tra: </td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td>Dal</td>
		<td><? disegna_cal("d_g","d_m", "d_a", unixtojd($_SESSION["dal_stat"])) ?></td>
		<td>Al</td>
		<td><? disegna_cal("a_g","a_m", "a_a", unixtojd($_SESSION["al_stat"])) ?></td>
		<td rowspan="2"><input type='submit' name='invia' class="btnsrc" value='Calcola' style="border:1px solid #000000; background-color:#ffffff"></td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td>Camera</td>
		<td colspan="3"><input class="inptext" autocomplete="off" type="text" name="camera"  value="<?= $_REQUEST["camera"] ?>"  /></td>
	</tr>
	</form>
</table>
<br>
<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc" bgcolor="#FFFFFF">
	
	<?
	if($_SESSION["dal_stat"] && $_SESSION["al_stat"]){
		$q.= " AND oraoperazione BETWEEN ".$_SESSION["dal_stat"]." AND ".$_SESSION["al_stat"]." ";
	}

	$full_qu= "SELECT ordini.*,(SELECT utente FROM utenti WHERE utenti.id = ordini.idutente) AS utente
 						FROM ordini 
 						WHERE stato=1  AND idoperazione>0
 						AND camera LIKE '".$_REQUEST["camera"]."%' $q
 						GROUP BY idoperazione 
						ORDER BY oraoperazione DESC";

	$res  = mysql_query($full_qu);

	if (!mysql_num_rows($res)){
		echo "<tr><td align='center' bgcolor='#ffffff' style='border:0px solid #000000; padding:3px'>Nessuna scheda trovata</td></tr>";
	}

	while ($scheda = mysql_fetch_assoc($res)){
		
		$cnt++;
		?>
			<tr bgcolor="#CCCCCC" style="font-weight:bold">
				<td>Camera: <?= $scheda["camera"] ?></td>
				<td>Checkout: <?= date("d-m-Y",$scheda["oraoperazione"] )?></td>
				<td>Utente: <?= $scheda["utente"]?></td>
			</tr>
			<tr>
				<td colspan="3">
					<table width="50%" cellspacing="0">
						<tr>
							<th style="font-size: 10px; text-align: left;">Articolo</th>
							<th style="font-size: 10px; text-align: right;">Quantit&agrave;</th>
							<th style="font-size: 10px; text-align: right;">Prezzo</th>
						</tr>
					<?php
						$full_qu= "
						SELECT ordini.articolo, count(ordini.id) AS quantita ,sum(ordini.prezzo) AS prezzo 
						 FROM ordini  WHERE stato=1  AND idoperazione=".$scheda["idoperazione"]." GROUP BY ordini.articolo ORDER BY articolo, ora";
						$resord  = mysql_query($full_qu);
						$dataTot = array();
						while ($dataArt = mysql_fetch_assoc($resord)){
							$dataTot["quantita"]+= $dataArt["quantita"];
							$dataTot["prezzo"]+= $dataArt["prezzo"];
							?>
							<tr>
								<td><?= htmlentities($dataArt["articolo"]) ?></td>
								<td align="right"><?= $dataArt["quantita"] ?></td>
								<td align="right"><?= show_prezzo($dataArt["prezzo"]) ?>&euro;</td>
							</tr>
							<?php 
						}
					?>
						<tr bgcolor="#eeeeee">
							<td>Totale</td>
							<td align="right"><?= $dataTot["quantita"] ?></td>
							<td align="right"><?= show_prezzo($dataTot["prezzo"]) ?>&euro;</td>
						</tr>
					</table>
				</td>
			</tr>
			<?
		?>

		<? if($cnt<mysql_num_rows($res)){ ?>
		<tr bgcolor="#ffffff">
				<td colspan="4">&nbsp;<br />&nbsp;</td>
			</tr>
		<? } ?>
		<?
	}
	?>
</table>

</body>
</html>
