<?php
/**
 * Extrai template do Diario Oficial do municipio do Rio, específico dele.
 */

ini_set("default_charset", 'utf-8');

$basedir = dirname(__DIR__);  //  clone root
$dirTree = 'data/do-info'; 		// csv
$originais = 'content/original'; 	// html original com UTF8 homologado
$filtrados = 'content/filtrado';

foreach (scandir("$basedir/$originais") as $f) if (substr($f,-5,5)=='.html') {
	echo "\n- $f";
	list($htm2,$meta) = superClen("$basedir/$originais/$f",true);
	file_put_contents("$basedir/$filtrados/$f",$htm2);
}

// // // // // // // // //

function superClen($file) {
	$METAchar = '<meta charset="UTF-8"/>';
	$clean = file_get_contents($file);
	$clean = preg_replace('/^\s*<html>/s', "<html>$METAchar", $clean);
	list($clean,$meta) = XMLpretty($clean);
	$clean = strip_tags($clean, '<p><pre><article><section><header><footer><h1><h2><h3><h4><h5><code><tt><b><i><u><br><sub><sup><td><tr><table><blockquote><q><li><ol><ul>'); // lost root
	$clean = preg_replace('/\n\s*\n\s*/su', "\n\n", $clean);
	// remover code e pre, limpar espaços, e colocar code e pre de volta.
	$head = "<head>\n\t".join("\n\t",[
		$METAchar
		, "<meta itemprop='identifier' content='$meta[materia]'/>"
		, "<meta itemprop='datePublished' content='$meta[datePublished]'/>"
		, "<meta itemprop='datePublished-pubid' content='{$meta['datePublished-pubid']}'/>"
	])."\n</head>";
	return ["<html>\n$head\n$clean\n</html>\n", $meta];
}

function x2dom($xml) {
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$dom->preserveWhiteSpace = false;
	$dom->resolveExternals = false; // external entities from a (HTML) doctype declaration
	$dom->recover = true; // Libxml2 proprietary behaviour. Enables recovery mode, i.e. trying to parse non-well formed documents
	$ok = @$dom->loadHTML($xml, LIBXML_NOENT | LIBXML_NOCDATA);
	if ($ok) return $dom; else return NULL;
}

function XMLpretty($xml) {
	$dom = x2dom($xml); // DOMDocument
	if (!$dom) die("\nOPS, confira ERRO DOM.\n");
	$dom->preserveWhiteSpace = false;
	$dom->resolveExternals = false; // external entities from a (HTML) doctype declaration
	$dom->formatOutput = true;
	foreach ($dom->getElementsByTagName('meta') as $e) $e->nodeValue = '';
	foreach ($dom->getElementsByTagName('script') as $e) $e->nodeValue = '';
	foreach ($dom->getElementsByTagName('style') as $e) $e->nodeValue = '';
	foreach ($dom->getElementsByTagName('head') as $e) $e->nodeValue = '';
	$xpath = new DOMXPath($dom);
	foreach ($xpath->query('//comment()') as $e) $e->nodeValue = '';
	foreach ($xpath->query('//*[@style]') as $at) $at->removeAttribute('style');

	// Assinaturas do DOM-RJ:
	$tabtopo = $xpath->query('//table[@class="topo_materia"]');
	$htm = '';
	$metadados = [];
	if (strlen(trim($tabtopo->item(0)->nodeValue))>100){
		$div = $xpath->query('//td//div[@id="pagina"]');
		if (strlen(trim($div->item(0)->nodeValue))>100) {
			print ".. conteúdo extraído da moldura ..";
			// Extração do conteúdo:
			$htm = $dom->saveXML($div->item(0));
			// Extração dos metadados externos ao conteúdo:
			$div->item(0)->nodeValue = '';
			$resto = trim(preg_replace('/\s+/us',' ',$dom->documentElement->nodeValue));
			if (preg_match('|Diário Oficial nº : (\d+) Data de publicação: (\d+)/(\d+)/(\d+) Matéria nº : (\d+)|uis',$resto,$m)) {
				list($domrj_num,$data,$materia) = [$m[1], "$m[4]-$m[3]-$m[2]", $m[5]];
				$metadados = ['datePublished-pubid'=>$domrj_num, 'datePublished'=>$data, 'materia'=>$materia];
				print "\n\tMetadados da moldura: DOM-RJ-num$domrj_num, data iso $data, materia $materia";
			}
			else print "\n\tERRO: perlfil da moldura não bate com o esperado.";
		}
	}
	if (!$htm) {
		print ".. full lixo!..";
		$htm = $dom->saveXML($dom->documentElement);
	}
	return  [$htm,$metadados];
}
