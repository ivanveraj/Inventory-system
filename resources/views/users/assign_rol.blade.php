<x-modalB modalId="assignRolM" title="Asignar rol de usuario" modalTitle="modalTitle">
    <form id="FormAssignRol" action="{{ route('user.change_rol') }}" method="POST">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" value="{{ $user->id }}">
            <div class="grid grid-cols-1 gap-4 md:px-28 sm:px-0">
                <div class="w-full">
                    <x-jet-label value="Rol"></x-jet-label>
                    <select id="rol_user" name="rol_id" class="form-control">
                        @foreach ($rols as $rol)
                            <option value="{{ $rol->id }}" {{ $rol->id == $user->rol_id ? 'selected' : '' }}>
                                {{ $rol->name }}</option>
                        @endforeach
                    </select>
                    <ul class="parsley-errors-list filled" data-error="rol_id">
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</x-modalB>
