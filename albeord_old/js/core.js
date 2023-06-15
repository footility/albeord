function change_stato(formname){
	if (document.forms[formname].elements["stampa"].value==1){
			document.forms[formname].elements["stampa"].value=0;
			document.images.stampatx.src="img/no.gif";
	}else{
		document.forms[formname].elements["stampa"].value=1;
		document.images.stampatx.src="img/si.gif";
	}
}
var abbb=new Image();
abbb.src="img/no.gif";
var abbbb=new Image();
abbbb.src="img/si.gif";


function show_prezzo(prezzo){
	prezzo = (prezzo/100);
	prezzo = prezzo.toString();
	prezzo = prezzo.split('.');
	if (!prezzo[1]) prezzo[1]='00';
	diff   = 2-prezzo[1].length;
	if (diff<0) diff=0;
	add    = "00000000000".substr(0,diff);
	resto  = prezzo[1].concat(add)
	prezzo = prezzo[0]+","+resto;
	return prezzo;
}
function insert_prezzo(prezzo){
	prezzo = prezzo.toString();
	prezzo = prezzo.replace(',', '.');
	pos    = prezzo.indexOf('.');
	if (pos>-1){
	    pos    = pos+1;
		resto  = prezzo.substr(pos,3);
		prezzo = prezzo.split('.');
		prezzo = prezzo[0]+"."+resto;
		prezzo = prezzo*100;
	}else{
		prezzo = parseInt(prezzo)*100;
	}
	if  (!parseInt(prezzo)) prezzo=0;
	return parseInt(prezzo);
}

function toggle(id){
	el=document.getElementById(id);
	if(el.style.display=='block' || el.style.display==''){
		el.style.display='none';
	}else{
		el.style.display='block';
	}
}

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}
function checkForm(f){
	if(isNaN(parseInt(f.elements["adulti"].value))){
		f.elements["adulti"].value=0;
	}
	if(isNaN(parseInt(f.elements["bambini"].value))){
		f.elements["bambini"].value=0;
	}
	mex="";
	
	if(f.elements["camera"] && f.elements["camera"].value.trim().length==0) mex+="Inserire la camera\n";
	
	if(isNaN(parseInt(f.elements["adulti"].value))) mex+="Il numero di adulti deve essere un numero oppure deve essere lasciato vuoto\n";
	if(isNaN(parseInt(f.elements["bambini"].value))) mex+="Il numero di bambini deve essere un numero oppure deve essere lasciato vuoto\n";
	
	if(parseInt(f.elements["adulti"].value)<=0 && parseInt(f.elements["bambini"].value)<=0) mex+="La scheda deve essere composta da almeno una persona (adulto o bambino)\n";
	
	if(f.elements["d_g"].value==0 || f.elements["d_m"].value==0 || f.elements["d_a"].value==0) mex+="Inserire una data di arrivo coretta\n";
	if(f.elements["a_g"].value==0 || f.elements["a_m"].value==0 || f.elements["a_a"].value==0) mex+="Inserire una data di partenza coretta\n";

	
	if(mex.length>0){
		alert(mex);
		return false;
	}
	return true;
} 
