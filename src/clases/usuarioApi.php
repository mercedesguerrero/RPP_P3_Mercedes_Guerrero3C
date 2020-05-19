<?php
namespace clases;
use App\Models\usuario;
use App\Models\compra;
use Slim\Http\Request;
use Slim\Http\Response;
use clases\VerificadorJWT;
require_once '../src/clases/VerificadorJWT.php';
class usuarioApi
{
    public function Login(Request $request, Response $response, array $args)
    {
        $usuario = $request->getAttribute('usuario');
        return $response->getBody()->write(VerificadorJWT::crearToken(["id"=>$usuario->id,"nombre"=>$usuario->nombre,"sexo"=>$usuario->sexo,"perfil"=>$usuario->perfil]));
    }

    public function Alta(Request $request, Response $response, array $args)
    {
        $usuario = $request->getAttribute('usuario');
        $usuario->save();
        $usuario->foto = usuario::subirFoto($request->getUploadedFiles(),'../public/IMGUsuarios/',$usuario);
        return $response->getBody()->write("\nUsuario dado de alta con exito");
    }

    public function TraerTodos(Request $request, Response $response, array $args)
    {
        return (usuario::all())->toJson();
    }

    static function modificarUsuario(Request $request, Response $response, $args)
    {//Modificar los datos de un usuario, cambiando el sexo y el password.
        $id = $request->getAttribute('id');
        $switch = $request->getAttribute('switch');
        $atributos = $request->getParsedBody();
        $usuario = usuario::find($id);
        switch($switch)
        {
            case 1:
            $usuario->sexo = $atributos["sexo"];
            $usuario->clave = $atributos["clave"];
            
            $usuario->save();
            //$usuario->foto = usuario::subirFoto($request->getUploadedFiles(),'../files/fotos/',$usuario);
            break;

            case 2:
            $usuario->sexo = $atributos["sexo"];
            $usuario->clave = $atributos["clave"];
            $usuario->save();
            break;
        }
        $usuario->save();
        return $response->getBody()->write("<br>Usuario modificado con exito");
    }

    function BorrarUnUsuario(Request $request, Response $response, $args)
    {
        $id_usuario=($request->getAttribute('id'));
        usuario::destroy($request->getAttribute('id'));
        compra::where('id_usuario', $id_usuario)->delete();
        $response->getBody()->write("Usuario y ventas eliminados exitosamente");
        return $response;
    }
}
?>