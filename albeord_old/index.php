<?php
$SKIP = 1;

include("core-config.php");

if ($_REQUEST["logout"] == 1) {
    setcookie("albeordlogged", "");
    $_SESSION = array();
    ?>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=index.php"><?php
    exit;
}

if (is_array($PRIVILEGI) && sizeof($PRIVILEGI) > 0) {
    ?>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=home.php"><?php
    exit;
}
if ($_REQUEST["login"]) {
    if ($IDC = multi_single_query($dbh, "SELECT * FROM utenti WHERE utente='" . $_REQUEST["utente"] . "' AND password='" . $_REQUEST["password"] . "' AND eliminato = 0")) {
        setcookie("albeordlogged", $IDC);
        ?>
        <META HTTP-EQUIV=Refresh CONTENT="0; URL=home.php"><?php
        exit;
    } else {
        $MSG = "Nome utente o password errati";
    }
}
?>
<html>
<head>
    <title>Login Albeord</title>
</head>
<link type="text/css" rel="stylesheet" href="css/style.css">
<body>
<table cellspacing="0" cellpadding="0" align="center" style="border:1px solid #333333" bgcolor="#ffffff" width="90%">
    <tr>
        <td style="border-bottom:1px solid #333333" background="img/bg.jpg"><br>
            <h1>
                <font color="#444444">&nbsp;&nbsp;Albeord Mercurio Sistemi</font>
            </h1>
        </td>
    </tr>
    <tr>
        <td>
            <br>
            <table border="0" align="center" cellspacing="0" cellpadding="3">
                <form name="frm" method="POST" action="<?= $_SERVER["PHP_SELF"] ?>">
                    <td width="25%">&nbsp;</td>
                    <td width="50%" colspan="2" style="border-bottom:1px solid #666666" align="center"><b>
                            <?php
                            if ($MSG) {
                                echo "<font color=\"#FF0000\">$MSG</font>";
                            } else {
                                echo "Login";
                            }
                            ?>
                        </b></td>
                    <td width="25%">&nbsp;</td>
    </tr>
    <tr>
        <td width="25%">&nbsp;</td>
        <td width="25%" style="border-left:1px solid #666666" align="right">Username</td>
        <td width="25%" style="border-right:1px solid #666666"><input type="text" name="utente"
                                                                      style="border:1px solid #666666"></td>
        <td width="25%">&nbsp;</td>
    </tr>
    <tr>
        <td width="25%">&nbsp;</td>
        <td width="25%" style="border-bottom:1px solid #666666;border-left:1px solid #666666" align="right">Password
        </td>
        <td width="25%" style="border-bottom:1px solid #666666;border-right:1px solid #666666"><input type="password"
                                                                                                      name="password"
                                                                                                      style="border:1px solid #666666">
        </td>
        <td width="25%">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td width="25%">&nbsp;</td>
        <td width="50%" colspan="2" align="center"><input type="submit" name="login" value="   Entra   "
                                                          style="border: 1px solid #666666; background-color: #ffffff">
        </td>
        <td width="25%">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    </form>
</table>
<br>
</td>
</tr>
</table>
</body>
</html>

