
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ auth()->user()->getImageUrlAttribute() }}" alt="{{auth()->user()->name}}" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">{{ auth()->user()->name }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
       
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               
                <li class="nav-item has-treeview menu-open">
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin') }}" class="nav-link {{ Route::is('admin.') ? 'active' : '' }}">
                                <i class="fas fa-home nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        @can('view user')
                        <li class="nav-item">
                            <a href="{{ url('admin/user') }}" class="nav-link {{ Route::is('admin.user.*') || Route::is('admin.user.*') || Route::is('admin.profile.*') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>User</p>
                            </a>
                        </li>
                        @endcan

                        @can('view role')
                        <li class="nav-item has-treeview {{ Route::is('admin.role.*') ? 'menu-open' : '' }}">
                            <a href="{{ route('admin.role.index') }}" class="nav-link {{ Route::is('admin.role.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Role & Permission</p>
                            </a>
                        </li>
                        @endcan

                        @can('view order')
                        <li class="nav-item has-treeview {{ Route::is('admin.order.*') ? 'menu-open' : '' }}">
                            <a href="{{ route('admin.order.index') }}" class="nav-link {{ Route::is('admin.order.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Order</p>
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>