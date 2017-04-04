O **Projeto `queriDO`** nasceu como  **"Projeto Nosso Querido Diario Oficial"**, com os membros mais antigos da [curadoria1](reports/curadoria001.md), onde são narrados outros detalhes dessa história.

O projeto ainda "gratis" e inscipiente. Precisamos de voluntários.

## Primeiros desafios

Generalizar para "qualquer município" e abrir para a participação de outros voluntários, com outros enfoques, parecia simples, mas aos poucos foram sendo revelado o caráter também ambicioso do projeto. Os primeiros desafios identificados foram os seguintes:

1. Criar uma metadologia para obter matérias de um Diário Oficial (por hora do Rio de Janeiro e assemelhados), dentro de um determinado escopo.

2. Automatizar o que for possível da metodologia, garantindo fontes de matéria com máxima fidelidade (bater com o original publicado), em formato aberto (UTF-8 HTML) e bem estruturado (sem perda de informação estrutural tal como títulos, itens, seções, negritos, itálicos, tabelas, etc.).

3. Criar metodologia para compensar perdas de estrutura (ex. perda de seções, títulos, identificadores, etc.) ou informação (ex. caso de PDF convertido por OCR).

4. Criar convençes para armazenar aqui no *git* os originais fornecidos, e os textos processados para recuperação de estrutura.

5. Criar convençes para a marcação adicional: extração de endereços (ex. CEP e rua), CPF, CNPJ, RG, nomes de empresa, códigos de contrato, citações de leis, etc.

6. Automatizar o que for possível no processo de marcação adicional.

## Histórico e motivações do projeto

Conforme colocado pelos fundadores do QueriDO, Bruna, João e Henrique:

> O [acidente da ciclovia Tim Maia, obra da Concremat](http://brasil.elpais.com/brasil/2016/04/21/politica/1461256688_847248.html), fez com que um grupo de amigos passasse a noite tentando extrair do diário oficial da cidade do Rio informações sobre os contratos do município com a empreiteira. A tarefa hercúlea nos alertou para o quão valioso seria um script capaz de extrair sistematicamente informações do diário e torná-las verdadeiramente abertas. As possibilidades a partir daí seriam inúmeras. Em planilha, gráfico, verso e prosa.

> (...) Na tentativa de torná-lo aberto e reutilizável, não só no formato, mas na linguagem, iniciamos uma saga não só de programação, mas de idealização desse projeto (...)

Resumindo o histórico pela sua linha do tempo,

*  entre **agosto e outubro de 2016**: o projeto chamou a atenção do Govlab em NY,  onde foi realizado um *coaching*.  

* entre  **agosto e setembro de 2016**: contato dos fundadores com a OKBr, e sugestão de unificar os requisitos do *QueriDO* com o Diário Livre.

* entre **final de dezembro de 2016 e inicio de 2017**: com a chegada de novos voluntários o projeto foi "reanimado", surgiram as curadorias, e diversas partes do software e da metodologia foram repensadas, aproximando-se de sua versão atual.

* **março de 2017**: o projeto recebe seu primeiro desmembramento (fatoração)  para atingir metas mais ambiciosas proporcionar mais foco no desenvolvimento de cada módulo do software. Surgem como projetos OKBR, no Github/okfn-brasil, os projetos [TrazDia](https://github.com/okfn-brasil/trazdia) e [Poppler](https://github.com/okfn-brasil/poppler).  

* **abril de 2017**: as metas e demandas do projeto foram apresentadas para o Conselho da OKBR.

## *Roadmap*

...
