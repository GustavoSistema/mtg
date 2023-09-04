 <div>
     <div class="container block justify-center m-auto py-6">
         <h1 class="text-2xl text-center font-bold text-indigo-500 uppercase">Consulta de inventario</h1>
         <div class="rounded-xl m-4 bg-white p-8 mx-auto max-w-max shadow-lg">
             <div class="flex flex-row">
                 <div class="w-full">
                     <x-jet-label value="Inspector:" for="Inspector" />
                     <select wire:model="inspector"
                         class="bg-gray-50 mx-2 border-indigo-500 rounded-md outline-none ml-1 block w-full ">
                         <option value="0">Seleccione</option>
                         @foreach ($inspectores as $inspector)
                             <option value="{{ $inspector->id }}">{{ $inspector->name }}</option>
                         @endforeach
                     </select>
                     <x-jet-input-error for="inspector" />
                 </div>
             </div>
             <div class="flex items-center justify-center mt-4">
                 <button class="p-3 bg-indigo-500 rounded-xl text-white text-sm hover:font-bold hover:bg-indigo-700"
                     wire:click="consultar">
                     <i class="fas fa-search"></i>
                     Buscar
                 </button>
             </div>
         </div>

         @if ($resultado->count())
             <div class="w-full border bg-white rounded-md shadow-md">
                 <div class="w-5/6 my-2 m-auto text-center">
                     <div class="flex flex-wrap justify-between">
                         <!-- Fila 1 -->
                         <div class="mb-12 lg:w-1/5 relative">
                             <i class="fa-solid fa-cubes fa-3x text-amber-600 mx-auto mb-6"></i>
                             <h5 class="text-lg font-medium text-blue-600 font-bold mb-4">{{ $resultado->count() }}</h5>
                             <h6 class="font-medium text-gray-500">Total</h6>                             
                         </div>

                         <div class="lg:w-1/5 flex items-center justify-center">
                            <hr class="w-px bg-gray-200 h-20" />
                        </div>

                         <!-- Fila 2 -->
                         <div class="mb-12 lg:w-1/5 relative">
                             <i class="fa-solid fa-circle-check fa-3x text-green-600 mx-auto mb-6"></i>
                             <h5 class="text-lg font-medium text-blue-600 font-bold mb-4">
                                 {{ $resultado->where('estado', 3)->count() }}</h5>
                             <h6 class="font-medium text-gray-500">Disponibles</h6>                             
                         </div>

                         <div class="lg:w-1/5 flex items-center justify-center">
                            <hr class="w-px bg-gray-200 h-20" />
                        </div>

                         <!-- Fila 3 -->
                         <div class="lg:w-1/5 relative">
                             <i class="fa-solid fa-ticket fa-3x text-gray-600 font-bold mb-4"></i>
                             <h5 class="text-lg font-medium text-blue-600 font-bold mb-4">
                                 {{ $resultado->where('estado', 4)->count() }}</h5>
                             <h6 class="font-medium text-gray-500 mb-0">Cosumidos</h6>
                         </div>

                         <div class="lg:w-1/5 flex items-center justify-center">
                            <hr class="w-px bg-gray-200 h-20" />
                        </div>

                         <!-- Fila 4 -->
                         <div class="mb-12 lg:w-1/5 relative">
                             <i class="fa-solid fa-circle-xmark text-red-600 fa-3x mx-auto mb-6"></i>
                             <h5 class="text-lg font-medium text-blue-600 font-bold mb-4">
                                 {{ $resultado->where('estado', 5)->count() }}</h5>
                             <h6 class="font-medium text-gray-500">Anulados</h6>
                         </div>

                         <div class="lg:w-1/5 flex items-center justify-center">
                            <hr class="w-px bg-gray-200 h-20" />
                        </div>

                         <!-- Fila 5 -->
                         <div class="lg:w-1/5 relative">
                             <div class="w-5/6 my-4 m-auto text-center">
                                 <!-- Título "Stock" -->
                                 <h4 class="text-lg font-medium text-gray-700 mb-4">Stock</h4>

                                 <!-- Formato GNV -->
                                 <div class="mb-1 relative">
                                     <div class="flex items-center justify-between">
                                         <h5 class="font-medium text-gray-500 mr-2">GNV</h5>
                                         <h5 class="text-lg font-medium text-blue-600 font-bold">
                                             {{ $formatoGNVDisponibles }}</h5>
                                     </div>
                                 </div>

                                 <!-- Formato Chip -->
                                 <div class="mb-1 relative">
                                     <div class="flex items-center justify-between">
                                         <h5 class="font-medium text-gray-500 mr-2">CHIP</h5>
                                         <h5 class="text-lg font-medium text-blue-600 font-bold">
                                             {{ $formatoChipDisponibles }}</h5>
                                     </div>
                                 </div>

                                 <!-- Formato GLP -->
                                 <div class="relative">
                                     <div class="flex items-center justify-between">
                                         <h5 class="font-medium text-gray-500 mr-2">GLP</h5>
                                         <h5 class="text-lg font-medium text-blue-600 font-bold">
                                             {{ $formatoGLPDisponibles }}</h5>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>

                 <div class="max-h-96 overflow-y-auto">
                     <table class="w-5/6 m-auto my-4 text-sm text-left text-gray-500 dark:text-gray-400">
                         <thead
                             class="text-xs text-gray-700 uppercase bg-indigo-300 rounded-t-lg dark:bg-gray-700 dark:text-gray-400">
                             <tr>
                                 <th scope="col" class="px-6 py-3">
                                     Material
                                 </th>
                                 <th scope="col" class="px-6 py-3">
                                     # Serie
                                 </th>
                                 <th scope="col" class="px-6 py-3">
                                     Estado
                                 </th>
                                 <th scope="col" class="px-6 py-3">
                                     Ubicación
                                 </th>
                             </tr>
                         </thead>
                         <tbody>
                             @foreach ($resultado as $item)
                                 <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                     <th scope="row"
                                         class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                         {{ $item->tipo->descripcion }}
                                     </th>
                                     <td class="px-6 py-4 ">
                                         {{ $item->numSerie ?? 'No data' }}
                                     </td>
                                     <td class="px-6 py-4 ">
                                         @switch($item->estado)
                                             @case(1)
                                                 <span
                                                     class="inline-block whitespace-nowrap rounded-full bg-indigo-100 px-[0.65em] pb-[0.25em] pt-[0.35em] text-center align-baseline text-[0.75em] font-bold leading-none text-indigo-700">
                                                     Almacenado
                                                 </span>
                                             @break

                                             @case(2)
                                                 <span
                                                     class="inline-block whitespace-nowrap rounded-full bg-blue-100 px-[0.65em] pb-[0.25em] pt-[0.35em] text-center align-baseline text-[0.75em] font-bold leading-none text-blue-700">
                                                     En envio
                                                 </span>
                                             @break

                                             @case(3)
                                                 <span
                                                     class="inline-block whitespace-nowrap rounded-full bg-green-100 px-[0.65em] pb-[0.25em] pt-[0.35em] text-center align-baseline text-[0.75em] font-bold leading-none text-green-700">
                                                     Disponible
                                                 </span>
                                             @break

                                             @case(4)
                                                 <span
                                                     class="inline-block whitespace-nowrap rounded-full bg-gray-100 px-[0.65em] pb-[0.25em] pt-[0.35em] text-center align-baseline text-[0.75em] font-bold leading-none text-gray-800">
                                                     Consumido
                                                 </span>
                                             @break

                                             @default
                                         @endswitch
                                     </td>
                                     <td class="px-6 py-4 ">
                                         {{ $item->ubicacion }}
                                     </td>
                                 </tr>
                             @endforeach
                         </tbody>
                     </table>
                 </div>

             </div>
         @else
             <div class="p-4 border rounded-md bg-indigo-300 shadow-lg">
                 <p class="text-center text-gray-200 font-semibold">
                     No se encontraron resultados
                 </p>
             </div>
         @endif




     </div>
 </div>
