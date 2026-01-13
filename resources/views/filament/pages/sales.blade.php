<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="col-span-1 md:col-span-2">
            {{ $this->table }}
        </div>
        <div>
            aa
        </div>
    </div>


    @if ($this->day)
        @livewire('general-sales-table')
    @endif

    @push('scripts')
        <script>
            let timers = {};

            document.addEventListener('livewire:initialized', () => {
                Livewire.on("startTimer", (data) => {
                    let cell = document.querySelector(`.timer-cell[data-id="${data.id}"]`);
                    if (cell && data.startTime) {
                        cell.dataset.startTime = data.startTime;
                        startTimer(cell, data.startTime, data.id);
                    }
                });

                Livewire.on("stopTimer", (data) => {
                    stopTimer(data.id);
                });

                function startAllTimers() {
                    document.querySelectorAll(".timer-cell").forEach(cell => {
                        let startTime = cell.dataset.startTime ? parseInt(cell.dataset.startTime) : null;
                        let id = cell.dataset.id;

                        if (startTime && !isNaN(startTime) && startTime <= Date.now()) {
                            if (timers[id]) {
                                clearInterval(timers[id]);
                            }

                            startTimer(cell, startTime, id);
                        }
                    });
                }

                function startTimer(element, startTime, id) {
                    if (!startTime || isNaN(startTime) || startTime > Date.now()) {
                        element.textContent = "-";
                        return;
                    }

                    function updateTime() {
                        let diff = Math.floor((Date.now() - startTime) / 1000);

                        if (diff < 0) {
                            element.textContent = "-";
                            return;
                        }

                        let hours = Math.floor(diff / 3600).toString().padStart(2, "0");
                        let minutes = Math.floor((diff % 3600) / 60).toString().padStart(2, "0");
                        let seconds = (diff % 60).toString().padStart(2, "0");

                        element.textContent = `${hours}:${minutes}:${seconds}`;
                    }

                    updateTime();
                    timers[id] = setInterval(updateTime, 1000);
                }

                function stopTimer(id) {
                    console.log("Deteniendo temporizador para ID:", id);

                    if (timers[id]) {
                        clearInterval(timers[id]);
                        delete timers[id];
                    }

                    let cell = document.querySelector(`.timer-cell[data-id="${id}"]`);
                    if (cell) {
                        console.log("Celda encontrada, actualizando contenido...");
                        cell.textContent = "-";
                        cell.removeAttribute("data-start-time");
                    } else {
                        console.warn("No se encontró la celda con ID:", id);
                    }
                }

                startAllTimers();
            });
        </script>
    @endpush

    <x-filament-actions::modals />
</x-filament-panels::page>
