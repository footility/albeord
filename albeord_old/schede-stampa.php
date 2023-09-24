<?php
include("core-config.php");

if (!$PRIVILEGI["schede"]) redirect_to("home.php");

head_page();

top_menu();


$res=multi_query($dbh, "SELECT * FROM trattamenti ORDER BY ordine ASC" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$Trattamenti[$dataT["id"]]=$dataT["nome"];
	if($i==0){
		$TrattamentiFirst=$dataT["id"];
	}
}
$res=multi_query($dbh, "SELECT * FROM servizi ORDER BY tipo,nome ASC" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$Servizi[$dataT["id"]]=$dataT;
}



$IDS=intval($_REQUEST["id"]);

$data=multi_single_query($dbh, "SELECT * FROM schede WHERE id=$IDS","ALL");

$res=multi_query($dbh, "SELECT * FROM schede_trattamenti_base WHERE idscheda=$IDS" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$TrattamentiBaseAbiltati[$dataT["idtrattamento"]]=$dataT["idtrattamento"];
}
$res=multi_query($dbh, "SELECT * FROM schede_prezzi WHERE idscheda=$IDS" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$PrezziBase[$dataT["idtrattamento"]]=$dataT["prezzo"];
}
$res=multi_query($dbh, "SELECT * FROM schede_prezzi_bambini WHERE idscheda=$IDS" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$ScontiBambini[$dataT["bambino"]]=$dataT["sconto"];
}
$res=multi_query($dbh, "SELECT * FROM schede_presenze WHERE idscheda=$IDS" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$Presenze[$dataT["giorno"]][$dataT["idtrattamento"]][$dataT["tipo"]][$dataT["persona"]]=1;
}
$res=multi_query($dbh, "SELECT * FROM schede_servizi WHERE idscheda=$IDS" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$ServiziAttivi[$dataT["idservizio"]]=$dataT["prezzo"];
}


ob_get_clean();

?>
<br>
<form action="schede-modifica.php" id="forminvio" method="post"  onsubmit="return checkForm(this)">
<input  type="hidden" name="adulti" value="<?=  $data["adulti"] ?>" />
<input  type="hidden" name="bambini" value="<?=  $data["bambini"] ?>" />

