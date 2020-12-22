<?php

namespace App\Services;

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Factories\Protocolo;
use stdClass;

class NFeService {
    
    private $config;
    private $tools;

    public function __construct($config) {
        $this->config = $config;
        $certificadoDigital = file_get_contents('C:\Users\Leticia\Downloads\test-certs\Wayne Enterprises, Inc.pfx');
        $this->tools = new Tools(json_encode($config), 
                                Certificate::readPfx($certificadoDigital, '1234'));
    }
    
    public function gerarNFe() {

        // Criar uma nota vazia
        $nfe = new Make();

        /** Inf NF-e */
        $stdInfNFe = new stdClass();
        $stdInfNFe->versao = '4.00'; //versão do layout (string)
        //$stdInfNFe->Id = 'NFe35150271780456000160550010000000021800700082'; //se o Id de 44 digitos não for passado será gerado automaticamente
        $stdInfNFe->pk_nItem = null; //deixe essa variavel sempre como NULL

        $infNFe = $nfe->taginfNFe($stdInfNFe);

        /** IDE  */
        $stdIde = new stdClass();
        $stdIde->cUF = 26;
        $stdIde->cNF = rand(11111111, 99999999);
        $stdIde->natOp = 'REVENDA DE MERCADORIAS SIMPLES NACIONAL ';

        //$std->indPag = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00

        $stdIde->mod = 55;
        $stdIde->serie = 1;
        $stdIde->nNF = 293;
        $stdIde->dhEmi = date("Y-m-d\TH:i:sP");
        $stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
        $stdIde->tpNF = 1; // Entrada ou saída
        $stdIde->idDest = 1; // Dentro ou fora do estado 
        $stdIde->cMunFG = 2611606 ;
        $stdIde->tpImp = 1; // Tipo de impressão: 1 - retrato; 2 - paisagem;
        $stdIde->tpEmis = 1; // Contingência ou normal
        $stdIde->cDV = 2; // Dígito verificador
        $stdIde->tpAmb = 2; // Homologação
        $stdIde->finNFe = 1; // 1- NF-e normal; 2 - NNF-e complementar; 3 - NF-e de ajuste
        $stdIde->indFinal = 1;
        $stdIde->indPres = 0;
        $stdIde->procEmi = 0; 
        $stdIde->verProc = '7.4.0'; // Versão do seu sistema
        $stdIde->dhCont = null; // data e hora da entrada em contingência
        $stdIde->xJust = null; // Sefaz local não responde

        $tagide = $nfe->tagide($stdIde);

        /** Emitente */
        $stdEmit = new stdClass();
        $stdEmit->xNome = "Wayne Enterprises, Inc";
        $stdEmit->xFant = "EXTRA CARNE";
        $stdEmit->IE = "033927243";
        $stdEmit->CRT = "3";
        $stdEmit->CNPJ = "34785515000166"; //indicar apenas um CNPJ ou CPF

        $tagEmit = $nfe->tagemit($stdEmit);

        /** Endereço do Emitente */
        $stdEnderEmit = new stdClass();
        $stdEnderEmit->xLgr = "EXTRA CARNE";
        $stdEnderEmit->nro = "67";
        $stdEnderEmit->xCpl = "";
        $stdEnderEmit->xBairro ="San Martin";
        $stdEnderEmit->cMun = "2611606";
        $stdEnderEmit->xMun = "Recife";
        $stdEnderEmit->UF = "PE"; 
        $stdEnderEmit->CEP = "50760580";
        $stdEnderEmit->cPais ="1058";
        $stdEnderEmit->xPais = "BRASIL";
        $stdEnderEmit->fone = "4121098000";

        $tagEnderEmmit = $nfe->tagenderEmit($stdEnderEmit);

        /** DESTINATÁRIO */
        $stdDest = new stdClass();
        $stdDest->xNome = "ACOMOS TECNOLOGIA";
        $stdDest->indIEDest = "9"; 
        $stdDest->ISUF = "";
        $stdDest->IM = "";
        $stdDest->email = "teste@gmail.com";
        $stdDest->CNPJ = "23519460000126"; //indicar apenas um CNPJ ou CPF ou idEstrangeiro

        $tagDest = $nfe->tagdest($stdDest);

        /** ENDEREÇO DE DESTINO */
        $stdEnderDest = new stdClass();
        $stdEnderDest->xLgr = "PROF ALGACYR MUNHOZ MADER";
        $stdEnderDest->nro = "2800";
        $stdEnderDest->xCpl = "";
        $stdEnderDest->xBairro = "CIC";
        $stdEnderDest->cMun = "2611606";
        $stdEnderDest->xMun = "Recife";
        $stdEnderDest->UF = "PE";
        $stdEnderDest->CEP = "51010040";
        $stdEnderDest->cPais = "1058";
        $stdEnderDest->xPais = "BRASIL";
        $stdEnderDest->fone = "4121098000";

        $tagEnderDest = $nfe->tagenderDest($stdEnderDest);

        /** PRODUTOS */

        //foreach -> vários produtos

        $stdProd = new stdClass();
        $stdProd->item = 1; //item da NFe
        $stdProd->cProd = "4450";
        $stdProd->cEAN = "7897534826649";
        $stdProd->xProd = "LIMPA TELAS 120ML";
        $stdProd->NCM = "44170010";

        $stdProd->cBenef = ""; //incluido no layout 4.00

        $stdProd->CFOP = "5102";
        $stdProd->uCom = "UN"; // compra
        $stdProd->qCom = "10";
        $stdProd->vUnCom = $this->format(6.99);
        $stdProd->cEANTrib = "7897534826649";
        $stdProd->uTrib = "UN"; // venda
        $stdProd->qTrib = "10";
        $stdProd->vUnTrib = $this->format(6.99);
        $stdProd->vProd = $this->format($stdProd->qTrib * $stdProd->vUnTrib);
        $stdProd->vFrete = "";
        $stdProd->vSeg = "";
        $stdProd->vDesc = "";
        $stdProd->vOutro = "";
        $stdProd->indTot = "1"; // se o valor vai compor o valor total da nota
        //$stdProd->xPed = "";
        //$stdProd->nItemPed = "";
        //$stdProd->nFCI = "";

        $tagProd = $nfe->tagprod($stdProd);

        /** INFORMAÇÃO ADICIONAL DO PRODUTO */
        $stdInfAdProd = new stdClass();
        $stdInfAdProd->item = 1; //item da NFe

        $stdInfAdProd->infAdProd = 'informacao adicional do item';

        $tagInfAdProd = $nfe->taginfAdProd($stdInfAdProd);

        /** IMPOSTO */
        $stdImposto = new stdClass();
        $stdImposto->item = 1; //item da NFe
        $stdImposto->vTotTrib = 4.00;

        $tagImposto = $nfe->tagimposto($stdImposto);

        /** ICMS */
        $stdIcms = new stdClass();
        $stdIcms->item = 1; //item da NFe
        $stdIcms->orig = 0;
        $stdIcms->CST = "00";
        $stdIcms->modBC = "0";
        $stdIcms->vBC = $this->format($stdProd->vProd);
        $stdIcms->pICMS = 18.00;
        $stdIcms->vICMS = $this->format($stdIcms->vBC * ($stdIcms->pICMS / 100));
        /*$stdIcms->pFCP;
        $stdIcms->vFCP;
        $stdIcms->vBCFCP;
        $stdIcms->modBCST;
        $stdIcms->pMVAST;
        $stdIcms->pRedBCST;
        $stdIcms->vBCST;
        $stdIcms->pICMSST;
        $stdIcms->vICMSST;
        $stdIcms->vBCFCPST;
        $stdIcms->pFCPST;
        $stdIcms->vFCPST;
        $stdIcms->vICMSDeson;
        $stdIcms->motDesICMS;
        $stdIcms->pRedBC;
        $stdIcms->vICMSOp;
        $stdIcms->pDif;
        $stdIcms->vICMSDif;
        $stdIcms->vBCSTRet;
        $stdIcms->pST;
        $stdIcms->vICMSSTRet;
        $stdIcms->vBCFCPSTRet;
        $stdIcms->pFCPSTRet;
        $stdIcms->vFCPSTRet;
        $stdIcms->pRedBCEfet;
        $stdIcms->vBCEfet;
        $stdIcms->pICMSEfet;
        $stdIcms->vICMSEfet;
        $stdIcms->vICMSSubstituto; //NT2018.005_1.10_Fevereiro de 2019*/

        $tagIcms = $nfe->tagICMS($stdIcms);

        /** PIS */

        $stdPis = new stdClass();
        $stdPis->item = 1; //item da NFe
        $stdPis->CST = '50';
        $stdPis->vBC = $this->format($stdProd->vProd);
        $stdPis->pPIS = 1.65;
        $stdPis->vPIS = $this->format($stdPis->vBC * ($stdPis->pPIS / 100));

        $tagPis = $nfe->tagPIS($stdPis);

        /** COFINS */
        $stdCofins = new stdClass();
        $stdCofins->item = 1; //item da NFe
        $stdCofins->CST = '50';
        $stdCofins->vBC = $this->format($stdProd->vProd);
        $stdCofins->pCOFINS = 0.65;
        $stdCofins->vCOFINS = $this->format($stdCofins->vBC * ($stdCofins->pCOFINS / 100));

        $tagCofins = $nfe->tagCOFINS($stdCofins);

        /** ICMS Total */
        $stdIcmsTot = new stdClass();
        $stdIcmsTot->vBC = $this->format($stdProd->vProd);
        $stdIcms->vICMS = $this->format($stdIcms->vBC * ($stdIcms->pICMS / 100));
        $stdIcmsTot->vICMSDeson = "";
        $stdIcmsTot->vFCP = ""; //incluso no layout 4.00
        $stdIcmsTot->vBCST = "";
        $stdIcmsTot->vST = "";
        $stdIcmsTot->vFCPST = ""; //incluso no layout 4.00
        $stdIcmsTot->vFCPSTRet = ""; //incluso no layout 4.00
        $stdIcmsTot->vProd = "";
        $stdIcmsTot->vFrete = "";
        $stdIcmsTot->vSeg = "";
        $stdIcmsTot->vDesc = "";
        $stdIcmsTot->vII = "";
        $stdIcmsTot->vIPI = "";
        $stdIcmsTot->vIPIDevol = ""; //incluso no layout 4.00
        $stdIcmsTot->vPIS = "";
        $stdIcmsTot->vCOFINS = "";
        $stdIcmsTot->vOutro = "";
        $stdIcmsTot->vNF = "";
        $stdIcmsTot->vTotTrib = "";

        $tagIcmsTot = $nfe->tagICMSTot($stdIcmsTot);

        /** TRANSPORTADORA  */
        $stdTransp = new stdClass();
        $stdTransp->modFrete = 1;

        $tagTransp = $nfe->tagtransp($stdTransp);

        /** VOLUMES */
        $stdVol = new stdClass();
        $stdVol->item = 1; //indicativo do numero do volume
        $stdVol->qVol = 1;
        $stdVol->esp = 'CAIXAS';

        $tagVol = $nfe->tagvol($stdVol);

        /** PAGAMENTO */
        $stdPag = new stdClass();
        $stdPag->vTroco = 0.00; //incluso no layout 4.00, obrigatório informar para NFCe (65)

        $tagPag = $nfe->tagpag($stdPag);

        /** DETALHE DO PAGAMENTO */
        $stdDetPag = new stdClass();
        $stdDetPag->tPag = '14';
        $stdDetPag->vPag = $stdProd->vProd; //Obs: deve ser informado o valor pago pelo cliente
        //$stdDetPag->indPag = '0'; //0= Pagamento à Vista 1= Pagamento à Prazo

        $tagDetPag = $nfe->tagdetPag($stdDetPag);

        /** INFORMAÇÃO ADICIONAL */
        $stdInfAdic = new stdClass();
        $stdInfAdic->infAdFisco = 'informacoes para o fisco';
        $stdInfAdic->infCpl = 'informacoes complementares';

        $tagInfAdic = $nfe->taginfAdic($stdInfAdic);
        // Monta a nota
        if($nfe->montaNFe()) {
            return $nfe->getXML();
        } else {
            dd($nfe->getErrors());
        }
    }

    /** 
     * Assina a nota fiscal
     */
    public function sign($xml) {
        return $this->tools->signNFe($xml);
    }

    /**
     * Transmite a nota para sefaz
     */
    public function transmitir($signed_xml) {
        $resp = $this->tools->sefazEnviaLote([$signed_xml], 1, 1);

        dd($resp);

        $st = new Standardize();
        $std = $st->toStd($resp);

        try {
            $protocol = new Protocol();
            $xmlProtocolado = $protocol->add($signed_xml, $resp);
        } catch (Exception $e) {
            // Aqui tratamos as possíveis exceções ao adicionar o protocolo
            exit($e->getMessage());
        }

        file_put_contents('nota.xml', $xmlProtocolado);

        return $xmlProtocolado;
    }


    public function format($number, $dec = 2) {
        return number_format((float) $number, $dec, ".", "");
    }

}