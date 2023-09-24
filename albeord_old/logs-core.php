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


if ($_REQUEST["invia"]){
	$_SESSION["dal_stat"] = mktime(0,0,1, $_REQUEST["d_m"], $_REQUEST["d_g"],$_REQUEST["d_a"]);

	$_SESSION["al_stat"]  = mktime(23,59,59, $_REQUEST["a_m"], $_REQUEST["a_g"],$_REQUEST["a_a"]);
}else{
    $_REQUEST["stato"]=array("0"=>"0","1"=>"1","2"=>"2");
}

?>
<br>
			<table width='980' border='0' cellspacing='0' cellpadding='3' align='center' style="border:1px solid #cccccc">
				<form name="sss" method='POST' action='<?= $_SERVER["PHP_SELF"] ?>'>
				<tr bgcolor='#ffffff'>
					<td>Dal</td>
					<td><?php disegna_cal("d_g","d_m", "d_a", unixtojd($_SESSION["dal_stat"])) ?></td>
					<td>Al</td>
					<td><?php disegna_cal("a_g","a_m", "a_a", unixtojd($_SESSION["al_stat"])) ?></td>
					<td rowspan="4"><input type='submit' name='invia' value='Calcola' style="border:1px solid #000000; background-color:#ffffff"></td>
				</tr>
				<tr bgcolor='#ffffff'>
                    <td colspan="1">Tipo camera:</td>
                    <td colspan="3">
                    	<select name="tipocamera">
                    		<option value="0">Tutte</option>
                    		<?phpphp for ($i = 65; $i<72; $i++) { ?>
                    			<option <?phpphp echo chr($i)==$_REQUEST["tipocamera"]?"selected":"" ?> value="<?phpphp echo chr($i)?>"><?phpphp echo chr($i)?></option>
                    		<?phpphp } ?>
                    	</select>
                    </td>
				</tr>
				<tr bgcolor='#ffffff'>
                    <td colspan="2">Visualizza solo:</td>
                    <td colspan="2">Stato:</td>
				</tr>
				<tr bgcolor='#ffffff'>
                    <td colspan="2">
                        <?php
                        	$res=multi_query($dbh, "SELECT * FROM utenti WHERE eliminato=0");
                        	for($i=0;$i<multi_num_rows($res);$i++){
                        		$data=multi_fetch_array($res, $i);
                        		echo "<input id='ute".$data["id"]."' ".(($_REQUEST["utenti"][$data["id"]])?"checked":"")." onclick=\"document.forms['sss'].elements['ut'].checked=false\" type='checkbox' name='utenti[".$data["id"]."]' value='".$data["id"]."'> <label for='ute".$data["id"]."'>".htmlentities($data["utente"])."</label>";
                        	}
							//echo "<br><input type='checkbox' name='altri_ut' value='-1'> ALTRI";
                       		//echo "<br><input type='checkbox' value='0' onclick='seltutti();'> Tutti";
                        ?>
                    </td>
                    <td colspan="2">
                    <input <?= (($_REQUEST["stato"][1]==1)?"checked":"") ?> type='checkbox' name='stato[1]' value='1'> Pagati     &nbsp;
                    <input <?= (($_REQUEST["stato"][0]=="0")?"checked":"") ?> type='checkbox' name='stato[0]' value='0'> Da Pagare     &nbsp;
                    <input <?= (($_REQUEST["stato"][2]==2)?"checked":"") ?> type='checkbox' name='stato[2]' value='2'> Storni
                    </td>
				</tr>
				</form>
				<script language="Javascript">
                    function seltutti(){

                        if (document.forms['sss'].elements['ut'].checked==true){
                            sta=true;
                        }else{
                            sta=false;
                        }

                        <?php
                        for($i=0;$i<multi_num_rows($res);$i++){
                        	$data=multi_fetch_array($res, $i);
                        	echo "document.forms['sss'].elements['utenti[".$data["id"]."]'].checked=sta;";
                       	}
                        ?>
                    }
				</script>
			</table>
<?php

	if ($_SESSION["dal_stat"]>0 & $_SESSION["al_stat"]>0 & $_SESSION["dal_stat"]<$_SESSION["al_stat"]){

		$dal = $_SESSION["dal_stat"];
		$al  = $_SESSION["al_stat"];
?>
	<br>
			<table width='980' border='0' cellspacing='1' cellpadding='2' align='center'  bgcolor='#cccccc'>
				<tr>
					<td bgcolor='#ffffff' colspan='7'>
						Operazioni
						dal: <b><?= date("d-m-Y", $dal); ?></b>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						al: <b><?=  date("d-m-Y", $al); ?></b>
					</td>
				</tr>
				</form>

				<tr bgcolor='#cccccc'>
					<td width="1%"><b>Ora</b></td>
					<td width="1%"><b>Camera</b></td>
					<td width="1%"><b>Utente</b></td>
					<td><b>Articolo</b></td>
					<td><b>Quantita</b></td>
					<td align="right"><b>Prezzo</b></td>
					<td><b>Stato</b></td>
				</tr>
				<?php
					$q="
                    SELECT
                        ordini.*,utenti.utente,utenti.id as idutente,
                        sum(ordini.prezzo) as prz, count(ordini.id) as quan
                    FROM
                        ordini
					LEFT JOIN utenti ON (utenti.id=ordini.idutente)
                    WHERE
                    	".($_REQUEST["tipocamera"]?" camera LIKE '%-".$_REQUEST["tipocamera"]."'  AND ":"")."
						".(is_array($_REQUEST["stato"])?" ordini.stato IN (".implode(",",$_REQUEST["stato"]).")  AND ":"")."
						".(is_array($_REQUEST["utenti"])?" ordini.idutente IN (".implode(",",$_REQUEST["utenti"]).")  AND ":"")."

                        ordini.ora BETWEEN $dal AND $al
                    GROUP by ordini.idordine, ordini.articolo, ordini.stato
                    ORDER BY ora DESC, idordine DESC, articolo";
					//echo $q;
					$res=multi_query($dbh, $q);

					for($i=0; $i<multi_num_rows($res); $i++){
						$data=multi_fetch_array($res, $i);

                        if ($data["idordine"]!=$PastIDord & $i>0){
                            echo "<tr bgcolor='#cccccc'><td colspan='5' style='padding:1px'><b></b></td></tr>";
                        }
                        $PastIDord=$data["idordine"];

                        if($data["stato"]!=2){
                            $tot+=$data["prz"];
                            $qtot+=$data["quan"];
                        }

				?>

				<tr <?= (($data["stato"]==2)?"bgcolor='#EBD1CE'":"bgcolor='#ffffff'") ?>>
					<td nowrap><?=  date("d-m-Y H:i", $data["ora"]); ?>&nbsp;&nbsp;</td>
					<td><?= $data["camera"] ?></td>
					<td><?= $data["utente"]?$data["utente"]:"Eliminato" ?></td>
					<td><?= $data["articolo"] ?></td>
					<td><?= $data["quan"] ?></td>
					<td align="right"><?= show_prezzo($data["prz"]) ?> &euro;</td>
					<td><?= $GlobStati[$data["stato"]] ?></td>
				</tr>
				<?php } ?>
				<tr bgcolor='#ffffff' style="font-weight:bold">
                    <td align="right" colspan="4">Totale:</td>
					<td><?= $qtot ?></td>
					<td align="right"><?= show_prezzo($tot) ?> &euro;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
<?php } ?>

</body>



