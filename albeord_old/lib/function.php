<?php

function jdtodate($cosa, $jd)
{
    $unix = jdtounix($jd);
    return date($cosa, $unix);
}

function disegna_cal($g, $m, $a, $time = 0)
{
    $MesiTradotti[1] = "Gennaio";
    $MesiTradotti[2] = "Febbraio";
    $MesiTradotti[3] = "Marzo";
    $MesiTradotti[4] = "Aprile";
    $MesiTradotti[5] = "Maggio";
    $MesiTradotti[6] = "Giugno";
    $MesiTradotti[7] = "Luglio";
    $MesiTradotti[8] = "Agosto";
    $MesiTradotti[9] = "Settembre";
    $MesiTradotti[10] = "Ottobre";
    $MesiTradotti[11] = "Novembre";
    $MesiTradotti[12] = "Dicembre";

    if (!$time) $time = unixtojd();

    $g_sel = jdtodate("j", $time);
    $m_sel = jdtodate("n", $time);
    $a_sel = jdtodate("Y", $time);
    ?>
    <select name="<?= $g ?>">
        <option value="0">Giorno</option>
        <?php for ($d = 1; $d < 32; $d++) echo "<option value=\"$d\" " . (($d == $g_sel) ? "selected" : "") . ">$d</option>\n"; ?>
    </select>
    <select name="<?= $m ?>">
        <option value="0">Mese</option>
        <?php for ($d = 1; $d <= 12; $d++) echo "<option value=\"$d\" " . (($d == $m_sel) ? "selected" : "") . ">" . $MesiTradotti[$d] . "</option>\n"; ?>
    </select>
    <select name="<?= $a ?>">
        <option value="0">Anno</option>
        <?php for ($d = date("Y") - 2; $d < date("Y") + 3; $d++) echo "<option value=\"$d\" " . (($d == $a_sel) ? "selected" : "") . ">$d</option>\n"; ?>
    </select>
    <?php
}

function insert_prezzo($prezzo)
{
    $prezzo = str_replace(',', '.', $prezzo);
    $resto = strstr("$prezzo", '.');
    if ($resto) {
        $resto = substr($resto, 1, 3);
        $prezzo = explode('.', $prezzo);
        $prezzo = ($prezzo[0] . "." . $resto) * 100;
    } else {
        $prezzo = intval($prezzo) . "00";
    }
    return intval($prezzo);
}

function show_prezzo($prezzo, $tipo = _EURO_)
{
    $prezzo = $prezzo / 100;
    if ($tipo == _EURO_) {
        $prezzo = explode('.', $prezzo);
        $diff = 2 - strlen($prezzo[1]);
        if ($diff < 0) $diff = 0;
        $resto = $prezzo[1] . str_repeat('0', $diff);
        $prezzo = $prezzo[0] . "," . $resto;
    } else {
        $prezzo = explode('.', "$prezzo");
        $resto = intval($prezzo[1]);
        if ($resto > 0) {
            $prezzo = $prezzo[0] . "," . $resto;
        } else {
            $prezzo = $prezzo[0];
        }
    }
    return $prezzo;
}

function base64_unserialize($str)
{
    $ary = unserialize($str);
    if (is_array($ary)) {
        foreach ($ary as $k => $v) {
            if (is_array(unserialize($v))) {
                $ary[$k] = base64_unserialize($v);
            } else {
                $ary[$k] = base64_decode($v);
            }
        }
    } else {
        return false;
    }
    return $ary;
}

function base64_serialize($ary)
{
    if (is_array($ary)) {
        foreach ($ary as $k => $v) {
            if (is_array($v)) {
                $ary[$k] = base64_serialize($v);
            } else {
                $ary[$k] = base64_encode($v);
            }
        }
    } else {
        return false;
    }
    return serialize($ary);
}

function stampawbs2($dbh, $IDORD, $TIPO)
{

    $PathS = multi_single_query($dbh, "SELECT pathstampa FROM utenti WHERE id=" . intval($_COOKIE["albeordlogged"]));
    $Stamp = multi_single_query($dbh, "SELECT stampante FROM utenti WHERE id=" . intval($_COOKIE["albeordlogged"]));


    if ($PathS[count($PathS) - 1] !== "\\") {
        $PathS += "\\";
    }


    $dir = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/stampe/send_stampa.php";

    $stampante = 0;

    if (strlen($Stamp) > 3) $stampante = base64_encode($Stamp);

    $pw = md5($dir . unixtojd() . $TIPO . $IDORD . $stampante);
    $secret = $TIPO . "-" . $IDORD . "-" . $pw . "-" . $stampante;
    return $secret;
}

