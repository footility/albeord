<?
include("core-config.php");

head_page();
top_menu();

	$PathS  = multi_single_query($dbh, "SELECT pathstampa FROM utenti WHERE id=".intval($_COOKIE["albeordlogged"]));
	if ($PathS{strlen($PathS)-1}!="\\") $PathS.="\\";
?>
	<script language="Javascript" type="text/javascript">
		if (!window.ActiveXObject) alert ("Controlli ActiveX non supportati dal browser, non sarà possibile stampare da questo computer.");
	</script>
	<script language="VBScript" type="text/vbscript">
		On Error Resume Next
		Set WshShell = CreateObject("WScript.Shell")		
		Dim Errori			
		If Err <> 0 Then
		  Errori = Errori & "Il browser in uso ha delle restrizioni sui controlli ActiveX, le funzioni di stampa potrebbero non funzionare."& chr(13)
		End if	
		
				
		Function FileExists(Fname)
		  Set fs = CreateObject("Scripting.FileSystemObject")
		  if fs.FileExists(Fname) = False then
		    FileExists = -1
		  else
		    FileExists = 0
		  end if
		Set fs = Nothing
		End Function
		
		If FileExists("<?= $PathS.'php.exe' ?>") = -1 Then 
			Errori = Errori & "Programma di stampa non installato o percorso errato."
		End If
		
		If Errori <> "" Then
			MsgBox Errori , vbExclamation, "Albeord"
		End If	


		
			
	</script>

<br>
<table bgcolor="#ffffff" width='980' height='400' border='0' cellspacing='0' cellpadding='2' align='center' style="border:1px solid #000000">
	<tr>
		<td valign='top'>
            <img src="img/bg.jpg">
		</td>
	</tr>
</table>

</body>
