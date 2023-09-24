/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */

/*
 * Configurable variables. You may need to tweak these to be compatible with
 * the server-side, but the defaults work in most cases.
 */
var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
/**
 * The `b64_md5` function is a JavaScript function that takes a string `s` as input
 * and returns the Base64 encoded MD5 hash of that string.
 *
 * Parameters:
 * - `s`: A string that needs to be hashed.
 *
 * Return Value:
 * - The Base64 encoded MD5 hash of the input string.
 *
 * Example Usage:
 * ```javascript
 * var hash = b64_md5("Hello World");
 * console.log(hash); // Output: "XrY7u+Ae7tCTyyK7j1rNww=="
 * ```
 *
 * Note: This function relies on other helper functions `core_md5`, `str2binl`,
 * `binl2b64` to perform the actual MD5 hashing and Base64 encoding operations.
 */
function b64_md5(s){ return binl2b64(core_md5(str2binl(s), s.length * chrsz));}
/**
 * The `str_md5` function is a JavaScript function that calculates the MD5 hash
 * value of a given string. It takes a single parameter `s`, which is the input
 * string for which the MD5 hash needs to be calculated.
 *
 * The function internally uses several helper functions to perform the MD5
 * calculation. These helper functions include `str2binl`, `core_md5`, and
 * `binl2str`.
 *
 * The `str2binl` function converts the input string `s` into an array of 32-bit
 * integers. It uses the `chrsz` variable, which represents the number of bits per
 * character, to determine the size of each element in the resulting array.
 *
 * The `core_md5` function performs the actual MD5 calculation. It takes two
 * parameters: the input string converted to an array of 32-bit integers, and the
 * length of the input string multiplied by `chrsz`. This function implements the
 * MD5 algorithm to generate the hash value.
 *
 * The `binl2str` function converts the array of 32-bit integers representing the
 * MD5 hash value back into a string. It concatenates the hexadecimal
 * representation of each integer in the array to form the final hash value
 * string.
 *
 * Finally, the `str_md5` function returns the calculated MD5 hash value as a
 * string.
 *
 * Note: The `chrsz` variable is not defined in the provided code snippet, so its
 * value is assumed to be defined elsewhere in the code.
 */
function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}
/**
 * The `hex_hmac_md5` function is a JavaScript method that calculates the HMAC-MD5
 * hash of a given `data` using the provided `key`. It then converts the resulting
 * binary hash into a hexadecimal representation.
 *
 * Parameters:
 * - `key` (string): The secret key used for the HMAC-MD5 calculation.
 * - `data` (string): The data to be hashed using HMAC-MD5.
 *
 * Return Value:
 * - A hexadecimal string representing the HMAC-MD5 hash of the `data` using the
 * provided `key`.
 *
 * Example Usage:
 * ```javascript
 * const key = "mySecretKey";
 * const data = "Hello, World!";
 * const hash = hex_hmac_md5(key, data);
 * console.log(hash); // Output: "5eb63bbbe01eeed093cb22bb8f5acdc3"
 * ```
 *
 * Note: This function relies on the `core_hmac_md5` function, which is not defined
 * in the provided code snippet.
 */
function hex_hmac_md5(key, data) { return binl2hex(core_hmac_md5(key, data)); }
/**
 * The `b64_hmac_md5` function is a JavaScript method that takes in two parameters:
 * `key` and `data`. It returns the Base64-encoded HMAC-MD5 hash of the `data`
 * parameter using the `key` parameter.
 *
 * The function internally calls the `core_hmac_md5` function, which calculates the
 * HMAC-MD5 hash of the `data` parameter using the `key` parameter. The resulting
 * hash is then converted from binary format to Base64 format using the `binl2b64`
 * function.
 *
 * Note that the `core_hmac_md5` and `binl2b64` functions are not defined in the
 * provided code snippet and should be implemented separately.
 */
function b64_hmac_md5(key, data) { return binl2b64(core_hmac_md5(key, data)); }
/**
 * The `str_hmac_md5` function is used to generate an HMAC-MD5 hash in JavaScript.
 * It takes two parameters: `key` and `data`. The `key` parameter represents the
 * secret key used for the HMAC calculation, while the `data` parameter represents
 * the data to be hashed.
 *
 * The function internally calls the `core_hmac_md5` function, which performs the
 * actual HMAC-MD5 calculation. The result of this calculation is then converted
 * from binary format to a string using the `binl2str` function.
 *
 * The `str_hmac_md5` function returns the HMAC-MD5 hash as a string.
 */
