		<table border='0' cellspacing='0' cellpadding='3' align='center' width='100%' style='border:1px solid #888888'>
			<tr>
				<td bgcolor='#cccccc' colspan="2" style='border:1px solid #ffffff'><b>Riepilogo scheda</b></td>
			</tr>
			<?php $prezzi = getPrezziScheda($dbh, $scheda["id"]);?>
			<tr>
				<td bgcolor='#FFFFFF' colspan="2" style='border:0px solid #000000' valign="top">
				<table width="100%">
					<tr>
						<td width="99%">
							<table cellspacing='0' cellpadding='1' border="0" style="padding:0px;" class="riepilogo">
							<?php
							foreach ($prezzi["trattamenti"] as $trattamento => $parti){
							 echo "<tr><td colspan='5'><i>".$ST[$trattamento]."</i></td></tr>";

							foreach ($parti as $parte){
								echo "<tr><td>&nbsp;&nbsp; Pers. n.  ".$parte["persone"]."</td><td>&nbsp;&nbsp;Per giorni n. ".$parte["giorni"]."</td><td align='right'>&nbsp;&nbsp; ".show_prezzo($parte["parziale"])."&euro;</td><td>&nbsp;&nbsp;Totale:</td><td align='right'>&nbsp;&nbsp;".show_prezzo($parte["totale"])."&euro;</td></tr>";
							}
							}
							echo "<tr><td colspan='5'>&nbsp;</td></tr>";
							if(count($prezzi["servizi+"])){
								$tot=0;
								foreach ($prezzi["servizi+"] as $idservizio => $data){
									$tot+=$data["prezzo"];
								}
								echo "<tr><td colspan='4'>Servizi</td><td align='right'>&nbsp;&nbsp;".show_prezzo($tot)."&euro;</td></tr>";
							}
							if(count($prezzi["supplementi"])){
								echo "<tr><td colspan='4'>Servizi</td><td align='right'>&nbsp;&nbsp;".show_prezzo(array_sum($prezzi["supplementi"]))."&euro;</td></tr>";
							}

							echo "<tr><td colspan='4'>Subtotale</td><td align='right'>&nbsp;&nbsp;".show_prezzo($prezzi["subtotale"])."&euro;</td></tr>";

							if(count($prezzi["servizi-"])){
								$tot=0;
								foreach ($prezzi["servizi-"] as $idservizio => $data){
									$tot+=$data["prezzo"];
								}
								$sotrazioniTotale += $tot;
								echo "<tr><td colspan='4'>Altro</td><td align='right'>&nbsp;&nbsp;-".show_prezzo($tot)."&euro;</td></tr>";
							}
							?>
							</table>
						</td>
						<?phpphp if($instat){ ?>
						<td width="1%" valign="bottom" class="stampa_singolo">
							<form action="?<?phpphp echo http_build_query($_REQUEST) ?>" method="post">
								<input type="hidden" value="<?= $scheda["id"] ?>" name="idscheda"/>
								<input type="submit"  style="border:1px solid #000000; background-color:#ffffff" name="stampa_singola" value="Stampa questa scheda"/>
							</form>

						</td>
						<?phpphp } ?>
					</tr>
				</table>

				</td>
			</tr>

			<tr>
				<td width='50%'><b>Totale</b></td>
				<td width='50%' align="right"><strong><?= show_prezzo($prezzi["totale"]) ?> &euro;</strong></td>
			</tr>
			<?phpphp $totalePeriodo += $prezzi["totale"] ?>

		</table>
