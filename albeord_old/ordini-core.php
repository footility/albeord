<?
include("core-config.php");

if (!$PRIVILEGI["ordini"]) redirect_to("home.php");

function write_table_cat($dbh, $idcat, $nomecat){
	$w=116;

	?>
	<table align='center' width='<?= $w ?>' bgcolor='#ffffff' cellspacing="1" cellpadding="2" id="sottocat_<?= $idcat ?>" style="display:none;">
		<tr><td bgcolor='#ffffff' width='<?= $w ?>' height='25' id="cel_scat_<?= $idcat ?>" onclick="mostra_from_subcat(<?= $idcat ?>, this)"><input type='button' name='aa' class="invisibile_nc" style="background-color:#cccccc;font-weight:bold;width:120px"  value='<?= $nomecat ?>' id='btn_scat_<?= $idcat ?>'></td></tr>
		<?
		$res=multi_query($dbh, "SELECT * FROM categorie WHERE dipendenza=".$idcat);
		$nr=multi_num_rows($res);

		for ($i=0; $i<$nr; $i++){
			$data=multi_fetch_array($res, $i);
			echo "<tr><td bgcolor='#ffffff' width=\"$w\"  height=\"25\" id=\"cel_scat_".$data["id"]."\" onclick=\"mostra_from_subcat(".$data["id"].")\"><input type='button' name='aa' value='   ".$data["nome"]."' id='btn_scat_".$data["id"]."' class='invisibile_nc' style='width:120px'></td></tr>";
		}
		?>
	</table>
	<?
}
function write_table_art($dbh, $idcat, $main=0){
	$c      = 150;
	$artcol = 4;

	$fw     = $c*$artcol;
	?>
	<table bgcolor='#000000' align='center' border='0' cellspacing='1' cellpadding='2' id="articoli_<?= $idcat ?>" style="display:none;">


	<tr><td align='center' style="background-color:#cccccc;font-weight:bold" bgcolor='#cccccc' colspan='<?= $artcol ?>'><?
	if ($main>0){
		echo multi_single_query($dbh, "SELECT nome FROM categorie WHERE id=".$main)." - ";
	}

	echo multi_single_query($dbh, "SELECT nome FROM categorie WHERE id=".$idcat)

	?></td></tr>

		<?
		$res  = multi_query($dbh, "SELECT * FROM articoli WHERE categoria=".$idcat);
		$nr   =multi_num_rows($res);
		if ($nr==0){
			echo "<tr><td align='center' colspan='$artcol' width='730' height='38'>Nessun articolo</td></tr>";
		}
		$cnt  = 0;
		$w    = floor($fw/$artcol);

		for ($i=0; $i<ceil($nr/$artcol);$i++){

			echo "<tr>";
			for ($l=0; $l<$artcol; $l++){
				if ($cnt<$nr){
					$data = multi_fetch_array($res, $cnt);
					$ocl  = "onMouseDown=\"colorize_down(this,new Array('".$data["id"]."','".addslashes(str_replace('"','&#34;',$data["nome"]))."','1',".intval($data["prezzo"])."))\"";
				}else{
					$data = NULL;
					$ocl  = "";

				}
				echo "<td id='art_".$data["id"]."' $ocl bgcolor='#ffffff' width='$w' height='38' ><input type='button' name='aa' value='".htmlentities($data["nome"], ENT_QUOTES)."' class='invisibile' style='width:150px'></td>";
				$cnt++;
			}
			echo "</tr>";
		}
		?>
	</table>
	<?
}

$cols = 5;
$res  = multi_query($dbh, "SELECT * FROM categorie WHERE dipendenza=0");

$nr   = $NMCAT=multi_num_rows($res);

$sottocxatw=120;

$w    = round((740-$sottocxatw)/$cols);

