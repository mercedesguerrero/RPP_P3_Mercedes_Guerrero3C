<?php
namespace App\Models;

class compra extends \Illuminate\Database\Eloquent\Model
{

    static public function subirFoto($archivos,$path,$compra)
    {
        $compras = compra::orderBy('id', 'desc')->get()->first();
        $newId=$compras->id+1;
        $nombreFoto = ($archivos["foto"])->getClientFileName();
        $extension = explode(".",$nombreFoto);
        $extension = array_reverse($extension)[0];
        $titulo = ($newId.'_'.$compra->articulo.'.'.$extension);
        $path .= $titulo;
        $archivos["foto"]->moveTo($path);
        return $path;
    }

    static public function subirFotoConMarcaDeAgua($archivos,$path,$compra)
    {
        $compras = compra::orderBy('id', 'desc')->get()->first();
        $newId=$compras->id+1;
        $nombreFoto = ($archivos["foto"])->getClientFileName();
        $extension = explode(".",$nombreFoto);
        $extension = array_reverse($extension)[0];
        $titulo = ($newId.'_'.$compra->articulo.'.'.$extension);
        $path .= $titulo;
        $archivos["foto"]->moveTo($path);
        compra::hacerMarca($path, '../src/recursos/marca_de_agua.png');//acepta como válidas imagenes .png en el $path
        return $path;
    }

    public static function hacerMarca ($fotoUno, $marcaDeAgua)
    {
        $im = imagecreatefrompng($fotoUno);
        $estampa = imagecreatefrompng( $marcaDeAgua);
        // Establecer los márgenes para la estampa y obtener el alto/ancho de la imagen de la estampa
        $margen_dcho = 10;
        $margen_inf = 10;
        $sx = imagesx($estampa);
        $sy = imagesy($estampa);
        // Copiar la imagen de la estampa sobre nuestra foto usando los índices de márgen y el
        // ancho de la foto para calcular la posición de la estampa.
        imagecopy($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa));
        // Imprimir y liberar memoria
        //header('Content-type: image/png');
        imagepng($im , $fotoUno);
    }

    public static function ValidarIdExistente($id)
    {
        $compras = compra::all();
        foreach($compras as $compra)
        {
            if($compra->id == $id)
            {
                return true;
            }
        }
        return false;
    }

    public static function ValidarIdUsuarioExistente($id_usuario)
    {
        $compras = compra::all();
        foreach($compras as $compra)
        {
            if($compra->id_usuario == $id_usuario)
            {
                return true;
            }
        }
        return false;
    }

    static function ConsultaPorUsuario($criterio)
    {
        $consulta = [];
        $consulta["articulo"] = (usuario::where('articulo',$criterio)->get());
        $consulta["precio"] = (usuario::where('precio',$criterio)->get());
        $consulta["tipopago"] = (usuario::where('tipopago',$criterio)->get());
        return $consulta;
    }
}
?>