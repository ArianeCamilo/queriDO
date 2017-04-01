<?php
/**
 * Busca, teste.
 * grep  "givenName" content/marcado/* | grep CPF
 */

ini_set("default_charset", 'utf-8');
$useGivenName = true;
$basedir = dirname(__DIR__);  //  clone root

$marcados = 'content/marcado'; 	// destino!

$xmlDoc = new DOMDocument();
//$xmlDoc->load("note.xml");


$d = "$basedir/$marcados";
foreach (scandir($d) as $f) if (substr($f,-5,5)=='.html') {
	$dom = new DOMDocument();
	$dom->load("$d/$f");
	$xpath = new DOMXPath($dom);
	$pname = $xpath->query("//p[.//mark/@class='givenName']");
	foreach ($pname as $p) if (preg_match('/\s(CPF|RG)\s/s',$p->nodeValue) || preg_match('/\s(identidade|documento)\s/siu',$p->nodeValue)) {
            echo "\n\n----------- $f ------------\n";
	    echo trim($p->nodeValue), PHP_EOL;
	}
}