?>
<? head_page() ?>
<? top_menu() ?>
<script>var pezzi=new Array();</script>
<br>
<table width='980' height='400' border='0' cellspacing='0' cellpadding='0' align='center' bgcolor='#000000'>
	<tr>
		<td width='730' bgcolor='#ffffff' valign='top'>

			<table width='730' border='0' bgcolor='#ffffff' id="concludi" cellspacing='0' cellpadding='3' align='center' height='100%' style="display:none; border:1px solid #000000">
				<tr>
					<td colspan='2'><font size='3'><b>Camera:</b></font></td>
				</tr>
				<tr>
					<td valign='top'>
						<table cellspacing='1' cellpadding='3' class="keypad_class">
							<tr><td id="keypad_n_1"><input id="bot1" onmousedown="keypad(this, 1)" type='button' value='1' class="invisibile_btn"></td><td id="keypad_n_2"><input id="bot2" onmousedown="keypad(this, 2)" type='button' value='2' class="invisibile_btn"></td><td id="keypad_n_3"><input id="bot3" onmousedown="keypad(this, 3)" type='button' value='3' class="invisibile_btn"></td></tr>
							<tr><td id="keypad_n_4"><input id="bot4" onmousedown="keypad(this, 4)" type='button' value='4' class="invisibile_btn"></td><td id="keypad_n_5"><input id="bot5" onmousedown="keypad(this, 5)" type='button' value='5' class="invisibile_btn"></td><td id="keypad_n_6"><input id="bot6" onmousedown="keypad(this, 6)" type='button' value='6' class="invisibile_btn"></td></tr>
							<tr><td id="keypad_n_7"><input id="bot7" onmousedown="keypad(this, 7)" type='button' value='7' class="invisibile_btn"></td><td id="keypad_n_8"><input id="bot8" onmousedown="keypad(this, 8)" type='button' value='8' class="invisibile_btn"></td><td id="keypad_n_9"><input id="bot9" onmousedown="keypad(this, 9)" type='button' value='9' class="invisibile_btn"></td></tr>
							<tr><td id="keypad_n_-1"><input id="botDEL" onmousedown="keypad(this, -1)" type='button' value='DEL' class="invisibile_btn"></td><td id="keypad_n_0"><input id="bot0" onmousedown="keypad(this, 0)" type='button' value='0' class="invisibile_btn"></td><td id="keypad_n_-2"><input id="botok" onmousedown="keypad(this, -2)" type='button' value='OK' class="invisibile_btn"></td></tr>
						</table>
						<br>
						<table cellspacing='1' cellpadding='3' class="keypad_class">
							<tr>
								<td id="keypad_n_A"><input id="botA" onmousedown="keypad(this, -65)" type='button' value='A' class="invisibile_btn"></td>
								<td id="keypad_n_B"><input id="botB" onmousedown="keypad(this, -66)" type='button' value='B' class="invisibile_btn"></td>
								<td id="keypad_n_C"><input id="botC" onmousedown="keypad(this, -67)" type='button' value='C' class="invisibile_btn"></td>
								<td id="keypad_n_D"><input id="botD" onmousedown="keypad(this, -68)" type='button' value='D' class="invisibile_btn"></td>
								<td id="keypad_n_E"><input id="botE" onmousedown="keypad(this, -69)" type='button' value='E' class="invisibile_btn"></td>
							</tr>
						</table>
					</td>
					<td valign='top'>

						<form name='SENDORD' method='POST' action='ordini-concludi.php'>
							<input type='hidden' name='datiordine' value=''>
							<input type='text' name='camera' value='' style="font-size:40px;font-weight:bold;width:300px;height:50px; border:1 solid #000000">
							<br><br><br>
							<input type='submit' name='camera_submit' onclick="return crea_ordine();" value='Salva Ordine' style="background-color:#ffffff;font-size:30px;font-weight:bold;width:300px;height:50px; border:1 solid #000000">
							<br><br><br>
							<div align='right' nowrap style="width:300;height:80px">
								<span style="font-size:20px;" onclick="change_stato('SENDORD')"><img align='middle' id="stampatx" src='img/si.gif' border='0'></span>
								<span style="font-size:20px;" onclick="change_stato('SENDORD')">Manda in stampa</span>
								<input type='hidden' value='1' name='stampa'>
							</div>
						</form>
					</td>
				</tr>
			</table>

			<table width='730' height='100%' border='0' bgcolor='#000000' id="mcat" class="main_table" cellspacing='1' cellpadding='0' align='center'>
				<tr><td width='<?= $sottocxatw ?>' rowspan='<?= ceil($nr/$cols) ?>' idcat="mcat" id="cel_mcat_0">
					<input type='button' name='mostraartlib' value='Articoli liberi' id='btn_mcat_0'   class="invisibile" onclick="mostra(0)">
					<img src='dfgdfgdfg' width='<?= $sottocxatw ?>' height='2' border='0'>
				</td>
					<?
					for ($i=0; $i<$nr; $i++){
						$data=multi_fetch_array($res, $i);

						echo "<td width='$w' align='center' height='30' id='cel_mcat_".$data["id"]."' onclick='mostra(".$data["id"].")'><input type='button' id='btn_mcat_".$data["id"]."'  name='aa' value='".htmlentities($data["nome"], ENT_QUOTES)."' class='invisibile' style='width:120px'></td>";

						if (($i+1)%$cols==0 & $i!=($nr-1)){
							echo "</tr><tr>";

						}
					}

					for ($i=0;$i<($cols*ceil($nr/$cols))-$nr; $i++) echo "<td>&nbsp;</td>";

					?>
				</tr>
				<tr>
					<td align='center' valign='top' width='<?= $sottocxatw ?>' style="background-color:#cccccc"><div align='center' style="margin-top:2px">
					<?
						$res=multi_query($dbh, "SELECT id, nome FROM categorie WHERE dipendenza=0");
						$nr =multi_num_rows($res);

						for ($i=0; $i<$nr; $i++){
							$data=multi_fetch_array($res, $i);
							write_table_cat($dbh, $data["id"], $data["nome"]);
					}
					?></div>
					</td>
					<td align='center' valign='top' width='<?= (740-$sottocxatw) ?>' colspan='<?= $cols ?>' style="padding:2px">
					<table id="articoli_A" width='625' cellspacing='0' cellpadding='3' border='0' style="border:1px solid #ffffff;">
						<tr>
							<td colspan='3' align='center'><b>Seleziona una categoria di artcoli...</b></td>
						</tr>
						<tr>
					</table>
					<table id="articoli_0" width='625' cellspacing='0' cellpadding='3' border='0' style="border:1px solid #000000;display:none">
						<form name='articlib_frm'>
						<tr>
							<td colspan='3' style="background-color:#cccccc;" align='center'><b>Articolo libero</b></td>
						</tr>
						<tr>
							<td style="font-weight:bold;border-bottom:1px solid #000000" nowrap>Nome</td>
							<td style="font-weight:bold;border-bottom:1px solid #000000" nowrap>Prezzo singolo</td>
							<td style="font-weight:bold;border-bottom:1px solid #000000" nowrap>Quantit&agrave;</td>
							</tr>
						<tr>
							<td nowrap><input type='text' name='articolo' value='' class="artlibtx"></td>
							<td nowrap><input type='text' name='przz' value='' size="8" class="artlibtx" style="text-align:right;"> &euro;</td>
							<td nowrap><input type='text' name='quan' value='1' size="3" class="artlibtx" style="text-align:center"></td>
						</tr>
						<tr>
							<td colspan='3' nowrap align='center' height='25' style="background-color:#cccccc"><input style="border:1px solid #000000; background-color:#ffffff; height:25px; width:150px" id="ins_artlib_id" onclick="insert_artlib()" type='button' name='prz' value='Conferma'>&nbsp;</td>
						</tr>
						</form>
					</table>

					<?
						$res = multi_query($dbh, "SELECT id, dipendenza FROM categorie");
						$nr  = multi_num_rows($res);
						for ($i=0; $i<$nr; $i++){
							$data = multi_fetch_array($res, $i);
							write_table_art($dbh, $data["id"],$data["dipendenza"]);
						}
					?>
					</td>
				</tr>
			</table>
		</td>
		<td width='260' bgcolor='#ffffff' valign='top' style="background-color:#D0E7F8;padding:2px;border-top:1px solid #000000;border-bottom:1px solid #000000;border-right:1px solid #000000;">
		<div id="carrello_tbl" name="carrello_tbl"></div>
		</td>
	</tr>
