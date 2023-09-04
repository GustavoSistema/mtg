<?php

namespace App\Http\Livewire;

use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RevisionInventario extends Component
{

    public $inspector,$inspectores;
    public $resultado;
    public $formatoGNVDisponibles = 0;
    public $formatoChipDisponibles = 0;
    public $formatoGLPDisponibles = 0;

    protected $rules=["inspector"=>"required|numeric|min:1"];

    public function render()
    {
        return view('livewire.revision-inventario');
    }

    public function mount(){                
        $this->inspectores=User::role(['inspector','supervisor'])
        ->where('id','!=',Auth::id())
        ->orderBy('name')->get();
        $this->resultado= new Collection();
    }


    public function consultar()
    {
    $this->validate();

    $materiales = Material::where("idUsuario", $this->inspector)->get();
    $formatoGNVDisponibles = 0;
    $formatoChipDisponibles = 0;
    $formatoGLPDisponibles = 0;
    
    foreach ($materiales as $material) {
        if ($material->estado === 3) { // Estado "Disponible"
            switch ($material->tipo->descripcion) {
                case 'FORMATO GNV':
                    $formatoGNVDisponibles++;
                    break;
                case 'CHIP':
                    $formatoChipDisponibles++;
                    break;
                case 'FORMATO GLP':
                    $formatoGLPDisponibles++;
                    break;
            }
        }
    }

    // Asignar valores a las propiedades del componente
    $this->formatoGNVDisponibles = $formatoGNVDisponibles;
    $this->formatoChipDisponibles = $formatoChipDisponibles;
    $this->formatoGLPDisponibles = $formatoGLPDisponibles;
    $this->resultado = $materiales;
}
    


}
