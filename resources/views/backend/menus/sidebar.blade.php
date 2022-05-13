<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('images/logologin.png') }}" alt="Logo" class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight-light">Panel Web</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


                <li class="nav-item">

                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Roles y Permisos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.permisos.index') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permisos</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('index.estadisticas') }}" target="frameprincipal" class="nav-link">
                        <i class="fas fa-edit nav-icon"></i>
                        <p>Estadísticas</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Mapa
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.zonas') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Zonas</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Personal
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.afiliados') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Propietarios</p>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.motoristas') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Motoristas</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Servicios
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.bloques') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Servicios</p>
                            </a>
                        </li>

                    </ul>

                </li>


                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-edit"></i>
                        <p>
                            Clientes
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('index.clientes.registrados.hoy') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Registrados Hoy</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('index.clientes.listado') }}" target="frameprincipal" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listado de Clientes</p>
                            </a>
                        </li>



                    </ul>

                </li>




                <!-- fin del acordeon -->
            </ul>
        </nav>




    </div>
</aside>






