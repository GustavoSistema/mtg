<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PdfController;
use App\Jobs\guardarArchivosEnExpediente;
use App\Models\CertifiacionExpediente;
use App\Models\Certificacion;
use App\Models\Duplicado;
use App\Models\Expediente;
use App\Models\Imagen;
use App\Models\Material;
use App\Models\Servicio;
use App\Models\ServicioMaterial;
use App\Models\Taller;
use App\Models\vehiculo;
use App\Traits\pdfTrait;
use Illuminate\Queue\Listener;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Prueba extends Component
{
    use pdfTrait;
    use WithFileUploads;

    //VARIABLES DEL SERVICIO
    public $talleres, $servicios, $taller, $servicio, $tipoServicio, $numSugerido,
    $estado, $busquedaCert, $placa, $certificaciones, $fechaCerti, $certificado, $chip;


    public $externo = false;

    public $imagenes=[];

    public $servicioExterno, $tallerExterno, $fechaExterno;

    public Certificacion $certificacion, $duplicado;

    //variables del certi
    public $vehiculo;

    protected $rules=["placa"=>"required|min:3|max:7"];

    public function mount()
    {
        $this->talleres = Taller::all()->sortBy('nombre');
        $this->estado = "esperando";
        //$this->certificacion=new Certificacion();
    }

    protected $listeners = ['cargaVehiculo' => 'carga', "refrescaVehiculo" => "refrescaVe"];

    public function render()
    {
        return view('livewire.prueba');
    }


    public function updatedExterno()
    {
        if ($this->certificado) {
            $this->certificado = null;
        }
        $this->reset(["tallerExterno","fechaExterno","servicioExterno"]);
    }

    public function carga($id)
    {
        $this->vehiculo = vehiculo::find($id);
    }

    public function updatedTaller($val)
    {
        if ($val) {
            $this->servicios = Servicio::where("taller_idtaller", $val)->get();
            $this->servicio = "";
        } else {
            $this->reset(["servicios", "servicio"]);
        }
    }


    public function updatedServicio($val){
        if ($val) {
            $this->tipoServicio = Servicio::find($val)->tipoServicio;
            $this->sugeridoSegunTipo($this->tipoServicio->id);
           if($this->tipoServicio->id==10){
            $this->chip=$this->obtieneChip();
           }
           $this->reset(["externo"]);
        } else {
            $this->tipoServicio = null;
        }
        
        
    }


    public function sugeridoSegunTipo($tipoServ)
    {
        $formatoGnv = 1;
        $formatoGlp = 3;
        if ($tipoServ) {
            switch ($tipoServ) {
                case 1:
                    $this->numSugerido = $this->obtieneFormato($formatoGnv);
                    break;
                case 2:
                    $this->numSugerido = $this->obtieneFormato($formatoGnv);
                    break;
                case 3:
                    $this->numSugerido = $this->obtieneFormato($formatoGlp);
                    break;
                case 4:
                    $this->numSugerido = $this->obtieneFormato($formatoGlp);
                    break;
                case 8:
                    $this->numSugerido = $this->obtieneFormato($formatoGnv);
                    break;
                case 9:
                    $this->numSugerido = $this->obtieneFormato($formatoGlp);
                    break;
                case 10:
                        $this->numSugerido = $this->obtieneFormato($formatoGnv);
                        break;
                case 12:
                        $this->numSugerido = $this->obtieneFormato($formatoGnv);
                        break;
                default:
                    $this->numSugerido = 0;
                    break;
            }
        }
    }

    public function obtieneFormato($tipo)
    {
        $formato = Material::where([
            ["idTipoMaterial", $tipo],
            ['idUsuario', Auth::id()],
            ["estado", 3],
        ])
            ->orderBy('numSerie', 'asc')
            ->min("numSerie");
        if (isset($formato)) {
            return $formato;
        } else {
            return null;
        }
    }

    //Selecciona una hoja segun el tipo de servicio
    public function seleccionaHojaSegunServicio($serie, $tipo)
    {
        $hoja = null;
        switch ($tipo) {
            case 1:
                $hoja = Material::where([['numSerie', $serie], ['idTipoMaterial', 1], ['estado', 3], ['idUsuario', Auth::id()]])->first();
                return $hoja;
                break;

            case 2:
                $hoja = Material::where([['numSerie', $serie], ['idTipoMaterial', 1], ['estado', 3], ['idUsuario', Auth::id()]])->first();
                return $hoja;
                break;

            case 3:
                $hoja = Material::where([['numSerie', $serie], ['idTipoMaterial', 3], ['estado', 3], ['idUsuario', Auth::id()]])->first();
                return $hoja;
                break;

            case 4:
                $hoja = Material::where([['numSerie', $serie], ['idTipoMaterial', 3], ['estado', 3], ['idUsuario', Auth::id()]])->first();
                return $hoja;
                break;

            case 8:
                $hoja = Material::where([['numSerie', $serie], ['idTipoMaterial', 1], ['estado', 3], ['idUsuario', Auth::id()]])->first();
                return $hoja;
                break;

            case 10:
                    $hoja = Material::where([['numSerie', $serie], ['idTipoMaterial', 1], ['estado', 3], ['idUsuario', Auth::id()]])->first();
                    return $hoja;
                    break;
            case 12:
                    $hoja = Material::where([['numSerie', $serie], ['idTipoMaterial', 1], ['estado', 3], ['idUsuario', Auth::id()]])->first();
                    return $hoja;
                    break;
            default:
                return $hoja;
                break;
        }
    }

    //Buscar una certificacion
    public function buscarCertificacion()
    {
        $this->validate(['placa' => 'required|min:3|max:7']);

        //implementar un switch o if else segun el servicio
        $certis = Certificacion::PlacaVehiculo($this->placa)
            ->orderBy('created_at', 'desc')
            ->get();

        $certs = $certis->whereBetween("tipo_servicio", [1, 2]);

        if ($certs->count() > 0) {
            $this->busquedaCert = true;
            $this->certificaciones = $certs;
        } else {
            $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "No se encontro ningúna certificación con la placa ingresada", "icono" => "warning"]);
        }
    }

    //Resetear busqueda de Certificado
    public function reseteaBusquedaCert()
    {
        $this->certificado = null;
    }

    public function calculaFecha($fecha)
    {
        $dif = null;

        $hoy = Carbon::now();

        $dif = $fecha->diffInDays($hoy);

        return $dif;
    }

    public function seleccionaCertificacion($id)
    {
        $certi = $this->certificaciones[$id];
        $this->certificado = $certi;
        $this->fechaCerti = $this->calculaFecha($certi->created_at);
        $this->certificaciones = null;
        $this->busquedaCert = false;
        $this->reset(['placa']);
    }

    public function procesaFormato($numSerieFormato, $servicio)
    {
        if ($numSerieFormato) {
            $hoja = $this->seleccionaHojaSegunServicio($numSerieFormato, $servicio);
            if ($hoja != null) {
                return $hoja;
            } else {
                $this->emit("CustomAlert", ["titulo" => "ERROR", "mensaje" => "El número de serie ingresado no corresponde con ningún formato en su poder", "icono" => "error"]);
                return null;
            }
        } else {
            $this->emit("CustomAlert", ["titulo" => "ERROR", "mensaje" => "Número de serie no válido.", "icono" => "error"]);
            return null;
        }
    }

    public function certificar()
    {
        $taller = Taller::findOrFail($this->taller);
        $servicio = Servicio::findOrFail($this->servicio);
        $hoja = $this->procesaFormato($this->numSugerido, $servicio->tipoServicio->id);

        if ($hoja != null) {
            if (isset($this->vehiculo)) {
                if ($this->vehiculo->esCertificable) {
                    $certi = Certificacion::certificarGnv($taller, $servicio, $hoja, $this->vehiculo, Auth::user());
                    if ($certi) {
                        $this->estado = "certificado";
                        $this->certificacion = $certi;
                        $expe=Expediente::create([
                            "placa"=>$this->vehiculo->placa,
                            "certificado"=>$hoja->numSerie,
                            "estado"=>1,
                            "idTaller"=>$taller->id,
                            'usuario_idusuario'=>Auth::id(),
                            'servicio_idservicio'=>$servicio->id,
                        ]);                        
                        $this->guardarFotos($expe);
                        //CARGA JOB PARA GUARDAR ARCHIVOS EN EL EXPEDIENTE
                        guardarArchivosEnExpediente::dispatch($expe,$certi); 
                        //Crea un servicio material                      
                        $certEx=CertifiacionExpediente::create(["idCertificacion"=>$certi->id,"idExpediente"=>$expe->id]);
                        $this->emit("minAlert", ["titulo" => "¡EXCELENTE TRABAJO!", "mensaje" => "Tu certificado N°: " . $certi->Hoja->numSerie . " esta listo.", "icono" => "success"]);
                    } else {
                        $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "No fue posible certificar", "icono" => "warning"]);
                    }
                } else {
                    $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes completar los datos de los equipos para poder certificar", "icono" => "warning"]);
                }
            } else {
                $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes ingresar un vehículo valido para poder certificar", "icono" => "warning"]);
            }
        }
    }

    public function certificarPreconver()
    {
        $taller = Taller::findOrFail($this->taller);
        $servicio = Servicio::findOrFail($this->servicio);
        $hoja = $this->procesaFormato($this->numSugerido, $servicio->tipoServicio->id);

        if ($hoja != null) {
            if (isset($this->vehiculo)) {
                if ($this->vehiculo->esCertificable) {
                    $certi = Certificacion::certificarGnvPre($taller, $servicio, $hoja, $this->vehiculo, Auth::user());
                    if ($certi) {
                        $this->estado = "certificado";
                        $this->certificacion = $certi;                        
                        $this->emit("minAlert", ["titulo" => "¡EXCELENTE TRABAJO!", "mensaje" => "Tu certificado N°: " . $certi->Hoja->numSerie . " esta listo.", "icono" => "success"]);
                    } else {
                        $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "No fue posible certificar", "icono" => "warning"]);
                    }
                } else {
                    $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes completar los datos de los equipos para poder certificar", "icono" => "warning"]);
                }
            } else {
                $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes ingresar un vehículo valido para poder certificar", "icono" => "warning"]);
            }
        }
    }


    public function obtieneChip(){
        $chip=Material::where([["idUsuario",Auth::id()],["estado",3],["idTipoMaterial",2]])->first();        
        return $chip;
    }

    public function certificarConChip()
    {
        $taller = Taller::findOrFail($this->taller);
        $servicio = Servicio::findOrFail($this->servicio);
        $hoja = $this->procesaFormato($this->numSugerido, $servicio->tipoServicio->id);
        $chip=$this->chip;

        if ($hoja != null) {
            if($chip != null){
                if (isset($this->vehiculo)) {
                    if ($this->vehiculo->esCertificable) {
                        $certi = Certificacion::certificarGnvConChip($taller, $servicio, $hoja, $this->vehiculo, Auth::user(),$chip);
                        if ($certi) {
                            $this->estado = "certificado";
                            $this->certificacion = $certi;
                            $expe=Expediente::create([
                                "placa"=>$this->vehiculo->placa,
                                "certificado"=>$hoja->numSerie,
                                "estado"=>1,
                                "idTaller"=>$taller->id,
                                'usuario_idusuario'=>Auth::id(),
                                'servicio_idservicio'=>$servicio->id,
                            ]);                        
                            $this->guardarFotos($expe);
                            guardarArchivosEnExpediente::dispatch($expe,$certi);                       
                            $certEx=CertifiacionExpediente::create(["idCertificacion"=>$certi->id,"idExpediente"=>$expe->id]);                            
                            $this->emit("minAlert", ["titulo" => "¡EXCELENTE TRABAJO!", "mensaje" => "Tu certificado N°: " . $certi->Hoja->numSerie . " esta listo.", "icono" => "success"]);
                        } else {
                            $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "No fue posible certificar", "icono" => "warning"]);
                        }
                    } else {
                        $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes completar los datos de los equipos para poder certificar", "icono" => "warning"]);
                    }
                } else {
                    $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes ingresar un vehículo valido para poder certificar", "icono" => "warning"]);
                }
            }else{
                $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "No cuentas con chips disponibles para realizar este servicio", "icono" => "warning"]);
            }
        }
    }
    
    //funcion para guardar las fotos del expediente en el storage local.
    public function guardarFotos(Expediente $expe){
        $this->validate(["imagenes"=>"nullable|array","imagenes.*"=>"image"]);
        if(count($this->imagenes)){
            foreach($this->imagenes as $key => $file){          
                $nombre=$expe->placa.'-foto'.($key+1).'-'.$expe->certificado;
                $file_save=Imagen::create([                
                    'nombre'=>$nombre,
                    'ruta'=>$file->storeAs('public/expedientes',$nombre.'.'.$file->extension()),
                    'extension'=>$file->extension(),
                    'Expediente_idExpediente'=>$expe->id,
                ]);            
            }
        }
       $this->reset(["imagenes"]);      
    }


    public function guardaDocumentos(){
        
    }

    public function refrescaVe()
    {
        $this->vehiculo->refresh();
    }

    public function duplicarCertificado()
    {
        /*
        $servicio=Servicio::find($this->servicio);
        if(Certificacion::findOrFail($this->certificado->id)){
            $hoja=$this->procesaFormato($this->numSugerido,$this->tipoServicio->id);
            $cert=Certificacion::create([
                "idVehiculo"=>$this->vehiculo->id,
                "idTaller"=>$this->taller,
                "idInspector"=>Auth::id(),
                "idServicio"=>$this->servicio,
                "estado"=>1,
                "precio"=>$servicio->precio,
                "pagado"=>0,
            ]);
           // $this->servicioCertificado=$cert;           
            $hoja->update(["estado"=>4]);
            $servM=ServicioMaterial::create([
                                            "idMaterial"=>$hoja->id,
                                            "idCertificacion"=>$cert->id
                                            ]);
            $this->duplicado=$cert;            
            $this->emit("minAlert",["titulo"=>"¡BUEN TRABAJO!","mensaje"=>"Tu certificado esta listo!","icono"=>"success",]);
        }else{
            $this->emit("minAlert",["titulo"=>"AVISO DEL SISTEMA","mensaje"=>"No se pudo realizar la certificación","icono"=>"warning"]);
        }
        */
        $this->duplicar();
    }

    public function duplicar()
    {
        $taller = Taller::findOrFail($this->taller);
        $servicio = Servicio::find($this->servicio);
        $hoja = $this->procesaFormato($this->numSugerido, $servicio->tipoServicio->id);


        if ($hoja != null) {
            if ($this->externo) {
                if (isset($this->vehiculo)) {
                    if ($this->vehiculo->esCertificable) {
                        $servicio = Servicio::find($this->servicio);

                        $duplicado = $this->creaDuplicadoExterno();
                        $certi = Certificacion::duplicarCertificadoExternoGnv(Auth::user(), $this->vehiculo, $servicio, $taller, $hoja, $duplicado);
                        $this->estado = "certificado";
                        $this->certificacion = $certi;
                        $this->emit("minAlert", ["titulo" => "¡EXCELENTE TRABAJO!", "mensaje" => "Tu certificado N°: " . $certi->Hoja->numSerie . " esta listo.", "icono" => "success"]);
                    } else {
                        $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes completar los datos de los equipos para poder certificar", "icono" => "warning"]);
                    }
                } else {
                    $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes ingresar un vehículo valido para poder certificar", "icono" => "warning"]);
                }
            } else {

                if ($this->certificado && $servicio) {                    
                    $dupli = $this->creaDuplicado($this->certificado);
                    $certi = Certificacion::duplicarCertificadoGnv($dupli,$taller,Auth::user(),$servicio,$hoja);
                    $this->estado = "certificado";
                    $this->certificacion = $certi;
                    $this->emit("minAlert", ["titulo" => "¡EXCELENTE TRABAJO!", "mensaje" => "Tu certificado N°: " . $certi->Hoja->numSerie . " esta listo.", "icono" => "success"]);
                } else {
                    $this->emit("minAlert", ["titulo" => "AVISO DEL SISTEMA", "mensaje" => "Debes buscar una certificacion para poder duplicar.", "icono" => "warning"]);
                }
            }
        }
    }


    public function creaDuplicadoExterno()
    {
        $this->validate(
            [
                "tallerExterno" => "required|min:3",
                "fechaExterno" => "required|date",
                "servicioExterno" => "required|min:1",
            ]
        );
        $dupli = Duplicado::create([
            "servicio" => $this->servicioExterno,
            "taller" => $this->tallerExterno,
            "externo" => 1,
            "fecha" => $this->fechaExterno,
        ]);
        if ($dupli) {
            return $dupli;
        } else {
            return null;
        }
    }

    public function creaDuplicado(Certificacion $anterior)
    {
        $dupli = Duplicado::create([
            "servicio" => $anterior->Servicio->tipoServicio->id,
            "taller" => $anterior->Taller->id,
            "externo" => 0,
            "fecha" => $anterior->created_at,
            "idAnterior" => $anterior->id,
        ]);

        return $dupli;
    }
}