<?php if($_REQUEST["nuova"]){ ?>
<input  type="hidden" name="nuova" value="1" />
<?php }else{ ?>
<input  type="hidden" name="id" value="<?=  $data["id"] ?>" />
<?php } ?>
<table style="cursor:pointer"  width='980' border='0' cellspacing='0' cellpadding='0' bgcolor='#000000' align='center'>
<tr>
<td width="600" valign="top" bgcolor="white">
	<table border='0' cellspacing='0' cellpadding='3' align='center' width='100%' bgcolor='#000000' style='border:1px solid #000000'>
		<tr>
			<td bgcolor='#cccccc'  colspan="2" style='border:1px solid #ffffff'><b>Scheda camera</b></td>
		</tr>
		<tr>
			<td bgcolor='#cccccc' width='50%' style='border:1px solid #ffffff'><b>Camera</b></td>
			<td bgcolor='#cccccc'  width='50%' style='border:1px solid #ffffff'><b>Titolare</b></td>
		</tr>
		<tr>
			<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000'>
			<input class="inptext" type="text" name="camera" size="6" value="<?= $data["camera"] ?>"/></td>
			<td bgcolor='#FFFFFF'  width='50%' style='border:0px solid #000000'>
			<input  class="inptext" type="text" name="titolare" value="<?= $data["titolare"] ?>"//></td>
		</tr>
		<tr>
			<td bgcolor='#cccccc' width='50%' style='border:1px solid #ffffff'><b>Adulti</b></td>
			<td bgcolor='#cccccc'  width='80%' style='border:1px solid #ffffff'><b>Bambini</b></td>
		</tr>
		<tr>
			<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000' valign="top">
			<b style="font-size:1.5em"><?=  $data["adulti"] ?></b>
			<table>
				<?php foreach ($Trattamenti as $idt=>$trattamento){ ?>
					<tr>
						<td><?= $trattamento ?></td>
						<td><input type="text" name="prezzo[<?= $idt ?>]" size="5" class="inptext" value="<?=  show_prezzo($PrezziBase[$idt]) ?>" /> &euro;</td>
					</tr>
				<?php } ?>
			</table>
			</td>
			<td bgcolor='#FFFFFF'  width='80%' style='border:0px solid #000000' valign="top">
				<b style="font-size:1.5em"><?=  $data["bambini"] ?></b>
				<table>
					<?php for ($i=1; $i<=$data["bambini"]; $i++){ ?>
					<tr>
						<td>Sconto bambino <?= $i?>:</td>
						<td><input type="text" name="sconto[<?= $i ?>]" size="3" class="inptext" value="<?=  show_prezzo($ScontiBambini[$i],_PRECENT_) ?>" /> %</td>
					</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
		<tr>
			<td bgcolor='#cccccc' width='20%' style='border:1px solid #ffffff'><b>Dal</b></td>
			<td bgcolor='#cccccc'  width='50%' style='border:1px solid #ffffff'><b>Al</b></td>
		</tr>
		<tr bgcolor='#ffffff'>
			<td><?php disegna_cal("d_g","d_m", "d_a", unixtojd($data["dal"])) ?></td>
			<td nowrap="nowrap"><?php disegna_cal("a_g","a_m", "a_a", unixtojd($data["al"])) ?><input type="submit" name="salva" value="Aggiorna" class="inpbtn" /></td>
		</tr>
		<tr bgcolor='#ffffff'>
			<td colspan="2">
				<table cellpadding="3" cellspacing="1" width="80%">
					<tr bgcolor="#CCCCCC">
						<th align="left" width="<?= intval(100/(count($Trattamenti)+1)) ?>%">Data</th>
						<?php foreach ($Trattamenti as $idt =>  $trattamento){ ?>
							<th align="left" width="<?= intval(100/(count($Trattamenti)+1)) ?>%">
								<input type="checkbox" onclick="autocheckAll(this,'<?= $idt ?>')" name="trattamenti_base[<?=$idt ?>]"  value="<?=$idt ?>"
								 id="tr_bs_<?=$idt ?>" <?= ($TrattamentiBaseAbiltati[$idt])?"checked":"" ?> />
								<label for="tr_bs_<?=$idt ?>"><?=$trattamento ?></label>
							</th>
						<?php } ?>
					</tr>
				<?php
				//$Presenze[$dataT["giorno"]][$dataT["idtrattamento"]][$dataT["tipo"]][$dataT["persona"]]
				for ($i=unixtojd($data["dal"]); $i<=unixtojd($data["al"]); $i++){ ?>
					<tr>
						<td valign="top" style='border-bottom:1px solid #cccccc'><?= jdtodate("d-m-Y", $i)?></td>
						<?php foreach ($Trattamenti as $idt => $trattamento){ ?>
							<?php
							 if($i==unixtojd($data["dal"]) && $idt==$TrattamentiFirst){
								echo "<td style='border-bottom:1px solid #cccccc'>&nbsp;</td>";
								continue;
							 }
							?>
							<td style='border-bottom:1px solid #cccccc' valign="top" <?php
							if(
							/*(count($Presenze[$i][$idt]["A"]) || count($Presenze[$i][$idt]["B"]))*/ $TrattamentiBaseAbiltati[$idt] &&
							(count($Presenze[$i][$idt]["A"])!=$data["adulti"] || count($Presenze[$i][$idt]["B"])!=$data["bambini"])){
								echo "bgcolor='#dddddd'";
							}
							?>>
								<input type="checkbox"  onclick="autocheck(this,'id_<?= $i ?>_<?= $idt ?>')" id="id_<?= $i ?>_<?= $idt ?>"
								<?= (count($Presenze[$i][$idt]["A"]) || count($Presenze[$i][$idt]["B"]))?"checked":"" ?>
								 /> <span style="cursor:pointer;" onclick="toggle('div_id_<?= $i ?>_<?= $idt ?>')">+</span><br />
								<div id="div_id_<?= $i ?>_<?= $idt ?>" style="display:none">
								<?php for ($j=1; $j<=$data["adulti"]; $j++){ ?>
									&nbsp;<input type="checkbox" name="presenze[<?= $i ?>][<?= $idt ?>][A][<?= $j ?>]"
									<?= ($Presenze[$i][$idt]["A"][$j])?"checked":"" ?>
									id="id_<?= $i ?>_<?= $idt ?>_A_<?= $j ?>"/><label for="id_<?= $i ?>_<?= $idt ?>_A_<?= $j ?>"> A <?= $j ?></label><br />
								<?php } ?>
								<?php for ($j=1; $j<=$data["bambini"]; $j++){ ?>
									&nbsp;<input type="checkbox"  name="presenze[<?= $i ?>][<?= $idt ?>][B][<?= $j ?>]"
									<?= ($Presenze[$i][$idt]["B"][$j])?"checked":"" ?>
									id="id_<?= $i ?>_<?= $idt ?>_B_<?= $j ?>"/><label for="id_<?= $i ?>_<?= $idt ?>_B_<?= $j ?>"> B <?= $j ?></label><br />
								<?php } ?>
								</div>
							</td>
							<?php
							 if($i==unixtojd($data["al"])){
								echo "<td style='border-bottom:1px solid #cccccc' colspan='".(count($Trattamenti)-1)."'>&nbsp;</td>";
								break;
							 }

							 ?>
						<?php } ?>
					</tr>
				<?php } ?>
				</table>
			 </td>
		</tr>
		<tr bgcolor='#cccccc'>
			<td colspan='2' align="right" bgcolor='#cccccc'  >


				<input  type="reset" value="Annulla modifiche" class="inpbtn" />


			</td>
		</tr>
	</table><br />
</td>
<td valign="top" bgcolor="white">
	<table border='0' cellspacing='0' cellpadding='3' align='center' width='100%' bgcolor='#000000' style='border:1px solid #000000'>
		<tr>
			<td bgcolor='#cccccc'  colspan="2" style='border:1px solid #ffffff'><b>Servizi</b></td>
		</tr>
		<tr>
			<td bgcolor='#cccccc' width='50%' style='border:1px solid #ffffff'><b>Servizio</b></td>
			<td bgcolor='#cccccc'  width='50%' style='border:1px solid #ffffff'><b>Prezzo</b></td>
		</tr>
		<?php foreach ($Servizi as $ids=>$servizio){ ?>
		<tr>
			<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000'><?= $servizio["nome"] ?></td>
			<td bgcolor='#FFFFFF'  width='50%' style='border:0px solid #000000'>
				<span style="font-family:'Courier New', Courier, monospace"><?= ($servizio["tipo"]==1?"+":"-") ?></span>
				<input  class="inptext" type="text" name="servizi[<?= $ids ?>]" size="7" value="<?= ($ServiziAttivi[$ids])?show_prezzo($ServiziAttivi[$ids]):"" ?>"/> &euro;
			</td>
		</tr>
		<?php } ?>
	</table>
</td>
</tr>
<tr>
	<td colspan="2">
		<table border='0' cellspacing='0' cellpadding='3' align='center' width='100%' bgcolor='#000000' style='border:1px solid #000000'>
			<tr>
				<td valign="top" width="33%" bgcolor='#cccccc'  tyle='border:1px solid #ffffff'>
				<input type="submit" name="salva" value="  Salva  " class="inpbtn" />
				</td>
				<td valign="top" width="34%"  bgcolor='#cccccc' align="center"  tyle='border:1px solid #ffffff'>
				<input  type="submit"  name="salva" value="Stampa" class="inpbtn" />
				</td>

				<td valign="top" width="33%"  bgcolor='#cccccc' align="right"  tyle='border:1px solid #ffffff'>

					<input style="width:10em" type="submit" name="salva" value="Salva e Chiudi" class="inpbtn" onclick="return confirm('Chiudere la scheda associata alla camera '+this.form.elements['camera'].value+'?')" />
						<?php if($PRIVILEGI["admin"]){ ?><br /><br /><br />
					<input style="width:10em"  type="submit" name="elimina"  value="Elimina scheda" class="inpbtn" onclick="return confirm('Eliminare la scheda associata alla camera '+this.form.elements['camera'].value+'?')" />
				<?php } ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</form>
</body>
</html>
