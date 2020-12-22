<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NFeService;

class NFeControlller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nfeService = new NFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => 2,
            "razaosocial" => "S L COMERCIO DE CARNES LTDA - ME",
            "cnpj" => "08080317000197",
            "siglaUF" => "PE",
            "schemes" => "PL_009_V4",
            "versao" => "4.00"
        ]);
       
        //header("Content-type: text/xml; charset=UTF-8;");
        
        // Gera o XML
        $xml = $nfeService->gerarNFe();

        // Assinar
        $signed_xml = $nfeService->sign($xml);
        
        // Transmitir 
        $resultado = $nfeService->transmitir($signed_xml);

        return $signed_xml;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
