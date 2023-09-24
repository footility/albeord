<?php
 $SKIP=1;
include("../core-config.php");

list($tipo, $data, $check, $stampante)=explode('-',$secret);
function show_prezzo2($p){
	return str_replace(",","@",show_prezzo($p));
}

if ($check!=md5(unixtojd().$tipo.$data.$stampante)) die("ERROR:Bad secret check");


$datiAnag=unserialize(@file_get_contents("dati.ser"));

$Modelli["S1"]=array( // scontrino
    "spacer"=>33,
    "head"=>"
            [logo450][l]
            [f2][x5]|Utente: %s|[l]
            [f3]|%s:|[x300]|%s|[l][l]
            [x5][f1]
            ",
    "row"=>"
            [f1][x6]|%s|[x50]|%s|[x410][n17,%s]|[x430]||[l]
            ",
    "foot"=>"
            [l]
            [f3][x5]|Totale:|[x410][n17,%s]|[x430]||[l]
            [f2][x5]|%s|[l]
            |Firma:|[l][l][l]
            |--------------------------------------------"
            .(strpos(strtolower($datiAnag["ragsoc"]), "punta")!==false?"|[l]|-":"")
,
);


$Modelli["A4"]=array(
    "spacer"=>60,
    "head"=>"
            [logo600][l]
            [r400][x700][f5]
            |".$datiAnag["ragsoc"]."|[l]
            |".$datiAnag["indirizzo1"]."[l]
            |".$datiAnag["indirizzo2"]."|[l]
            |".($datiAnag["tel"]?"Tel ".$datiAnag["tel"]:"").($datiAnag["fax"]?" Fax ".$datiAnag["fax"]:"").($datiAnag["extra"]?" - ".$datiAnag["extra"]:"")."[l][l][l]
            [f5][x30]|Stampa effettuata dall'utente: %s|[l]
            [f7]|%s:|[x800]|%s|[l][l]
            [f5]
            [x105]|Articolo|[x800]|Prezzo|[l]
            [x30]
            [d2,1050,-1]
            ",
    "row"=>"
            [x30]|%s|[x105]|%s|[x965]|[n28,%s]|[x1000]||[l]
            ",
    "row_dett"=>"
            [x30]|Ordini relativi al giorno %s|[l]
            ",
    "foot"=>"
            [l]
            [f7][x30]|Totale:|[x960]|[n30,%s]|[x1000]||[l][l]
            [f5][x30]|Ora stampa: %s|[l]
    ",

);
$Modelli["A41"]=array(
    "spacer"=>60,
    "head"=>"
            [logo600][l]
            [r400][x700][f5]
			|".$datiAnag["ragsoc"]."|[l]
            |".$datiAnag["indirizzo1"]."[l]
            |".$datiAnag["indirizzo2"]."|[l]
            |".($datiAnag["tel"]?"Tel ".$datiAnag["tel"]:"").($datiAnag["fax"]?" Fax ".$datiAnag["fax"]:"").($datiAnag["extra"]?" - ".$datiAnag["extra"]:"")."[l][l][l]
			[l][l]
            [f5][x30]|Stampa effettuata dall'utente: %s|[l]
            [f7]|%s:|[x800]|%s|[l][l]
            [f5]
            [x105]|Articolo|[x850]|Prezzo|[l]
            [x30]
            [d2,1050,-1]
            ",
    "row_dett"=>"
            [x30]|Ordini relativi al giorno %s|[l]
            ",
    "row"=>"
            [x30]|%s|[x105]|%s|[x960]|[n27,%s]|[x1000]||[l]
            ",
    "foot"=>"
            [l]
            [f7][x30]|Totale:|[x960]|[n30,%s]|[x1000]||[l][l]
            [f5][x30]|Ora stampa: %s|[l]
    ",

);