function stampawbs($dbh, $IDORD, $TIPO)
{

    $Stamp = multi_single_query($dbh, "SELECT stampante FROM utenti WHERE id=" . intval($_COOKIE["albeordlogged"]));
    $stampante = 0;
    if (strlen($Stamp) > 3) $stampante = base64_encode($Stamp);

    $pw = md5(unixtojd() . $TIPO . $IDORD . $stampante);
    $secret = $TIPO . "-" . $IDORD . "-" . $pw . "-" . $stampante;


    ?>

    <script type="text/javascript">

        jQuery(document).ready(() => {

            let secret = "<?=$secret?>";

            jQuery.post('/albeord/stampe/stampa.php', {

                secret: secret
            }, (res => {
                //ok
                jQuery(".print.error").html(res);
            })).catch(err => {
                //ko
                jQuery(".print.error").html(err);
            }).always(function () {
                if (redirectDopoStampa != undefined) {
                    setTimeout(() => {
                        window.location.href = redirectDopoStampa;
                    }, 3000);
                }
            });
        })

    </script>
    <?php
}

function top_menu()
{
    global $PRIVILEGI, $GlobPrivilegi;

    $t = 0;

    foreach ($GlobPrivilegi as $key => $val) {
        if ($PRIVILEGI[$key]) $t++;
    }

    if ($t >= 3) {
        $logoutw = 980 / ($t + 1);
    } else {
        $logoutw = 150;
        $t = 1;
    }

    $w = (980 - $logoutw) / $t;


    ?>
    <table style="cursor:pointer" class="topmenu" width='980' border='0' cellspacing='1' cellpadding='3'
           bgcolor='#000000' align='center'>
        <tr bgcolor='#ffffff'>
            <?php
            foreach ($GlobPrivilegi as $key => $val) {
                if ($PRIVILEGI[$key]) {
                    ?>
                <td width='<?= $w ?>' onClick="location.href='<?= $key ?>-core.php'"
                    align='center' <?= (strstr(basename($_SERVER["PHP_SELF"]), "$key-")) ? "bgcolor='#cccccc'" : " onmouseout=\"this.style.backgroundColor='#ffffff'\" onmouseover=\"this.style.backgroundColor='#cccccc'\" " ?>> <?= $val ?></td><?php
                }
            }
            ?>
            <td bgcolor='#EBD1CE' width='<?= $logoutw ?>'
                onClick="if (confirm('Continuare?'))location.href='index.php?logout=1'" align='center'>Logout
            </td>
        </tr>
    </table>
    <?php
    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") === false) {
        ?>
        <center style="color:#7C0C0C">Albeord &egrave; in grado di stampare unicamente con Internet Explorer 6.0 o
            superiore
        </center><?php
    }
}

function redirect_to($URL)
{
    header("Location:" . $URL);
    ob_end_flush();
    exit;
}

function abort($m)
{
    ob_clean();
    die($m);
}

function head_page(){
?>
<head>
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <script type='text/javascript' language='Javascript' src='js/md5.js'></script>
    <script type='text/javascript' language='Javascript' src='js/core.js'></script>
    <script type='text/javascript' language='Javascript' src='js/jquery.js'></script>

    <title>Albeord Mercurio Sistemi</title>

</head>
<body topmargin='8' leftmargin='0' vlink="#0000ff">
<?php
}
// parte utile al calcolo del prezzzo sul preventivo
function getPrezzoGiornaliero($trattamento, $tipo, $prezzi, $prezzif, $ScontiBambini, $periodo)
{

    if ($periodo["tipo"] == "F") {
        $p = $prezzif[$trattamento];
    } else {

        $p = 0;
        for ($i = 0; $i < strlen($tipo); $i++) {
            if ($tipo[$i] == "B") {
                $p += $prezzi[$trattamento] - ($prezzi[$trattamento] / 10000 * $ScontiBambini[$tipo[++$i]]);
            } else {
                $p += $prezzi[$trattamento];
            }
        }
    }

    return $p;
}

