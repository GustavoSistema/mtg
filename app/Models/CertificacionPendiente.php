<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificacionPendiente extends Model
{
    use HasFactory;

    protected $table="certificados_pendientes";

    protected $fillable=
    ['id',
    'idVehiculo',
    'idTaller',
    'idServicio',
    'idInspector',
    'estado',
    'idCertificacion',
    "idExpediente",
    'pagado',    
    'created_at',
    'updated_at',        
    ]; 

    public function Vehiculo(){
        return $this->belongsTo(vehiculo::class,'idVehiculo');
    }

    public function Taller(){
        return $this->belongsTo(Taller::class,'idTaller');
    }

    public function Inspector(){
        return $this->belongsTo(User::class,'idInspector');
    }

    public function Servicio(){
        return $this->belongsTo(Servicio::class,'idServicio');
    }

    public function scopePlacaVehiculo($query,$search): void{
        if($search){
        $query->orWhereHas('Vehiculo', function (Builder $query) use ($search) {
            $query->where('placa', 'like', '%'.$search.'%');
        });
        }
    }
    public function scopeIdInspector(Builder $query, string $search): void{   
        if($search){
            $query->where('idInspector', $search);
        }
       
    }
}
