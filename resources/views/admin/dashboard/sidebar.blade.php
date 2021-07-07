<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{url(isset(Auth::guard('admin')->user()->profile_image)?Auth::guard('admin')->user()->profile_image:asset('/adminlte/dist/img/user2-160x160.jpg'))}}" class="img-circle" alt="User Image">

            </div>
            <div class="pull-left info">
                <p>{{Auth::guard('admin')->user()->name}}</p>
                <a href=""><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <?php $segment = Request::segment(2);?>


            <li class="<?=$segment == 'admin' ? 'active' : '' ?>">
                <a href="{{route('admin.dashboard')}}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li class="treeview <?= $segment == 'users' ? 'active' : '' ?>"
                style="height: auto;">
                <a href="#">
                    <i class="fa fa-user"></i>
                    <span>Users</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
{{--                    <li><a href="{{ route('admin.product.add') }}"><i class="fa fa-circle-o"></i> Add Product</a></li>--}}
                    <li><a href="{{ route('admin.users') }}"><i class="fa fa-circle-o"></i> All Users</a></li>
                    <li><a href="{{ route('admin.matchmakers') }}"><i class="fa fa-circle-o"></i> Matchmakers</a></li>
                    <li><a href="{{ route('admin.athletes') }}"><i class="fa fa-circle-o"></i> Athletes</a></li>
                </ul>

            </li>
                <li class="treeview <?= $segment == 'fights' ? 'active' : '' ?>"
                    style="height: auto;">
                    <a href="#">
                        <i class="fa fa-user"></i>
                        <span>Fights</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu" style="display: none;">
                        {{--                    <li><a href="{{ route('admin.product.add') }}"><i class="fa fa-circle-o"></i> Add Product</a></li>--}}
                        <li><a href="{{ route('admin.sparrings') }}"><i class="fa fa-circle-o"></i> Sparring</a></li>
                        <li><a href="{{ route('admin.fights') }}"><i class="fa fa-circle-o"></i> Fight</a></li>
                        <li><a href="{{ route('admin.highlights') }}"><i class="fa fa-circle-o"></i> Highlights</a></li>
                    </ul>
                </li>


                <li class="treeview <?= $segment == 'list' ? 'active' : '' ?>"
                    style="height: auto;">
                    <a href="#">
                        <i class="fa fa-user"></i>
                        <span>Lists</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu" style="display: none;">
                        <li><a href="{{ route('admin.tags') }}"><i class="fa fa-circle-o"></i> Tags</a></li>
                        <li><a href="{{ route('admin.stances') }}"><i class="fa fa-circle-o"></i>Stances</a></li>
                        <li><a href="{{ route('admin.events') }}"><i class="fa fa-circle-o"></i> Events</a></li>
                        <li><a href="{{ route('admin.statuses') }}"><i class="fa fa-circle-o"></i> Statuses</a></li>
                    </ul>
                </li>
                <li class="treeview <?= $segment == 'plans' ? 'active' : '' ?>"
                    style="height: auto;">
                    <a href="#">
                        <i class="fa fa-user"></i>
                        <span>Plans</span>
                        <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu" style="display: none;">
                        <li><a href="{{ route('admin.add.plan') }}"><i class="fa fa-circle-o"></i>Add</a></li>
                        <li><a href="{{ route('admin.plans') }}"><i class="fa fa-circle-o"></i>Plans</a></li>
                    </ul>
                </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<!-- =============================================== -->
