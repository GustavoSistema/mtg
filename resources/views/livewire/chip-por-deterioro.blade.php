<div class="max-w-5xl m-auto bg-white rounded-lg shadow-md   justify-center items-center">
    @if($chips->count())
            @if($estado=='esperando')
                <div class="py-2 px-4 bg-indigo-200 w-full rounded-t-md font-semibold flex justify-between items-center">   
                    <p>Ingrese datos</p>
                    <p class="py-1 px-2 bg-green-300 text-green-800 rounded-full">Chips disponibles: {{var_export($chips->count())}}</p>
                </div>
                <div class="rounded-lg py-4 px-4 grid grid-cols-1 gap-8 sm:grid-cols-2 w-full">
                    <div>
                        <x-jet-label value="Nombre propietario:" for="nombre" />
                        <x-jet-input type="text" wire:model="nombre" class="w-full" />
                        <x-jet-input-error for="nombre" />
                    </div>
                    <div>
                        <x-jet-label value="Placa:" />
                        <x-jet-input type="text" wire:model="placa" class="w-full" />
                        <x-jet-input-error for="placa" />
                    </div>
                </div> 
                <div class="p-2 w-full flex items-center justify-center ">   
                    <button class="p-2 bg-indigo-400 text-sm text-white rounded-md hover:bg-indigo-600" wire:click="consumirChip">
                        Procesar
                    </button>
                </div> 
            @else
                <div class=" my-2 px-4 py-2 bg-indigo-200 w-full rounded-md font-semibold flex justify-between items-center ">   
                    <p class="text-center">Chip consumido correctamente! <i class="fa-regular fa-circle-check text-green-500"></i></p>
                </div>
                <div class="p-2 w-full flex items-center justify-center ">
                    <a href="{{ route('servicio') }}"
                        class="hover:cursor-pointer focus:ring-2 focus:ring-offset-2 focus:ring-amber-600 sm:mt-0 inline-flex items-center justify-center px-6 py-3 bg-red-400 hover:bg-red-500 focus:outline-none rounded">
                        <p class="text-sm font-medium leading-none text-white">
                            <i class="fas fa-archive"></i>&nbsp;Nuevo Servicio
                        </p>
                    </a>
                </div> 
            @endif       
    @else
    <div class="rounded-lg py-4 bg-red-400 px-2  w-full text-center text-white font-semibold ">
        NO TINES CHIPS DISPONIBLES PARA REALIZAR ESTE SERVICIO.
    </div>        
    @endif

    
        
    
</div>
