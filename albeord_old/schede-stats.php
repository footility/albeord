<?
include("core-config.php");

if (!$PRIVILEGI["schede"]) redirect_to("home.php");

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
		<td colspan="5">Data di partenza compresa tra: </td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td>Dal</td>
		<td><? disegna_cal("d_g","d_m", "d_a", unixtojd($_SESSION["dal_stat"])) ?></td>
		<td>Al</td>
		<td><? disegna_cal("a_g","a_m", "a_a", unixtojd($_SESSION["al_stat"])) ?></td>
		<td rowspan="2"><input type='submit' name='invia' class="btnsrc" value='Calcola' style="border:1px solid #000000; background-color:#ffffff"></td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td>Titolare</td>
		<td><input class="inptext" autocomplete="off" type="text" name="titolare" value="<?= $_REQUEST["titolare"] ?>" /></td>
		<td>Camera</td>
		<td><input class="inptext" autocomplete="off" type="text" name="camera"  value="<?= $_REQUEST["camera"] ?>"  /></td>
	</tr>
	</form>
</table>
<br>
<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc" bgcolor="#FFFFFF">
	
	<?
	if($_SESSION["dal_stat"] && $_SESSION["al_stat"]){
		$q.= " AND id IN (
							SELECT idscheda 
							FROM (SELECT idscheda, min( dal ) AS dal, max( al ) AS al FROM schede_periodi GROUP BY idscheda) as ct 
							WHERE  al BETWEEN ".$_SESSION["dal_stat"]." AND ".$_SESSION["al_stat"]." )";
	}

	$full_qu= "SELECT schede.*, 
							(SELECT min(al) FROM schede_periodi WHERE idscheda=schede.id) as al
 						FROM schede 
 						WHERE stato=1 
 						AND titolare LIKE '%".$_REQUEST["titolare"]."%' 
 						AND camera LIKE '%".$_REQUEST["camera"]."%' $q 
						ORDER BY al";

	$res  = mysql_query($full_qu);

	if (!mysql_num_rows($res)){
		echo "<tr><td align='center' bgcolor='#ffffff' style='border:0px solid #000000; padding:3px'>Nessuna scheda trovata</td></tr>";
	}
	$totalePeriodo = 0;
	while ($scheda = mysql_fetch_assoc($res)){
		
		$scheda["dal"]  = multi_single_query($dbh, "SELECT min(dal) FROM schede_periodi where idscheda=".$scheda["id"]);
		$scheda["al"]  =  multi_single_query($dbh, "SELECT max(al) FROM schede_periodi where idscheda=".$scheda["id"]);
		$cnt++;
		?>
			<tr bgcolor="#CCCCCC" style="font-weight:bold">
				<td colspan="2">Camera: <?= $scheda["camera"] ?></td>

				<td colspan="2">Titolare: <?= $scheda["titolare"] ?></td>

			</tr>
			<tr bgcolor="#ffffff">
				<td colspan="2">Dal: <?= date("d-m-Y",$scheda["dal"] )?></td>

				<td colspan="2">Al: <?= date("d-m-Y",$scheda["al"] ) ?></td>

			</tr>
			<tr bgcolor="#ffffff" style="font-weight:bold">
				<td colspan="4"><? include("schede-riepilogo.php"); ?></td>
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
	<tr bgcolor="#CCCCCC" style="font-weight:bold">
		<td colspan="3">Totale</td>

		<td align="right">-<?= show_prezzo($sotrazioniTotale) ?> &euro;    / <?= show_prezzo($totalePeriodo) ?> &euro;</td>

	</tr>
	<?php if ($totalePeriodo){?>
	<tr>
		<td colspan="4" align="right">
			<input type='submit' name='invia' class="btnsrc" value='Stampa tutto' onclick="window.print()" style="border:1px solid #000000; background-color:#ffffff">
		</td>
	</tr>	
	<?php } ?>
</table>

</body>
</html>
