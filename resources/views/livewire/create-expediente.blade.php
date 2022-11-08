<div class="mb-4">
           
    <button  wire:click="$set('open',true)" class="bg-indigo-600 px-6 py-4 rounded-md text-white font-semibold tracking-wide cursor-pointer">Agregar</button>


    <x-jet-dialog-modal wire:model="open">
        <x-slot name="title">
            Crear nuevo Expediente
                      
        </x-slot>
        
        <x-slot name="content">           
            <div class="mb-4">
                <select wire:model="servicio" class="bg-gray-50 mx-2 border-indigo-500 rounded-md outline-none ml-1 block w-full ">
                    <option value="">Seleccione</option>
                    <option value="1">Conversión a GNV</option>
                    <option value="2">Revisión anual GNV</option>
                    <option value="3">Conversión a GLP</option>
                    <option value="4">Revisión anual GLP</option>                    
                </select>
                <x-jet-input-error for="servicio"/>
            </div>
            <div class="mb-4">
                <x-jet-label value="Placa:"/>
                <x-jet-input type="text" class="w-full" wire:model="placa" />
                <x-jet-input-error for="placa"/>
            </div>
            <div class="mb-4">
                <x-jet-label value="Certificado:"/>
                <x-jet-input type="text" class="w-full" wire:model="certificado"/>
                <x-jet-input-error for="certificado"/>
            </div>            
            <div class="mb-4">
                <x-jet-label value="Archivos:"/>
                <x-jet-input type="file" id="{{$identificador}}" multiple class="w-full" wire:model="files" />
                <x-jet-input-error for="files"/>
            </div>
            <div wire:loading wire:target="files"  class="my-4 w-full px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                Espere un momento mientras se carga la imagen.
            </div> 

            <h1 class="pt-8  font-semibold sm:text-lg text-gray-900">
                Galeria:
            </h1>
            
            @if ($files)
                {{--
                <section  class="h-full border-dotted border-2 overflow-auto p-8 w-full h-full flex flex-col">
                    <ul id="gallery-{{$identificador}}" class="flex flex-3 m-1">
                        @foreach ($files as $fil)
                            <li id="empty" class="h-84 w-84 object-fill mx-2 text-center flex flex-col items-center justify-center items-center">
                                <img src="{{$fil->temporaryUrl()}}" class=" mb-4 hover:object-scale-down"/> 
                                <a>                                    
                                    <i class="fas fa-trash hover:text-indigo-400"></i>
                                </a>
                            </li> 
                        @endforeach                       
                    </ul>
                </section>
                --}}
                <section class="mt-4 overflow-hidden border-dotted border-2 text-gray-700 " id="{{'section-'.$identificador}}">
                    <div class="container px-5 py-2 mx-auto lg:pt-12 lg:px-32">
                        <div class="flex flex-wrap -m-1 md:-m-2">
                            @foreach ($files as $key=>$fil)
                            <div class="flex flex-wrap w-1/3 ">
                                <div class="w-full p-1 md:p-2 items-center justify-center">
                                    <img alt="gallery" class="mx-auto flex object-cover object-center w-36 h-36 rounded-lg" 
                                    src="{{ $fil->temporaryUrl() }}">
                                    <a class="flex" wire:click="deleteFileUpload({{ $key }})" ><i class="fas fa-trash mt-1 mx-auto hover:text-indigo-400"></i></a>
                                </div>
                            </div>   
                            @endforeach
                        </div>
                    </div>
                </section>   
                
            @else
                <section class="h-full overflow-auto p-8 w-full h-full flex flex-col">
                    <ul id="gallery" class="flex flex-1 flex-wrap -m-1">
                        <li id="empty" class="h-full w-full text-center flex flex-col items-center justify-center items-center">
                          <img class="mx-auto w-32" src="https://user-images.githubusercontent.com/507615/54591670-ac0a0180-4a65-11e9-846c-e55ffce0fe7b.png" alt="no data" />
                          <span class="text-small text-gray-500">Aun no seleccionaste ningún archivo</span>
                        </li>
                      </ul>
                </section>
            @endif 

            
            {{--
            <section class="h-full overflow-auto p-8 w-full h-full flex flex-col">
                <header class="border-dashed border-2 border-gray-400 py-12 flex flex-col justify-center items-center">
                  <p class="mb-3 font-semibold text-gray-900 flex flex-wrap justify-center">
                    <span>Arrastre y suelte</span>&nbsp;<span>Sus archivos o</span>
                  </p>
                  <xjet-input id="{{$identificador}}" type="file"  class="hidden"  wire:model="files"/>
                  <button id="button" wire:model="files" class="mt-2 rounded-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 focus:shadow-outline focus:outline-none">
                    Carguelos desde aqui
                  </button>
                </header>
    
                
    
                
            </section>
            --}}
        </x-slot>
        
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('open',false)" class="mx-2">
                Cancelar
            </x-jet-secondary-button>
            <x-jet-button wire:click="save" wire:loading.attr="disabled" wire:target="save">
                Guardar
            </x-jet-button>            
        </x-slot>

    </x-jet-dialog-modal>
</div>