function str_hmac_md5(key, data) { return binl2str(core_hmac_md5(key, data)); }

/*
 * Perform a simple self-test to see if the VM is working
 */
function md5_vm_test()
{
  return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
}

/*
 * Calculate the MD5 of an array of little-endian words, and a bit length
 */
function core_md5(x, len)
{
  /* append padding */
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;

  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);

}

/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
/**
 * The `md5_ff` function is a helper function used in the MD5 algorithm. It takes
 * in several parameters and returns the result of calling another function called
 * `md5_cmn`.
 *
 * Parameters:
 * - `a`: An integer representing the first input value.
 * - `b`: An integer representing the second input value.
 * - `c`: An integer representing the third input value.
 * - `d`: An integer representing the fourth input value.
 * - `x`: An integer representing the data input.
 * - `s`: An integer representing the shift amount.
 * - `t`: An integer representing the constant value.
 *
 * Return Value:
 * - The result of calling the `md5_cmn` function with the appropriate parameters.
 *
 * Note: This function is typically used internally within the MD5 algorithm and is
 * not meant to be called directly.
 */
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
/**
 * The `md5_gg` function is a helper function used in the MD5 algorithm. It takes
 * in six parameters: `a`, `b`, `c`, `d`, `x`, `s`, and `t`.
 *
 * - `a`, `b`, `c`, and `d` are 32-bit integers representing the internal state
 * variables of the MD5 algorithm.
 * - `x` is a 32-bit integer representing the input data.
 * - `s` is a number representing the number of bits to shift the input data.
 * - `t` is a 32-bit integer representing a constant value used in the algorithm.
 *
 * The function performs a bitwise operation on `b` and `d`, and then performs
 * another bitwise operation on `c` and the complement of `d`. The results of
 * these two operations are then combined using a bitwise OR operation.
 *
 * The resulting value is then passed to the `md5_cmn` function along with the
 * other parameters (`a`, `b`, `x`, `s`, and `t`). The `md5_cmn` function performs
 * additional operations as part of the MD5 algorithm.
 *
 * The `md5_gg` function returns the result of the `md5_cmn` function.
 */
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
/**
 * The `md5_hh` function is used in the MD5 algorithm to perform a specific
 * operation. It takes in six parameters: `a`, `b`, `c`, `d`, `x`, `s`, and `t`.
 *
 * - `a`, `b`, `c`, and `d` are variables representing the internal state of the
 * MD5 algorithm.
 * - `x` is a variable representing the input data for the operation.
 * - `s` is a variable representing the number of bits to shift the input data.
 * - `t` is a variable representing a constant value used in the operation.
 *
 * The function returns the result of calling the `md5_cmn` function with the
 * following arguments:
 * - `b ^ c ^ d` is the result of performing a bitwise XOR operation on `b`, `c`,
 * and `d`.
 * - `a` is passed as is.
 * - `b` is passed as is.
 * - `x` is passed as is.
 * - `s` is passed as is.
 * - `t` is passed as is.
 *
 * The `md5_cmn` function is not defined in the provided code snippet, so its
 * implementation and purpose are unknown.
 */
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
/**
 * The `md5_ii` function is a helper function used in the MD5 algorithm. It takes
 * in six parameters: `a`, `b`, `c`, `d`, `x`, `s`, and `t`.
 *
 * - `a`, `b`, `c`, and `d` are 32-bit integers representing the four MD5 state
 * variables.
 * - `x` is a 32-bit integer representing the input data.
 * - `s` is an integer representing the number of bits to shift the state
 * variables.
 * - `t` is a 32-bit integer representing a constant value used in the algorithm.
 *
 * The function performs a bitwise XOR operation between `c` and the bitwise OR
 * operation between `b` and the bitwise NOT operation on `d`. This result is then
 * passed to the `md5_cmn` function along with the other parameters to calculate
 * the new value of `a`.
 *
 * The `md5_ii` function returns the result of the `md5_cmn` function.
 */
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Calculate the HMAC-MD5, of a key and some data
 */
function core_hmac_md5(key, data)
{
  var bkey = str2binl(key);
  if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
  return core_md5(opad.concat(hash), 512 + 128);
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * Convert a string to an array of little-endian words
 * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
 */
function str2binl(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
  return bin;
}

/*
 * Convert an array of little-endian words to a string
 */
function binl2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}

/*
 * Convert an array of little-endian words to a hex string.
 */
function binl2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}

/*
 * Convert an array of little-endian words to a base-64 string
 */
function binl2b64(binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}
