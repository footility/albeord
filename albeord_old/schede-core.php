<?
include("core-config.php");

if (!$PRIVILEGI["schede"]) redirect_to("home.php");

head_page();

top_menu();
?>

<br>
<table border='0' cellspacing='1' cellpadding='3' align='center' width='980' bgcolor='#000000'>
	<tr bgcolor='#cccccc'><td bgcolor='#ffffff' colspan='9 ' width='980' style='border:0px solid #000000; cursor:pointer' onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#cccccc'" onclick="location.href='schede-nuova.php'">Nuova scheda</td></tr>
</table>

<br>
<table border='0' cellspacing='1' cellpadding='3' align='center' width='980' bgcolor='#000000'>
	<tr bgcolor='#cccccc'><td bgcolor='#cccccc' colspan='9 ' width='980' style='border:0px solid #000000'><b>Schede attive</b></td></tr>
	<?
		$artcol = 9;
		$fw     = 980;

		$res  = multi_query($dbh, "SELECT id, camera, titolare FROM schede WHERE stato=0 order by camera");
		$nr   = multi_num_rows($res);

		$w2    = $fw/$artcol;

		echo "";
		if ($nr==0){
			echo "<tr><td align='center' colspan='$artcol'  width='980' height='38' bgcolor='#ffffff'>Nessuna scheda attiva</td></tr>";
		}

		$cnt  = 0;
		for ($i=0; $i<ceil($nr/$artcol);$i++){
			echo "<tr>";
			for ($l=0; $l<$artcol; $l++){
				if ($cnt%2==0){
					$w=floor($w2);
				}else{
					$w=ceil($w2);
				}
				if ($cnt<$nr){
					$data = multi_fetch_array($res, $cnt);
					$onc  = " style='cursor:pointer' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" onclick=\"location.href='schede-modifica.php?id=".$data["id"]."'\"";
				}else{
					$onc  = "";
					$data=NULL;
				}

				echo "<td id='cam_".$data["id"]."' $onc bgcolor='#ffffff' width='$w' align='center' height='38' >".$data["camera"].(($data["titolare"])?("<br/>".substr($data["titolare"],0,6)):"")."&nbsp;</td>";
				$cnt++;
			}
			echo "</tr>";
		}
	?>
</table>
<br />
<table border='0' cellspacing='1' cellpadding='3' align='center' width='980' bgcolor='#000000'>
	<tr bgcolor='#cccccc'>
		<td bgcolor='#ffffff' colspan='9 ' width='980' style='border:0px solid #000000; cursor:pointer' onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#cccccc'" onclick="location.href='schede-stats.php'">Statistiche schede</td></tr>
</table>
</body>
</html>