</table>
</body>
<script language="Javascript">
var clicked_mcat=0;
var clicked_scat=0;
var clicked_arti=0;
function insert_artlib(){
	var err= '';
	var qu = parseInt(document.forms["articlib_frm"].elements["quan"].value);
	var pr = insert_prezzo(document.forms["articlib_frm"].elements["przz"].value);
	var nm = document.forms["articlib_frm"].elements["articolo"].value;
	if (!parseInt(qu)) err="Inserire la quantita";
	if (!pr)           err="Inserire il prezzo";
	if (nm.length==0)  err="Inserire il nome dell'articolo";
	if (err!=''){
		alert(err);
		return false;
	}else{
			id = hex_md5(nm);
			ins = new Array(id,nm,qu,pr);
			document.forms["articlib_frm"].elements["quan"].value="1";
			document.forms["articlib_frm"].elements["przz"].value="";
			document.forms["articlib_frm"].elements["articolo"].value="";
			colorize_down(document.getElementById("ins_artlib_id"),ins);

	}

}

function mostra(idcat){

	nascondi('articoli_'+clicked_arti);
	
	set_bg('cel_scat_'+clicked_scat, "#ffffff");
	set_bg('btn_scat_'+clicked_scat, "#ffffff");
	
	set_bg('btn_mcat_'+clicked_mcat, "#ffffff");
	set_bg('cel_mcat_'+clicked_mcat, "#ffffff");

	
	set_bg('cel_mcat_'+idcat, "#cccccc");
	set_bg('btn_mcat_'+idcat, "#cccccc");
	
	set_bg('cel_scat_'+idcat, "#cccccc");
	set_bg('btn_scat_'+idcat, "#cccccc");

	mostra_articoli(idcat);
	mostra_subcat(idcat);
	
	clicked_mcat=idcat;
}
function mostra_subcat(idcat){
	nascondi('sottocat_'+clicked_mcat);
	if (document.getElementById('sottocat_'+idcat)){
		document.getElementById('sottocat_'+idcat).style.display='block';
	}
	clicked_scat=idcat;
}

