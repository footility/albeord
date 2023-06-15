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
$res=multi_query($dbh, "SELECT * FROM servizi ORDER BY tipo,nome ASC" );
for ($i=0; $i<multi_num_rows($res); $i++){
	$dataT=multi_fetch_array($res, $i);
	$Servizi[$dataT["id"]]=$dataT;
}

if($_REQUEST["elimina"]){
	multi_query($dbh, "DELETE  FROM schede_periodi WHERE id=".intval($_REQUEST["id"]));
	multi_query($dbh, "DELETE  FROM schede_prezzi_bambini WHERE idperiodo=".intval($_REQUEST["id"]));
	multi_query($dbh, "DELETE  FROM schede_presenze WHERE idperiodo=".intval($_REQUEST["id"]));
	multi_query($dbh, "DELETE  FROM schede_supplementi WHERE idperiodo=".intval($_REQUEST["id"]));
	$_REQUEST["id"]=$_REQUEST["idscheda"];
}

if($_REQUEST["elimina_sch"] && $PRIVILEGI["admin"]){	
	
	multi_query($dbh, "DELETE FROM schede_prezzi_bambini WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda=".intval($_REQUEST["id"]).")");
	multi_query($dbh, "DELETE FROM schede_presenze WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda=".intval($_REQUEST["id"]).")");
	multi_query($dbh, "DELETE FROM schede_supplementi WHERE idperiodo IN (SELECT id FROM schede_periodi WHERE idscheda=".intval($_REQUEST["id"]).")");
	
	multi_query($dbh, "DELETE FROM schede WHERE id=".intval($_REQUEST["id"]));
	
	multi_query($dbh, "DELETE FROM schede_periodi WHERE idscheda =".intval($_REQUEST["id"]));
	multi_query($dbh, "DELETE FROM schede_trattamenti_base WHERE idscheda=".intval($_REQUEST["id"]));
	multi_query($dbh, "DELETE FROM schede_servizi WHERE idscheda=".intval($_REQUEST["id"]));

  	redirect_to("schede-core.php");
	
}


