<?php
include("core-config.php");

if (!$PRIVILEGI["schede"]) redirect_to("home.php");

head_page();

$ST[1]="Prenottamento e colazione";
$ST[2]="Mezza Pensione";
$ST[3]="Pensione Completa";

?>
	<table width="100%" cellpadding="3" bgcolor="#FFFFFF" style="border-collapse:collapse;border:1px solid black;">
	<?php
	$res  = multi_query($dbh, "SELECT * FROM schede WHERE stato=1 AND titolare LIKE '%".$_REQUEST["titolare"]."%' ORDER BY id DESC");
	if (!multi_num_rows($res)){
		echo "<tr><td align='center' bgcolor='#ffffff' style='border:0px solid #000000; padding:3px'>Nessuna scheda trovata</td></tr>";
	}
	while ($scheda = mysqli_fetch_assoc($res)){

		$scheda["dal"]  = multi_single_query($dbh, "SELECT min(dal) FROM schede_periodi where idscheda=".$scheda["id"]);
		$scheda["al"]  =  multi_single_query($dbh, "SELECT max(al) FROM schede_periodi where idscheda=".$scheda["id"]);
		$cnt++;
		?>

			<tr bgcolor="#CCCCCC" style="font-weight:bold">
				<td colspan="2">Camera: <?= $scheda["camera"] ?></td>

				<td colspan="2">Titolare: <?= $scheda["titolare"] ?></td>

			</tr>
			<tr bgcolor="#ffffff">
				<td colspan="2">Dal: <?= date("d-m-Y",$scheda["dal"] )?></td>

				<td colspan="2">Al: <?= date("d-m-Y",$scheda["al"] ) ?></td>

			</tr>
			<tr bgcolor="#ffffff" style="font-weight:bold">
				<td colspan="4"><?php include("schede-riepilogo.php"); ?></td>
			</tr>
			<?php if($cnt<mysqli_num_rows($res)){ ?>
			<tr bgcolor="#ffffff">
					<td colspan="4">&nbsp;<br />&nbsp;</td>
				</tr>
		<?php } ?>
		<?php } ?>
		</table>

</body>
</html>
