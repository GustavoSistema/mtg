<?php

namespace App\Http\Livewire;

use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Provincia;
use App\Models\Servicio;
use App\Models\Taller;
use App\Models\TipoServicio;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateTaller extends Component
{

    use WithFileUploads;

    public $servicios=[];
    public $tipos=[];
    public $precios=[];

    public $departamentos,$provincias,$distritos,$logo,$firma;

    public $departamentoSel=Null;
    public $provinciaSel=Null;
    public $distritoSel=Null;

    public $nombre,$direccion,$ruc,$contador;
    public $open=false; 

    
    protected $rules=[
        'nombre'=>'required|min:5|max:110',      
        'direccion'=>'required|min:5|max:110',
        'ruc'=>'required|min:11|max:11|unique:taller,ruc',
        'precios'=>'array|min:1|required',
        'logo'=>'image',
        'firma'=>'image',
        'distritoSel'=>'required|numeric|min:1'

        //'servicios'=>'array|required',  
        //'servicios.precio'=>'numeric|required',     
        
        //'tipos'=>'array|min:1|required',
        //'tipos.*'=>'numeric|min:1',
        
        //'precios.*'=>'numeric|required|min:1',         
    ]; 
    
    
    public function mount(){
        $this->cargaTipoServicios();
        $this->cargaServicios();
        $this->departamentos=Departamento::all();
    }
     
    public function render()
    {
        return view('livewire.create-taller');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save(){
        //$rules=[];
        foreach($this->tipos as $key=>$item){
            if($item!=0){                             
                $this->rules+=[('precios.'.$key)=>'required|numeric',];                 
            }
        }
        /*
        if(count($rules)>0){
            $this->validate($rules); 
        }else{
            $this->validate();
        }
        */
        $this->validate(); 
        
        $rutaLogo=$this->logo->storeAs('public/Logos','logo-'.$this->ruc.'.'.$this->logo->extension());
        $rutaFirma=$this->firma->storeAs('public/Firmas','firma-'.$this->ruc.'.'.$this->firma->extension());
        $taller=Taller::create([
            'nombre'=>$this->nombre,
            'direccion'=>$this->direccion,
            'ruc'=>$this->ruc,
            'rutaLogo'=>$rutaLogo,
            'rutaFirma'=>$rutaFirma,
            'idDistrito'=>$this->distritoSel,
        ]);

        foreach($this->tipos as $key=>$item){
            if($item){
                $serv=Servicio::create([
                    'precio'=>$this->precios[$key],
                    'estado'=>1,
                    'taller_idtaller'=>$taller->id,
                    'tipoServicio_idtipoServicio'=>$item,
                ]);
            }
        }

        $this->reset(['open','nombre','direccion','ruc']);
        $this->emitTo('talleres','render');
        $this->emit('alert','El taller se registro correctamente!');
    }

    public function cargaTipoServicios(){
        $this->servicios=TipoServicio::all();        
    }

    public function cargaServicios(){
        foreach($this->servicios as $key=>$item){
           $this->tipos[$key]=0;
           $this->precios[$key]=null;
        }
    }
    
    public function sumar(){
        foreach($this->tipos as $key=>$tipo){
            if($tipo>0){
                $this->contador++;
            }
        }
    }

    public function updatedDepartamentoSel($depa){        
        $this->provincias=Provincia::where("idDepa",$depa)->get();  
        $this->provinciaSel=null;         
    }

    public function updatedProvinciaSel($prov){        
        $this->distritos=Distrito::where("idProv",$prov)->get();       
        $this->distritoSel=null;          
    }

    protected $messages = [
        //'precios.*.min' => 'Ingrese un precio valido', 
        'precios.*.required' => 'Ingrese un precio valido.'      
    ];   
    
}
