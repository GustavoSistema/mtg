<?php

namespace App\Traits;

use App\Models\Certificacion;
use App\Models\Duplicado;
use App\Models\Expediente;
use App\Models\Imagen;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

//Trait para guardar los PDF.

trait pdfTrait
{
    //Funcion para guardar ficha tecnica
    public function guardarFichaTecnica(Certificacion $certi, Expediente $expe)
    {
        $data = $this->datosParaFichaTecnica($certi);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('fichaTecnicaGnv', $data);
        $archivo = $pdf->download('FT-' . $data['carro']->placa . '-' . $data['hoja']->numSerie . '.pdf')->getOriginalContent();
        //$nombre = $expe->placa . '-doc' . (rand()) . '-' . $expe->certificado;
        Storage::put('public/expedientes/' . 'FT-' . $data['carro']->placa . '-' . $data['hoja']->numSerie . '.pdf', $archivo);
        Imagen::create([
            'nombre' => 'FT-' . $data['carro']->placa . '-' . $data['hoja']->numSerie,
            'ruta' => 'public/expedientes/' . 'FT-' . $data['carro']->placa . '-' . $data['hoja']->numSerie . '.pdf',
            'extension' => 'pdf',
            'Expediente_idExpediente' => $expe->id,
        ]);        
    }


    //genera datos para el pdf de la ficha tecnica.
    public function datosParaFichaTecnica(Certificacion $certificacion)
    {
        $chip = $certificacion->vehiculo->Equipos->where("idTipoEquipo", 1)->first();
        $equipos = $certificacion->vehiculo->Equipos->where("idTipoEquipo", "!=", 1)->sortBy("idTipoEquipo");
        //dd($equipos); 
        $hoja = $certificacion->Materiales->where('idTipoMaterial', 1)->first();
        $fechaCert = $certificacion->created_at;
        $fec = $fechaCert->format("d/m/Y");
        $data = [
            "certificacion" => $certificacion,
            "fecha" => $fec,
            "empresa" => "MOTORGAS COMPANY S.A.",
            "carro" => $certificacion->Vehiculo,
            "taller" => $certificacion->Taller,
            "servicio" => $certificacion->Servicio,
            "hoja" => $hoja,
            "equipos" => $equipos,
            "chip" => $chip,
        ];

        return $data;
    }


    //Funcion para guardar certificado
    public function guardaCertificado(Certificacion $certi, Expediente $expe){
        switch ($certi->Servicio->tipoServicio->id) {
            case 1: //tipo servicio = inicial gnv
                $this->guardarPdfInicialGnv($certi,$expe);                
            break; 
            case 2://tipo servicio = anual gnv
               $this->guardarPdfAnualGnv($certi,$expe);
            break;  
            
            case 8://tipo servicio = duplicado gnv
                $dupli=Duplicado::find($this->attributes["idDuplicado"]);    
                if($dupli){
                   
                }else{
                    
                }                 
            break; 

            default:
                
                break;
        }
    }


    public function devuelveDatoParseado($num){
        $str=(string) $num;
        if(substr($num, -1) != 0){
            return rtrim($num);
        }else{
            return  bcdiv($num, '1', 2);
        }        
        
    }

     //Funcion para generar y guardar pdf anual de GNV
    public function guardarPdfAnualGnv(Certificacion $certificacion,Expediente $expe){        
            
                    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                    $fechaCert=$certificacion->created_at;
                    $fecha=$fechaCert->format('d').' días del mes de '.$meses[$fechaCert->format('m')-1].' del '.$fechaCert->format('Y').'.';  
                    $hoja=$certificacion->Materiales->where('idTipoMaterial',1)->first();              
                    $data=[
                    "fecha"=>$fecha,
                    "empresa"=>"MOTORGAS COMPANY S.A.",
                    "carro"=>$certificacion->Vehiculo,
                    "taller"=>$certificacion->Taller, 
                    "hoja"=>$hoja, 
                    "fechaCert"=>$fechaCert,
                    "largo"=>$this->devuelveDatoParseado($certificacion->Vehiculo->largo),
                    "ancho"=>$this->devuelveDatoParseado($certificacion->Vehiculo->ancho),
                    "altura"=>$this->devuelveDatoParseado($certificacion->Vehiculo->altura),
                    ];                 
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadView('anualGnv',$data);        
                    $archivo =  $pdf->download($certificacion->Vehiculo->placa.'-'.$hoja->numSerie.'-anual.pdf')->getOriginalContent();                    
                    Storage::put('public/expedientes/' . $certificacion->Vehiculo->placa.'-'.$hoja->numSerie.'-anual.pdf', $archivo);
                    Imagen::create([
                        'nombre' => $certificacion->Vehiculo->placa.'-'.$hoja->numSerie,
                        'ruta' => 'public/expedientes/' . $certificacion->Vehiculo->placa.'-'.$hoja->numSerie.'-anual.pdf',
                        'extension' => 'pdf',
                        'Expediente_idExpediente' => $expe->id,
                    ]);
    }


    //Funcion para generar y guardar pdf inicial de GNV
    public function guardarPdfInicialGnv(Certificacion $certificacion,Expediente $expe){        
           
                    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                    $fechaCert=$certificacion->created_at;
                    $fecha=$fechaCert->format('d').' días del mes de '.$meses[$fechaCert->format('m')-1].' del '.$fechaCert->format('Y').'.';              
                    $chip=$certificacion->vehiculo->Equipos->where("idTipoEquipo",1)->first();           
                    $equipos=$certificacion->vehiculo->Equipos->where("idTipoEquipo","!=",1)->sortBy("idTipoEquipo");                    
                    $hoja=$certificacion->Materiales->where('idTipoMaterial',1)->first();
                    $data=[
                    "fecha"=>$fecha,
                    "empresa"=>"MOTORGAS COMPANY S.A.",
                    "carro"=>$certificacion->Vehiculo,
                    "taller"=>$certificacion->Taller, 
                    "hoja"=>$hoja, 
                    "equipos"=>$equipos,
                    "chip"=>$chip,
                    "fechaCert"=>$fechaCert,
                    "pesos"=>$certificacion->calculaPesos,
                    "largo"=>$this->devuelveDatoParseado($certificacion->Vehiculo->largo),
                    "ancho"=>$this->devuelveDatoParseado($certificacion->Vehiculo->ancho),
                    "altura"=>$this->devuelveDatoParseado($certificacion->Vehiculo->altura),
                    ];                 
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadView('conversionGnv',$data);       
                    $archivo= $pdf->download($certificacion->Vehiculo->placa.'-'.$hoja->numSerie.'-inicial.pdf')->getOriginalContent();                    
                    Storage::put('public/expedientes/' . $certificacion->Vehiculo->placa.'-'.$hoja->numSerie.'-inicial.pdf', $archivo);
                    Imagen::create([
                        'nombre' => $certificacion->Vehiculo->placa.'-'.$hoja->numSerie,
                        'ruta' => 'public/expedientes/' . $certificacion->Vehiculo->placa.'-'.$hoja->numSerie.'-inicial.pdf',
                        'extension' => 'pdf',
                        'Expediente_idExpediente' => $expe->id,
                    ]);
    }
       
    
}
