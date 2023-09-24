<?php
include("core-config.php");

if (!$PRIVILEGI["stats"]) redirect_to("home.php");

if (!$_SESSION["dal_stat"]){
	$_SESSION["dal_stat"] = mktime(0,0,0,    date("m"), date("d"),date("Y"));
	$_SESSION["al_stat"]  = mktime(23,59,59, date("m"), date("d"),date("Y"));
}
?>
<?php head_page() ?>
<?php top_menu() ?>

<?php


if (strlen($_REQUEST["stampa_simp"])>0 | strlen($_REQUEST["stampa_dett"])>0){

    $data = $_SESSION["dal_stat"]."*".$_SESSION["al_stat"];

    if(strlen($_REQUEST["stampa_simp"])>0) stampawbs($dbh, $data , 5);

    if(strlen($_REQUEST["stampa_dett"])>0) stampawbs($dbh, $data , 6);

}

if ($_REQUEST["invia"]){
	$_SESSION["dal_stat"] = mktime(0,0,1, $_REQUEST["d_m"], $_REQUEST["d_g"],$_REQUEST["d_a"]);

	$_SESSION["al_stat"]  = mktime(23,59,59, $_REQUEST["a_m"], $_REQUEST["a_g"],$_REQUEST["a_a"]);
}

?>
<br>
			<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc">
				<form method='POST' action='<?= $_SERVER["PHP_SELF"] ?>'>
				<tr bgcolor='#ffffff'>
					<td>Dal</td>
					<td><?php disegna_cal("d_g","d_m", "d_a", unixtojd($_SESSION["dal_stat"])) ?></td>
					<td>Al</td>
					<td><?php disegna_cal("a_g","a_m", "a_a", unixtojd($_SESSION["al_stat"])) ?></td>
					<td><input type='submit' name='invia' value='Calcola' style="border:1px solid #000000; background-color:#ffffff"></td>
				</tr>
				</form>
			</table>
<?php

	if ($_SESSION["dal_stat"]>0 & $_SESSION["al_stat"]>0 & $_SESSION["dal_stat"]<$_SESSION["al_stat"]){

		$dal = $_SESSION["dal_stat"];
		$al  = $_SESSION["al_stat"];
?>
	<br>
			<table width='980' border='0' cellspacing='1' cellpadding='2' align='center'  bgcolor='#cccccc'>
			<form method='POST'></form>
				<tr>
					<td bgcolor='#ffffff' colspan='4'>
						Statistiche<br>
						dal: <b><?= date("d-m-Y H:i:s", $dal); ?></b>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						al: <b><?=  date("d-m-Y H:i:s", $al); ?></b>
					</td>
				</tr>
				</form>

				<tr bgcolor='#cccccc'>
					<td colspan='2'><b>Gruppo</b></td>
					<td><b>Articoli</b></td>
					<td><b>Valore</b></td>
				</tr>
				<?php
					$TipiOrd[1]="Incassi";
					$TipiOrd[2]="Storni";
					//$TipiOrd[3]="Da Pagare";
					foreach ($TipiOrd as $tp =>$txtord){
					if ($tp==1)  $addq=" (ordini.stato=1 OR ordini.stato=0) ";
					if ($tp==2)  $addq=" ordini.stato=2 ";
				?>
					<tr><td bgcolor='#ffffff' colspan='4'><b><?= $txtord ?></b></td></tr>
					<?php
						$res=multi_query($dbh, "SELECT * FROM gruppi order by nome");

						for($i=0; $i<multi_num_rows($res); $i++){
							$data=multi_fetch_array($res, $i);
					?>
					<tr bgcolor='#ffffff'>
						<td colspan='2'>&nbsp;&nbsp;&nbsp;<a href='?dett[0]=<?= $tp ?>&dett[1]=<?= $data["id"] ?>'><?= $data["nome"] ?></a></td>
						<td><?= multi_single_query($dbh, "SELECT count(ordini.id) FROM ordini, utenti, gruppi WHERE $addq AND ordini.ora>=$dal AND ordini.ora<=$al and ordini.idutente=utenti.id AND utenti.idgruppo=gruppi.id AND gruppi.id=".$data["id"]); ?></td>
						<td><?= show_prezzo(multi_single_query($dbh, "SELECT sum(ordini.prezzo) FROM ordini, utenti, gruppi WHERE $addq AND ora>=$dal AND ora<=$al and ordini.idutente=utenti.id AND utenti.idgruppo=gruppi.id AND gruppi.id=".$data["id"])); ?>&euro;</td>
					</tr>
					<?php
						if ($_REQUEST["dett"][0]==$tp & $_REQUEST["dett"][1]==$data["id"]){
							?>
							<tr>
								<td colspan='4' bgcolor='#ffffff' style="padding-left:25px">
									<table width='100%'  bgcolor='#cccccc' cellpadding='2' cellspacing='1'>
										<tr  bgcolor='#cccccc'>
											<td>Articolo</td>
											<td align="right">Quantita</td>
											<td align="right">Valore</td>
										</tr>
										<?php
												$res2=multi_query($dbh, "SELECT ordini.articolo, count(ordini.id) as quantita ,sum(ordini.prezzo) as prezzo  FROM ordini, utenti, gruppi WHERE $addq AND ordini.ora>=$dal AND ordini.ora<=$al and ordini.idutente=utenti.id AND utenti.idgruppo=gruppi.id AND gruppi.id=".$data["id"]." GROUP BY ordini.articolo ORDER BY ordini.articolo" );
												$nr2=multi_num_rows($res2);
												if ($nr2==0){
													echo "<tr><td bgcolor='#ffffff' colspan='3' align='center'>Nessun Articolo</td></tr>";
												}
												for ($i2=0; $i2<$nr2; $i2++){
													$dataArt=multi_fetch_array($res2, $i2);
													?>
														<tr bgcolor='#ffffff'>
															<td><?= htmlentities($dataArt["articolo"]) ?></td>
															<td align="right"><?= $dataArt["quantita"] ?></td>
															<td align="right"><?= show_prezzo($dataArt["prezzo"]) ?>&euro;</td>
														</tr>
													<?php
												}

										?>

									</table>
								</td>
							</tr>
							<?php
						}

					?>

					<?php } ?>

				<?php } ?>

			</table>
<?php } ?>
<br><br>
            <table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc">
                <form method='POST' action='<?= $_SERVER["PHP_SELF"] ?>'>
                <tr bgcolor="#ffffff">
                    <td align="right">
                        <input style="border:1px solid #000000; background-color:#ffffff" type="submit" name="stampa_simp" value="Stampa generale">  &nbsp;&nbsp;&nbsp;&nbsp;
                        <input style="border:1px solid #000000; background-color:#ffffff" type="submit" name="stampa_dett" value="Stampa dettagliata">
                    </td>
				</tr>
				</form>
            </table>

</body>

