<?
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
$TrattamentiBaseAbiltati[$TrattamentiFirst]=$TrattamentiFirst;
?>
<br>
<form action="schede-modifica.php" method="post"  onsubmit="return checkForm(this)">
<input type="hidden" name="nuova" value="1" />
<table border='0' cellspacing='0' cellpadding='3' align='center' width='50%' bgcolor='#000000' style='border:1px solid #000000'>
	<tr bgcolor='#cccccc'>
		<td bgcolor='#cccccc' colspan='2'  style='border:1px solid #ffffff'><b>Nuova scheda</b></td>
	</tr>
	<tr>
		<td bgcolor='#cccccc' width='50%' style='border:1px solid #ffffff'><b>Camera</b></td>
		<td bgcolor='#cccccc'  width='50%' style='border:1px solid #ffffff'><b>Titolare</b></td>
	</tr>
	<tr>
		<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000;'>
		<input class="inptext" autocomplete="off" type="text" name="camera" size="6" style="font-size:2em"/></td>
		<td bgcolor='#FFFFFF'  width='50%' style='border:0px solid #000000'>
		<input  class="inptext"  autocomplete="off" type="text" name="titolare"  style="font-size:2em"/></td>
	</tr>	
	<!--<tr>
		<td bgcolor='#cccccc' width='50%' style='border:1px solid #ffffff'><b>Adulti</b></td>
		<td bgcolor='#cccccc'  width='50%' style='border:1px solid #ffffff'><b>Bambini</b></td>
	</tr>
	<tr>
		<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000'><input class="inptext" type="text" name="adulti" size="3" style="font-size:2em"/></td>
		<td bgcolor='#FFFFFF'  width='50%' style='border:0px solid #000000'><input  class="inptext" type="text" name="bambini" size="3" style="font-size:2em"/></td>
	</tr>
	<tr>
		<td bgcolor='#cccccc' width='50%' style='border:1px solid #ffffff'><b>Dal</b></td>
		<td bgcolor='#cccccc'  width='50%' style='border:1px solid #ffffff'><b>Al</b></td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td><? disegna_cal("d_g","d_m", "d_a", unixtojd()) ?></td>
		<td><? disegna_cal("a_g","a_m", "a_a", unixtojd()+7) ?></td>
	</tr>	-->
	<tr>
		<td bgcolor='#cccccc' colspan="2" style='border:1px solid #ffffff'><b>Trattamenti</b></td>
	</tr>	
	<tr bgcolor='#ffffff'>
		<td colspan="2">
			<table cellpadding="3" cellspacing="1" width="80%">
				<tr>
					<? foreach ($Trattamenti as $idt =>  $trattamento){ ?>
						<th align="left" width="<?= intval(100/count($Trattamenti)) ?>%">
							<input type="checkbox" name="trattamenti_base[<?=$idt ?>]"  value="<?=$idt ?>"
							 id="tr_bs_<?=$idt ?>" <?= ($TrattamentiBaseAbiltati[$idt])?"checked":"" ?> />
							<label for="tr_bs_<?=$idt ?>"><?=$trattamento ?></label>
						</th>
					<? } ?>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#cccccc'>
		<td colspan='2' align="center" bgcolor='#cccccc'  >
			<input type="submit" name="go" value="Continua" class="inpbtn" />
		</td>
	</tr>
</table>
</form>
</body>
</html>
