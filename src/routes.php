<?php
require_once '../src/clases/VerificadorMW.php';
require_once '../src/clases/usuarioApi.php';
require_once '../src/clases/compraApi.php';
require_once '../src/clases/registroApi.php';
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use clases\usuarioApi;
use clases\registroApi;
use clases\compraApi;
use clases\VerificadorMW;
use App\Models\registro;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/Usuario',function()
    {
        //ALTA(con token): recibe nombre, clave, sexo(masculino-femenino) y perfil(admin-user)
        //PUT Modificar los datos de un usuario, cambiando el sexo y el password.
        $this->put('/{id}',usuarioApi::class.':modificarUsuario')->add(VerificadorMW::class.':MWModificar')->add(VerificadorMW::class.':MWValidarIdExistenteGet');
        $this->post('/',usuarioApi::class.':Alta')->add(VerificadorMW::class.':VerificarAlta')->add(VerificadorMW::class.':VerificarCredenciales');
        $this->get('/',usuarioApi::class.':TraerTodos')->add(VerificadorMW::class.':VerificarCredenciales');
        $this->delete('/',usuarioApi::class.':BorrarUnUsuario')->add(VerificadorMW::class.':MWValidarIdExistenteNoGet');
    })->add(VerificadorMW::class.':VerificarJWT')->add(VerificadorMW::class.':RegistrarApi');
    
    $app->group('/Compra',function()
    {
        //$this->post('/',compraApi::class.':EfectuarCompra');
        $this->post('/',compraApi::class.':EfectuarCompraConImagen');//importante: deshabilitar el content-Type en el header del postman y usar "form-data" en el body.
        $this->get('/',compraApi::class.':VerCompras');
        $this->delete('/',compraApi::class.':BorrarUnaCompra')->add(VerificadorMW::class.':MWValidarIdExistenteNoGet')->add(VerificadorMW::class.':VerificarCredencialesBaja');
        $this->put('/{id}',compraApi::class.':modificarCompra')->add(VerificadorMW::class.':MWModificar');
    })->add(VerificadorMW::class.':VerificarJWT')->add(VerificadorMW::class.':RegistrarApi');

    //login con nombre,clave,sexo
    $app->group('/Login',function()
    {
        $this->post('/',usuarioApi::class.':Login');
    })->add(VerificadorMW::class.':VerificarLogin')->add(VerificadorMW::class.':RegistrarApi');
    
    $app->group('/Listado', function()
    {
        $this->get('/', compraApi::class.':ComprasToTable');
    });
    //})->add(VerificadorMW::class.':RegistrarApi'); //para verificar con token, sacarlo al mostrar en explorador


    //(GET) mostrar las ventas filtradas por el nombre del usuario
    $app->group('/Ventas', function()
    {
        $this->get('/', compraApi::class.':MostrarVentasFiltradas');
    });

    //mostrar las ventas filtradas por un parámetro llamado filtro, que puede ser el nombre del usuario, el nombre del artículo o el tipo de pago,traer todos los datos que coincidan con el dato en cualquiera de los criterios de búsqueda .
    $app->group('/Filtro', function()
    {
        $this->get('/', compraApi::class.':MostrarConFiltro');
    });

    //(GET)Mostrar los datos recopilados en el punto 6 filtrados por el método utilizado.
    $app->group('/Registro', function()
    {
        $this->get('/{metodo}',registroApi::class.':MostrarRegistros');

    })->add(VerificadorMW::class.':RegistrarApi');
    
};
