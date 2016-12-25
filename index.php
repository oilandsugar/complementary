<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Complementary encryption prototype</title>
<script language="JavaScript" src="convertuni.js"></script>
<script language="JavaScript" src="uni_ajax.js"></script>
<style>
.result {display:block; padding:5px; border:1px solid magenta; margin-top:5px; background:#fff;}
.resultdec {display:block; padding:5px; border:1px solid #00FF00; margin-top:5px; background:#fff;}
.pixel {width:10px; height:10px; float:left;}
.pixelwrapper {
margin-top:20px;	
}
.mag-field {
	width:97%; margin:20px 0 5px 0; border:1px solid magenta; padding:5px;}
</style>
</head>

<body style="font-family:helvetica, sans-serif; background: #F3F3F3; padding:20px;">
<div style="max-width:800px; margin:0 auto; position:relative;">
<div style="max-width:400px; width:49%; margin-right:1%; float:left;">
<h1 style="color:magenta;">Complementary color based encrypter</h1>
<hr style="margin-bottom:20px; background:magenta; border:0; height:1px;">

<form action="complementary.php" method="get">
Input: <input type="text" name="input" class="mag-field"><br><small style="margin-bottom:20px; display:block;">(string of characters, not really working for now but working on it)</small>
<input type="submit" value="encrypt" style="border:0; background:magenta; color:#fff;">
</form>


<?php

/* split string into array of characters */

function mbStringToArray ($string) { 
    $strlen = mb_strlen($string); 
    while ($strlen) { 
        $array[] = mb_substr($string,0,1,"UTF-8"); 
        $string = mb_substr($string,1,$strlen,"UTF-8"); 
        $strlen = mb_strlen($string); 
    } 
    return $array; 
} 

/* encode single character function */

function encode($str){
	$res = preg_replace('/([\200-\277])/e', "'&#'.(ord('\\1')).';'", $str);
	$unwanted = array('&', '#', ';');
	$striped = str_replace($unwanted,"", $res);
	$hexcolor = str_pad($striped, 6 , '0');
	$rgb = hexdec($hexcolor); 
	$rgb ^= 0xffffff; 
	$hex = substr('000000'.dechex($rgb), -6); 
	$unwantedf = array('f');
	$striped2 = str_replace($unwantedf, "", $hex);
	$replacea = str_replace("a", "4", $striped2);
	$replaceb = str_replace("b", "5", $replacea);
	$replacec = str_replace("c", "6", $replaceb);
	$replaced = str_replace("d", "7", $replacec);
	$replacee = str_replace("e", "8", $replaced);
	$ranged = $replacee + 30;
	$text = "&#" . $ranged . ";";
	
	$result = "$text/$hex/"; 
	return $result;
	
};



/* encode each character of an array function */

function encodearray(&$value, $key){
	 $value = encode($value);	
};

$stringen = $_GET["input"];
$inarray = mbStringToArray($stringen);
$mainstring = array("0" => "&#34", "1" => "&#38;", "2" => "&#60;	", "3" => "&#8364;");
$outputexp = explode("/", $outputfull);
$outputen = $outputexp[0];
$lastcol = $outputexp[1];

function test_print($value, $key)
{
    echo "$value";
}

function print_pixels($value, $key){
	$parts = explode("/", $value);
	echo "<div class=pixel style=background:#" . $parts[1] . "></div>";
}


$encoded = preg_replace('/([\200-\277])/e', "'&#'.(ord('\\1')).';'", $stringen);
$unwanted2 = array('&', '#', ';');
$striped2 = str_replace($unwanted2,"", $encoded);




echo "<br>Result: <div class=result>";
echo "html entities<br>";
echo $encoded;
echo "<br>";
echo "striped html entities<br>";
echo $striped2;
echo "<br>";
echo "Before<br>";
array_walk($inarray, 'test_print');
echo "<br>";
array_walk($inarray, 'encodearray');
echo "and after<br>";
array_walk($inarray, 'test_print');
echo "</div>";
echo "<small>(character * initial color * complementary color)</small>";
echo "<div class=pixelwrapper>";
array_walk($inarray, 'print_pixels');
echo "</div>";
?>

<textarea name="name1" style="font-size:16px" class="forminputcontent80" value="" onfocus="" onblur=""></textarea>
<input style="font-size:15px" type="button" onclick="calstr(name1.value);
return false;" name="lengthA" value="Convert">

<textarea name="name2" style="font-size:16px" class="forminputcontent80" value="" onfocus="" onblur=""></textarea>
<input style="font-size:15px" type="button" onclick="convertDecNCR2CP(name2.value)" name="lengthB" value="Convert">



</div>
<div style="max-width:400px; width:49%; margin-left:1%; float:left;">
<h1 style="color:#00FF00;">Complementary color based decrypter</h1>
<hr style="margin-bottom:20px; background:#00FF00; border:0; height:1px;">

<form action="complementary.php" method="get">
Input: <input type="text" name="inputdec" style="width:97%; margin:20px 0 5px 0; border:1px solid #00FF00; padding:5px;"><br><small style="margin-bottom:20px; display:block;">(just a placeholder for now)</small>
<input type="submit" value="decrypt" style="border:0; background:#00FF00; color:#fff;">
</form>

<?php

/* decode function */

function decode($str){
	$str = mb_convert_encoding($str , 'UTF-32', 'UTF-8'); //big endian
    $split = str_split($str, 4);

    $res = "";
    foreach ($split as $c) {
        $cur = 0;
        for ($i = 0; $i < 4; $i++) {
            $cur |= ord($c[$i]) << (8*(3 - $i));
        }
		$res .= "&#" . $cur . ";";
	}
	$unwanted = array('&', '#', ';');
	$striped = str_replace($unwanted,"", $res);
	$hexcolor = str_pad($striped, 6 , '0');
	$rgb = hexdec($hexcolor); 
	$rgb ^= 0xffffff; 
	$hex = substr('000000'.dechex($rgb), -6); 
	$unwantedf = array('f');
	$striped2 = str_replace($unwantedf, "", $hex);
	$replacea = str_replace("a", "4", $striped2);
	$replaceb = str_replace("b", "5", $replacea);
	$replacec = str_replace("c", "6", $replaceb);
	$replaced = str_replace("d", "7", $replacec);
	$replacee = str_replace("e", "8", $replaced);
	$ranged = $replacee + 30;
	$text = "&#" . $ranged . ";";
	$char = html_entity_decode($text);
	return $char;	
};

$stringdec = $_GET["inputdec"];
$outputdec = decode($stringdec);

echo "<br>Result: <div class=resultdec>" . $output . "</div>";
?>

</div>
<small style="text-align:center; display:block; padding-top:20px; bottom:20px; width:100%;opacity:0.5; clear:both;"> - Complémentaire mon cher Watson - </small>
</div>

</body>
</html>