/**
 * The `change_stato` function is a JavaScript function that takes one parameter
 * `formname`. It is used to toggle the value of a form element named "stampa"
 * between 1 and 0.
 *
 * If the current value of the "stampa" element is 1, the function sets the value
 * to 0 and changes the source of an image with the ID "stampatx" to "img/no.gif".
 *
 * If the current value of the "stampa" element is not 1 (i.e., 0 or any other
 * value), the function sets the value to 1 and changes the source of the
 * "stampatx" image to "img/si.gif".
 */
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


/**
 * The `show_prezzo` function is a JavaScript function that takes a `prezzo`
 * parameter as input. The purpose of this function is to format the `prezzo`
 * value as a string representing a price.
 *
 * The function first divides the `prezzo` value by 100 to convert it to a decimal
 * value. It then converts the decimal value to a string using the `toString`
 * method. The string is then split into an array using the dot as the separator.
 *
 * If the array does not have a second element (i.e., there is no decimal part),
 * the function adds '00' as the decimal part.
 *
 * The function then calculates the difference between 2 and the length of the
 * decimal part. If the difference is negative, it is set to 0.
 *
 * The function creates a string `add` by concatenating '0' with itself multiple
 * times, using the `substr` method to ensure that the length of `add` is equal to
 * the calculated difference.
 *
 * The function concatenates `prezzo[1]` with `add` to get the decimal part with
 * the correct number of digits.
 *
 * Finally, the function concatenates `prezzo[0]` (the integer part) with the
 * formatted decimal part, separated by a comma, and returns the resulting string
 * as the formatted price.
 */
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
/**
 * The `insert_prezzo` function is used to convert a price value into an integer
 * representation.
 *
 * Parameters:
 * - `prezzo` (number or string): The price value to be converted.
 *
 * Returns:
 * - `prezzo` (number): The converted price value as an integer.
 *
 * Description:
 * - The function first converts the `prezzo` value to a string and replaces any
 * comma (`,`) with a dot (`.`) to ensure consistent decimal representation.
 * - It then checks if there is a decimal point in the string. If found, it
 * extracts the decimal part (up to 3 digits) and stores it in the `resto`
 * variable.
 * - The `prezzo` value is then split into an array using the dot as the separator,
 * and the first element (integer part) is concatenated with the `resto` value and
 * a dot to form a new string.
 * - The new string is then multiplied by 100 to convert it to an integer
 * representation.
 * - If the `prezzo` value is not a valid number, it is set to 0.
 * - Finally, the function returns the converted `prezzo` value as an integer.
 */
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

/**
 * The `toggle` function is a JavaScript method that takes an `id` parameter. It is
 * used to toggle the display of an HTML element with the specified `id`.
 *
 * The function first retrieves the element using the `getElementById` method and
 * assigns it to the `el` variable.
 *
 * It then checks the current display style of the element. If the display style is
 * either "block" or an empty string (indicating that the element is visible), the
 * function sets the display style to "none" to hide the element.
 *
 * On the other hand, if the display style is not "block" or an empty string
 * (indicating that the element is hidden), the function sets the display style to
 * "block" to show the element.
 *
 * In summary, the `toggle` function allows you to toggle the visibility of an HTML
 * element by changing its display style between "block" and "none".
 */
function toggle(id){
	el=document.getElementById(id);
	if(el.style.display=='block' || el.style.display==''){
		el.style.display='none';
	}else{
		el.style.display='block';
	}
}

/**
 * trim() is a method that can be called on a string object in JavaScript. It
 * removes any leading and trailing whitespace characters from the string and
 * returns the resulting string. This method uses regular expressions to match and
 * replace the whitespace characters. The regular expression /^\s+|\s+$/g is used
 * to match one or more whitespace characters at the beginning (^) or end ($) of
 * the string. The g flag is used to perform a global search and replace. The
 * matched whitespace characters are then replaced with an empty string,
 * effectively removing them from the original string. The resulting string with
 * no leading or trailing whitespace is returned as the output of the trim()
 * method.
 */
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
/**
 * The `ltrim` method is an extension of the `String` prototype in JavaScript. It
 * removes any leading whitespace characters from a string and returns the
 * modified string.
 *
 * Syntax:
 * ```
 * string.ltrim()
 * ```
 *
 * Parameters:
 * This method does not accept any parameters.
 *
 * Return Value:
 * - The modified string with leading whitespace characters removed.
 *
 * Example Usage:
 * ```javascript
 * const str = "   Hello, World!";
 * const trimmedStr = str.ltrim();
 * console.log(trimmedStr); // Output: "Hello, World!"
 * ```
 *
 * Note:
 * - This method only removes whitespace characters from the beginning of the
 * string. It does not modify any whitespace characters within the string or at
 * the end of the string.
 */
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
/**
 * The `rtrim` method is an extension of the `String` prototype in JavaScript. It
 * removes trailing whitespace characters from a string.
 *
 * Syntax:
 * ```
 * string.rtrim()
 * ```
 *
 * Parameters:
 * This method does not accept any parameters.
 *
 * Return Value:
 * - A new string with trailing whitespace characters removed.
 *
 * Description:
 * The `rtrim` method is used to remove any whitespace characters (spaces, tabs,
 * etc.) that appear at the end of a string. It does not modify the original
 * string, but instead returns a new string with the trailing whitespace removed.
 *
 * Examples:
 * ```javascript
 * const str1 = "Hello World    ";
 * console.log(str1.rtrim()); // Output: "Hello World"
 *
 * const str2 = "   JavaScript   ";
 * console.log(str2.rtrim()); // Output: "   JavaScript"
 * ```
 *
 * In the first example, the `rtrim` method removes the trailing whitespace from
 * the string "Hello World", resulting in the string "Hello World". In the second
 * example, the method does not remove any trailing whitespace as it is not
 * present at the end of the string.
 */
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}
/**
 * The `checkForm` function is a JavaScript function that takes a form object `f`
 * as a parameter. It is used to validate the form data before submitting it.
 *
 * The function first checks if the value entered for the "adulti" field is a valid
 * number. If not, it sets the value to 0.
 *
 * Next, it checks if the value entered for the "bambini" field is a valid number.
 * If not, it sets the value to 0.
 *
 * Then, it initializes an empty string variable `mex` which will be used to store
 * error messages.
 *
 * The function checks if the "camera" field exists and if its value is empty. If
 * so, it adds an error message to `mex` indicating that the camera field should
 * be filled.
 *
 * Next, it checks if the values entered for "adulti" and "bambini" are valid
 * numbers. If not, it adds an error message to `mex` indicating that the number
 * of adults and children should be numeric or left empty.
 *
 * The function then checks if both the number of adults and children is less than
 * or equal to 0. If so, it adds an error message to `mex` indicating that the
 * form should have at least one person (adult or child).
 *
 * Finally, the function checks if the values entered for the arrival and departure
 * dates are valid. If any of the date fields are not selected, it adds an error
 * message to `mex` indicating that a correct date should be entered.
 *
 * If `mex` is not empty, it displays an alert with the error messages and returns
 * false, indicating that the form should not be submitted. Otherwise, it returns
 * true, indicating that the form can be submitted.
 */
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
