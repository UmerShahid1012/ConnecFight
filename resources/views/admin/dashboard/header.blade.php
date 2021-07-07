<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="{{route('admin.dashboard')}}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>OF</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>CNF</b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                            <img src="{{url(isset(Auth::guard('admin')->user()->profile_image)?Auth::guard('admin')->user()->profile_image:asset('/adminlte/dist/img/user2-160x160.jpg'))}}" class="user-image" alt="User Image">
                            <span class="hidden-xs">{{Auth::guard('admin')->user()->name}}</span>


                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                               <img src="{{url(isset(Auth::guard('admin')->user()->profile_image)?Auth::guard('admin')->user()->profile_image:asset('/adminlte/dist/img/user2-160x160.jpg'))}}"

{{--                                @if(!Auth::guard('admin')->user()->profile_image)--}}
{{--                                    <img src="{{asset('/adminlte/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">--}}

{{--                                @else--}}
{{--                                    <img src="{{Auth::guard('admin')->user()->profile_image}}" class="img-circle" alt="User Image">--}}


{{--                                @endif--}}


                                @if (Auth::guard('admin')->check())
                                    <p>
                                    {{Auth::guard('admin')->user()->name}}
                                    {{-- <small>Member since Nov. 2012</small> --}}
                                </p>
                                @else
                                      <p>
                                    N/A

                                </p>
                                @endif


                            </li>
                            <!-- Menu Body -->

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    @if(Auth::guard('admin')->check())
                                        <a href="{{route('admin.profile')}}" class="btn btn-default btn-flat"><i class="fa fa-user" aria-hidden="true"></i></a>
                                    @endif

                                </div>
                                <div class="pull-left">
                                    @if(Auth::guard('admin')->check())
                                        <a href="{{route('admin.profile')}}" class="btn btn-default btn-flat"><i class="fa fa-key" aria-hidden="true"></i>
                                        </a>
                                    @endif

                                </div>
                                <div class="pull-left">
                                    @if(Auth::guard('admin')->check())
                                        <a href="{{route('admin.logout')}}" class="btn btn-default btn-flat"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
<!--                     <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li> -->
                </ul>
            </div>
        </nav>
    </header>
