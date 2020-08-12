# busca-nfe-ba

Busca informações da NFE do estado da Bahia

## Dependência

* PHP 7.4+

## Instalação

``` bash
$ composer require guilhermejr/busca-nfe-ba
```

## Exemplo de uso via console

``` bash
$ vendor/bin/busca-nfe-ba array "http://nfe.sefaz.ba.gov.br/servicos/nfce/qrcode.aspx?p=29200835133777000109650010000001231796633581|2|1|1|B75D96ACC1EA7E3AAF163C0C0F01D547DCACF815"
```

## Exemplo de uso via código
```php
<?php

require 'vendor/autoload.php';

use BuscaNFEBa\Buscador;

$buscador = new Buscador("http://nfe.sefaz.ba.gov.br/servicos/nfce/qrcode.aspx?p=29200835133777000109650010000001231796633581|2|1|1|B75D96ACC1EA7E3AAF163C0C0F01D547DCACF815");
print_r($buscador->getJSON());
print_r($buscador->getArray());
```

## Contato
Dúvidas e Sugestões favor enviar e-mail para falecom@guilhermejr.net