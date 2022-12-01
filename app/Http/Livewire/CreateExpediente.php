<?php

namespace App\Http\Livewire;

use App\Models\Expediente;
use App\Models\Imagen;
use App\Models\Servicio;
use App\Models\Taller;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CreateExpediente extends Component 
{
    use WithFileUploads;   
  

    //DECLARACION DE VARIABLES
    public $talleres=[];    
    public $documentos=[];
    public $files=[];
    public $servicios=[];
    public $open=false;   
    public $estado=1;    
    public $idus,$placa,$certificado,$servicio,$identificador,$tallerSeleccionado;
    
    protected $rules=[
        'placa'=>'required|min:6|max:6',
        'certificado'=>'required|min:7|max:7|unique:expedientes,certificado',
        'servicio'=>'required',
        'files'=>'required',
        //'files.*'=>'mimes:jpg,jpeg,png,tif|size:5010',
        //'documentos.*'=>'required|mimes:pdf,xls,xlsx,doc,docx|size:5120',
        'documentos'=>'required',
        'tallerSeleccionado'=>'required'
        //'documentos.*'=>'file|mimes:pdf,xls,xlsx,doc,docx|max:10024',
    ];

    
    
    
    public function mount(){
        $this->idus=Auth::id();
        $this->identificador=rand();
        $this->placa=strtoupper($this->placa);
        $user=User::find($this->idus);
        /*        
        $this->servicios= json_decode(DB::table('servicio')
        ->select('servicio.*','tiposervicio.descripcion')
        ->join('tiposervicio','servicio.tipoServicio_idtipoServicio','=','tiposervicio.id')
        ->where('taller_idtaller',$user->taller_idtaller)
        ->get(),true);
        */ 
        $this->talleres=Taller::all();
        
    }

    public function listaServicios(){
        if($this->tallerSeleccionado != null){
            $this->servicios=json_decode(DB::table('servicio')
            ->select('servicio.*','tiposervicio.descripcion')
            ->join('tiposervicio','servicio.tipoServicio_idtipoServicio','=','tiposervicio.id')
            ->where('taller_idtaller',$this->tallerSeleccionado)
            ->get(),true);            
        }
    }

    public function updatedtallerSeleccionado($idTaller){
        if($idTaller){
        $this->servicios=json_decode(DB::table('servicio')
            ->select('servicio.*','tiposervicio.descripcion')
            ->join('tiposervicio','servicio.tipoServicio_idtipoServicio','=','tiposervicio.id')
            ->where('taller_idtaller',$idTaller)
        ->get(),true);      
        }else{
            $this->servicios=null;
        }   
    }


    public function render()
    {
        return view('livewire.create-expediente');
        
    }
    public function updatedFiles()
    {
        $this->validate( [
            'files'=>'required|max:10',
            'files.*'=>'mimes:jpg,jpeg,png,tif|max:5010',
        ]);
    }

    public function updatedDocumentos()
    {
        $this->validate( [
            'documentos'=>'array|max:3',
            'documentos.*'=>'file|mimes:pdf,xls,xlsx,doc,docx|max:5010'
        ]);
    }

    public function save(){

        $this->validate();        

        $expe=Expediente::create([
            'placa'=> strtoupper($this->placa),
            'certificado'=>$this->certificado,            
            'estado'=>$this->estado,
            'idTaller'=>$this->tallerSeleccionado,
            'servicio_idservicio'=>$this->servicio,
            'usuario_idusuario'=>$this->idus,
        ]);

        foreach($this->files as $key=>$file){
            $file_save= new imagen();
            $file_save->nombre=$expe->certificado.'-foto'.$key.'-'.$expe->placa;
            $file_save->extension=$file->extension();
            $file_save->ruta = $file->store('public/expedientes');
            $file_save->Expediente_idExpediente=$expe->id;

            Imagen::create([
                'nombre'=>$file_save->nombre,
                'ruta'=>$file_save->ruta,
                'extension'=>$file_save->extension,
                'Expediente_idExpediente'=>$file_save->Expediente_idExpediente,
            ]);
        }


        foreach($this->documentos as $key=>$file){
            $file_save= new imagen();
            $file_save->nombre=$expe->certificado.'-doc'.$key.'-'.$expe->placa;
            $file_save->extension=$file->extension();
            $file_save->ruta = $file->store('public/expedientes');
            $file_save->Expediente_idExpediente=$expe->id;

            Imagen::create([
                'nombre'=>$file_save->nombre,
                'ruta'=>$file_save->ruta,
                'extension'=>$file_save->extension,
                'Expediente_idExpediente'=>$file_save->Expediente_idExpediente,
            ]);
        }        
     
        $this->reset(['open','placa','certificado','servicio','files','documentos']);
        $this->identificador=rand();
        $this->emitTo('expedientes','render');
        $this->emit('alert','El Expediente se registro correctamente!');
    }

    public function deleteFileUpload($id){
        unset($this->files[$id]);
    }

    public function deleteDocumentUpload($id){
        unset($this->documentos[$id]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingOpen(){
        if($this->open==false){
            $this->reset(['placa','certificado','servicio','files','documentos','tallerSeleccionado','servicios']);
            $this->identificador=rand();            
        }
    }

    protected $messages = [
        'tallerSeleccionado.required'=>'El campo taller es obligatorio',
        'files.required'=>'Por favor cargue una foto',
        'documentos.required' => 'Por favor cargue un documento',        
    ];
    
}
