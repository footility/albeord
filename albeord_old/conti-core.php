<?
include("core-config.php");

if (!$PRIVILEGI["conti"]) redirect_to("home.php");

head_page();

top_menu();
?>
<br>
	<?
		$artcol = 9;
		$fw     = 980;

		$res  = multi_query($dbh, "SELECT distinct camera FROM ordini WHERE stato=0 order by camera");
		$nr   = multi_num_rows($res);

		$w2    = $fw/$artcol;

		echo "<table border='0' cellspacing='1' cellpadding='3' align='center' width='980' bgcolor='#000000'>";
		echo "<tr bgcolor='#cccccc'><td bgcolor='#cccccc' colspan='$artcol ' width='980' style='border:0px solid #000000'><b>Camere attive</b></td></tr>";
		if ($nr==0){
			echo "<tr><td align='center' colspan='$artcol'  width='980' height='38' bgcolor='#ffffff'>Nessuna camera attiva</td></tr>";
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
					$onc  = " style='cursor:pointer' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" onclick=\"location.href='conti-camera.php?camera=".$data["camera"]."'\"";
				}else{
					$onc  = "";
					$data=NULL;
				}

				echo "<td id='cam_".$data["id"]."' $onc bgcolor='#ffffff' width='$w' align='center' height='38' >".$data["camera"]."&nbsp;</td>";
				$cnt++;
			}
			echo "</tr>";
		}
	?>
</table>
<br />
<table border='0' cellspacing='1' cellpadding='3' align='center' width='980' bgcolor='#000000'>
	<tr bgcolor='#cccccc'>
		<td bgcolor='#ffffff' colspan='9 ' width='980' style='border:0px solid #000000; cursor:pointer' onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#cccccc'" onclick="location.href='conti-recupera.php'">Ricerca conti</td></tr>
</table>
</body>
