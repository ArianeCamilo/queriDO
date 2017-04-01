<?php
/**
 * Gera regular expression (big-data) para nomes próprios mais prováveis no Brasil.
 * (apenas https://schema.org/givenName).
 */

ini_set("default_charset", 'utf-8');

// CONFIGS:
  $splitRgx = 3;  // split to avoid PHP error "regular expression is too large"
  $basedir = dirname(__DIR__);
  $lst = file("$basedir/data/cache/nomes-proprios.unionRgx.csv");
  $basePath = "$basedir/data/cache/nomes-proprios";


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
$rgxLen = [];
foreach($all_sort as $x) {
	// print "\n--$x=".count($all[$x]);
	$rgx = join('|',$all[$x]); //count($all[$x]);
	$rgxAll[] = $rgx;
	$rgxLen[] = strlen($rgx);
}

if ($splitRgx==1) // use STDOUT
  print  join('|',$rgxAll);  // decisão de ($x) ou (?:$x) é posterior.
else {  // make std-filenames
	$sum = 0;
	foreach($rgxLen as $x) $sum+=$x;
	$avg = $sum/$splitRgx;  // to balance parts
	$idx = $sum = 0;
	$allPart = [];
	for($part=1; $part<=$splitRgx; $part++) {
		$sum = 0;
		$thisPart = [];
		for($i = $idx; $sum<$avg && $i<count($rgxLen); $i++) {
			$thisPart[] = $rgxAll[$i];
			$sum += $rgxLen[$i];
		}
		$idx = $i;
		$f = "$basePath-final$part.rgx.txt";
		print "\n-- saving $f";
		file_put_contents($f,join('|',$thisPart));
	}
}



// // //
/// LIB

function array_assocAdd(&$a,$key,$val) {
	if (array_key_exists($key,$a))
		$a[$key][] = $val;
	else
		$a[$key] = [$val];
}


