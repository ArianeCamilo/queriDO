<?php
/**
 * Busca, teste.
 * grep  "givenName" content/marcado/* | grep CPF
 */

ini_set("default_charset", 'utf-8');
$useGivenName = true;
$basedir = dirname(__DIR__);  //  clone root

$marcados = 'content/marcado'; 	// destino!

$d = "$basedir/$marcados";
foreach (scandir($d) as $f) if (substr($f,-5,5)=='.html') {
	echo "\n\n----------- $f ------------";
	$dom = new DOMDocument;
	$dom->load("$d/$f");
	$xpath = new DOMXPath($dom);
	$pname = $xpath->query("//p[.//mark/@givenName='givenName']");
	//foreach ($dom->getElementsByTagName('p') as $p) {
	foreach ($pname as $p) {
	    echo trim($p->nodeValue), PHP_EOL;
	}
}



