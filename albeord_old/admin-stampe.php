<?
if (!$PRIVILEGI["admin"]) redirect_to("home.php");

if ($_REQUEST["invia"]){
	foreach ($_FILES as $key => $val){
		if ($val['error']==0 & $val['size']>0){
			move_uploaded_file($val['tmp_name'],"stampe/logo.bmp");
		}
	}
	foreach ($_REQUEST["dati"] as $k=> $v){
		$_REQUEST["dati"][$k]=stripslashes($v);
	}
	file_put_contents("stampe/dati.ser", serialize($_REQUEST["dati"]));
}



$data=unserialize(@file_get_contents("stampe/dati.ser"));
?>
<form method='POST' enctype='multipart/form-data'>
<table cellspacing='0' cellpadding='3' width='550'>
		<tr><td bgcolor='#cccccc' nowrap>Logo</td><td><input type='file' name='logo'><br>
		Dimensione massima: <b>200x200 px</b><br>
		Formato: <b>Widows Bitmap</b>
		<br>
			<?
				if (is_file("stampe/logo.bmp")) echo "<img style='border:1px solid #000000' src='stampe/logo.bmp'>";
			?>
		</tr>
		<tr>
			<td bgcolor='#cccccc'>Ragione Sociale</td>
			<td><input class="inptext"  type="text" name="dati[ragsoc]"  value="<?= htmlentities($data["ragsoc"],ENT_QUOTES); ?>" size="40"/></td>	
		</tr>
		<tr>
			<td bgcolor='#cccccc'>Indirizzo (via, n)</td>
			<td><input class="inptext"  type="text" name="dati[indirizzo1]" value="<?= htmlentities($data["indirizzo1"],ENT_QUOTES); ?>" size="40"/></td>	
		</tr>
		<tr>
			<td bgcolor='#cccccc'>Indirizzo (cap, paese, provincia)</td>
			<td><input class="inptext"  type="text" name="dati[indirizzo2]" value="<?= htmlentities($data["indirizzo2"],ENT_QUOTES); ?>" size="40"/></td>	
		</tr>		
		<tr>
			<td bgcolor='#cccccc'>Tel</td>
			<td><input class="inptext"  type="text" name="dati[tel]"   value="<?= htmlentities( $data["tel"],ENT_QUOTES); ?>" size="40"/></td>	
		</tr>
		<tr>
			<td bgcolor='#cccccc'>Fax</td>
			<td><input class="inptext"  type="text" name="dati[fax]"  value="<?= htmlentities($data["fax"],ENT_QUOTES); ?>"  size="40"/></td>	
		</tr>
		<tr>
			<td bgcolor='#cccccc'>Extra</td>
			<td><input class="inptext"  type="text" name="dati[extra]"  value="<?= htmlentities($data["extra"],ENT_QUOTES); ?>"  size="40"/></td>	
		</tr>
		<tr bgcolor='#cccccc'>
			<td align='center' colspan='3'><input type='submit' name='invia' value='  Salva  ' class="inpbtn"></td>
		</tr>
</table>
</form>
<br>
</form>
