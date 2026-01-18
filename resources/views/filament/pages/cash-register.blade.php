<x-filament-panels::page>
    @if ($currentDay)
        <x-filament::section>
            <x-slot name="heading">
                Información del Día
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fecha de Apertura</p>
                    <p class="text-lg font-medium">{{ $currentDay->created_at->format('d/m/Y H:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Saldo Inicial</p>
                    <p class="text-lg font-medium text-green-600">{{ formatMoney($currentDay->opening_balance) }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="col-span-2">
                <div class="mb-4">
                    {{ $this->table }}
                </div>
                <div class="mb-4">
                    @livewire('cash-movements', ['currentDay' => $currentDay->id], key('cash-movements'))
                </div>
            </div>
            <div class="col-span-3">
                @livewire('history-sales', ['currentDay' => $currentDay->id], key('history-sales'))
            </div>
        </div>

        {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1"> --}}

        {{-- </div>
            <div>
                aa
            </div>
        </div> --}}
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <h3 class="text-lg font-medium my-2">No hay día activo</h3>
                <p class="mb-4">
                    Usa el botón "Abrir Caja" en la parte superior para
                    iniciar un nuevo día con el saldo inicial.
                </p>
            </div>
        </div>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>
