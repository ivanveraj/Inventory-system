<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalTitle }}" aria-hidden="true"
    data-bs-backdrop="static">
    <div {{ $attributes->merge(['class' => 'modal-dialog']) }}>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
