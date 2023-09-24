<?php
if (!$PRIVILEGI["admin"]) redirect_to("home.php");


if($_REQUEST["schede"]){
	$idsSchede = array_filter(array_map("intval",$_REQUEST["schede"]), 'intval');
}
if($_REQUEST["ordini"]){
	$idsOrdini = array_filter(array_map("intval",$_REQUEST["ordini"]), 'intval');
}

//$percorso = trovaUsb();
$percorso = "C:\Schede Esportate Albeord";

function fetch_all($res){
	$r=array();
	while($data =mysqli_fetch_assoc($res)){
		$r[]=$data;
	}
	return $r;
}

include_once("lib/tar.class.php");
function elimina_scheda($id){
	global $dbh;
	multi_query($dbh, "DELETE FROM schede_trattamenti_base WHERE idscheda =".$id );
	multi_query($dbh, "DELETE FROM schede_servizi WHERE idscheda =".$id );

	multi_query($dbh, "DELETE FROM schede_presenze WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda  =".$id.")" );
	multi_query($dbh, "DELETE FROM schede_prezzi_bambini WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda =".$id.")" );
	multi_query($dbh, "DELETE FROM schede_supplementi WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda =".$id.")" );

	multi_query($dbh, "DELETE FROM schede_periodi WHERE idscheda =".$id );
	multi_query($dbh, "DELETE FROM schede WHERE id=".$id );
}
function elimina_ordine($id){
	global $dbh;
	multi_query($dbh, "DELETE FROM ordini WHERE idoperazione = $id");
}

function elimina_esportate(){
	global $dbh;
	$resP=mysqli_query("SELECT id FROM schede WHERE stato=1 AND backup = 1");

	while($data=mysqli_fetch_assoc($resP)){
		elimina_scheda($data["id"] );
	}
	array_map("unlink",glob("temp/*"));

	mysqli_query("DELETE FROM ordini WHERE backup  = 1");
}

if ($_REQUEST["elimina_esportate"]) {
	elimina_esportate();
}

if ($_REQUEST["esporta_schede"] && (count($idsSchede) || count($idsOrdini)) && $percorso) {
	array_map("unlink",glob("temp/*"));
    $tar = new Tar();
	$idss = array();
	$idso = array();
    $fname ="esp-".date("d-m-Y_H-i-s")."_".uniqid().".tgz";
	if($idsSchede){
	    $resP=mysqli_query("SELECT * FROM schede WHERE stato=1 AND (backup = 0 or backup is null) AND id IN (".implode(",",$idsSchede).")");

		while($data=mysqli_fetch_assoc($resP)){
			$final = array();
			$idss[$data["id"] ]= $data["id"] ;
			$final["schede"][]=$data;

			$res=multi_query($dbh, "SELECT * FROM schede_periodi WHERE idscheda=".$data["id"] );
			$final["schede_periodi"]= fetch_all($res);

			$res=multi_query($dbh, "SELECT * FROM schede_trattamenti_base WHERE idscheda=".$data["id"] );
			$final["schede_trattamenti_base"]=fetch_all($res);

			$res=multi_query($dbh, "SELECT * FROM schede_servizi WHERE idscheda=".$data["id"] );
			$final["schede_servizi"]=fetch_all($res);

			$res=multi_query($dbh, "SELECT * FROM schede_supplementi WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda=".$data["id"].")" );
			$final["schede_supplementi"]=fetch_all($res);

			$res=multi_query($dbh, "SELECT * FROM schede_prezzi_bambini WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda=".$data["id"].")"  );
			$final["schede_prezzi_bambini"]=fetch_all($res);

			$res=multi_query($dbh, "SELECT * FROM schede_presenze WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda=".$data["id"].")"  );
			$final["schede_presenze"]=fetch_all($res);

			$sugg = preg_replace("/[^a-z0-9_\\-]/i", "-",$data["camera"]."_".$data["titolare"]);
			$name="temp/sch".$data["id"]."__".$sugg.".ser";

			file_put_contents($name,serialize($final));
			$tar->addFile($name);
		}
	}
	if($idsOrdini){
		$resP=mysqli_query("SELECT idoperazione, camera,oraoperazione FROM ordini WHERE idoperazione IN (".implode(",",$idsOrdini).") GROUP BY idoperazione");

		while($row=mysqli_fetch_assoc($resP)){
			$resPt=mysqli_query("SELECT * FROM ordini WHERE idoperazione =".$row["idoperazione"]);

			while($data=mysqli_fetch_assoc($resPt)){
				$final = array();
				$final["ordini"][]=$data;
				$idso[$row["idoperazione"]]=$row["idoperazione"];
				$name="temp/ord".$row["idoperazione"]."__".$sugg.".ser";
				file_put_contents($name,serialize($final));

				$sugg = preg_replace("/[^a-z0-9_\\-]/i", "-",$row["camera"]."_".date("d-m-Y",$row["oraoperazione"]));

				$tar->addFile($name);
			}
		}
	}
    $tar->toTar("temp/$fname",true);

	array_map("unlink",glob("temp/*.ser"));

	if($tar->numFiles && copy("temp/$fname",$percorso."/".$fname)){
		echo "<p style='color:green'>Esportazione di " .$tar->numFiles." voci completata ($fname)</p>";
		foreach ($idss as $idsch){
			elimina_scheda($idsch);
		}
		foreach ($idso as $idsch){
			elimina_ordine($idsch);
		}
	}elseif($tar->numFiles){
		echo "<h2 color='red'>Non posso scrivere i file di backup. Controllare che il supporto ($fname) non sia pieno o protetto da scrittura.</h2>";
	}else{
		echo "<p>Nessuna scheda da esportare</p>";
	}
	array_map("unlink",glob("temp/*"));

}

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


