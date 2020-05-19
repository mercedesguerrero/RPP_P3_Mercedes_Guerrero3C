<?php
namespace clases;
use Slim\Http\Request;
use Slim\Http\Response;
use clases\VerificadorJWT;
use App\Models\ejemploModelo;
use App\Models\registro;
require_once '../src/clases/VerificadorJWT.php';

class registroApi
{
    public function MostrarRegistros(Request $request,Response $response,$args)
    {
        return (registro::where('metodo',$args['metodo'])->get())->toJson();
    }
}
?>