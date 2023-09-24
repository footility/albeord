<?php
include("core-config.php");

if (!$PRIVILEGI["utenti"]) redirect_to("home.php");

head_page();
top_menu();



if ($_REQUEST["dove"]) $_SESSION["dove"] =basename($_REQUEST["dove"]);
if (!$_SESSION["dove"]) $_SESSION["dove"]="creacat";

?>
<br>
<table width='980' height='400' border='0' cellspacing='0' cellpadding='2' align='center'>
	<tr>
		<td width='200' valign='top'>
			<table width='200' border='0' cellspacing='1' cellpadding='2' align='center' bgcolor='#000000'>
				<tr><td onclick="location.href='?dove=gruppi'"   <?= (($_SESSION["dove"]=="gruppi")?"bgcolor='#cccccc'":"bgcolor='#ffffff'") ?>>Gruppi</td></tr>
				<tr><td onclick="location.href='?dove=utenti'"   <?= (($_SESSION["dove"]=="utenti")?"bgcolor='#cccccc'":"bgcolor='#ffffff'") ?>>Utenti</td></tr>
			</table>
		</td>
		<td width='776' valign='top'>
			<table width='776' border='0' cellspacing='1' cellpadding='2' align='center' bgcolor='#000000'>
				<tr><td bgcolor='#ffffff'>
				<?php if (is_file("utenti-".$_SESSION["dove"].".php"))  include("utenti-".$_SESSION["dove"].".php"); else echo "Seleziona un elenento dal menu di sinistra..."; ?>
				</td></tr>
		</td>
	</tr>
</table>
</body>
