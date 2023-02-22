<div class="vertical-menu">
    <div data-simplebar class="h-100">
        @php
            $LogUser = Auth::user();
            $permissions = VerifyPermissions($LogUser, [1, 2, 3, 4, 5]);
        @endphp
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="fas fa-home mr-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if ($permissions[1])
                    <li>
                        <a href="{{ route('settings.index') }}" class="waves-effect"><i class="fas fa-cogs mr-2"></i>
                            <span>Configuraciones</span></a>
                    </li>
                @endif

                @if ($permissions[2])
                    <li>
                        <a href="{{ route('users.index') }}" class="waves-effect"><i class="fas fa-users mr-2"></i>
                            <span>Gestion usuarios</span></a>
                    </li>
                @endif

                @if ($permissions[3])
                    <li>
                        <a href="{{ route('roles.index') }}" class="waves-effect"><i
                                class="fas fa-people-carry mr-2"></i> <span>Gestion roles</span></a>
                    </li>
                @endif

                @if ($permissions[4])
                    <li>
                        <a href="{{ route('products.index') }}" class="waves-effect"><i class="fas fa-store mr-2"></i>
                            <span>Inventario</span></a>
                    </li>
                @endif

                @if ($permissions[5])
                    <li>
                        <a href="{{ route('tables.index') }}" class="waves-effect"><i class="fas fa-table mr-2"></i>
                            <span>Gestion de Mesas</span></a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('sales.index') }}" class="waves-effect"><i class="fas fa-dollar-sign mr-2"></i>
                        <span>Ventas</span></a>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        <span>Historiales</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li>
                            <a href="{{ route('sales.detail_sale') }}" class="waves-effect">
                                <span>Ventas</span></a>
                        </li>
                        @if (in_array($user->rol_id, [1, 2]))
                            <li>
                                <a href="{{ route('history.inventoryDiscount') }}"
                                    class="waves-effect"><span>Inventario</span></a>
                            </li>
                        @endif
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
