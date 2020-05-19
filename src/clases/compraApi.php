<?php
namespace clases;
use App\Models\compra;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use clases\VerificadorJWT;
use App\Models\usuario;
use App\Models\registro;

require_once '../src/clases/VerificadorJWT.php';
require_once '../src/app/models/compra.php';
class compraApi
{
    
    public static function EfectuarCompra(Request $request, Response $response, array $args)
    {
        $token = $request->getHeader('token')[0];
        $payload = VerificadorJWT::TraerData($token);
        $atributos = $request->getParsedBody();
        $compra = new compra();
        $compra->articulo = $atributos["articulo"];
        $compra->fecha = date('Y-m-d H:i:s');
        $compra->precio = $atributos["precio"];
        $compra->id_usuario=$payload->id;
        if ($atributos["tipo"] == "mercadopago" || $atributos["tipo"] == "efectivo" || $atributos["tipo"] == "tarjeta") {
            $compra->tipopago = $atributos["tipo"];
            $compra->save();
            $response->getBody()->write("\nProducto comprado con exito");
        } else {
            $response->getBody()->write("\nDebe elegir un medio de pago correcto");
        }
        return $response;
    }

    public static function EfectuarCompraConImagen(Request $request, Response $response, array $args)
    {
        $token = $request->getHeader('token')[0];
        $payload = VerificadorJWT::TraerData($token);
        $atributos = $request->getParsedBody();
        $compra = new compra();
        $compra->articulo= $atributos["articulo"];
        $compra->fecha = date('Y-m-d H:i:s');
        $compra->precio = $atributos["precio"];
        $compra->id_usuario=$payload->id;
        if ($atributos["tipo"] == "mercadopago" || $atributos["tipo"] == "efectivo" || $atributos["tipo"] == "tarjeta") {
            $compra->tipopago = $atributos["tipo"];
            $rutaFoto=compra::subirFotoConMarcaDeAgua($request->getUploadedFiles(),'../public/IMGCompras/',$compra);
            $rutaDirectorio=(explode('/',$rutaFoto,3));
            $compra->foto= '../'.$rutaDirectorio[2];
            $compra->save();
            $response->getBody()->write("<br>Compra realizada con exito y con foto subida");
        } else {
            $response->getBody()->write("\nDebe elegir un medio de pago correcto");
        }
        return $response;
    }

    public static function VerCompras(Request $request, Response $response, array $args)
    {
        $token = $request->getHeader('token')[0];
        $payload = VerificadorJWT::TraerData($token);
        if($payload->perfil=='user')
        {
            $response->getBody()->write(compra::where("id_usuario","=",$payload->id)->get());
        }
        else
        {
            $response->getBody()->write(compra::all()->toJson());
        }
        return $response;
    }

    public static function ComprasToTable(Request $request, Response $response, array $args)
    {
        $compras=compra::all();
        $texto = "<table border='1' align='center'>";
        $texto .= "<thead bgcolor='lightgrey'>";
        $texto .= "<tr>";
        $texto .= "<th>Id</th>";
        $texto .= "<th>Artículo</th>";
        $texto .= "<th>Fecha</th>";
        $texto .= "<th>Precio</th>";
        $texto .= "<th>Forma de pago</th>";
        $texto .= "<th>Imagen</th>";
        $texto .= "</tr>";
        $texto .= "</thead>";
        $texto .= "<tbody>";
        foreach ($compras as $compra)
        {
            $texto .= "<tr>";
            $texto .= "<td>".$compra->id."</td>";
            $texto .= "<td>".$compra->articulo."</td>";
            $texto .= "<td>".$compra->fecha."</td>";
            $texto .= "<td>".$compra->precio."</td>";
            $texto .= "<td>".$compra->tipopago."</td>";
            $texto .= "<td><img src='".$compra->foto. "'height='120' width='120' /></td>";
            $texto .= "</tr>";
        }
        $texto .= "</tbody>";
        $texto .= "</table>";
        $response->getBody()->write($texto);
        return $response;
    }

    function BorrarUnaCompra(Request $request, Response $response, $args)
    {
        compra::destroy($request->getAttribute('id'));
        $response->getBody()->write("Compra eliminada exitosamente");
        return $response;
    }

    public function MostrarVentasFiltradas(Request $request,Response $response,$args)
    {
        $nombre = $request->getParam('nombre');
        $usuarios = usuario::where('nombre',$nombre)->get();
        $newId="";
        foreach($usuarios as $usuario)
        {
            $newId=$usuario->id;
        }
     
    return $response->getBody()->write(json_encode(compra::where('id_usuario',$newId)->get()));
    }

    public function MostrarConFiltroAll(Request $request,Response $response,$args)
    {
        $filtro = $request->getParam('filtro');
        $ocurrencias[]=null;
        $data[]=null;

        $compraName=(compra::where('articulo',$filtro)->get());
        $compraPrecio=(compra::where('precio',$filtro)->get());
        $compraPago=(compra::where('tipopago',$filtro)->get());
        $userName=(usuario::where('nombre',$filtro)->get());
        $userSexo=(usuario::where('sexo',$filtro)->get());
        $userPerfil=(usuario::where('perfil',$filtro)->get());

        array_push($ocurrencias,$compraName,$compraPrecio,$compraPago,$userName,$userSexo,$userPerfil);
        
        foreach($ocurrencias as $ocurrencia)
        {
            if (isset($ocurrencia)==true && json_decode($ocurrencia, true))
            {
                    array_push($data, $ocurrencia);
            }
        }
        unset($data[0]);
        return $response->getBody()->write(json_encode($data));
    }

    public function MostrarConFiltro(Request $request,Response $response,$args)
    {
        $filtro = $request->getParam('filtro');
        $ocurrencias[]=null;
        $data[]=null;

        $compraName=(compra::where('articulo',$filtro)->get());
        $compraPago=(compra::where('tipopago',$filtro)->get());
        $userName=(usuario::where('nombre',$filtro)->get());
        
        if(isset($userName[0]->id)==true){
            $idUser=$userName[0]->id;
        }
        
        if(isset($idUser)==true){
            $compraUsuario=(compra::where('id_usuario',$idUser)->get());
            array_push($ocurrencias,$compraUsuario);
        }

        array_push($ocurrencias,$compraName,$compraPago);
        
        foreach($ocurrencias as $ocurrencia)
        {
            if (isset($ocurrencia)==true && json_decode($ocurrencia, true))
            {
                    array_push($data, $ocurrencia);
            }
        }
        unset($data[0]);
        return $response->getBody()->write(json_encode($data));
    }

    static function modificarCompra(Request $request, Response $response, $args)
    {//modificar el artículo y el precio de la compra.
        $id = $request->getAttribute('id');
        $switch = $request->getAttribute('switch');
        $atributos = $request->getParsedBody();
        $compra = compra::find($id);
        switch($switch)
        {
            case 1:
            $compra->articulo = $atributos["articulo"];
            $compra->precio = $atributos["precio"];
            
            $compra->save();
            //$usuario->foto = usuario::subirFoto($request->getUploadedFiles(),'../files/fotos/',$usuario);
            break;

            case 2:
            $compra->articulo = $atributos["articulo"];
            $compra->precio = $atributos["precio"];
            $compra->save();
            break;
        }
        $compra->save();
        return $response->getBody()->write("<br>compra modificado con exito");
    }

}
?>
