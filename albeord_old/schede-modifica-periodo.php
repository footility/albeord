<?php
	if(!$NUOVO){
		$Presenze=$ScontiBambini=$Supplementi=array();
		$res=multi_query($dbh, "SELECT * FROM schede_prezzi_bambini WHERE idperiodo=".$data["id"] );
		for ($i=0; $i<multi_num_rows($res); $i++){
			$dataT=multi_fetch_array($res, $i);
			$ScontiBambini[$dataT["bambino"]]=$dataT["sconto"];
		}
		$res=multi_query($dbh, "SELECT * FROM schede_presenze WHERE idperiodo=".$data["id"] );

		for ($i=0; $i<multi_num_rows($res); $i++){
			$dataT=multi_fetch_array($res, $i);
			$Presenze[$dataT["giorno"]][$dataT["idtrattamento"]][$dataT["tipo"]][$dataT["persona"]]=1;
		}
		$res=multi_query($dbh, "SELECT * FROM schede_supplementi WHERE idperiodo=".$data["id"] );
		for ($i=0; $i<multi_num_rows($res); $i++){
			$dataT=multi_fetch_array($res, $i);
			$Supplementi[$dataT["giorno"]]=$dataT["supplemento"];
		}
	}
?>
<form action="schede-modifica.php" id="forminvio" method="post"  onsubmit="return checkForm(this)">
	<input  type="hidden" name="idscheda" value="<?=  $IDS ?>" />
	<input  type="hidden" name="id" value="<?=  $data["id"] ?>" />
	<input  type="hidden" name="camera" value="<?=  $scheda["camera"] ?>" />
	<table border='0' cellspacing='0' cellpadding='3' align='center' width='100%' bgcolor='#ffffff' style='border:1px solid #888888'>
		<tr>
			<td bgcolor='#cccccc'  colspan="2" style='border:1px solid #ffffff'><b>Periodo</b></td>
		</tr>
		<tr>
			<td bgcolor='#ffffff'  width="50%"><b>Dal</b></td>
			<td bgcolor='#ffffff' width="50%"><b>Al</b></td>
		</tr>
		<tr bgcolor='#ffffff'>
			<td><?php disegna_cal("d_g","d_m", "d_a", unixtojd($data["dal"])) ?></td>
			<td nowrap="nowrap"><?php disegna_cal("a_g","a_m", "a_a", unixtojd($data["al"])) ?></td>
		</tr>

		<tr>
			<td bgcolor='#ffffff' ><strong>Adulti</strong></td>
			<td bgcolor='#ffffff'><strong>Bambini</strong></td>
		</tr>
		<tr>
			<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000' valign="top">
				<input type="text" name="adulti" class="inptext" value="<?=  $data["adulti"] ?>" size="3" />
			</td>
			<td bgcolor='#FFFFFF'  width='80%' style='border:0px solid #000000' valign="top">
				<input type="text" name="bambini" class="inptext" value="<?=  $data["bambini"] ?>" size="3" />
			</td>
		</tr>
		<tr>
			<td bgcolor='#ffffff' colspan="2"><strong>Prezzi</strong></td>
		</tr>
		<tr>
			<td bgcolor='#ffffff' width='50%' style='border:1px solid #ffffff'>
				<input type="radio" onclick="colora_disab(this,'<?=  $data["id"] ?>')" name="tipo" value="N" id="tipo_n<?=  $data["id"] ?>" <?= ($data["tipo"]=="N"?"checked":"") ?> /><label for="tipo_n<?=  $data["id"] ?>">Normale</label>
			</td>
			<td bgcolor='#ffffff' width='50%' style='border:1px solid #ffffff'>
				<input type="radio" onclick="colora_disab(this,'<?=  $data["id"] ?>')" name="tipo" value="F" id="tipo_f<?=  $data["id"] ?>" <?= ($data["tipo"]=="F"?"checked":"") ?> /><label for="tipo_f<?=  $data["id"] ?>">Forfettario</label>
			</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td bgcolor='#ffffff' width='50%' style='border:1px solid #ffffff; visibility:<?= ($data["tipo"]!="N"?"hidden":"visible") ?>' id="tipo_n_td<?=  $data["id"] ?>" >
				<table>
					<tr>
						<td valign="top">
							<table>
								<tr>
									<td>BB</td>
									<td><input autocomplete="off"  type="text" name="bb" size="5" class="inptext" value="<?=  show_prezzo($data["bb"]) ?>" /> &euro;</td>
								</tr>
								<tr>
									<td>HB</td>
									<td><input  autocomplete="off" type="text" name="hb" size="5" class="inptext" value="<?=  show_prezzo($data["hb"]) ?>" /> &euro;</td>
								</tr>
								<tr>
									<td>FB</td>
									<td><input  autocomplete="off" type="text" name="fb" size="5" class="inptext" value="<?=  show_prezzo($data["fb"]) ?>" /> &euro;</td>
								</tr>
							</table>
						</td>
						<td valign="top">
							<table>
								<?php for ($i=1; $i<=$data["bambini"]; $i++){ ?>
								<tr>
									<td>Bambino <?= $i?>:</td>
									<td><input type="text" autocomplete="off"  name="sconto[<?= $i ?>]" size="3" class="inptext" value="<?=  show_prezzo($ScontiBambini[$i],_PRECENT_) ?>" /> %</td>
								</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
				</table>



			</td>
			<td bgcolor='#ffffff' width='50%' style='border:1px solid #ffffff;; visibility:<?= ($data["tipo"]!="F"?"hidden":"visible") ?>' id="tipo_f_td<?=  $data["id"] ?>">
				<table>
					<tr>
						<td>BB</td>
						<td><input type="text" name="fbb" autocomplete="off"  size="5" class="inptext" value="<?=  show_prezzo($data["fbb"]) ?>" /> &euro;</td>
					</tr>
					<tr>
						<td>HB</td>
						<td><input type="text" name="fhb" autocomplete="off"  size="5" class="inptext" value="<?=  show_prezzo($data["fhb"]) ?>" /> &euro;</td>
					</tr>
					<tr>
						<td>FB</td>
						<td><input type="text" name="ffb"  autocomplete="off" size="5" class="inptext" value="<?=  show_prezzo($data["ffb"]) ?>" /> &euro;</td>
					</tr>
				</table>

			</td>
		</tr>

		<?php if(!$NUOVO){ ?>
		<tr bgcolor='#ffffff'>
			<td colspan="2">
				<table cellpadding="3" cellspacing="1" width="80%">

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
							(count($Presenze[$i][$idt]["A"]) || count($Presenze[$i][$idt]["B"])) &&  /*$TrattamentiBaseAbiltati[$idt] && */
							(count($Presenze[$i][$idt]["A"])!=$data["adulti"] || count($Presenze[$i][$idt]["B"])!=$data["bambini"])){
								echo "bgcolor='#dddddd'";
							}
							?>>
								<input type="checkbox"
								<?php if($i !=unixtojd($data["dal"]) && $idt!=$TrattamentiFirst && !count($Presenze[$i][$TrattamentiFirst]["A"]) && !count($Presenze[$i][$TrattamentiFirst]["B"])  ){ ?>
										disabled="disabled"
								<?php } ?>
								onclick="<?php

								if($idt==$TrattamentiFirst){
									?>autocheckDisable(this, <?= $i ?>, <?= $idt ?>, <?=  $data["id"] ?>, <?=  $data["adulti"]  ?>,<?=  $data["bambini"]  ?>, [<?= implode(",",array_keys($Trattamenti)) ?>])<?php
								}else{
									?>autocheck(this,'id_<?= $i ?>_<?= $idt ?>_<?=  $data["id"] ?>', <?=  $data["adulti"]  ?>,<?=  $data["bambini"]  ?>)<?php
								}?>"
								id="id_<?= $i ?>_<?= $idt ?>_<?=  $data["id"] ?>"
								<?= (count($Presenze[$i][$idt]["A"]) || count($Presenze[$i][$idt]["B"]))?"checked":"" ?>
								 /> <span style="cursor:pointer;" onclick="toggle('div_id_<?= $i ?>_<?= $idt ?>_<?=  $data["id"] ?>')">+</span><br />
								<div id="div_id_<?= $i ?>_<?= $idt ?>_<?=  $data["id"] ?>" style="display:none">
								<?php for ($j=1; $j<=$data["adulti"]; $j++){ ?>

									&nbsp;<input type="checkbox" name="presenze[<?= $i ?>][<?= $idt ?>][A][<?= $j ?>]"
									<?php if($i!=unixtojd($data["dal"]) && $idt!=$TrattamentiFirst && !$Presenze[$i][$TrattamentiFirst]["A"][$j]){ ?>
										disabled="disabled"
									<?php } ?>
									onclick="<?php

									if($idt==$TrattamentiFirst){
										?>autocheckDisableLow(this, <?= $i ?>, <?= $idt ?>, <?=  $data["id"] ?>, 'A',<?=  $j ?>, [<?= implode(",",array_keys($Trattamenti)) ?>])<?php
									}?>"
									<?= ($Presenze[$i][$idt]["A"][$j])?"checked":"" ?>
									id="id_<?= $i ?>_<?= $idt ?>_<?=  $data["id"] ?>_A_<?= $j ?>"/><label for="id_<?= $i ?>_<?= $idt ?>_A_<?= $j ?>"> A <?= $j ?></label><br />
								<?php } ?>
								<?php for ($j=1; $j<=$data["bambini"]; $j++){ ?>
									&nbsp;<input type="checkbox"  name="presenze[<?= $i ?>][<?= $idt ?>][B][<?= $j ?>]"
									<?php if($i!=unixtojd($data["dal"]) && $idt!=$TrattamentiFirst && !$Presenze[$i][$TrattamentiFirst]["B"][$j]){ ?>
										disabled="disabled"
									<?php } ?>
									onclick="<?php

									if($idt==$TrattamentiFirst){
										?>autocheckDisableLow(this, <?= $i ?>, <?= $idt ?>, <?=  $data["id"] ?>,'B',<?=  $j  ?>, [<?= implode(",",array_keys($Trattamenti)) ?>])<?php
									}?>"
									<?= ($Presenze[$i][$idt]["B"][$j])?"checked":"" ?>
									id="id_<?= $i ?>_<?= $idt ?>_<?=  $data["id"] ?>_B_<?= $j ?>"/><label for="id_<?= $i ?>_<?= $idt ?>_B_<?= $j ?>"> B <?= $j ?></label><br />
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
						<td>
							 <?php if($i<unixtojd($data["al"])){ ?>
							<input class="inptext" type="text" style="text-align:right" name="supplemento[<?= $i ?>]" size="6" value="<?= $Supplementi[$i]?show_prezzo($Supplementi[$i]):"" ?>" autocomplete="off" /> &euro;
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</table>
			 </td>
		</tr>
		<?php }?>



			<tr bgcolor='#cccccc'>
				<td bgcolor='#ffffff'  >

					<?php if ($NUOVO){ ?>
					<input style="width:10em"  type="submit" value="Aggiungi" name="aggiungi" class="inpbtn" />
					<?php }else{ ?>
					<input style="width:10em"  type="submit" name="salva_periodo" value="Salva" class="inpbtn" />
					<?php }?>
				</td>
				<td bgcolor='#ffffff' align="right">

					<?php if (!$NUOVO){ ?>
						<input style="width:10em"  type="submit" name="elimina" value="Elimina" class="inpbtn" />
					<?php }?>
				</td>
			</tr>


	</table>
</form>
<?php
if(!$NUOVO){

	$schdede_LAST["bb"]=$data["bb"];
	$schdede_LAST["hb"]=$data["hb"];
	$schdede_LAST["fb"]=$data["fb"];

	$schdede_LAST["fbb"]=$data["fbb"];
	$schdede_LAST["fhb"]=$data["fhb"];
	$schdede_LAST["ffb"]=$data["ffb"];

	$schdede_LAST["dal"]=$data["al"];
	$schdede_LAST["al"]=strtotime("+7days",$data["al"]);

	$schdede_LAST["tipo"]=$data["tipo"];
}
?>

