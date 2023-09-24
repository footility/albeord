<?php
if (!$PRIVILEGI["utenti"]) redirect_to("home.php");

function disegautenti($dbh,$sel=''){
	$res=multi_query($dbh, "SELECT * FROM utenti WHERE eliminato=0");
	for($i=0;$i<multi_num_rows($res);$i++){
		$data=multi_fetch_array($res, $i);
		echo "<tr><td onclick=\"location.href='?selected=".$data["id"]."'\" ".(($sel==$data["id"])?"bgcolor='#cccccc'":"bgcolor='#ffffff'").">".$data["id"]." - ".htmlentities($data["utente"])."</td></tr>\n";
	}
	echo "<tr><td onclick=\"location.href='?selected=0'\" ".(($sel=="0")?"bgcolor='#cccccc'":"bgcolor='#ffffff'").">[Nuovo Utente]</td></tr>\n";

}

function disegnagruppi($dbh,$sel=0){
	$res=multi_query($dbh, "SELECT * FROM gruppi");
	for($i=0;$i<multi_num_rows($res);$i++){
		$data=multi_fetch_array($res, $i);
		echo "<option value='".$data["id"]."' ".(($data["id"]==$sel)?"selected":"").">".htmlentities($data["nome"])."</option>\n";
	}
}


$err=array();
$_REQUEST["id"]=intval($_REQUEST["id"]);


