<div>
    {{ $this->table }}

    @if($summary['total_products'] > 0)
        <div class="mt-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-sm text-black">Productos Diferentes</p>
                    <p class="text-xl font-bold text-black">{{ $summary['total_products'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-black">Unidades Vendidas</p>
                    <p class="text-xl font-bold text-primary-600">{{ $summary['total_quantity'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-black">Total Vendido</p>
                    <p class="text-xl font-bold text-success-600">{{ formatMoney($summary['total_amount']) }}</p>
                </div>
            </div>
        </div>
    @endif
</div>