?>

<h2>Cerca le schede per il backup</h2>
<form method='POST' action='<?= $_SERVER["PHP_SELF"] ?>'>
	<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc">

		<tr bgcolor='#ffffff'>
			<td colspan="5">Data di partenza compresa tra: </td>
		</tr>
		<tr bgcolor='#ffffff'>
			<td>Dal</td>
			<td><?php disegna_cal("d_g","d_m", "d_a", unixtojd($_SESSION["dal_stat"])) ?></td>
			<td>Al</td>
			<td><?php disegna_cal("a_g","a_m", "a_a", unixtojd($_SESSION["al_stat"])) ?></td>
			<td rowspan="2"><input type='submit' name='invia' class="btnsrc" value='Cerca' style="border:1px solid #000000; background-color:#ffffff"></td>
		</tr>
		<tr bgcolor='#ffffff'>
			<td>Titolare</td>
			<td><input class="inptext" autocomplete="off" type="text" name="titolare" value="<?= $_REQUEST["titolare"] ?>" /></td>
			<td>Camera</td>
			<td><input class="inptext" autocomplete="off" type="text" name="camera"  value="<?= $_REQUEST["camera"] ?>"  /></td>
		</tr>


	</table>
</form>

<form method='POST' action='<?= $_SERVER["PHP_SELF"] ?>'>
	<input type="hidden" name="camera" value="<?= $_REQUEST["camera"] ?>"/>
	<input type="hidden" name="titolare" value="<?= $_REQUEST["titolare"] ?>"/>

	<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc">
		<tr bgcolor='#ffffff'>
			<td align="right">
				<?phpphp if($percorso){ ?>
				<input type='submit' name='esporta_schede' class="btnsrc" value='Esporta selezionate' style="border:1px solid #000000; background-color:#ffffff">
				<?phpphp } ?>
				<!-- <input type='submit' name='elimina_schede' onclick="return confirm('Confermi?')" class="btnsrc" value='Elimina selezionate' style="border:1px solid #000000; background-color:#ffffff"> -->
				<input type='submit' name='elimina_esportate' onclick="return confirm('Confermi?')" class="btnsrc" value='Pulisci archivio' style="border:1px solid #000000; background-color:#ffffff">
			</td>
		</tr>
</table>
<?phpphp
if(!$percorso){
	echo "<p style='color:red'>Non trovo il drive per l'esportazione delle schede</p>";
}
?>
<br>
<script type="text/javascript">

function selezTutti(t){
	n = document.getElementsByTagName("input");
	for ( var i = 0; i < n.length; i++) {
		var el = n[i];
		if(el.name.indexOf(t)!=-1){
			el.checked = !el.checked;
		}
	}
}

