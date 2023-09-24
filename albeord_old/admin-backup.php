<?php
if (!$PRIVILEGI["admin"]) redirect_to("home.php");
include_once("lib/tar.class.php");

if ($_REQUEST["expb"]){

    $tar = new tar();
    $oldcwd=getcwd();

    $dir = str_replace("www\\albeord", 'mysql\\data\\albeord',$oldcwd);
    chdir($dir);
    $dir=".";
    if (is_dir($dir)) {
       if ($dh = opendir($dir)){

         while (($file = readdir($dh)) !== false) {
            if (is_file($file)){
               $tar->addFile($file);
           }
         }
         closedir($dh);
       }
    }
    chdir($oldcwd);
    $tar->toTar("temp/sqlbackup.tgz",TRUE);
    echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL=temp/sqlbackup.tgz">';
}

?>
<form method='POST' enctype='multipart/form-data'>
<table cellspacing='0' cellpadding='3' width='550'>
	<tr>
        <td bgcolor='#cccccc' nowrap colspan="2">Operazioni di backup</td>
	</tr>
	<tr>
        <td></td>
		<td><input type='submit' name='expb' value='  Esegui Backup  ' class="inpbtn"></td>
	</tr>
</table>
</form>
<br>
</form>
