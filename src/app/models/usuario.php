<?php
namespace App\Models;

class usuario extends \Illuminate\Database\Eloquent\Model
{
    static public function subirFoto($archivos,$path,$compra)
    {
        $usuarios = usuario::orderBy('id', 'desc')->get()->first();
        $newId=$usuarios->id+1;
        $nombreFoto = ($archivos["foto"])->getClientFileName();
        $extension = explode(".",$nombreFoto);
        $extension = array_reverse($extension)[0];
        $titulo = ($newId.'_'.$compra->articulo.'.'.$extension);
        $path .= $titulo;
        $archivos["foto"]->moveTo($path);
        return $path;
    }
    
    function VerificarUsuarioExistente()
    {
        $usuarios = self::all();
        foreach($usuarios as $usuario)
        {
            if($usuario->nombre == $this->nombre && $usuario->clave == $this->clave)
            {
                return $usuario;
            }
        }
        return false;
    }   

    static function ValidarIdExistente($id)
    {
        $usuarios = usuario::all();
        foreach($usuarios as $usuario)
        {
            if($usuario->id == $id)
            {
                return true;
            }
        }
        return false;
    }
}
?>