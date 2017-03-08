<?php
/**
 * Marca texto com padrões previamente analisados.
 */

ini_set("default_charset", 'utf-8');
$useGivenName = false;
$basedir = dirname(__DIR__);  //  clone root

// $dirTree = 'data/do-info'; 		// csv
// $originais = 'content/original'; 	// html original com UTF8 homologado
$filtrados = 'content/filtrado'; 	// html limpo
$marcados = 'content/marcado'; 	// destino!
$givenName_rgx = file_get_contents("$basedir/data/nomes-proprios.rgx.txt");
$gnRegex = '#(?<=[\s>])(?:'.$givenName_rgx.')(?=[\s<])#uis'; // ideal is compile first!

foreach (scandir("$basedir/$filtrados") as $f) if (substr($f,-5,5)=='.html') {
	echo "\n- $f";
	$htm2 = mark("$basedir/$filtrados/$f",true);
	file_put_contents("$basedir/$marcados/$f",$htm2);
}

// // // // // // // // //

function mark($file) {
	$clean = file_get_contents($file);

	// especifico da curadoria:
	$clean = preg_replace(
		'#Cons[óo]rcio\s+Concremat.Arcadis(?:\s+Logos)?(?:\s+S.A)?|CONCREMAT(?:\s+ENGENHARIA)?(\s+E\s+TECO?NOLOGIA)?(\s+(?:S[/\.]A\.?|Ltda\.))?#uis',
		'<mark class="organization_name">$0</mark>',
		$clean
	);

	if ($useGivenName) $clean = preg_replace(
		$gnRegex,
		'<mark class="givenName">$0</mark>',
		$clean
	);  // internamente a regex é pre-compilada por cache, https://bugs.php.net/bug.php?id=32470


	// homologados
	$clean = preg_replace('#(?:CNPJ[\s:\-\.]*)?\d\d\.\d\d\d\.\d\d\d/\d\d\d\d\-\d\d#uis', '<data class="urn-org-vatID">$0</data>', $clean);
	$clean = preg_replace('#Artigo\s+[\d\.]+\sInciso\s+.+?\s+da\s+Lei\s+n?º?\s*\d[\d\.]+\d[/\s]+\d\d\d\d#uis', '<cite class="urn-lex">$0</cite>', $clean);
	$clean = preg_replace('#Artigo\s+[\d\.]+\sda\s+Lei\s+n?º?\s*\d[\d\.]+\d[/\s]+\d\d\d\d#uis', '<cite class="urn-lex">$0</cite>', $clean);
	$clean = preg_replace('#(?:Lei|Descreto\s+Lei|Decreto|Portaria)\s+n?º?\s*\d[\d\.]+\d[/\s]+\d\d(\d\d)?#uis', '<cite class="urn-lex">$0</cite>', $clean);

	$clean = preg_replace('#Contrato\s+(?:N\.?º?\s*)?\d[\d\-\./]+(/\d\d\d\d)?#uis', '<cite class="urn-cntrt">$0</cite>', $clean);
	$clean = preg_replace('#Processo(?:\s+INSTRUTIVO|\s+administrativo)?\s*(?:N\.?º?\s*|:\s*)?\d[\d\-\./]+#uis', '<cite class="urn-gov-proc">$0</cite>', $clean);
	$clean = preg_replace('#matr(?:\.|[ií]cula)\s+(?:N\.?º?\s*)?\d[\d\-\./]+#uis', '<cite class="urn-gov-profreg">$0</cite>', $clean); // ProfessionalService Registration ID

	$clean = preg_replace('#(?:\s*<[ubi]>\s*)*Valor(?:\s*</[ubi]>\s*)*[\s:]*(de\s)?R\$\s*\d[\d\,\.]+#uis', '<data class="currencyValue">$0</data>', $clean); // itemtype="http://schema.org/MonetaryAmount"
	$clean = preg_replace('#\d\d?\s+de\s+(?:janeiro|fevereiro|mar[çc]o|abril|maio|junho|julho|agosto|setembro|outubro|novembro|dezembro)\s+de\s+\d\d\d\d#uis', '<time class="date">$0</time>', $clean);

	// sem efeito (e ainda não-testados)
	$clean = preg_replace('#(?:CPF[\s:\.]*)?\d\d\d\.\d\d\d\.\d\d\d\-\d\d\d#uis', '<data class="urn-person-vatID">$0</data>', $clean);
	$clean = preg_replace('#RG[\s:\.]*\d\d[\d\-\.]+#uis', '<data class="urn-person-id">$0</data>', $clean);
	$clean = preg_replace('#CEP[\s:\-\.]*\d\d\.\d\d\d\.\d\d\d/\d\d\d\d\-\d\d#uis', '<data class="urn-postalCode">$0</data>', $clean);

	return $clean;
}
