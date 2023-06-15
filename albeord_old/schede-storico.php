<?
include("core-config.php");

if (!$PRIVILEGI["schede"]) redirect_to("home.php");

head_page();

$ST[1]="Prenottamento e colazione";
$ST[2]="Mezza Pensione";
$ST[3]="Pensione Completa";

?>
<table border='0' cellspacing='1' cellpadding='0' align='center'  bgcolor='#000000' width="100%">
	<tr bgcolor='#cccccc'><td bgcolor='#cccccc'  style='border:0px solid #000000; padding:3px'><b>Schede</b></td></tr>
	<tr>
	<td bgcolor='#F5F5F5'  >
	<?
	$res  = multi_query($dbh, "SELECT * FROM schede WHERE stato=1 AND titolare LIKE '%".$_REQUEST["titolare"]."%' ORDER BY id DESC");
	if (!multi_num_rows($res)){
		echo "<tr><td align='center' bgcolor='#ffffff' style='border:0px solid #000000; padding:3px'>Nessuna scheda trovata</td></tr>";
	}
	while ($scheda = mysql_fetch_assoc($res)){
		$cnt++;
		$prezzi = getPrezziScheda($dbh, $scheda["id"]);
		?>
		<table width="100%" cellpadding="3" bgcolor="#FFFFFF" style="border-collapse:collapse;border-bottom:1px solid black;border-top:1px solid black;">
			<tr>
				<td>Camera</td>
				<td><?= $scheda["camera"] ?></td>
				<td>Titolare</td>
				<td><strong><?= $scheda["titolare"] ?></strong></td>
			</tr>
			<tr>
				<td >Dal</td>
				<td ><?= date("d/m/Y",$scheda["dal"]) ?></td>
				<td >Al</td>
				<td ><?= date("d/m/Y",$scheda["al"]) ?></td>
			</tr>
			<tr>
				<td style="border-bottom:1px solid black" >Adulti</td>
				<td style="border-bottom:1px solid black" ><?= $scheda["adulti"] ?></td>
				<td style="border-bottom:1px solid black" >Bambini</td>
				<td style="border-bottom:1px solid black" ><?= $scheda["bambini"] ?></td>
			</tr>
			<tr>
				<td colspan="4">
				<? if($scheda["tipo"]=="N"){ ?>
					Normale:
					<table width="100%" style="border-bottom:1px solid #cccccc">
						<tr>
							<td>BB</td>
							<td><?= show_prezzo($scheda["bb"]) ?></td>
		
							<td>HB</td>
							<td><?= show_prezzo($scheda["hb"]) ?></td>
		
							<td>FB</td>
							<td><?= show_prezzo($scheda["fb"]) ?></td>
						</tr>
					</table>
					<table width="100%">
						<tr>
						<? for ($i=1; $i<=$scheda["bambini"]; $i++){ ?>
							<td nowrap="nowrap">Bambino <?= $i?>: <?=  show_prezzo($ScontiBambini[$i],_PRECENT_) ?>%</td>
						<? } ?>
						</tr>
					</table>
				<? }else{ ?>
					Forfettatiro:
					<table width="100%">
						<tr>
							<td>BB</td>
							<td><?=  show_prezzo($scheda["fbb"]) ?></td>
							<td>HB</td>
							<td><?=  show_prezzo($scheda["fhb"]) ?></td>
							<td>FB</td>
							<td><?=  show_prezzo($scheda["ffb"]) ?></td>
						</tr>
					</table>
				<? } ?> 
				</td>
			</tr>
			
			<!--
			<?
			foreach ($prezzi["trattamenti"] as $trattamento => $parti){
				?>
				<tr><td colspan="4" bgcolor="#eeeeee"><strong><?= $ST[$trattamento] ?></strong></td></tr>
				<tr>
					<td style="border-bottom:1px solid #eeeeee"><strong>Persone</strong></td>
					<td style="border-bottom:1px solid #eeeeee"><strong>Giorni</strong></td>
					<td style="border-bottom:1px solid #eeeeee" align="right"><strong>Parziale</strong></td>
					<td style="border-bottom:1px solid #eeeeee" align="right"><strong>Totale</strong></td>
				</tr>
				<?
				foreach ($parti as $parte){
					?>
					<tr>
						<td style="border-bottom:1px solid #eeeeee"><?= $parte["persone"] ?></td>
						<td style="border-bottom:1px solid #eeeeee"><?= $parte["giorni"]  ?></td>
						<td style="border-bottom:1px solid #eeeeee" align="right"><?= show_prezzo($parte["parziale"]) ?></td>
						<td style="border-bottom:1px solid #eeeeee" align="right"><?= show_prezzo($parte["totale"]) ?></td>
					</tr>
					<?
				} 
				?>
				<tr><td colspan="4" bgcolor="#ffffff">&nbsp;</td></tr>
				<?
			}
			if(count($prezzi["servizi+"])){
				?>
				<tr>
					<td colspan="4" bgcolor="#eeeeee"><strong>Extra</strong></td>
				</tr>
				<?
				foreach ($prezzi["servizi+"] as $idservizio => $data){
					?>
					<tr>
						<td colspan="3" style="border-bottom:1px solid #eeeeee">&nbsp;&nbsp;<?= $data["nome"] ?></td>
						<td align="right" style="border-bottom:1px solid #eeeeee"><?= show_prezzo($data["prezzo"]) ?></td>
					</tr>
					<?
				}
			}
			?>
			<tr bgcolor="#eeeeee">
				<td><strong>Subtotale</strong></td>
				<td colspan="3" align="right"><?= show_prezzo($prezzi["subtotale"]) ?></td>
			</tr>
			<?
			foreach ((array)$prezzi["servizi-"] as $idservizio => $data){
				?>
					<tr>
						<td colspan="3" style="border-bottom:1px solid #eeeeee">&nbsp;&nbsp;<?= $data["nome"] ?></td>
						<td align="right" style="border-bottom:1px solid #eeeeee">-<?= show_prezzo($data["prezzo"]) ?></td>
					</tr>
					<?
			}
			
			?>-->
			<tr>
				<td colspan="3" style="border-bottom:1px solid #eeeeee"><strong>Totale</strong></td>
				<td align="right" style="border-bottom:1px solid #eeeeee"><strong><?= show_prezzo($prezzi["totale"]) ?></strong></td>
			</tr>
			<?
		?>
		</table>
		<? if($cnt<multi_num_rows($res)){ ?>
		<br />
		<? } ?>
		
		<?
	}
	?>
		</td>
		</tr>
</table>

</body>
</html>
