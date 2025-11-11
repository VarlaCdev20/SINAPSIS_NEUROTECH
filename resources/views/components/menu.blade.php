<x-guest-layout>
    <div x-data="{ sidebarOpen: true, mobileOpen: false }" class="flex min-h-screen bg-gray-100 text-gray-800">

        <!-- SIDEBAR -->
        <aside
            class="fixed top-0 left-0 z-40 h-screen bg-white border-r border-gray-200 shadow-md flex flex-col transition-all duration-300"
            :class="sidebarOpen ? 'w-64' : 'w-20'">

            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <a href="/dashboard" class="flex items-center space-x-2">
                    <img src="{{ asset('img/icon.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                    <span x-show="sidebarOpen" class="font-bold text-lg text-accent-600 tracking-wide">SINAPSIS</span>
                </a>
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-1.5 rounded-md text-gray-600 hover:bg-accent-500/10 transition">
                    <i class="bi bi-chevron-left text-lg transition-transform"
                        :class="{ 'rotate-180': !sidebarOpen }"></i>
                </button>
            </div>

            <!-- Menú principal -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                <p x-show="sidebarOpen" class="uppercase text-xs text-gray-500 mb-2 font-semibold tracking-wide">
                    Menú principal
                </p>

                @can('Panel')
                    <a href="/dashboard"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition-all duration-200 group">
                        <i
                            class="bi bi-speedometer2 text-lg text-accent-500 group-hover:scale-110 transition-transform"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Panel</span>
                    </a>
                @endcan

                @can('Gestion_usuarios')
                    <!-- Gestión de Usuarios -->
                    <div x-data="{ openUsers: false }" class="space-y-1">
                        <button @click="openUsers = !openUsers"
                            class="flex items-center justify-between w-full px-2 py-2 rounded-lg text-gray-700 hover:bg-accent-500/10 transition">
                            <div class="flex items-center space-x-2">
                                <i class="bi bi-people-fill text-accent-500 text-lg"></i>
                                <span x-show="sidebarOpen" class="font-medium text-sm">Gestión de Usuarios</span>
                            </div>
                            <i class="bi bi-chevron-down text-gray-600 text-sm transition-transform duration-300"
                                x-show="sidebarOpen" :class="{ 'rotate-180': openUsers }"></i>
                        </button>

                        <div x-show="openUsers" x-collapse x-cloak class="pl-6 mt-1 space-y-1">
                            <a href="{{ route('users.index') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-accent-500/10 hover:text-accent-600 rounded-lg transition">
                                <i class="bi bi-person-lines-fill mr-2 text-accent-500"></i>
                                Usuarios Administrativos
                            </a>

                            <a href="{{ route('roles.index') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-accent-500/10 hover:text-accent-600 rounded-lg transition">
                                <i class="bi bi-person-badge mr-2 text-accent-500"></i>
                                Roles
                            </a>

                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-accent-500/10 hover:text-accent-600 rounded-lg transition">
                                <i class="bi bi-lock-fill mr-2 text-accent-500"></i>
                                Permisos
                            </a>
                        </div>
                    </div>
                @endcan

                @can('Pacientes')
                    <a href="{{ route('pacientes.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-person-lines-fill text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Pacientes</span>
                    </a>
                @endcan

                @can('Mis_Pacientes')
                    <a href="{{ route('mis_pacientes.listar') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-person-lines-fill text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Mis Pacientes</span>
                    </a>
                @endcan

                @can('Agenda')
                    <a href="{{ route('admin.agenda') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-calendar-event text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Agenda</span>
                    </a>
                @endcan

                @can('Episodios')
                    <a href="#"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-activity text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Episodios</span>
                    </a>
                @endcan

                @can('Historial_Clinico')
                    <a href="#"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-clipboard-heart text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Historiales Clinicos</span>
                    </a>
                @endcan

                @can('Calendario_Medico')
                    <a href="{{ route('medico.agenda') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-calendar4-week text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Calendario</span>
                    </a>
                @endcan

                @can('Calendario_Paciente')
                    <a href="{{ route('paciente.agenda') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-calendar4-week text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Mi Agenda</span>
                    </a>
                @endcan

                @can('Solicitudes')
                    <a href="{{ route('medico.solicitudes.listar') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-envelope-check text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Solicitudes</span>
                    </a>
                @endcan

                @can('Reseñas')
                    <a href="#"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-star text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Reseñas</span>
                    </a>
                @endcan

                @can('Mis_Episodios')
                    <a href="#"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-clipboard-heart text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Mis Episodios</span>
                    </a>
                @endcan

                @can('Mi_Historial_Clinico')
                    <a href="#"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-clipboard-heart text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Mi Historial</span>
                    </a>
                @endcan

                @can('Reportes_Administrativos')
                    <a href="#"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-bar-chart-line text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Reportes</span>
                    </a>
                @endcan

                @can('Reportes_Medicos')
                    <a href="#"
                        class="flex items-center p-2 rounded-lg hover:bg-accent-500/10 text-gray-700 transition">
                        <i class="bi bi-clipboard-heart text-accent-500 text-lg"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Reportes</span>
                    </a>
                @endcan

                @can('Ajustes')
                    <div x-data="{ open: false }" class="mt-3 space-y-2">
                        <button @click="open = !open"
                            class="flex items-center justify-between w-full px-2 py-2 rounded-lg text-gray-700 hover:bg-accent-500/10 transition">
                            <div class="flex items-center space-x-2">
                                <i class="bi bi-gear text-accent-500 text-lg"></i>
                                <span x-show="sidebarOpen" class="font-medium text-sm">Ajustes</span>
                            </div>
                            <i class="bi bi-chevron-down text-gray-600 text-sm transition-transform duration-300"
                                x-show="sidebarOpen" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open" x-collapse x-cloak class="pl-6 mt-1 space-y-1">
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-accent-500/10 hover:text-accent-600 rounded-lg transition">
                                <i class="bi bi-person-gear mr-2 text-accent-500"></i>
                                Configuración de perfil
                            </a>

                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-accent-500/10 hover:text-accent-600 rounded-lg transition">
                                <i class="bi bi-shield-lock mr-2 text-accent-500"></i>
                                Cambiar contraseña
                            </a>

                            <form action="/CerrarSesion" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="bi bi-box-arrow-right mr-2 text-red-500"></i>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan
            </nav>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'">

            <!-- NAVBAR -->
            <header
                class="flex items-center justify-between bg-white border-b border-gray-200 shadow-sm px-5 py-3 sticky top-0 z-30">

                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-700 text-2xl">
                    <i class="bi bi-list"></i>
                </button>

                <form class="flex items-center space-x-2 w-1/2">
                    <input type="search" placeholder="Buscar..."
                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-sm px-3 py-1.5 focus:ring-accent-500 focus:border-accent-500 outline-none">
                    <button type="submit" class="p-2 rounded-lg bg-accent-500/10 hover:bg-accent-500/20 transition">
                        <i class="bi bi-search text-accent-600"></i>
                    </button>
                </form>

                <div x-data="{ openUser: false }" class="relative">
                    <button @click="openUser = !openUser"
                        class="flex items-center space-x-2 bg-accent-600 hover:bg-accent-700 text-white px-3 py-1.5 rounded-lg transition shadow-glow">
                        <i class="bi bi-person-circle text-lg"></i>
                        <span class="text-sm font-medium hidden md:inline">
                            {{ Auth::user()->name ?? 'Usuario' }}
                            {{ Auth::user()->paterno . ' ' . Auth::user()->materno }}
                        </span>
                        <i class="bi bi-chevron-down text-xs transition-transform duration-300"
                            :class="{ 'rotate-180': openUser }"></i>
                    </button>

                    <div x-show="openUser" x-collapse x-cloak
                        class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 shadow-md rounded-lg overflow-hidden">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <strong class="block text-gray-800">{{ Auth::user()->name ?? 'Usuario' }}</strong>
                            <small class="text-gray-500">
                                {{ Auth::user()->getRoleNames()->first() ?? 'Sin rol asignado' }}
                            </small>
                        </div>

                        <a href="#"
                            class="block px-4 py-2 text-sm hover:bg-accent-500/10 transition">Configuración del
                            perfil</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto bg-gray-50">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-guest-layout>
