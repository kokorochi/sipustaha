<!-- START @SIDEBAR LEFT -->
<aside id="sidebar-left" class="sidebar-circle">
    <!-- Start left navigation - profile shortcut -->
    <div id="tour-8" class="sidebar-content">
        <div class="media">
            <a class="pull-left has-notif avatar">
                <img src="{{$user_info['photo']}}" alt="admin">
                <i class="online"></i>
            </a>
            <div class="media-body">
                <h4 class="media-heading">Hello, <span id='username'>{{$user_info['full_name']}}</span></h4>
                <small>NIP : {{$user_info['username']}}</small>
            </div>
        </div>
    </div><!-- /.sidebar-content -->
    <!--/ End left navigation -  profile shortcut -->

    <!-- Start left navigation - menu -->
    <ul id="tour-9" class="sidebar-menu">

        <!-- Start navigation - dashboard -->
        <li class="submenu {!! Request::is('/', '/') ? 'active' : null !!}">
            <a href="{{url('/')}}">
                <span class="icon"><i class="fa fa-cloud-upload"></i></span>
                <span class="text">Pustaha</span>
                {!! Request::is('/', '/') ? '<span class="selected"></span>' : null !!}
            </a>
        </li>

        @can('approval-menu')
            <li class="submenu {!! Request::is('approvals', 'approvals/*') ? 'active' : null !!}">
                <a href="{{url('approvals')}}">
                    <span class="icon"><i class="fa fa-cloud-download"></i></span>
                    <span class="text">Approvals</span>
                    {!! Request::is('approvals', 'approvals/*') ? '<span class="selected"></span>' : null !!}
                </a>
            </li>
        @endcan

        @can('admin-menu')
            <li class="submenu {!! Request::is('pustahas/report') ? 'active' : null !!}">
                <a href="{{url('pustahas/report')}}">
                    <span class="icon"><i class="fa fa-line-chart"></i></span>
                    <span class="text">Report</span>
                    {!! Request::is('pustahas/report') ? '<span class="selected"></span>' : null !!}
                </a>
            </li>
            <li class="submenu {!! Request::is('users', 'users/*') ? 'active' : null !!}">
                <a href="javascript:void(0);">
                    <span class="icon"><i class="fa fa-lock"></i></span>
                    <span class="text">Admin </span>
                    <span class="arrow"></span>
                    {!! Request::is('users', 'users/*') ? '<span class="selected"></span>' : null !!}
                </a>
                <ul>
                    <li><a href="{{url('users')}}">User</a></li>
                </ul>
            </li>
        @endcan
        <!--/ End navigation - dashboard -->
    </ul>

</aside><!-- /#sidebar-left -->
<!--/ END SIDEBAR LEFT -->