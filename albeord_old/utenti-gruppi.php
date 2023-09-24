<?php
if (!$PRIVILEGI["utenti"]) redirect_to("home.php");

if (strlen($_REQUEST["invia"])>0){

	foreach ($_REQUEST["nome"] as $id => $nome){
		if ($id==0 & strlen($nome)>0){
			$IDAssegnato = multi_nextval($dbh, "gruppi");
			multi_query($dbh, "INSERT INTO gruppi (id, nome) VALUES ($IDAssegnato,'".$_REQUEST["nome"][$id]."')");
		}elseif($_REQUEST["elimina"][$id]==1){
			multi_query($dbh, "DELETE FROM gruppi WHERE id=$id");
		}else{
			multi_query($dbh, "UPDATE gruppi SET nome='".$_REQUEST["nome"][$id]."' WHERE id=$id");
		}
	}
}
?>
<form method='POST'>
<table cellspacing='0' cellpadding='3' width='550'>
	<tr bgcolor='#cccccc'><td>Gruppo</td><td>Elimina</td></tr>
		<?php
		$res=multi_query($dbh, "SELECT * FROM gruppi order by nome");
		for ($i=0; $i<multi_num_rows($res); $i++){
			$data=multi_fetch_array($res, $i);
		?>
		<tr>
			<td><input type='text' size='40' name='nome[<?= $data["id"] ?>]' value='<?= htmlentities($data["nome"],ENT_QUOTES); ?>' class="inptext"></td>
			<td><input type='checkbox' name='elimina[<?= $data["id"] ?>]' value='1'></td>
		</tr>
		<?php }	?>
		<tr bgcolor='#cccccc'>
			<td align='center' colspan='2'>Nuovo gruppo</td>
		</tr>
		<tr>
			<td><input type='text' name='nome[0]' value='' size='40' class="inptext"></td>
			<td>&nbsp;</td>
		</tr>
			<tr bgcolor='#cccccc'>
			<td align='center' colspan='2'><input type='submit' name='invia' value='  Salva  ' class="inpbtn"></td>
		</tr>
</table>
</form>
<br>
</form>
