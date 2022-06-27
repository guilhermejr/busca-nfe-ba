<?php

namespace BuscaNFEBa;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class Buscador
{

    private $url;
    private $crawlerPrincipal;
    private $crawlerAbas;
    private $crawlerEmitente;
    private $crawlerProduto;
    
    // --- Construtor ---------------------------------------------------------
    public function __construct(string $url)
    {

        // --- URL da NFE ---
        $this->url = $url;

        // --- Inicia browser ---
        $browser = new HttpBrowser(HttpClient::create());

        // --- Pega o html da NFE ---
        $this->crawlerPrincipal = $browser->request('GET', $url);

        // --- Clica em Visualizar em Abas ---
        $formPrincipal = $this->crawlerPrincipal->selectButton('Visualizar em Abas')->form();

        // --- Pega o html das abas ---
        $this->crawlerAbas = $browser->submit($formPrincipal);

        // --- Clica na aba Emitente ---
        $formEmitente = $this->crawlerAbas->selectButton('btn_aba_emitente')->form();

        // --- Pega o html da aba Emitente ---
        $this->crawlerEmitente = $browser->submit($formEmitente);

        // --- Clica na aba Produtos / Serviços ---
        $formProduto = $this->crawlerAbas->selectButton('btn_aba_produtos')->form();

        // --- Pega o html da aba Produtos / Serviços ---
        $this->crawlerProduto = $browser->submit($formProduto);

    }

    // --- Retorna em JSON ----------------------------------------------------
    public function getJSON(bool $cli = false) : string
    {
        if ($cli) {
            return json_encode($this->getArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode($this->getArray(), JSON_UNESCAPED_SLASHES);
        }
    }

    // --- Retorna em Array ---------------------------------------------------
    public function getArray() : array
    {

        $retorno = [];
        $retorno['url'] = $this->url;
        $retorno['dataCompra'] = $this->getDataCompra();
        $retorno['cnpj'] = $this->getCNPJLoja();
        $retorno['ie'] = $this->getIELoja();
        $retorno['nomeFantasia'] = $this->getNomeFantasia();
        $retorno['chaveDeAcesso'] = $this->getChaveDeAcesso();
        $retorno['informacoesComplementares'] = $this->getInformacoesComplementares();
        $retorno['produtos'] = $this->getProdutos();

        return $retorno;

    }

    private function getDataCompra() : string
    {
        return substr($this->crawlerAbas->filter('#NFe')->filter('.col-6')->eq(0)->filter('span')->eq(3)->html(), 0, -6);
    }

    private function getCNPJLoja() : string
    {
        return $this->crawlerAbas->filter('.fixo-nfe-cpf-cnpj')->filter('span')->html();
    }

    private function getIELoja() : string
    {
        return $this->crawlerAbas->filter('.fixo-nfe-iest')->filter('span')->html();
    }

    private function getNomeFantasia() : string 
    {
        return $this->crawlerEmitente->filter('#Emitente')->filter('.col-2')->eq(0)->filter('span')->eq(1)->html();
    }

    private function getChaveDeAcesso() : string
    {
        return $this->crawlerProduto->filter('#lbl_chave_acesso')->html();
    }

    private function getInformacoesComplementares() : string
    {
        if ($this->crawlerPrincipal->filter('li')->eq(4)->count()) {
            return $this->crawlerPrincipal->filter('li')->eq(4)->html();
        } else {
            return false;
        }
    }

    private function getProdutos() : array
    {
        $produtos = [];
        for ($i = 0; $i < $this->crawlerProduto->filter('.table_produtos')->count(); $i++) {
            $produtos[$i]['ean'] = $this->crawlerProduto->filter('.table_produtos')->eq($i)->filter('.col-3')->eq(0)->filter('span')->html();
            $produtos[$i]['nome'] = $this->crawlerProduto->filter('.table_produtos')->eq($i)->filter('.fixo-prod-serv-descricao')->filter('span')->html();
            $produtos[$i]['qtd'] = $this->crawlerProduto->filter('.table_produtos')->eq($i)->filter('.fixo-prod-serv-qtd')->filter('span')->html();
            $produtos[$i]['unidade'] = $this->crawlerProduto->filter('.table_produtos')->eq($i)->filter('.fixo-prod-serv-uc')->filter('span')->html();
            $produtos[$i]['valor'] = $this->crawlerProduto->filter('.table_produtos')->eq($i)->filter('.fixo-prod-serv-vb')->filter('span')->html();
        }

        return $produtos; 
    }

}