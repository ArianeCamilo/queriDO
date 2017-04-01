<?php
/**
 * Marca texto com padrões previamente analisados.
 */

ini_set("default_charset", 'utf-8');
$useGivenName = true;
$basedir = dirname(__DIR__);  //  clone root

// $dirTree = 'data/do-info'; 		// csv
// $originais = 'content/original'; 	// html original com UTF8 homologado
$filtrados = 'content/filtrado'; 	// html limpo
$marcados = 'content/marcado'; 	// destino!


// BEGIN:PREPARE proper name regexes:
//   (see http://www.regular-expressions.info/unicode.html )
//   NOTE1: no need of regex-cache, as https://bugs.php.net/bug.php?id=32470
 $ridx = 0;
 $givenName_rgx =[];
 $gnRegexAgg =[];
 $gnRegex =[];
 $gnRegex2 =[];
 $gnRegex3 =[];
 $oldmakrs = NULL;
 foreach (glob("$basedir/data/cache/nomes-proprios-final*.rgx.txt") as $f) {
   //   NOTE2: need of multi-regex, to avoid  “Regular Expression is too large” error.
   echo "\n... using $f";
   $givenName_rgx[$ridx] = file_get_contents($f);
   $gnRegexAgg[$ridx] = '/(?<=[\s>])('. $givenName_rgx[$ridx] .')\s+_#gname_(\d+)#_/usi';
   $gnRegex[$ridx]    = '#(?<=[\s>])(?:'. $givenName_rgx[$ridx] .')\s+(?:(?:\p{Lu}\p{Ll}+|d[oa]s?|de[lr]?|dal|e|van)\s+){0,8}(?:\p{Lu}\p{Ll}+)(?=[,;\.\(\)\[\]\s<])#us'; 
    // full name in free context, case sensitive.
   $gnRegex2[$ridx] = '#<b>((?:[^<]*?|\s+)?(?:'. $givenName_rgx[$ridx] .')\s[^<]+?)</b>#ius';  // clue for something into the bolds...
   $gnRegex3[$ridx] = '#(?<=[\s>,;]|^)(?:'. $givenName_rgx[$ridx ].')(?:\s+\p{L}+){0,8}\s+\p{L}+#ius';  // full name in bold context.
   $ridx++;
 }
// END:PREPARE



/* LEMBRETE: topônimos e cia, no parsing de segunda ordem,
     "Instituto <mark class="givenName">Fulano</mark>, rua <mark class="givenName">Ciclano</mark>".
     envelopar como Place ou NamedEntity
     <span class="NamedEntity" data-type="instituto">Instituto <mark class="givenName">Fulano</mark></span>,
     <span class="Place" data-type="via">rua <mark class="givenName">Ciclano</mark></span>.
     <span class="Place" data-type="via">avenida Marginal</span>.
*/


foreach (scandir("$basedir/$filtrados") as $f) if (substr($f,-5,5)=='.html') {
	echo "\n- $f";
	$htm2 = mark("$basedir/$filtrados/$f",true);
	file_put_contents("$basedir/$marcados/$f",$htm2);
}

// // // // // // // // //

function oldmark_enfold0($m) {
        global $oldmarks;
        $oldmarks[] =$m[0]; // or 1
        $n = count($oldmarks)-1;
       return "_#gname_$n#_";
}


