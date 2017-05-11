
Links principais do **Projeto QueriDO**:

* [site da **Documentação**](https://okfn-brasil.github.io/queriDO/site/).

* [site para Demo e testes](https://okfn-brasil.github.io/queriDO/).

* [git (repositório do projeto)](https://github.com/okfn-brasil/queriDO).

* [discussões ténicas e definição de tarefas (*issues*)](https://github.com/okfn-brasil/queriDO/issues).

* [discussões gerais (DiscussOKBR)](https://discuss.okfn.org/c/local-groups/okbr).

## Documentação

* [**Metodologia** (index)](index.md): breve explicação de como funciona,  e procedimentos a seguir para colaborar.

* [**Histórico**](historico.md): histórico, motivações e evolução do projeto.

* [**Curadorias**](curadorias.md) (ver também menu): grupos de interesse definidos pelos seus alvos. Seu conteúdo está na forma de relatórios, sob a pasta `/reports`, ou ainda, em alguns casos, na forma de datasets, sob a pasta `/data`.

* **Relatórios** (ver menu): resultados dos trabalhos realizados por cada curadoria. Estão sob a pasta `/reports`. 

* **Software**: descrição da arquitetura e das decisões de projeto relativas ao software e sua infraestrutura.

Na pasta [**/reports**](reports) pode ser encontrado cada um dos "estatutos" das curadorias, e a cada subpasta um conjunto de relatórios da respectiva curadoria. Por exemplo a *Curadoria-1* é apresentada em [reports/curadoria001.md](reports/curadoria001.md) e tem seus relatórios em [reports/curadoria001](reports/curadoria001) (por exemplo o *Relatório-1.1* em [reports/curadoria001/report01.md](reports/curadoria001/report01.md)).

## Geração do site de documentação

Apesar da visualização pelos arquivos  `*.md`  ser razoável, é dada preferência ao uso do site de documentação, que simplesmente apresenta os mesmos arquivos `*.md` em um template mais limpo e navegável. O site é gerado pelo comando `mkdocs build`, exatamente da mesma maneira que os [*miniguias* da OKBr](https://github.com/okfn-brasil/miniguias).