function getPrezziScheda($dbh, $ID)
{
    global $Servizi;
    $scheda = multi_single_query($dbh, "SELECT * FROM schede where id= $ID", "ALL");

    if (!$Servizi) {
        $res = multi_query($dbh, "SELECT * FROM servizi ORDER BY tipo,nome ASC");
        for ($i = 0; $i < multi_num_rows($res); $i++) {
            $dataT = multi_fetch_array($res, $i);
            $Servizi[$dataT["id"]] = $dataT;
        }
    }
    $ret["trattamenti"] = array();

    $resP = mysqli_query("SELECT * FROM schede_periodi WHERE idscheda =  $ID order by  dal");
    while ($periodo = mysqli_fetch_assoc($resP)) {
        // normale
        $prezzi[1] = $periodo["bb"];
        $prezzi[2] = $periodo["hb"];
        $prezzi[3] = $periodo["fb"];
        // forfettario
        $prezzif[1] = $periodo["fbb"];
        $prezzif[2] = $periodo["fhb"];
        $prezzif[3] = $periodo["ffb"];

        $res = multi_query($dbh, "SELECT * FROM schede_prezzi_bambini WHERE idperiodo=" . $periodo["id"] . "");
        for ($i = 0; $i < multi_num_rows($res); $i++) {
            $dataT = multi_fetch_array($res, $i);
            $ScontiBambini[$dataT["bambino"]] = $dataT["sconto"];
        }


        $res = multi_query($dbh, "select giorno from schede_presenze WHERE idperiodo=" . $periodo["id"] . " group by giorno order by giorno");
        $pasti = array();
        $pasti2 = array();
        while ($data = mysqli_fetch_assoc($res)) {

            if ($data["giorno"] == unixtojd($periodo["dal"])) {
                continue;
            } elseif ($data["giorno"] == unixtojd($periodo["al"])) {
                $q = "( giorno=" . $data["giorno"] . " OR giorno=" . unixtojd($periodo["dal"]) . ") ";
            } else {
                $q = "giorno=" . $data["giorno"];
            }

            $query2 = "select tipo, persona, count(*) as c from schede_presenze WHERE idperiodo=" . $periodo["id"] . " AND $q GROUP  BY tipo, persona";

            $res2 = mysqli_query($query2);
            while ($data2 = mysqli_fetch_assoc($res2)) {
                $tipo = $data2["tipo"];
                if ($data2["tipo"] == "B") {
                    $tipo .= $data2["persona"];
                }
                $pasti[$data2["c"]][$data["giorno"]] .= $tipo;
            }
        }


        foreach ($pasti as $key => $val) {
            foreach ($val as $g => $t) {
                $pasti2[$key][$t]++;
            }
        }
        ksort($pasti2);

        foreach ($pasti2 as $trattamento => $quantita) {
            foreach ($quantita as $tipo => $giorni) {
                $prezzo = getPrezzoGiornaliero($trattamento, $tipo, $prezzi, $prezzif, $ScontiBambini, $periodo);
                $subtot = $prezzo * $giorni;
                $tot += $subtot;
                $ret["trattamenti"][$trattamento][] = array(
                    "persone" => strlen(str_replace(range(0, 9), "", $tipo)),
                    "giorni" => $giorni,
                    "parziale" => $prezzo,
                    "totale" => $subtot);
            }
        }
        $res = multi_query($dbh, "SELECT supplemento FROM schede_supplementi WHERE idperiodo=" . $periodo["id"] . "");
        for ($i = 0; $i < multi_num_rows($res); $i++) {
            $dataT = multi_fetch_array($res, $i);
            $ret["supplementi"][$periodo["id"]] += $dataT["supplemento"];
        }

    }


    // calcolo prezzo servizi
    $res = multi_query($dbh, "SELECT * FROM schede_servizi WHERE idscheda=$ID");
    for ($i = 0; $i < multi_num_rows($res); $i++) {
        $dataT = multi_fetch_array($res, $i);
        $ServiziAttivi[$dataT["idservizio"]] = $dataT["prezzo"];
    }
    foreach ($Servizi as $idservizio => $data) {
        if ($ServiziAttivi[$idservizio] && $data["tipo"] == 1) {
            $tot += $ServiziAttivi[$idservizio];
            $ret["servizi+"][$idservizio] = array("nome" => $data["nome"], "prezzo" => $ServiziAttivi[$idservizio]);
        }
    }
    if ($ret["supplementi"]) {
        $tot += array_sum($ret["supplementi"]);
    }
    $ret["subtotale"] = $tot;
    foreach ($Servizi as $idservizio => $data) {
        if ($ServiziAttivi[$idservizio] && $data["tipo"] == 2) {
            $tot -= $ServiziAttivi[$idservizio];
            $ret["servizi-"][$idservizio] = array("nome" => $data["nome"], "prezzo" => $ServiziAttivi[$idservizio]);
        }
    }
    $ret["totale"] = $tot;

    return $ret;
}


$ST[1] = "Prenottamento e colazione";
$ST[2] = "Mezza Pensione";
$ST[3] = "Pensione Completa";


?>
