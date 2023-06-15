<?
if (!$PRIVILEGI["admin"]) redirect_to("home.php");

function disegnacat($dbh, $idcatsupp=0, $tabs='', $sel){
	$res=multi_query($dbh, "SELECT * FROM categorie WHERE dipendenza=$idcatsupp");
	for($i=0;$i<multi_num_rows($res);$i++){
		$data=multi_fetch_array($res, $i);
		echo "<tr><td onclick=\"location.href='?selected=".$data["id"]."'\" ".(($sel==$data["id"])?"bgcolor='#cccccc'":"bgcolor='#ffffff'  onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ").">".$tabs.htmlentities($data["nome"])."</td></tr>\n";
		if(multi_single_query($dbh, "SELECT count(id) FROM categorie WHERE dipendenza=".$data["id"])){
				disegnacat($dbh, $data["id"], $tabs."&nbsp;&nbsp;&nbsp;&nbsp;",$sel);
		}
	}
}
if (strlen($_REQUEST["invia"])>0){
	foreach ($_REQUEST["nome"] as $id => $nome){
		if ($id==0 & strlen($nome)>0){
			$IDAssegnato = multi_nextval($dbh, "articoli");
			multi_query($dbh, "INSERT INTO articoli (id, categoria , nome, prezzo) VALUES ($IDAssegnato, ".$_REQUEST["curcat"].", '".$_REQUEST["nome"][$id]."', ".insert_prezzo($_REQUEST["prezzo"][$id]).")");
		}elseif($_REQUEST["elimina"][$id]==1){
			multi_query($dbh, "DELETE FROM articoli WHERE id=$id");
		}else{
			multi_query($dbh, "UPDATE articoli SET nome='".$_REQUEST["nome"][$id]."', prezzo=".insert_prezzo($_REQUEST["prezzo"][$id])." WHERE id=$id");
		}
	}
}


if ($_REQUEST["selected"])$_SESSION["curcat"]=intval($_REQUEST["selected"]);

?>
<table width="100%">
	<tr>
		<td valign='top' width="1%">
			<table cellspacing='1' cellpadding='2' bgcolor='#000000' width='200' style='cursor:pointer'>
				<? disegnacat($dbh, 0, '', $_SESSION["curcat"]); ?>
			</table>
		</td>
		<td valign='top' width="99%">
		<? if($_SESSION["curcat"]){ ?>
			<table  cellspacing='0' cellpadding='3' width='100%'>
					<form method='POST'>
					<tr bgcolor='#cccccc'>
						<td align='center' colspan='3' style="border-bottom:1px solid #ffffff">Elenco articoli <b><?= multi_single_query($dbh, "SELECT nome FROM categorie WHERE id=".$_SESSION["curcat"]) ?></b></td>
					</tr>
					<tr bgcolor='#cccccc'><td>Nome</td><td>Prezzo</td><td>Elimina</td></tr>
					<?
					$res=multi_query($dbh, "SELECT * FROM articoli WHERE categoria=".$_SESSION["curcat"]);
					for ($i=0; $i<multi_num_rows($res); $i++){
						$data=multi_fetch_array($res, $i);
					?>
					<tr>
						<td><input type='text' size='40' name='nome[<?= $data["id"] ?>]' value='<?= htmlentities($data["nome"],ENT_QUOTES); ?>' class="inptext"></td>
						<td nowrap><input  type='text' size='6' name='prezzo[<?= $data["id"] ?>]' value='<?= show_prezzo($data["prezzo"]); ?>' class="inptext"> &euro;</td>
						<td><input type='checkbox' name='elimina[<?= $data["id"] ?>]' value='1'></td>
					</tr>
					<? }	?>
					<tr bgcolor='#cccccc'>
						<td align='center' colspan='3' style="border-top:1px solid #ffffff">Nuovo Articolo</td>
					</tr>
					<tr>
						<td><input type='text' size='40' name='nome[0]' value='' class="inptext"></td>
						<td nowrap><input  type='text' size='6' name='prezzo[0]' value='' class="inptext"> &euro;</td>
						<td>&nbsp;</td>
					</tr>
						<tr bgcolor='#cccccc'>
						<td align='center' colspan='3'><input type='submit' name='invia' value='  Salva  ' class="inpbtn"></td>
					</tr>
					<input type='hidden' name='curcat' value='<?= $_SESSION["curcat"] ?>'>
					</form>
			</table>
		<? } ?>
		</td>
	</tr>
</table>
