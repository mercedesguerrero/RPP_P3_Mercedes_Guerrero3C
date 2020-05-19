<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();//estamos creando un servidor 

$app->setBasePath('/ProgramacionIII/Slim');

$app->get('/persona/{detalle}-{id}', function (Request $request, Response $response, array $args) {

    $queryString= $request->getQueryParams();

    //$headers= $request->getHeaders();
    $headers= $request->getHeader("token");

    $respuesta= array("success"=> true, 
                        "headers"=> $headers,
                        "data"=> $args, 
                        "query"=> $queryString);

    $rtaJson= json_encode($respuesta);

    $response->getBody()->write($rtaJson);

    return $response-> withHeader('Content-Type', 'application/json')
                        ->withStatus(302);
});

$app->get('/personas', function (Request $request, Response $response, array $args) {

    $queryString= $request->getQueryParams();

    //$headers= $request->getHeaders();
    $headers= $request->getHeader("token");

    $respuesta= array("success"=> true, 
                        "headers"=> $headers,
                        "data"=> $args, 
                        "query"=> $queryString);

    $rtaJson= json_encode($respuesta);

    $response->getBody()->write($rtaJson);

    return $response-> withHeader('Content-Type', 'application/json')
                        ->withStatus(302);
});

$app->post('/persona', function (Request $request, Response $response) {

    $body= $request->getParsedBody();//trae la info del body

    $files= $_FILES;//getUploadedFiles();//devuelve un array de files

    $respuesta= array("success"=> true, 
                        "data"=> "POST",
                        "body"=> $body,
                        "files"=> $files["files"]
                    );

    $rtaJson= json_encode($respuesta);

    $response->getBody()->write($rtaJson);

    return $response-> withHeader('Content-Type', 'application/json')
                        ->withStatus(302);
});

$app->put('/persona', function (Request $request, Response $response) {

});

$app->delete('/persona', function (Request $request, Response $response) {

});


$app->group('/alumno', function ($group) {

    $group->get('/{id}', function (Request $request, Response $response){

        $response->getBody()->write("alumno/{id}");

        return $response-> withHeader('Content-Type', 'application/json')
                        ->withStatus(302);
    });
});

$app->run();//se inicia el servidor



?>