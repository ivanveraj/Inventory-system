<div class="space-y-4">
    <form wire:submit="processSale">
        {{ $this->form }}

        <div class="flex justify-center mt-4">
            <x-filament::button type="submit" size="lg" color="info" class="text-xl font-bold">
                Total a pagar: {{ formatMoney($this->data['total_sale']) }}
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