function mostra_from_subcat(idcat){

	nascondi('articoli_'+clicked_arti);
	
	set_bg('cel_scat_'+clicked_scat, "#ffffff");
	set_bg('btn_scat_'+clicked_scat, "#ffffff");
	
	set_bg('cel_scat_'+idcat, "#cccccc");
	set_bg('btn_scat_'+idcat, "#cccccc");
	
	mostra_articoli(idcat);
	clicked_scat=idcat;
}
function mostra_articoli(idcat){
	if (document.getElementById('articoli_'+idcat)){
		document.getElementById('articoli_'+idcat).style.display='block';
	}
	clicked_arti=idcat;
}

function colorize_down(obej,ary){
	aggiungi(ary);
	obej.style.backgroundColor='#cccccc';

	setTimeout("colorize_up('"+obej.getAttribute("id")+"')",100);
}
function colorize_up(obej){
	document.getElementById(obej).style.backgroundColor='#ffffff';
}

function keypad(obej,valore){
	frmname="SENDORD";
	var curr=document.forms[frmname].elements["camera"].value;
	if (valore>=0){
		var bis="";
		if (curr.indexOf("-")!=-1){
			bis=curr.substr(curr.indexOf("-"),2);
			document.forms[frmname].elements["camera"].value=curr.substr(0,curr.indexOf("-"));
		}
		document.forms[frmname].elements["camera"].value += valore+bis;

	}



	if (valore==-1){
		var cnc=1;
		if (curr.indexOf("-")!=-1){
			cnc=2;
		}
		document.forms[frmname].elements["camera"].value = curr.substr(0,(curr.length-cnc));
	}
	if (valore<-64){
		if (curr.indexOf("-")!=-1){
			document.forms[frmname].elements["camera"].value = curr.substr(0,(curr.length-2));
		}
		document.forms[frmname].elements["camera"].value += "-"+String.fromCharCode(Math.abs(valore));



	}

	obej.style.backgroundColor='#cccccc';


	setTimeout("colorize_up('"+obej.getAttribute("id")+"')",100);
}