if (strlen($_REQUEST["elimina"])>0){
	multi_query($dbh, "UPDATE utenti SET eliminato=1 WHERE id=".$_REQUEST["id"]);
	$_REQUEST=array();
}
if (strlen($_REQUEST["invia"])>0){

	if (strlen($_REQUEST["utente"])<2){
		$err[0]="<span class='error'>Il nome utente deve essere di almeno 5 caratteri</span>";
	}
	if (($_REQUEST["pwd1"]!=$_REQUEST["pwd2"] | strlen($_REQUEST["pwd1"])<7) & ($_REQUEST["id"]==0)){
		$err[1]="<span class='error'>La password deve essere di almeno 8 caratteri e i 2 campi password devono corrispondere</span>";
	}
	if (($_REQUEST["id"]>0 & strlen($_REQUEST["pwd1"])>0) & ($_REQUEST["pwd1"]!=$_REQUEST["pwd2"] |  strlen($_REQUEST["pwd1"])<7)){
		$err[1]="<span class='error'>La password deve essere di almeno 8 caratteri e i 2 campi password devono corrispondere</span>";
	}
	if (multi_single_query($dbh,"SELECT count(id) FROM utenti WHERE utente='".$_REQUEST["utente"]."' AND id<>".$_REQUEST["id"])>0){
		$err[2]="<span class='error'>Nome utente gia in uso</span>";
	}
	if (sizeof($err)==0){
		if ($_REQUEST["id"]){
			$IDAssegnato=$_REQUEST["id"];
		}else{
			$IDAssegnato=multi_nextval($dbh, "utenti");
			multi_query($dbh, "INSERT INTO utenti (id) VALUES ($IDAssegnato)");
		}
		multi_query($dbh, "
			UPDATE
				utenti
			SET
				utente     = '".$_REQUEST["utente"]."',
				privilegi  = '".serialize($_REQUEST["privilegi"])."',
				pathstampa = '".$_REQUEST["pathstampa"]."',
				idgruppo   = '".intval($_REQUEST["idgruppo"])."',
				stampante  = '".$_REQUEST["stampante"]."'
			WHERE id = $IDAssegnato");

		if ($_REQUEST["pwd1"]){
			multi_query($dbh, "UPDATE utenti SET password='".md5($_REQUEST["pwd1"])."' WHERE id = $IDAssegnato");
		}
		$_REQUEST["selected"]=$IDAssegnato;
		$msg= "<span class='msgok'>Modifica eseguita</span>";
	}else{
		$data["utente"]=stripslashes($_REQUEST["utente"]);
		$data["privilegi"]=$_REQUEST["privilegi"];
		foreach ($err as $errore) $msg.= $errore."<br>";
	}
}
echo "<center>$msg</center>";


?>
<table>
	<tr>
		<td valign='top'>
			<table cellspacing='1' cellpadding='2' bgcolor='#000000' width='200'>
				<?php disegautenti($dbh,$_REQUEST["selected"]); ?>
			</table>
		</td>
		<td valign='top' align='center'>
		<?php if(isset($_REQUEST["selected"])){
		if (sizeof((array)$err)==0){
			$data=multi_single_query($dbh, "SELECT * FROM utenti WHERE id=".intval($_REQUEST["selected"]), ALL);
			$data["privilegi"]=unserialize($data["privilegi"]);
			if (!$data["pathstampa"]) $data["pathstampa"]="C:\\Programmi\\Mercurio_Sistemi\\Albeord\\";
		}
		?>
			<table  cellspacing='0' cellpadding='3' width='400'>
					<form method='POST'>
					<input type='hidden' name='id' value='<?= intval($data["id"]) ?>'>
					<tr bgcolor='#cccccc'>
						<td align='center' colspan='2' style="border-bottom:1px solid #ffffff">Utente:<?= intval($_REQUEST["selected"]) ?></td>
					</tr>

					<tr><td bgcolor='#cccccc'>Nome</td><td><input type='text' name='utente' value='<?= htmlentities($data["utente"],ENT_QUOTES) ?>' class="inptext"></td></tr>
					<tr><td bgcolor='#cccccc'>Password</td><td><input type='password' name='pwd1'  value='' class="inptext"></td></tr>
					<tr><td nowrap bgcolor='#cccccc'>Riperti password</td><td><input type='password' name='pwd2'  value='' class="inptext"></td></tr>
					<tr><td nowrap bgcolor='#cccccc'>Gruppo</td><td><select name='idgruppo'><?php disegnagruppi($dbh, $data["idgruppo"])  ?></select></td></tr>
					<tr><td nowrap bgcolor='#cccccc'>Path Progrmma Stampa</td><td><input type='text' name='pathstampa'  size='40' value='<?= htmlentities($data["pathstampa"],ENT_QUOTES) ?>' class="inptext"><br>(spazi non consentiti)</td></tr>
					<tr><td nowrap bgcolor='#cccccc'>Nome Stampante</td><td><input type='text' name='stampante'  size='40' value='<?= htmlentities($data["stampante"],ENT_QUOTES) ?>' class="inptext"><br>(sono consentite anche le stampanti di rete tipo "\\SERVER\STAMPANTE". se il campo non  compilato verra usata la stampante predefinita di sistema)</td></tr>
					<tr><td bgcolor='#cccccc' valign='top'>Privilegi</td><td>
					<?php foreach($GlobPrivilegi as $key => $val){ ?>
						<input type='checkbox' name='privilegi[<?= $key ?>]' value='1' <?= (($data["privilegi"][$key]==1)?"checked":"") ?>> <?= $val ?><br>
					<?php } ?>
						<input type='checkbox' name='privilegi[storni]' value='1' <?= (($data["privilegi"]["storni"]==1)?"checked":"") ?>> Storni<br>
					</td>
					</tr>
						<tr bgcolor='#cccccc'>
						<td align='right'><input type='submit' name='invia' value='  Salva  ' class="inpbtn"></td>
						<td align='right'><input type='submit' name='elimina' value='  Elimina  ' class="inpbtn" onclick="return confirm('Confermi l\'eliminazione dell\'utente? Questo comporter l\'eliminazione di tutti gli ordini eseguiti dall\'utente.')"></td>
					</tr>
					</form>
			</table>
			<?php }else{	?>
				Seleziona un utente...
			<?php } ?>
		</td>
	</tr>
</table>
