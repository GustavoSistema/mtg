<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\Imagen;

use File;
use Zip;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ZipController extends Controller
{
    public function descargaFotosExpediente($id){        
        $expediente=Expediente::findOrFail($id);
        $fotos=[];
        $fileName = $expediente->placa.'.zip';        
        $imagenes=Imagen::where('Expediente_idExpediente','=',$expediente->id)->whereIn('extension',['jpg','jpeg','png','gif','tif','tiff','bmp'])->get();

        $zip=Zip::create($fileName);
        foreach($imagenes as $key=>$imagen){            
            $ruta=storage_path("app/".$imagen->ruta);
            $nombre=$imagen->nombre.'.'.$imagen->extension;
                $zip->add($ruta,$expediente->placa."/".$nombre);
        }
        return $zip;   	
    }

    public function descargaFotosAprobados(){
        
    }



}
