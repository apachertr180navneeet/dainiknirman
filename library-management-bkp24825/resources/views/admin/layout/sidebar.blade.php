<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('public/dist/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity: 0.8;" />
        <span class="brand-text font-weight-light">DainikNirman</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('public/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ $user_data->name ?? 'User' }}</a>
            </div>
        </div>
        @php
        $user = auth()->user();
        $routeParameters = request()->route()->parameters();
        $currentPrefix = request()->route()->action['prefix'];
        $currentPrefix = substr($currentPrefix, strpos($currentPrefix, '/')+1, strlen($currentPrefix));
        $masterOpenClass = '';
        $masterActiveClass = '';
        $masterModules = [
            'categories',
            'services',
            'banners'
        ]
        @endphp

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{route('admin.dashboard')}}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p class="text">Dashboard</p>
                    </a>
                </li>

                @if(in_array($currentPrefix, $masterModules))
                    @php 
                    $masterOpenClass = 'menu-open';
                    $masterActiveClass = 'active';
                    @endphp
                @endif
                {{-- <li class="nav-item {{$masterOpenClass}}">
                    <a href="#" class="nav-link {{$masterActiveClass}}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Master
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if(auth()->user()->hasPermissionTo('View User'))
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link {{$currentPrefix == 'users' ? 'active' : ''}}">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>
                                    Users
                                </p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li> --}}

                @if(auth()->user()->hasPermissionTo('View User'))
                <li class="nav-item">
                    <a href="{{route('admin.users.index')}}" class="nav-link {{$currentPrefix == 'users' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Users
                        </p>
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('View Book'))
                <li class="nav-item">
                    <a href="{{route('admin.books.index')}}" class="nav-link {{$currentPrefix == 'books' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                            Books
                        </p>
                    </a>
                </li>
                @endif

                <!-- <li class="nav-item">
                    <a href="javascript:;" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Settings
                        </p>
                    </a>
                </li> -->
                
                <li class="nav-item">
                    <a href="{{route('admin.logout')}}" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