</script>
<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc" class="esp" bgcolor="#FFFFFF">
	<caption>Schede</caption>
	<tr>
		<th align="left">Camera</th>
		<th align="left">Titolare</th>
		<th align="left">Dal</th>
		<th align="left" width="120">Al</th>
		<th align="left" width="100">Esporta<br/><a style="font-size: 10px" href="javascript://" onclick="selezTutti('schede[')">Inv selez.</a></th>
	</tr>

	<?php
	$q.="";
	if($_SESSION["dal_stat"] && $_SESSION["al_stat"]){
		$q.= " AND id IN (
							SELECT idscheda
							FROM (SELECT idscheda, min( dal ) AS dal, max( al ) AS al FROM schede_periodi GROUP BY idscheda) as ct
							WHERE  al BETWEEN ".$_SESSION["dal_stat"]." AND ".$_SESSION["al_stat"]." )";
	}
	if($_REQUEST["giabackup"]){
		$q.= " AND backup =1 ";
	}
	$full_qu= "SELECT schede.*,
							(SELECT min(al) FROM schede_periodi WHERE idscheda=schede.id) as al
 						FROM schede
 						WHERE stato=1
 						AND titolare LIKE '%".$_REQUEST["titolare"]."%'
 						AND camera LIKE '%".$_REQUEST["camera"]."%' $q
						ORDER BY al";

	$res  = mysqli_query($full_qu);

	if (!mysqli_num_rows($res)){
		echo "<tr><td colspan='5' align='center' bgcolor='#ffffff' style='border:0px solid #000000; padding:3px'>Nessuna scheda trovata</td></tr>";
	}
	$totalePeriodo = 0;
	while ($scheda = mysqli_fetch_assoc($res)){

		$scheda["dal"]  = multi_single_query($dbh, "SELECT min(dal) FROM schede_periodi where idscheda=".$scheda["id"]);
		$scheda["al"]  =  multi_single_query($dbh, "SELECT max(al) FROM schede_periodi where idscheda=".$scheda["id"]);
		$cnt++;
		?>
			<tr class="<?phpphp  if($cnt%2==0) echo "pari" ?> <?phpphp  if($scheda["backup"]) echo "isbackup" ?>">
				<td ><?= $scheda["camera"] ?></td>

				<td><?= $scheda["titolare"] ?></td>

				<td> <?= date("d-m-Y",$scheda["dal"] )?></td>

				<td><?= date("d-m-Y",$scheda["al"] ) ?></td>
				<td>
					<?phpphp  if(!$scheda["backup"]){ ?>
					<input type="checkbox" class="<?phpphp  if($scheda["backup"]) echo "isbackup" ?>" name="schede[<?phpphp echo $scheda["id"] ?>]" <?phpphp if($_REQUEST["schede"][$scheda["id"]]) echo "checked='checked'"; ?> value="<?phpphp echo $scheda["id"] ?>"/>
					<?phpphp } ?>
				</td>
			</tr>
			<?php
	}
?>
</table>

<table class="esp" width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc" bgcolor="#FFFFFF">
	<caption>Ordini</caption>
	<tr>
		<th align="left">Camera</th>
		<th align="left"  width="120">Checkout</th>
		<th align="left" width="100">Esporta<br/><a style="font-size: 10px" href="javascript://" onclick="selezTutti('ordini[')">Inv selez.</a></th>
	</tr>
	<?php
	$q = '';
	if($_SESSION["dal_stat"] && $_SESSION["al_stat"]){
		$q.= " AND oraoperazione BETWEEN ".$_SESSION["dal_stat"]." AND ".$_SESSION["al_stat"]." ";
	}
	if($_REQUEST["giabackup"]){
		$q.= " AND backup =1 ";
	}
	$full_qu= "SELECT ordini.*
 				FROM ordini
 				WHERE stato=1  AND idoperazione>0
 				AND camera LIKE '%".$_REQUEST["camera"]."%' $q
 				GROUP BY idoperazione
				ORDER BY oraoperazione DESC";

	$res  = mysqli_query($full_qu);

	if (!mysqli_num_rows($res)){
		echo "<tr><td align='center' bgcolor='#ffffff' style='border:0px solid #000000; padding:3px'>Nessuna scheda trovata</td></tr>";
	}
	while ($scheda = mysqli_fetch_assoc($res)){

		$cnt++;
		?>
		<tr  class="<?phpphp  if($cnt%2==0) echo "pari" ?> <?phpphp  if($scheda["backup"]) echo "isbackup" ?>">
			<td><?= $scheda["camera"] ?></td>
			<td><?= date("d-m-Y",$scheda["oraoperazione"] )?></td>
			<td>
				<?phpphp  if(!$scheda["backup"]){ ?>
				<input type="checkbox" class="<?phpphp  if($scheda["backup"]) echo "isbackup" ?>" name="ordini[<?phpphp echo $scheda["idoperazione"] ?>]" <?phpphp if($_REQUEST["ordini"][$scheda["idoperazione"]]) echo "checked='checked'"; ?> value="<?phpphp echo $scheda["idoperazione"] ?>"/>
				<?phpphp } ?>
			</td>
		</tr>

		<?php
	}
	?>
</table>
</form>