function nascondi(cosa){
	if(document.getElementById(cosa))
		document.getElementById(cosa).style.display = 'none';
}
function set_bg(cosa, bg){
	if(document.getElementById(cosa))
		document.getElementById(cosa).style.backgroundColor=bg;
}

function aggiungi(ary){
	if (pezzi[ary[0]]){
		pezzi[ary[0]][2] = parseInt(pezzi[ary[0]][2]) + parseInt(ary[2]);
	}else{
		pezzi[ary[0]] = new Array();
		pezzi[ary[0]] = ary;
	}
	drawtable(pezzi, "carrello_tbl");
}

function concludi_funct(){
	document.getElementById("mcat").style.display="none";
	document.getElementById("concludi").style.display="block";
}

function drawtable(pezzi, into){
	var stringa="<table  width='100%' border='0' cellspacing='0' cellpadding='3' class='carrello'><tbody>";

	stringa +=  "<tr class='carrello_intest'><td width='100%' colspan='3' align='center'> - Conto - </td></tr>";
	stringa +=  "<tr class='carrello_intest'><td width='4%'></td><td width='66%'>Nome</td><td align='right' width='30%'>Prezzo</td></tr>";
	totale=0;
	conter=0;
	for (id in pezzi){
		if (pezzi[id]){
			totale  += (pezzi[id][3]*pezzi[id][2]);
			stringa += "<tr style='cursor:pointer' class='carrello_c"+((conter%2==0)?1:2)+"'><td height='40' onclick=\"sottrai('"+id+"')\">"+pezzi[id][2]+"</td><td onclick=\"sottrai('"+id+"')\">"+pezzi[id][1]+"</td><td align='right' onclick=\"sottrai('"+id+"')\">"+show_prezzo(pezzi[id][3]*pezzi[id][2])+" &euro;</td>";
			
//            stringa += "<td bgcolor='#cccc00' style='border-bottom:1px solid #000000' onclick=\"mostra('1')\">&nbsp;&nbsp;+&nbsp;&nbsp;</td>";
            stringa += "</tr>";
			conter++;
		}
	}
	stringa +=  "<tr class='carrello_intest'><td colspan='2'>Totale</td><td align='right'>"+show_prezzo(totale)+" &euro;</td></tr>";
	stringa +=  "<tr><td colspan='3' align='center'>&nbsp;</td></tr>";
	stringa +=  "<tr><td colspan='3' align='center' style='border:1px solid #000000; height:30px;cursor:pointer' onclick='concludi_funct()'>Concludi</td></tr>";
	stringa +=  "</tbody></table>";

	if (conter==0){
		stringa =	"<table width='100%' border='0' cellspacing='0' cellpadding='3' class='carrello'><tbody>";
		stringa +=  "<tr class='carrello_intest'><td colspan='3'>Nessun articolo</td></tr>";
		stringa +=  "</tbody></table>";
	}
	document.getElementById(into).innerHTML=stringa;
}
function sottrai(id){
	pezzi[id][2]--;
	if (pezzi[id][2]==0) pezzi[id]=null;
	drawtable(pezzi, "carrello_tbl");
}
function crea_ordine(){
	if (document.forms["SENDORD"].elements["camera"].value.length==0){
		alert("Specificare la camera!");
		return false;
	}
	if(confirm('Continuare?')){
		var stringa="";
		
		for (id in pezzi){
            if (pezzi[id]){
			 stringa += pezzi[id][0]+"@"+pezzi[id][1]+"@"+pezzi[id][2]+"@"+pezzi[id][3]+"|";
			}
		}
		document.forms["SENDORD"].elements["datiordine"].value=stringa;
		return true;
	}else{
		return false;
	}
}
</script>

