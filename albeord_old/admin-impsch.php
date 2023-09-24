<?php
if (!$PRIVILEGI["admin"]) redirect_to("home.php");
include_once("lib/tar.class.php");

function quotize($v){
	return "'". addslashes($v)."'";
}
function array2replace($ary, $tbl){
	if($tbl=="schede"){
		$ary["backup"]=1;
	}elseif($tbl=="ordini"){
		$ary["backup"]=1;
	}
	return "REPLACE INTO $tbl (".implode(",",array_keys($ary)).") VALUES (".implode(",",array_map("quotize",$ary)).")";
}
function importFile($content){
	global $dbh;
	if($data=unserialize($content)){
		foreach ($data as $tbl=> $rows){
			foreach ($rows as  $row){
				$sql=array2replace($row,$tbl);
				multi_query($dbh,$sql);
			}
		}
		return true;
	}else{
		return false;
	}
}
function importTar($fname, $anno = 0){

	$parts = explode("_", basename($fname));
	// d-m-Y_H-i-s
	$partsData = explode("-", $parts[0]);
	$datafile = strtotime("$partsData[3]-$partsData[2]-$partsData[1]");
	if(date("Y",$datafile) == $anno || !$anno){
		echo "<center style='color:gray'>Imprtazione...</center>";
		if(filesize($fname)>0 && is_readable($fname)){
			 $tar = new Tar();
			 if($tar->openTar($fname)){
				if($tar->numFiles > 0) {
					foreach($tar->files as $id => $information) {
						if(preg_match("/\\.ser$/",$information["name"])){
							if(!importFile($information["file"]) ){
								echo "<center style='color:red'>Errore importazione file  ".basename($information["name"])." </center>";
							}else{
								echo "<center style='color:green'>Importato  ".basename($information["name"])." </center>";
							}
						}
					}
				} else {
					echo "<center style='color:gray'>File vuoto</center>";
				}

			 }else{
				echo "<center style='color:red'>Errore apertura file tar</center>";
			}
		}else{
			echo "<center style='color:red'>Errore apertura file</center>";
		}
	}
}
if($_REQUEST["imp"]){
	if($_FILES["file"] && $_FILES["file"]["size"]>0 && $_FILES["file"]["error"]==0){
		importTar($_FILES["file"]["tmp_name"], 0);
	}else{
		echo "<center style='color:red'>Errore caricamento file</center>";
	}
}

if($_REQUEST["imp_drive"]){
	//$percorso  = trovaUsb();
	$percorso = "C:\Schede Esportate Albeord";
	foreach (glob("$percorso/*.tgz") as $fname){
		echo "<center style='color:#638159'>Tar: $fname </center>";
		importTar($fname, intval($_REQUEST["anno"]));
	}
}

?>
<form method='POST' enctype='multipart/form-data'>
<table cellspacing='0' cellpadding='3' width='550'>
	<tr>
        <td bgcolor='#cccccc' nowrap colspan="3">Importazione</td>

	</tr>
	<tr>
		<td>File esportato</td>
        <td><input type="file" name="file"  /></td>

		<td><input type='submit' name='imp' value='  Importa  ' class="inpbtn"></td>
	</tr>
	<?phpphp
	//$percorso  = trovaUsb();
	$percorso = "C:\Schede Esportate Albeord";
	if($percorso ) {
		?>
		<tr>
	        <td bgcolor='#cccccc' nowrap colspan="3">Trovato drive di backup in <b><?phpphp echo $percorso; ?></b></td>
		</tr>

		<tr>
			  <td>


	        </td>
			<td colspan="3">
			Anno:
				<select name="anno">
		        	<?phpphp  foreach (range( date("Y"),2008) as $annoSel) {?>
		        	<option value="<?phpphp  echo $annoSel?>"><?phpphp  echo $annoSel?></option>
		        	 <?phpphp } ?>
		        	 <option value="0">Tutto</option>
		        </select>
				<input type='submit' name='imp_drive' value='  Importa automaticamente  ' class="inpbtn">

			</td>
		</tr>
	<?phpphp } ?>
</table>
</form>
