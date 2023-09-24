<?php

function printer_write_num($printer,$input,$ypos,$xpos, $dist){
   $s=strlen($input);
   $y=$xpos;
   for ($i=1;$i<$s+1;$i++){
       $u=$i*-1;
       printer_draw_text($printer,substr($input,$u,1),$y,$ypos);
       $y=$y-$dist;
   }
}

$curdir = ".";
$secret     = $_REQUEST['secret'];

include "send_stampa.php";


if (substr($httpdata,0,6)=="ERROR:"){
	echo $httpdata;
	sleep(3);
	exit;
}

$httpdata   = @gzuncompress($httpdata);
if (!$httpdata){
	echo "errore GZ\n\n";
	sleep(5);
	exit;
}

$httpdata   = base64_unserialize($httpdata);

$httpdata["stampa"] = str_replace("\n", '', $httpdata["stampa"]);
$pezzi=explode("[l]", $httpdata["stampa"]);

$logo = $curdir."\\".microtime();
file_put_contents($logo, $httpdata["logo"]);

$spacer = $httpdata["spacer"];
$font   = "Arial";


if ($httpdata["stampante"]){
	$handle = @printer_open(base64_decode($httpdata["stampante"]));
}else{
	$handle = @printer_open();
}
if (!$handle){
	echo "Non posso comunicare con la stampante ".base64_decode($httpdata["stampante"]);
	printer_abort($handle);
    printer_close($handle);
	sleep(5);
	exit;
}

$doc_ok=@printer_start_doc($handle, "Stampa".md5($httpdata["stampa"]));
if (!$doc_ok){
	echo "La stampante si rifiuta di generare l'operazione di stampa";
	printer_abort($handle);
    printer_close($handle);

	sleep(5);
	exit;
}

printer_start_page($handle);


$size1 = printer_create_font($font, 25, 14, 100, false, false, false ,0);
$size2 = printer_create_font($font, 25, 15, 150, false, false, false ,0);
$size3 = printer_create_font($font, 30, 17, 170, false, false, false ,0);
$size4 = printer_create_font($font, 35, 19, 200, false, false, false ,0);
$size5 = printer_create_font($font, 40, 21, 220, false, false, false ,0);
$size6 = printer_create_font($font, 45, 24, 250, false, false, false ,0);
$size7 = printer_create_font($font, 50, 27, 280, false, false, false ,0);
$size8 = printer_create_font($font, 55, 32, 300, false, false, false ,0);
$size9 = printer_create_font($font, 60, 38, 320, false, false, false ,0);


printer_select_font($handle, $size2);

$inizio = 0;
$next   = 0;
$xpos   = 0;
$pagn   = 1;
foreach ($pezzi as $righe){
    foreach (explode('|',$righe) as $stringa){
        if (!$stringa) continue;

        if (preg_match('/\[f[0-9]+\]/',$stringa)){
            $pos = strpos($stringa, '[f')+2;
            $sz  = $stringa{$pos};
            printer_select_font($handle, ${"size".$sz});
        }
    	if (strstr($stringa, "[r")){
            if (strstr($stringa, "[r]")){
    		  $inizio -= $spacer;
    		}else{
              $in = strpos($stringa, '[r')+2;
              $ou = strpos($stringa, ']', $in);
              $inizio -= substr($stringa, $in,$ou-$in);
            }
    	}
        if (preg_match('/\[x[0-9]+\]/',$stringa)){
            $in  = strpos($stringa, '[x')+2;
            $ou  = strpos($stringa, ']', $in);
            $xpos= substr($stringa, $in, $ou-$in);
        }
        if (preg_match('/\[d[0-9,]/',$stringa)){
            $in  = strpos($stringa, '[d')+2;
            $ou  = strpos($stringa, ']', $in);
            $str = substr($stringa, $in, $ou-$in);
            list($Wp, $Xa, $Ya)=explode(',', $str);
            if ($Ya==-1)  $Ya=$inizio;

            $penna = printer_create_pen(PRINTER_PEN_SOLID, $Wp, "000000");
            printer_select_pen($handle, $penna);

            printer_draw_line($handle, $xpos, $inizio-2, $Xa, $Ya-2);
        }
        if (strstr($stringa,'[n')){
            $in  = strpos($stringa, '[n')+2;
            $ou  = strpos($stringa, ']', $in);
            $st  = substr($stringa, $in, $ou-$in);
            list($dist, $str)=explode(',',$st);
            printer_write_num($handle,str_replace('@',',',$str),$inizio,$xpos, $dist);
        }
        if (strstr($stringa,'[s')){
            $in  = strpos($stringa, '[s')+2;
            $ou  = strpos($stringa, ']', $in);
            $st  = substr($stringa, $in, $ou-$in);
            $spacer=$st;
        }

    	if (!strstr($stringa,"[")){
            printer_draw_text($handle, $stringa, $xpos, $inizio);
        }
    	if (strstr($stringa, "[logo")){
            $in  = strpos($stringa, '[logo')+5;
            $ou  = strpos($stringa, ']', $in);
            $maxw= substr($stringa, $in, $ou-$in);

    		$infoimg = getimagesize($logo);

    		$h       = (($maxw/$infoimg[0])*$infoimg[1]);
    		printer_draw_bmp($handle, $logo, $xpos, $inizio, $maxw, $h);
    		$inizio += $h;
    	}
	}
    $inizio += $spacer;

    if ($inizio>3230){
        printer_draw_text($handle, "Pagina $pagn",50, 3200);
        printer_end_page($handle);
        printer_start_page($handle);
        $inizio=0;
        $pagn++;
    }

}


printer_draw_text($handle, "-", 0, ($inizio+($spacer*2)));

printer_end_page($handle);
printer_end_doc($handle);
printer_close($handle);
@unlink($logo);
echo "Stampa in corso...";
?>
