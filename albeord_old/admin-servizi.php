<?php
if (!$PRIVILEGI["utenti"]) redirect_to("home.php");

if (strlen($_REQUEST["invia"])>0){

	foreach ($_REQUEST["nome"] as $id => $nome){
		if ($id==0 & strlen($nome)>0){
			multi_query($dbh, "INSERT INTO servizi (nome, tipo) VALUES ('".$_REQUEST["nome"][$id]."','".intval($_REQUEST["tipo"][$id])."')");
		}elseif($_REQUEST["elimina"][$id]==1){
			multi_query($dbh, "DELETE FROM servizi WHERE id=$id");
		}else{
			multi_query($dbh, "UPDATE servizi SET nome='".$_REQUEST["nome"][$id]."',tipo='".intval($_REQUEST["tipo"][$id])."' WHERE id=$id");
		}
	}
}
?>
<form method='POST'>
<table cellspacing='0' cellpadding='3' width='550'>
	<tr bgcolor='#cccccc'><td>Servizio</td><td>Tipo</td><td>Elimina</td></tr>
		<?php
		$res=multi_query($dbh, "SELECT * FROM servizi order by tipo, nome");
		for ($i=0; $i<multi_num_rows($res); $i++){
			$data=multi_fetch_array($res, $i);
		?>
		<tr>
			<td><input type='text' size='40' name='nome[<?= $data["id"] ?>]' value='<?= htmlentities($data["nome"],ENT_QUOTES); ?>' class="inptext"></td>
			<td>
				<select name='tipo[<?= $data["id"] ?>]'>
					<option value="1" <?= ($data["tipo"]==1)?"selected":"" ?>>+</option>
					<option value="2" <?= ($data["tipo"]==2)?"selected":"" ?>>-</option>
				</select>
			</td>
			<td><input type='checkbox' name='elimina[<?= $data["id"] ?>]' value='1'></td>
		</tr>
		<?php }	?>
		<tr bgcolor='#cccccc'>
			<td align='center' colspan='3'>Nuovo servizio</td>
		</tr>
		<tr>
			<td><input type='text' name='nome[0]' value='' size='40' class="inptext"></td>
				<td>
				<select name='tipo[0]' >
					<option value="1">+</option>
					<option value="2">-</option>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
			<tr bgcolor='#cccccc'>
			<td align='center' colspan='3'><input type='submit' name='invia' value='  Salva  ' class="inpbtn"></td>
		</tr>
</table>
</form>
<br>
</form>