function mark($file) {
	global $useGivenName;
        global $gnRegexAgg;
	global $gnRegex;
	global $gnRegex2;
	global $ridx;
	global $oldmarks;

	$clean = file_get_contents($file);

	// especifico da curadoria:
	$clean = preg_replace(
		'#Cons[óo]rcio\s+Concremat.Arcadis(?:\s+Logos)?(?:\s+S.A)?|CONCREMAT(?:\s+ENGENHARIA)?(\s+E\s+TECO?NOLOGIA)?(\s+(?:S[/\.]A\.?|Ltda\.))?#uis',
		'<mark class="organization_name">$0</mark>',
		$clean
	);

	if ($useGivenName) {
	  $oldmarks = [];
	  /*
	  $clean = preg_replace_callback(   // (REDUNDANT NO EFECT HERE) remove and count marks
                '#<mark class="givenName">([^<]+)</mark>#s',
		'oldmark_enfold',
                $clean
          );
	  */
	  for($i=0;$i<$ridx; $i++) {  // each block of proper-name-regexes

                $clean = preg_replace_callback(   // aggregates firstname to oldmark.
                        $gnRegexAgg[$i],
                        function ($m) {
				global $oldmarks;
				$oldmarks[] ="$m[1] ".$oldmarks[$m[2]];
                            	$n = count($oldmarks)-1;
                            	return "_#gname_$n#_";
			},
                        $clean
                );
		$clean = preg_replace_callback(   // marca em texto livre.
			$gnRegex[$i], 'oldmark_enfold0', $clean
		);
		$clean = preg_replace_callback(   // case-insensitiveness only for bolds:
			$gnRegex2[$i],
		        function ($matches) use ($i) {
			    global $gnRegex3;
		            return preg_replace_callback($gnRegex3[$i], 'oldmark_enfold0', $matches[0]);
        		},
			$clean
		);

	  } // for
          $clean = preg_replace_callback( // give back the oldmarks
                '/_#gname_(\d+)#_/',
                function ($m) {global $oldmarks; $nome=$oldmarks[$m[1]]; return "<mark class=\"givenName\">$nome</mark>";},
                $clean
          );
	} // if


	// // // //
	// quase homologados, marcações atômicas, de primeira ordem:

	$clean = preg_replace('#(?:CNPJ[\s:\-\.]*)?\d\d\.\d\d\d\.\d\d\d/\d\d\d\d\-\d\d#uis', '<data class="urn-org-vatID">$0</data>', $clean);

	//(nao usar .+) $n1 = 0; $clean = preg_replace('#Artigo\s+[\d\.]+\sInciso\s+.+?\s+da\s+Lei\s+n?º?\s*\d[\d\.]+\d[/\s]+\d\d\d\d#uis', 
	//	'<cite class="urn-lex">$0</cite>', $clean, -1, $n1);
	$n = 0; $clean = preg_replace(
		'#Artigo\s+[\d\.]+\sda\s+Lei\s+n?º?\s*\d[\d\.]+\d[/\s]+\d\d\d\d#uis', 
		'<cite class="urn-lex">$0</cite>', $clean,-1, $n
	);
	if (!$n) $clean = preg_replace(
		'#(?:Lei|Descreto\s+Lei|Decreto|Portaria)\s+n?º?\s*\d[\d\.]+\d[/\s]+\d\d(\d\d)?#uis', 
		'<cite class="urn-lex">$0</cite>', 
		$clean
	  );

	$clean = preg_replace('#Contrato\s+(?:N\.?º?\s*)?\d[\d\-\./]+(/\d\d\d\d)?#uis', '<cite class="urn-cntrt">$0</cite>', $clean);
	$clean = preg_replace(
	  '#Processo(?:\s+INSTRUTIVO|\s+administrativo)?\s*(?:N\.?º?\s*|:\s*)?\d[\d\-\./]+#uis', 
	  '<cite class="urn-gov-proc">$0</cite>',
	  $clean
	);
	$clean = preg_replace(
	  '#matr(?:\.|[ií]cula)\s+(?:N\.?º?\s*)?\d[\d\-\./]+#uis', 
	  '<cite class="urn-gov-profreg">$0</cite>', 
	  $clean
	); // ProfessionalService Registration ID


	// Restringindo (flag $n) estilo do documento para evitar vazamentos da regex:
	$n = 0;
	$clean = preg_replace(
		'#(?:\s*<[ubi]>\s*)(?:\s*<[ubi]>\s*)Valor(?:\s*</[ubi]>\s*)(?:\s*</[ubi]>\s*)[\s:]*(de\s)?R\$\s*\d[\d\,\.]+#uis', 
		'<data class="currencyValue">$0</data>',
		$clean,
		-1,
		$n
	);
	if (!$n)
		$clean = preg_replace('#(?:\s*<[ubi]>\s*)Valor(?:\s*</[ubi]>\s*)[\s:]*(de\s)?R\$\s*\d[\d\,\.]+#uis',
			'<data class="currencyValue">$0</data>', $clean,-1,$n);
	if (!$n)
		$clean = preg_replace('#Valor[\s:]*(de\s)?R\$\s*\d[\d\,\.]+#uis',
			'<data class="currencyValue">$0</data>', $clean,-1,$n);
	if (!$n)
		$clean = preg_replace('#R\$\s*\d[\d\,\.]+#uis', '<data class="currencyValue">$0</data>', $clean);

	$clean = preg_replace(
	  '#\d\d?\s+de\s+(?:janeiro|fevereiro|mar[çc]o|abril|maio|junho|julho|agosto|setembro|outubro|novembro|dezembro)\s+de\s+\d\d\d\d#uis', 
	  '<time class="date">$0</time>',
	  $clean
	);

	// sem efeito (e ainda não-testados)
	$clean = preg_replace('#(?:CPF[\s:\.]*)?\d\d\d\.\d\d\d\.\d\d\d\-\d\d\d#uis', '<data class="urn-person-vatID">$0</data>', $clean);
	$clean = preg_replace('#RG[\s:\.]*\d\d[\d\-\.]+#uis', '<data class="urn-person-id">$0</data>', $clean);
	$clean = preg_replace('#CEP[\s:\-\.]*\d\d\.\d\d\d\.\d\d\d/\d\d\d\d\-\d\d#uis', '<data class="urn-postalCode">$0</data>', $clean);

	return $clean;
}
