<x-modalB modalId="PermissionsRol" title="Gestion de permisos ({{ $rol->name }})" modalTitle="modalTitle">
    <form id="FormPermissionsRol" action="{{ route('rol.permissionRol') }}" method="POST">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="rol_id" value="{{ $rol->id }}">
            @if (empty($array))
                <div class="flex justify-center">
                    No existen permisos asociados a este rol
                </div>
            @else
                <div class="accordion" id="accordionRol">
                    <div class="accordion-item">
                        @foreach ($array as $permission)
                            <h2 class="accordion-header" id="heading_{{ $permission['permissionG'] }}">
                                <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse_{{ $permission['permissionG'] }}" aria-expanded="true"
                                    aria-controls="collapse_{{ $permission['permissionG'] }}">
                                    {{ __($permission['namePermissionG']) }}
                                </button>
                            </h2>
                            @if (!empty($permission['permissions']))
                                @foreach ($permission['permissions'] as $array)
                                    <div id="collapse_{{ $permission['permissionG'] }}"
                                        class="accordion-collapse collapse"
                                        aria-labelledby="heading_{{ $permission['permissionG'] }}"
                                        data-bs-parent="#accordionRol">
                                        <div class="accordion-body">
                                            <div class="flex justify-between">
                                                {{ __($array['name']) }}
                                                <x-jet-input type="checkbox" style="transform: scale(1.2);"
                                                    class="float-right"
                                                    field_check="{{ in_array($array['id'], $rolPerms) ? true : false }}"
                                                    name="check_{{ $array['id'] }}" id="check_{{ $array['id'] }}">
                                                </x-jet-input>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="collapse_{{ $permission['permissionG'] }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading_{{ $permission['permissionG'] }}"
                                    data-bs-parent="#accordionRol">
                                    <div class="accordion-body">
                                        <div class="flex justify-center">
                                            No existen permisos asociados a este rol
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</x-modalB>
