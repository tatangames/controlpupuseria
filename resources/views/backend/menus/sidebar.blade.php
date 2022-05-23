<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight-light">Panel Web</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('index.estadisticas') }}" target="frameprincipal" class="nav-link">
                        <i class="fas fa-edit nav-icon"></i>
                        <p>Estadísticas</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-list-alt"></i>
                        <p>
                            Ordenes
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ route('index.ordenes.hoy') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ordenes Hoy</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.ordenes') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listado de Ordenes</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('index.motoristas.ordenes') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Motoristas Ordenes</p>
                            </a>
                        </li>
                    </ul>

                </li>

            </ul>
        </nav>

    </div>
</aside>






