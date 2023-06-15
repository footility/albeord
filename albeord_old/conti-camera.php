<?
include("core-config.php");

$IDcamera = $_REQUEST["camera"];

if (!$PRIVILEGI["conti"]) redirect_to("home.php");
if(!$IDcamera)            redirect_to("conti-core.php");

head_page();
top_menu()
?>
<br>
	<table width='980' align='center' cellspacing='0' cellpadding='3'>
		<tr>
			<td bgcolor="#ffffff" style="border:1px solid #000000" colspan="2" align='center'>
			<b>Camera: <font size="+1"><?= $IDcamera ?></font></b>
			</td>
		</tr>
		<tr>
			<td align='center' width='490'>
			<b>Totale</b>
			</td>
			<td align='center' width='490'>
			<b>Parziale</b>
			</td>
		</tr>
		<tr>
			<td align='center' width='490' valign='top'>
			<div id="totale"></div>
			</td>
			<td align='center' width='490' valign='top'>
			<div id="parziale"></div>
			</td>
		</tr>
	</table>

	<form name='conticam' method='POST' action='conti-concludi.php'>
		<input type='hidden' name='azione' value=''>
		<input type='hidden' name='articoli' value=''>
		<input type='hidden' name='camera' value='<?= $IDcamera ?>'>
		<input type='hidden' value='1' name='stampa'>
	</form>
</body>
</html>
<script language="Javascript">

function drawtable(pezzis, into){
	if (into=="totale")   azione="sposta";
	if (into=="parziale") azione="ripristina";

	var stringa="<table width='100%' border='0' cellspacing='0' cellpadding='3' class='carrello'><tbody>";

	stringa +=  "<tr class='carrello_intest'><td width='4%'></td><td width='66%'>Nome</td><td align='right' width='30%'>Prezzo</td></tr>";
	var totale=0;
	var conter=0;
	for (id in pezzis){
		l=pezzis[id][-1].length;
		if (l>0){
			totale  += pezzis[id][3]*l;
			stringa += "<tr style='cursor:pointer' onclick=\""+azione+"('"+id+"')\" class='carrello_c"+((conter%2==0)?1:2)+"'><td height='30'>"+l+"</td><td>"+pezzis[id][1]+"</td><td align='right'>"+show_prezzo(pezzis[id][3]*l)+" &euro;</td></tr>";
			conter++;
		}
	}
	stringa +=  "<tr class='carrello_intest'><td colspan='2'>Totale</td><td align='right'>"+show_prezzo(totale)+" &euro;</td></tr>";
	if (into=="totale"){
		stringa +=  "<tr><td colspan='2'>&nbsp;</td><td align='right' onclick='sposta_tutto();'><b><font color='#0000ff'>Sposta tutto &raquo;</font></b></td></tr>";
	}else{
		stringa +=  "<tr><td colspan='2'>&nbsp;</td><td align='right' onclick='ripristina_tutto();'><b><font color='#0000ff'>&laquo; Ripristina tutto</font></b></td></tr>";
		stringa +=  "<tr><td colspan='3' align='center'>&nbsp;</td></tr>";
		stringa +=  "<tr><td colspan='3' align='center'><input class='stornicontibtn' type='button' onclick='concludi_camera(0)' name='' value='Conto'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='stornicontibtn' type='button' onclick='concludi_camera(2)' name='' value='Preventivo'>";
		<? if ($PRIVILEGI["storni"]){ ?>
		stringa += "<br><br><input style='height:25px;font-size:15px;width:100px; ' class='stornicontibtn' type='button' onclick='concludi_camera(1)' name='' value='Storno'>";
		<? } ?>
		stringa += "</td></tr>";

		stringa +=  "<tr><td colspan='3' align='center'>";
		stringa +=  "<span style='font-size:20px;' onclick=\"change_stato('conticam')\"><img align='middle' id='stampatx' src='img/si.gif' border='0'> Manda in stampa</span>";
		stringa +=  "</td></tr>";
	}

	stringa +=  "</tbody></table>";

	if (conter==0){
		stringa =		"<table width='100%' border='0' cellspacing='0' cellpadding='3' class='carrello'><tbody>";
		stringa +=  "<tr class='carrello_intest'><td colspan='3'>Nessun articolo</td></tr>";
		stringa +=  "</tbody></table>";
	}
	document.getElementById(into).innerHTML=stringa;
}

var pezzi= new Array();
var parz = new Array();

<?

	$res=multi_query($dbh, "SELECT * FROM ordini WHERE stato=0 AND camera='$IDcamera' order by articolo");
	$nr = multi_num_rows($res);
	if ($nr==0){
		redirect_to("conti-core.php");
	}
	for ($i=0; $i<$nr; $i++){
		$data = multi_fetch_array($res, $i);
		$id   = md5($data["articolo"].$data["prezzo"]);
		$Pez[$id]["articolo"]= $data["articolo"];
		$Pez[$id]["prezzo"]  = $data["prezzo"];
		$Pez[$id]["ids"][]   = $data["id"];
	}

	foreach ($Pez as $md5code => $data){
		echo "pezzi['$md5code']    = new Array();
					pezzi['$md5code'][1] = '".addslashes($data["articolo"])."';
					pezzi['$md5code'][3] = ".$data["prezzo"]."
					pezzi['$md5code'][-1]= [".implode(",", $data["ids"])."];\n";

		echo "parz['$md5code']    = new Array();
					parz['$md5code'][1] = '".addslashes($data["articolo"])."';
					parz['$md5code'][3] = ".$data["prezzo"].";
					parz['$md5code'][-1]= new Array();\n";
	}
?>


drawtable(pezzi, "totale");
drawtable(parz, "parziale");

function sposta_tutto(){
	for (md5code in pezzi){
		while(pezzi[md5code][-1].length>0){
			sposta(md5code, 1);
		}
	}
	drawtable(pezzi, "totale");
	drawtable(parz, "parziale");
}
function ripristina_tutto(){
	for (md5code in parz){
		while(parz[md5code][-1].length>0){
			ripristina(md5code, 1);
		}
	}
	drawtable(pezzi, "totale");
	drawtable(parz, "parziale");
}
function sposta (md5code, draw){
	idart=pezzi[md5code][-1].pop();
	parz[md5code][-1].push(idart);
	if (!draw){
		drawtable(pezzi, "totale");
		drawtable(parz, "parziale");
	}
}
function ripristina (md5code, draw){
	idart=parz[md5code][-1].pop();
	pezzi[md5code][-1].push(idart);
	if (!draw){
		drawtable(pezzi, "totale");
		drawtable(parz, "parziale");
	}
}



function concludi_camera(operazione){
	if (operazione==1){//storno
		document.forms["conticam"].elements["azione"].value=2;
    }else if(operazione==2){//preventivo
        document.forms["conticam"].elements["azione"].value=3;
	}else if(operazione==0){//conto
		document.forms["conticam"].elements["azione"].value=1;
	}
	if(confirm('Continuare?')){
		var stringa="";
		for (md5code in parz){
			for(chiave in parz[md5code][-1]){
				stringa +=parz[md5code][-1][chiave]+",";
			}
		}
		document.forms["conticam"].elements["articoli"].value=stringa;
		document.forms["conticam"].submit();
	}


}
</script>
