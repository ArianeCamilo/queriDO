<?php
/**
 * Gera regular expression (big-data) para nomes próprios mais prováveis no Brasil. 
 * (apenas https://schema.org/givenName).
 */

ini_set("default_charset", 'utf-8');
$basedir = dirname(__DIR__);

$lst = file("$basedir/data/nomes-proprios.unionRgx.csv");
array_shift($lst); // rm header
$all = [];
$repetiu=[];

foreach(array_map('trim',$lst) as $nome) if ($nome) {
	$nomeAux = preg_replace('/\[[^\]]+\]/us','X',$nome);
	$len = mb_strlen($nomeAux);
	// debug print "\n-- $nomeAux=$len";
	$reg = str_replace(' ','\s',$nome);
	if ($len>2 && !isset($repetiu[$reg])) array_assocAdd($all,$len,$reg);
	$repetiu[$reg] = 1;
}

$all_sort = array_keys($all);
rsort($all_sort); // nao precisa pois csv já vem ordenado... Conferir efeito na array.

$rgxAll = [];

foreach($all_sort as $x) {
	// print "\n--$x=".count($all[$x]);
	$rgx = join('|',$all[$x]); //count($all[$x]);
	$rgxAll[] = $rgx;
}

print  join('|',$rgxAll);  // decisão de ($x) ou (?:$x) é posterior.


// // //
/// LIB

function array_assocAdd(&$a,$key,$val) {
	if (array_key_exists($key,$a))
		$a[$key][] = $val;
	else
		$a[$key] = [$val];
}


