<x-filament-panels::page>
    @if($day)
        <div class="space-y-6">
            {{ $this->table }}
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500">No se encontró información del día.</p>
        </div>
    @endif
</x-filament-panels::page>