$Modelli["A42"]=array(  // stampa scheda
    "spacer"=>60,
    "head"=>"
            [logo600][l]
            [r400][x700][f5]
            |".$datiAnag["ragsoc"]."|[l]
            |".$datiAnag["indirizzo1"]."[l]
            |".$datiAnag["indirizzo2"]."|[l]
            |".($datiAnag["tel"]?"Tel ".$datiAnag["tel"]:"").($datiAnag["fax"]?" Fax ".$datiAnag["fax"]:"").($datiAnag["extra"]?" - ".$datiAnag["extra"]:"")."[l][l][l]
            [x30][l]
            "

);





if ($tipo==1){//conto standard
	$query= "SELECT ordini.*, utenti.utente FROM ordini, utenti WHERE utenti.id=ordini.idutente AND idordine=$data and stato=0 ";
	$msg  = "Ordine camera";
	$Mod  = "S1";
}
if ($tipo==2){//pagato
	$query= "SELECT ordini.*, utenti.utente FROM ordini, utenti WHERE utenti.id=ordini.modificatoda AND idoperazione=$data ORDER BY ora";
	$msg  = "Conto camera";
    $Mod  = "A41";
}
if ($tipo==3){//storno
	$query= "SELECT ordini.*, utenti.utente FROM ordini, utenti WHERE utenti.id=ordini.modificatoda AND idoperazione=$data ORDER BY ora";
	$msg  = "** Storno camera **";
	$Mod  = "S1";
}
if ($tipo==4){//preventivo
	$query= "SELECT * FROM ordini WHERE  idoperazione=$data";
	$msg  = "Preventivo spese camera";
	$Mod  = "A4";
}
if ($tipo==10){//preventivo
	$msg  = "Spese camera";
	$Mod  = "A42";
}

