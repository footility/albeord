<?php

if (!$PRIVILEGI["admin"]) redirect_to("home.php");

function disegnacat($dbh, $idcatsupp=0, $tabs='', $sel=0){
	$res=multi_query($dbh, "SELECT * FROM categorie WHERE dipendenza=$idcatsupp order by nome");
	for($i=0;$i<multi_num_rows($res);$i++){
		$data=multi_fetch_array($res, $i);
		echo "<option value='".$data["id"]."' ".(($data["id"]==$sel)?"selected":"").">&nbsp;&nbsp;&nbsp;&nbsp;".$tabs.htmlentities($data["nome"])."</option>\n";
	}
}
if (strlen($_REQUEST["invia"])>0){

	foreach ($_REQUEST["nome"] as $id => $nome){
		if ($id==0 & strlen($nome)>0){
			$IDAssegnato = multi_nextval($dbh, "categorie");
			multi_query($dbh, "INSERT INTO categorie (id, dipendenza , nome) VALUES ($IDAssegnato, ".intval($_REQUEST["dipendente"][$id]).", '".$_REQUEST["nome"][$id]."')");
		}elseif($_REQUEST["elimina"][$id]==1){
			multi_query($dbh, "DELETE FROM categorie WHERE id=$id");
		}else{
			multi_query($dbh, "UPDATE categorie SET nome='".$_REQUEST["nome"][$id]."', dipendenza=".intval($_REQUEST["dipendente"][$id])." WHERE id=$id");
		}
	}
}
?>
<form method='POST'>
<table cellspacing='0' cellpadding='3' width='100%'>
	<tr bgcolor='#cccccc'><td>Categoria</td><td>Nome</td><td>Elimina</td></tr>
		<?php
		$res=multi_query($dbh, "SELECT * FROM categorie WHERE dipendenza=0 order by nome");
		for ($i=0; $i<multi_num_rows($res); $i++){
			$data=multi_fetch_array($res, $i);
		?>
		<tr>
			<td>
				<select name='dipendente[<?= $data["id"] ?>]'>
					<option value='0'>&nbsp;&nbsp;-</option>
					<?php disegnacat($dbh, 0,'',$data["dipendenza"]); ?>
				</select>
			</td>
			<td><input type='text' size='45' name='nome[<?= $data["id"] ?>]' value='<?= htmlentities($data["nome"],ENT_QUOTES); ?>' class="inptext"></td>
			<td><input type='checkbox' name='elimina[<?= $data["id"] ?>]' value='1'></td>
		</tr>
			<?php
			$res2=multi_query($dbh, "SELECT * FROM categorie WHERE dipendenza=".$data["id"]);
			for ($i2=0; $i2<multi_num_rows($res2); $i2++){
				$data=multi_fetch_array($res2, $i2);
			?>
			<tr bgcolor='#f2f2f2'>
				<td>&nbsp;
					<select name='dipendente[<?= $data["id"] ?>]'>
						<option value='0'>&nbsp;&nbsp;-</option>
						<?php disegnacat($dbh, 0,'',$data["dipendenza"]); ?>
					</select>
				</td>
				<td><input type='text' size='45' name='nome[<?= $data["id"] ?>]' value='<?= htmlentities($data["nome"], ENT_QUOTES); ?>' class="inptext"></td>
				<td><input type='checkbox' name='elimina[<?= $data["id"] ?>]' value='1'></td>
			</tr>
			<?php }	?>
		<?php }	?>
		<tr bgcolor='#cccccc'>
			<td align='center' colspan='3'>Nuova Categoria</td>
		</tr>
		<tr>
			<td>
				<select name='dipendente[0]'>
					<option value='0'>&nbsp;&nbsp;-</option>
					<?php disegnacat($dbh, 0,'',intval($_REQUEST["dipendente"][0])); ?>
				</select>
			</td>
			<td><input type='text' name='nome[0]' value='' size='45' class="inptext"></td>
			<td>&nbsp;</td>
		</tr>
			<tr bgcolor='#cccccc'>
			<td align='center' colspan='3'><input type='submit' name='invia' value='  Salva  ' class="inpbtn"></td>
		</tr>
</table>
</form>
<br>
</form>
