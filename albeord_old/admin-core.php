<?php
include("core-config.php");

if (!$PRIVILEGI["admin"]) redirect_to("home.php");

head_page();
top_menu();



if ($_REQUEST["dove"]) $_SESSION["dove"] =basename($_REQUEST["dove"]);

?>
<br>
<table width='980' height='400' border='0' cellspacing='0' cellpadding='2' align='center'>
	<tr>
		<td width='200' valign='top'>
			<table style='cursor:pointer' width='200' border='0' cellspacing='1' cellpadding='2' align='center' bgcolor='#000000'>
				<tr><td onclick="location.href='?dove=creacat'"  <?= (($_SESSION["dove"]=="creacat")?"bgcolor='#cccccc'":"bgcolor='#ffffff' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ") ?>>Categorie</td></tr>
				<tr><td onclick="location.href='?dove=articoli'" <?= (($_SESSION["dove"]=="articoli")?"bgcolor='#cccccc'":"bgcolor='#ffffff' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ") ?>>Articoli</td></tr>
				<tr><td onclick="location.href='?dove=stampe'"   <?= (($_SESSION["dove"]=="stampe")?"bgcolor='#cccccc'":"bgcolor='#ffffff' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ") ?>>Stampe</td></tr>
				<tr><td onclick="location.href='?dove=servizi'"   <?= (($_SESSION["dove"]=="servizi")?"bgcolor='#cccccc'":"bgcolor='#ffffff' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ") ?>>Servizi</td></tr>
				<tr bgcolor="#FFFFFF"><td>&nbsp;</td></tr>
				<tr><td onclick="location.href='?dove=backup'"   <?= (($_SESSION["dove"]=="backup")?"bgcolor='#cccccc'":"bgcolor='#ffffff' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ") ?>>Backup Database</td></tr>
				<tr><td onclick="location.href='?dove=espsch'"   <?= (($_SESSION["dove"]=="espsch")?"bgcolor='#cccccc'":"bgcolor='#ffffff' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ") ?>>Esportazione Schede</td></tr>
				<tr><td onclick="location.href='?dove=impsch'"   <?= (($_SESSION["dove"]=="impsch")?"bgcolor='#cccccc'":"bgcolor='#ffffff' onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" ") ?>>Importazione Schede</td></tr>
			</table>
		</td>
		<td width='776' valign='top'>
			<table width='776' border='0' cellspacing='1' cellpadding='2' align='center' bgcolor='#000000'>
				<tr><td bgcolor='#ffffff'>
				<?php if (is_file("admin-".$_SESSION["dove"].".php")) include("admin-".$_SESSION["dove"].".php"); else echo "Seleziona un elenento dal menu di sinistra..."; ?>
		</td>
	</tr>
</table>
</body>