////ELABORAZIONE
if ($tipo==10){//stats
 	$ary["spacer"]=$Modelli[$Mod]["spacer"];


	$scheda  = multi_single_query($dbh, "SELECT * FROM schede where id= $data","ALL");
	$scheda["dal"]  = multi_single_query($dbh, "SELECT min(dal) FROM schede_periodi where idscheda= $data");
	$scheda["al"]  =  multi_single_query($dbh, "SELECT max(al) FROM schede_periodi where idscheda= $data");
	$prezzi  = getPrezziScheda($dbh, $data);


	$ST[1]="Prenottamento e colazione";
	$ST[2]="Mezza Pensione";
	$ST[3]="Pensione Completa";

	$stringa = $Modelli[$Mod]["head"];
	$stringa.="[f5][x100]|Camera:|[x350]|".$scheda["camera"]."|[l]";
	if($scheda["titolare"])
		$stringa.="[f5][x100]|Nome:|[x350]|".$scheda["titolare"]."|[l]";
	$stringa.="[f5][x100]|Arrivo:|[x350]|".date("d/m/Y",$scheda["dal"])."|[l]";
	$stringa.="[f5][x100]|Partenza:|[x350]|".date("d/m/Y",$scheda["al"])."|[l]";
	$stringa.="[l]"	;

	foreach ($prezzi["trattamenti"] as $trattamento => $parti){
		  $stringa.="[f5][x100]| - ".$ST[$trattamento]."|[l]";
		  foreach ($parti as $parte){
		  		$stringa.="[f4][x150]| Pers. n.  ".$parte["persone"]."|[x500]| Per giorni n. ".$parte["giorni"]." |[x960]|[n27,".show_prezzo2($parte["parziale"])."]|[x1000]|euro|[x1230]|Totale|[x1600]|[n27,".show_prezzo2($parte["totale"])."]|[x1640]|euro|[l]|"; //|[x1000]|euro|[x1300]|Totale|[n27,".show_prezzo2($prezzo*$giorni)."]|[x1520]|euro|
		  }
		  $stringa.="|[l][l]|";
	}

	if(count($prezzi["servizi+"])){
		$stringa.="[f5][x100]| - Extra|[l]";
		foreach ($prezzi["servizi+"] as $idservizio => $data){
			$stringa.="[f4][x150]|".$data["nome"]."|[x950]|[x1600]|[n27,".show_prezzo2($data["prezzo"])."]|[x1640]|euro|[l]|";
		}
		$stringa.="|[l][l]|";
	}
	if(count($prezzi["supplementi"])){

		$stringa.="[f5][x100]|Supplementi|[x950]|[x1600]|[n27,".show_prezzo2(array_sum($prezzi["supplementi"]))."]|[x1640]|euro|[l]|";

		$stringa.="|[l][l]|";
	}

	$stringa.="[f4][x100]|Sub totale|[x950]|[x1600]|[n27,".show_prezzo2($prezzi["subtotale"])."]|[x1640]|euro|[l]|";
	foreach ((array)$prezzi["servizi-"] as $idservizio => $data){
		$stringa.="[f4][x150]|".$data["nome"]."|[x950]|[x1600]|[n27,-".show_prezzo2($data["prezzo"])."]|[x1640]|euro|[l]|";
	}
	$stringa.="|[l][l][x1730][d2,100,-1]|[l]|";

	$stringa.="[f5][x100]|Totale|[x950]|[x1600]|[n27,".show_prezzo2($prezzi["totale"])."]|[x1640]|euro|[l]|";

	$stringa.="[l][f3][x30]|Ora stampa: ".date("d/m/Y H:i:s")."|[l]";

}elseif ($tipo==5 | $tipo==6){//stats
    $ary["spacer"] = 60;
    list($dal, $al)=explode('*', $data);
    $stringa="
            [logo600][l]
            [r400][x700][f5]
            |Fam. Parpinel|[l]
            |33054 Lignano Sabbiadoro (Udine) Italia|[l]
            |Lungomare Trieste, 150|[l]
            |Tel 0431-71315 Fax 0431-720191 - Invernale - Tel 0431-71391[l][l][l]";
        if($tipo==5){
            $stringa.="
                [x30]
                [f7]|Statistiche incassi dal: ".date("d-m-Y", $dal)."  al: ".date("d-m-Y", $al)."|[l][l]
                [f5]
                [x100]|Gruppo|[x550]|Quantit|[x900]|Valore|[l]
                [x30][d2,1100,-1][x60]|
            ";
        }
        if($tipo==6){
            $stringa.="
                [x30]
                [f7]|Statistiche dettagliate incassi dal: ".date("d-m-Y", $dal)."  al: ".date("d-m-Y", $al)."|[l][l]
                [f5]
            ";
        }

    $res=multi_query($dbh, "SELECT * FROM gruppi order by nome");
	for($i=0; $i<multi_num_rows($res); $i++){
		$data = multi_fetch_array($res, $i);
		$q    = (integer) multi_single_query($dbh, "SELECT count(ordini.id) FROM ordini, utenti, gruppi WHERE (ordini.stato=1 OR ordini.stato=0) AND ordini.ora>=$dal AND ordini.ora<=$al and ordini.idutente=utenti.id AND utenti.idgruppo=gruppi.id AND gruppi.id=".$data["id"]);
		$p    = (integer) multi_single_query($dbh, "SELECT sum(ordini.prezzo) FROM ordini, utenti, gruppi WHERE (ordini.stato=1 OR ordini.stato=0) AND ora>=$dal AND ora<=$al and ordini.idutente=utenti.id AND utenti.idgruppo=gruppi.id AND gruppi.id=".$data["id"]);
		$qt  += $q;
		$pt  += $p;

		if($tipo==6){
	       $stringa .="[s60][f5][x100]|".$data["nome"]."|[x550][f2]|Articoli: $q|[x800]|Valore: |[f5][r10][x920]|$p |[l]";
	    }else{
    	   $stringa .="[s60][f5][x100]|".$data["nome"]."|[x550]|$q|[x1000][n28,".show_prezzo2($p)."]|[x1050]||[l]";

        }
	    if($tipo==6){


			$res2 = multi_query($dbh, "SELECT ordini.articolo, count(ordini.id) as quantita ,sum(ordini.prezzo) as prezzo  FROM ordini, utenti, gruppi WHERE (ordini.stato=1 OR ordini.stato=0) AND ordini.ora>=$dal AND ordini.ora<=$al and ordini.idutente=utenti.id AND utenti.idgruppo=gruppi.id AND gruppi.id=".$data["id"]." GROUP BY ordini.articolo ORDER BY ordini.articolo" );
			$nr2  = multi_num_rows($res2);
			if ($nr2==0){
				$stringa .= "[x200][f3]|Nessun articolo nel gruppo ".strtolower($data["nome"])."|[l]";
			}else{
	    	    $stringa .="
                [f3][s43]
                [x200]|Dettaglio incassi ".strtolower($data["nome"])."|[l]
                [x200]|Articolo|[x900]|Quantita|[x1200]|Valore|[l]
                [x200][d2,1350,-1]";
    			for ($i2=0; $i2<$nr2; $i2++){
    				$dataArt = multi_fetch_array($res2, $i2);
                    $stringa.= "[x200]|".$dataArt["articolo"]."|[x900]|".$dataArt["quantita"]."|[x1280][n20,".show_prezzo2($dataArt["prezzo"])."]|[x1320]||[l]";
    			}
			}
	    }
	    $stringa .="|[l]|";
    }
	if($tipo==6){
       $stringa .="[l][s60][f5][x100]|Totale|[x550][f2]|Articoli: $qt|[x800]|Valore: |[f5][r10][x920]|".show_prezzo($pt)." |[l]";
    }else{
	   $stringa .="[x30][d2,1100,-1][l][r20][s60][f5][x100]|Totale|[x550]|$q|[x1000][n28,".show_prezzo2($pt)."]|[x1050]||[l]";
    }
}else{
    $ary["spacer"]=$Modelli[$Mod]["spacer"];
    $ordine  = multi_single_query($dbh, $query." LIMIT 1",ALL);


    $stringa = sprintf($Modelli[$Mod]["head"], $ordine["utente"],$msg, $ordine["camera"]);

    $res     = multi_query($dbh, $query);
    $totale  = 0;
    $Pez     = array();
    for ($i=0; $i<multi_num_rows($res); $i++){
    	$data = multi_fetch_array($res, $i);
        if ($tipo==2 | $tipo==4){
    	   $id       = md5($data["articolo"].$data["prezzo"].date("d-m-Y", $data["ora"]));
    	}else{
           $id       = md5($data["articolo"].$data["prezzo"]);
        }
    	$Pez[$id]["articolo"]= $data["articolo"];
    	$Pez[$id]["quantita"]++;
    	$Pez[$id]["przunit"] = $data["prezzo"];
    	$Pez[$id]["przunit"] = $data["prezzo"];
    	$Pez[$id]["ora"]     = date("d-m-Y", $data["ora"]);
    }

    foreach($Pez as $data){
    	$quantita = $data["quantita"];
    	$prezzo   = $data["przunit"]*$data["quantita"];
    	$totale  += $prezzo;

    	if ($tipo==2 | $tipo==4){
            if ($orario!=$data["ora"]){
                $stringa .= sprintf($Modelli[$Mod]["row_dett"], $data["ora"]);
            }
        }
    	$stringa .= sprintf($Modelli[$Mod]["row"],$quantita,$data["articolo"],show_prezzo2($prezzo));
      	$orario   = $data["ora"];
    }

    $stringa .= sprintf($Modelli[$Mod]["foot"],show_prezzo2($totale),date("d-m-Y H:i"));

}
////FINE ELABORAZIONE

if($stampante) $ary["stampante"]=$stampante;

$ary["stampa"] = str_replace("\t",'', $stringa);

$ary["logo"]   = file_get_contents("logo.bmp");
//print_r($ary);
$httpdata   = gzcompress(base64_serialize($ary), 8);

?>