if($_REQUEST["aggiungi"] || $_REQUEST["salva_periodo"]){
	if($_REQUEST["aggiungi"]){
		multi_query($dbh, "INSERT INTO schede_periodi (id,	idscheda) VALUES (NULL, ".intval($_REQUEST["idscheda"]).")" );
		$ID=mysql_insert_id();
	}else{
		$ID=$_REQUEST["id"];
	}
	$_REQUEST["dal"]    =mktime(0,0,1, $_REQUEST["d_m"], $_REQUEST["d_g"],$_REQUEST["d_a"]);
	$_REQUEST["al"]     =mktime(23,59,59, $_REQUEST["a_m"], $_REQUEST["a_g"],$_REQUEST["a_a"]);
	
	if($_REQUEST["tipo"]=="N"){
		$_REQUEST["fbb"]=$_REQUEST["fhb"]=$_REQUEST["ffb"]=0;
	}else{
		$_REQUEST["bb"]=$_REQUEST["hb"]=$_REQUEST["fb"]=0;
		$_REQUEST["sconto"]=array();
	}
	multi_query($dbh, "UPDATE schede_periodi SET

	adulti=".intval($_REQUEST["adulti"]).",
	bambini=".intval($_REQUEST["bambini"]).",
	dal=".intval($_REQUEST["dal"]).",
	al=".intval($_REQUEST["al"]).",
	hb=".insert_prezzo($_REQUEST["hb"]).",
	bb=".insert_prezzo($_REQUEST["bb"]).",
	fb=".insert_prezzo($_REQUEST["fb"]).",
	
	tipo='".$_REQUEST["tipo"]."',
	
	fhb=".insert_prezzo($_REQUEST["fhb"]).",
	fbb=".insert_prezzo($_REQUEST["fbb"]).",
	ffb=".insert_prezzo($_REQUEST["ffb"])."
	WHERE id=$ID");	
	
	if($_REQUEST["aggiungi"]){

		foreach ($Trattamenti as $idt =>  $trattamento){ 
			
			if(!multi_single_query($dbh, "SELECT idtrattamento FROM schede_trattamenti_base WHERE idtrattamento=$idt and idscheda=".intval($_REQUEST["idscheda"]) )){
				continue;
			}

			for ($j=unixtojd($_REQUEST["dal"]); $j<=unixtojd($_REQUEST["al"]); $j++){
	
				if($j==unixtojd($_REQUEST["dal"]) && $idt==$TrattamentiFirst){
					continue;
				}
				for ($q=1; $q<=intval($_REQUEST["adulti"]); $q++){
		
					multi_query($dbh, "INSERT INTO schede_presenze (idperiodo ,tipo ,persona ,giorno ,idtrattamento) 
						VALUES ($ID ,'A' ,$q  ,$j ,$idt)" );
				}
				for ($q=1; $q<=intval($_REQUEST["bambini"]); $q++){
					multi_query($dbh, "INSERT INTO schede_presenze (idperiodo ,tipo ,persona ,giorno ,idtrattamento) 
						VALUES ($ID ,'B' ,$q  ,$j ,$idt)" );
				}
				if($j==unixtojd($_REQUEST["al"])){
					break;
				}
			}			
		}
	}else{
		multi_query($dbh, "DELETE FROM schede_presenze WHERE idperiodo=$ID");
		if(is_array($_REQUEST["presenze"])){
			foreach ($_REQUEST["presenze"] as $jd => $trattamenti ){
				foreach ($trattamenti as $idt => $tipi){
					foreach ($tipi as $tipo => $persone){
						foreach ($persone as $persona => $si){
							if(($persona>intval($_REQUEST["adulti"]) && $tipo =="A") || ($persona>intval($_REQUEST["bambini"]) && $tipo =="B"))continue;
							multi_query($dbh, "INSERT INTO schede_presenze (idperiodo ,tipo ,persona ,giorno ,idtrattamento) 
							VALUES ($ID ,'$tipo' ,$persona  ,$jd ,$idt)" );
						}
					}
					
				}
			}
		}
	}
	multi_query($dbh, "DELETE FROM schede_prezzi_bambini WHERE idperiodo=$ID");
	foreach ((array)$_REQUEST["sconto"] as $idb => $sconto ){
		multi_query($dbh, "INSERT INTO schede_prezzi_bambini (idperiodo, bambino, sconto) VALUES ($ID, $idb,".insert_prezzo($sconto).")" );
	}
	multi_query($dbh, "DELETE FROM schede_supplementi WHERE idperiodo=$ID");
	foreach ((array)$_REQUEST["supplemento"] as $giorno => $sup ){
		if(insert_prezzo($sup)>0){
			multi_query($dbh, "INSERT INTO schede_supplementi (idperiodo, giorno, supplemento) VALUES ($ID, $giorno,".insert_prezzo($sup).")" );
		}
	}
	
	
	$_REQUEST["id"]=$_REQUEST["idscheda"];

}

if($_REQUEST["salva"]){

	$ID=intval($_REQUEST["id"]);
	
	multi_query($dbh, "UPDATE schede SET camera='".addslashes($_REQUEST["camera"])."', titolare='".addslashes($_REQUEST["titolare"])."', note='".addslashes($_REQUEST["note"])."' WHERE id=$ID");
	
	
	multi_query($dbh, "DELETE FROM schede_trattamenti_base WHERE idscheda=$ID");
	if(is_array($_REQUEST["trattamenti_base"])){
		foreach ($_REQUEST["trattamenti_base"] as $idt ){
			multi_query($dbh, "INSERT INTO schede_trattamenti_base (idscheda, idtrattamento) VALUES ($ID, $idt)" );
		}
	}
		
	multi_query($dbh, "DELETE FROM schede_servizi WHERE idscheda=$ID");
	foreach ($_REQUEST["servizi"] as $ids => $prezzo ){
		if(insert_prezzo($prezzo)>0){
			multi_query($dbh, "INSERT INTO schede_servizi (idscheda, idservizio,prezzo) VALUES ($ID, $ids,".insert_prezzo($prezzo).")" );
		}
	}
}
if($_REQUEST["salva_2"]){
	$ID=intval($_REQUEST["id"]);
	
	if($_REQUEST["salva_2"]=="Chiudi"){
		multi_query($dbh, "UPDATE schede SET stato=1 WHERE id=$ID");
		redirect_to("schede-core.php");
	}
	if($_REQUEST["salva_2"]=="Stampa" || $_REQUEST["salva_2"]=="Stampa e chiudi"){
		stampawbs($dbh, $ID, 10);
		if($_REQUEST["salva_2"]=="Stampa e chiudi"){
			multi_query($dbh, "UPDATE schede SET stato=1 WHERE id=$ID");
			?><script type="text/javascript">location.href='schede-core.php';</script><?
			exit;
		}
	}
}






$Periodi=array();

if($_REQUEST["nuova"] || (count($Periodi)==0 && $_REQUEST["id"])){
	if($_REQUEST["nuova"]){
		multi_query($dbh, "INSERT INTO schede (id, camera, titolare) VALUES (NULL,'".$_REQUEST["camera"]."','".$_REQUEST["titolare"]."')" );
		$_REQUEST["id"]=mysql_insert_id() ;
		if(is_array($_REQUEST["trattamenti_base"])){
			foreach ($_REQUEST["trattamenti_base"] as $idt ){
				multi_query($dbh, "INSERT INTO schede_trattamenti_base (idscheda, idtrattamento) VALUES (".$_REQUEST["id"].", $idt)" );
			}
		}
	}
		
	$schdede_LAST["dal"] = time();
	$schdede_LAST["al"]  = strtotime("+7days");
	$schdede_LAST["tipo"]="N";

	//alcuni valori predefiniti
	$dataOld=multi_single_query($dbh, "SELECT * FROM schede_periodi WHERE idscheda=(SELECT max(idscheda) FROM schede_periodi WHERE tipo='N')","ALL");	
	$schdede_LAST["bb"]=$dataOld["bb"];
	$schdede_LAST["hb"]=$dataOld["hb"];
	$schdede_LAST["fb"]=$dataOld["fb"];
	
	//alcuni valori predefiniti
	$dataOld=multi_single_query($dbh, "SELECT * FROM schede_periodi WHERE idscheda=(SELECT max(idscheda) FROM schede_periodi WHERE tipo!='N')","ALL");	
	$schdede_LAST["fbb"]=$dataOld["fbb"];
	$schdede_LAST["fhb"]=$dataOld["fhb"];
	$schdede_LAST["ffb"]=$dataOld["ffb"];	

	
}
if($_REQUEST["id"]){
	
	$IDS=intval($_REQUEST["id"]);
	
	$scheda=multi_single_query($dbh, "SELECT * FROM schede WHERE id=$IDS","ALL");	

	$res=multi_query($dbh, "SELECT * FROM schede_trattamenti_base WHERE idscheda=$IDS" );
	for ($i=0; $i<multi_num_rows($res); $i++){
		$dataT=multi_fetch_array($res, $i);
		$TrattamentiBaseAbiltati[$dataT["idtrattamento"]]=$dataT["idtrattamento"];
	}	
	$res=multi_query($dbh, "SELECT * FROM schede_servizi WHERE idscheda=$IDS" );
	for ($i=0; $i<multi_num_rows($res); $i++){
		$dataT=multi_fetch_array($res, $i);
		$ServiziAttivi[$dataT["idservizio"]]=$dataT["prezzo"];
	}
	$res=multi_query($dbh, "SELECT * FROM schede_periodi WHERE idscheda=$IDS order by dal, id" );
	for ($i=0; $i<multi_num_rows($res); $i++){
		$Periodi[]=multi_fetch_array($res, $i);
	}


}



?>
<script type="text/javascript">
function $(id){
	return document.getElementById(id);
}
function autocheck(o,id, a, b){
	for (i=1; i<=a; i++){
		if ($(id+'_A_'+i)) $(id+'_A_'+i).checked=o.checked;
	}

	for (i=1; i<=b; i++){
		if ($(id+'_B_'+i)) $(id+'_B_'+i).checked=o.checked;
	}
}
function autocheckDisableLow(o, jd, trt, idp, tipo, i,ids){
	for (j=0; j<ids.length; j++){
		if(ids[j]!=trt){
			id2='id_'+jd+'_'+ids[j]+'_'+idp;
			if ($(id2+'_'+tipo+'_'+i)){
				$(id2+'_'+tipo+'_'+i).disabled=!o.checked;
			}
		}
	}
}

function autocheckDisable(o, jd, trt, idp, a, b,ids){
	id='id_'+jd+'_'+trt+'_'+idp;
	for (j=0; j<ids.length; j++){
		if(ids[j]!=trt){
			id2='id_'+jd+'_'+ids[j]+'_'+idp;
			if ($(id2)){
				$(id2).disabled=!o.checked;
			}
		}
	}
	
	for (i=1; i<=a; i++){
		if ($(id+'_A_'+i)){
			$(id+'_A_'+i).checked=o.checked;
		}
		autocheckDisableLow(o, jd, trt, idp, 'A', i,ids);
	}
	for (i=1; i<=b; i++){
		if ($(id+'_B_'+i)){
			$(id+'_B_'+i).checked=o.checked;
		}
		autocheckDisableLow(o, jd, trt, idp, 'B', i,ids);
	}
	
}


function autocheckAll(o,idt){
	<? for ($i=unixtojd($data["dal"]); $i<=unixtojd($data["al"]); $i++){ ?>
		if ($('id_<?= $i ?>_'+idt)){
			$('id_<?= $i ?>_'+idt).checked=o.checked;
			autocheck($('id_<?= $i ?>_'+idt),'id_<?= $i ?>_'+idt);
		}
	<? } ?>
}

<? 

if($_REQUEST["nuova"] && is_array($_REQUEST["trattamenti_base"])){?>
	window.onload=function (){
		<? foreach ($_REQUEST["trattamenti_base"] as $idt ){ ?>
			$('tr_bs_<?=$idt ?>').checked=true;
			autocheckAll($('tr_bs_<?=$idt ?>'),'<?= $idt ?>');
		<? } ?>
	}
<? 
}
?>
function colora_disab(obj,p){
	$('tipo_n_td'+p).style.visibility=((obj.id==('tipo_n'+p) && obj.checked)?'visible':'hidden');
	$('tipo_f_td'+p).style.visibility=((obj.id==('tipo_f'+p) && obj.checked)?'visible':'hidden');
}

function getStorico(str){
	w=window.open('schede-storico.php?titolare='+escape(str),'storico', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=500,height=500,left = 390,top = 262');
	w.focus();
}

function prt(id){
	//alert($dbh);
	//alert($ID);
	
	var file="C:\\print.bat "+id;
	alert(id);
	var wshShell = new ActiveXObject("WScript.Shell");
   wshShell.Run(file);
   //debugger;
     return true;
    } 
</script>
<br>


<table width='980' border='0' cellspacing='0' cellpadding='0' bgcolor='#000000' align='center'>
<tr>
<td width="600" valign="top" bgcolor="white">

	<? 
		foreach ($Periodi as $idp => $data){
			include("schede-modifica-periodo.php");
			if(unixtojd($data["al"])<unixtojd($Periodi[($idp+1)]["dal"]) && $Periodi[($idp+1)]){
				?><div style="color:red">Attenzione peridi staccati</div><br /><?
			}elseif(unixtojd($data["al"])>unixtojd($Periodi[($idp+1)]["dal"]) && $Periodi[($idp+1)]){
				?><div style="color:red">Attenzione peridi sovraposti</div><br /><?
			}
		}

	 ?>
	 <? if(count($Periodi)>0){ ?>
	<a style="margin:1em;display:block; text-decoration:none " href="#" onclick="toggle('agg'); return false;">Aggiungi periodo</a>
	<? } ?>
	<div id="agg" <? if(count($Periodi)>0){ ?> style="display:none"<? } ?>>
	<? 
		$data=$schdede_LAST;
		$NUOVO=1;
		$idp=-1;
		include("schede-modifica-periodo.php");
	 ?>
	 </div>

</td>
<td valign="top" bgcolor="white" style="padding-left:5px;">
	<form method="post">
		<input type="hidden" name="id" value="<?= $scheda["id"] ?>" />
		<table border='0' cellspacing='0' cellpadding='3' align='center' width='100%' style='border:1px solid #888888'>
			<tr>
				<td bgcolor='#cccccc' colspan="2" style='border:1px solid #ffffff'><b>Dati scheda</b></td>
			</tr>
			<tr>
				<td width='50%'><b>Camera</b></td>
				<td width='50%'><b>Titolare</b></td>
			</tr>
			<tr>
				<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000' valign="top">
				<input class="inptext" type="text" name="camera" size="6" value="<?= $scheda["camera"] ?>" autocomplete="off" /></td>
				<td bgcolor='#FFFFFF'  width='50%' style='border:0px solid #000000' valign="top">
				<input  class="inptext" type="text" name="titolare" style="width:100%" value="<?= $scheda["titolare"] ?>" autocomplete="off" />
				<input type="button" name="schede" value="Storico cliente" style="width:100%"  onclick="getStorico(this.form.elements['titolare'].value)" class="inpbtn" />
				</td>
			</tr>
			<tr>
				<td  colspan="2"><b>Trattamenti</b></td>
			</tr>
			<tr>
			<td bgcolor='#ffffff' colspan="2" style='border:1px solid #ffffff'>
				<? foreach ($Trattamenti as $idt =>  $trattamento){ ?>
					
						<input type="checkbox" name="trattamenti_base[<?=$idt ?>]"  value="<?=$idt ?>"
						 id="tr_bs_<?=$idt ?><?=  $data["id"] ?>" <?= ( $idt==$TrattamentiFirst|| $TrattamentiBaseAbiltati[$idt])?"checked":"" ?> />
						<label for="tr_bs_<?=$idt ?><?=  $data["id"] ?>"><?=$trattamento ?></label>
					
				<? } ?>
				</td>
			</tr>
				
		<tr>
			<td width='50%'><b>Servizio</b></td>
			<td width='50%'><b>Prezzo</b></td>
		</tr>
		<? foreach ($Servizi as $ids=>$servizio){ ?>
		<tr>
			<td bgcolor='#FFFFFF' width='50%' style='border:0px solid #000000'><?= $servizio["nome"] ?></td>
			<td bgcolor='#FFFFFF'  width='50%' style='border:0px solid #000000'>
				<span style="font-family:'Courier New', Courier, monospace"><?= ($servizio["tipo"]==1?"+":"-") ?></span>
				<input  class="inptext" type="text" name="servizi[<?= $ids ?>]" size="7" style="text-align:right"  autocomplete="off"  value="<?= ($ServiziAttivi[$ids])?show_prezzo($ServiziAttivi[$ids]):"" ?>"/> &euro;
			</td>
		</tr>
		<? } ?>	
		<tr>
			<td  colspan="2"><b>Note</b></td>
		</tr>
		<tr>
			<td  colspan="2">
				<textarea rows="4" cols="" style="width: 100%" name="note"><?=  htmlspecialchars($scheda["note"]) ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" style="width:10em" name="salva" value="Salva" class="inpbtn" />
			</td>

		</tr>
	</table>
	
	</form>
	
	<? include("schede-riepilogo.php"); ?>
	</td>
</tr>
<tr>
	<td colspan="2">
		
	
	<form method="post" style="display:inline">
		<input type="hidden" name="id" value="<?= $scheda["id"] ?>" />
		<table border='0' cellspacing='0' cellpadding='3' align='center' width='100%' style='border:1px solid #888888'>
			<tr>
				<td valign="top" width="33%" bgcolor='#cccccc'  tyle='border:1px solid #ffffff'>
				<!--<input type="submit" name="salva" value="  Salva  " class="inpbtn" />-->
				</td>
				<td valign="top" width="34%"  bgcolor='#cccccc' align="center"  tyle='border:1px solid #ffffff'>
				<div class="print error"></div>
				<input style="width:10em"  type="submit"  name="salva_2" value="Stampa" class="inpbtn" /><br /><br /><br />
				<input style="width:10em"  type="submit"  name="salva_2" value="Stampa e chiudi" class="inpbtn" onclick="return confirm('Stampare e chidere la scheda associata alla camera <?= $scheda["camera"] ?>? (la scheda verra archiviata)')" />
				
				</td>
				
				<td valign="top" width="33%"  bgcolor='#cccccc' align="right"  tyle='border:1px solid #ffffff'>
				
					<input style="width:10em" type="submit" name="salva_2" value="Chiudi" class="inpbtn" onclick="return confirm('Chiudere la scheda associata alla camera <?= $scheda["camera"] ?>? (la scheda verra archiviata)')" />
						<? if($PRIVILEGI["admin"]){ ?><br /><br /><br />
					<input style="width:10em"  type="submit" name="elimina_sch"  value="Elimina" class="inpbtn" onclick="return confirm('Eliminare la scheda associata alla camera <?= $scheda["camera"] ?>? (la scheda verra completamente eliminata)')" />
				<? } ?>
				</td>
			</tr>
		</table>
	</form>
	
	</td>
</tr>
</table>

</body>
</html>
