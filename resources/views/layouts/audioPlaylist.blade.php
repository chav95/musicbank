<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">

    <title>{{preg_replace('/\b(\w)|./', '$1', ucwords(str_replace('_', ' ', config('app.name'))))}}</title>
  </head>
  <body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper" id="app">
      <upload-music></upload-music>
      <create-playlist></create-playlist>
    
      <!-- Main Header -->
      <header class="main-header">
    
        <!-- Logo -->
        <a href="index2.html" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>A</b>LT</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">{{preg_replace('/\b(\w)|./', '$1', ucwords(str_replace('_', ' ', config('app.name'))))}}</span>
        </a>
    
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <!--<button class="btn">
            <i class="fas fa-bars"></i>
          </div>-->
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
    
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
    
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="{{asset('storage/app_image/profile_default.png')}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p>{{ucwords(Auth::user()->name)}}</p>
              <!-- Status -->
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
    
          <!-- search form (Optional) -->
          <!--<form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                  <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                  </button>
                </span>
            </div>
          </form>-->
          <!-- /.search form -->
    
          <!-- Sidebar Menu -->
          <ul class="sidebar-menu" data-widget="tree">
            <li class="header">Main Menu</li>
            <li>
                <a href="" data-toggle="modal" data-target="#UploadMusic">
                  <i class="fas fa-upload nav-icon fa-fw"></i>
                  <span>Upload</span>
                </a>
              </li>
            <!--<li>
              <router-link to="/all-music">
                <i class="fas fa-database nav-icon color-blue fa-fw"></i>
                <span>All Music</span>
              </router-link>
            </li>-->
            <li>
              <router-link to="/wishlist">
                <i class="nav-icon fas fa-headphones color-purple fa-fw"></i>
                <span>Wishlist</span>
              </router-link>
            </li>
            <li>
              <router-link to="/log">
                <i class="nav-icon fas fa-clipboard-list color-orange fa-fw"></i>
                <span>Log</span>
              </router-link>
            </li>
            <li>
              <router-link to="/report">
                <i class="fas fa-chart-line nav-icon color-blue fa-fw"></i>
                <span>Report</span>
              </router-link>
            </li>
            @if(Auth::user()->privilege == 1)
              <li>
                <router-link to="/manage-users">
                  <i class="nav-icon fas fa-cog color-green fa-fw"></i>
                  <span>Manage Users</span>
                </router-link>
              </li>
            @endif
            <li class="treeview">
              <!--<a href="#" class="fa fa-link active">-->
              <router-link to="/playlist" class="playlist-container">
                <i class="nav-icon fas fa-compact-disc color-yellow fa-fw"></i>
                <span>Playlist</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </router-link>
              <playlist-sidebar :playlist="{{$playlist}}"></playlist-sidebar>            
            </li>
            <li>
              <a href="{{ route('logout') }}">
                <i class="fas fa-power-off nav-icon color-red fa-fw"></i>
                <span>{{ __('Logout') }}</span>
              </a>
            </li>
          </ul>
          <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>
    
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <router-view></router-view>
      </div>
      <!-- /.content-wrapper -->
    
      <!-- Main Footer -->
      <footer class="main-footer">
        <!-- To the right -->
        <div class="pull-right hidden-xs">
          Anything you want
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2016 <a href="#">Company</a>.</strong> All rights reserved.
      </footer>
    
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
          <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
          <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <!-- Home tab content -->
          <div class="tab-pane active" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Activity</h3>
            <ul class="control-sidebar-menu" data-widget="tree">
              <li>
                <a href="javascript:;">
                  <i class="menu-icon fa fa-birthday-cake bg-red"></i>
    
                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
    
                    <p>Will be 23 on April 24th</p>
                  </div>
                </a>
              </li>
            </ul>
            <!-- /.control-sidebar-menu -->
    
            <h3 class="control-sidebar-heading">Tasks Progress</h3>
            <ul class="control-sidebar-menu">
              <li>
                <a href="javascript:;">
                  <h4 class="control-sidebar-subheading">
                    Custom Template Design
                    <span class="pull-right-container">
                        <span class="label label-danger pull-right">70%</span>
                      </span>
                  </h4>
    
                  <div class="progress progress-xxs">
                    <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                  </div>
                </a>
              </li>
            </ul>
            <!-- /.control-sidebar-menu -->
    
          </div>
          <!-- /.tab-pane -->
          <!-- Stats tab content -->
          <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
          <!-- /.tab-pane -->
          <!-- Settings tab content -->
          <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
              <h3 class="control-sidebar-heading">General Settings</h3>
    
              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Report panel usage
                  <input type="checkbox" class="pull-right" checked>
                </label>
    
                <p>
                  Some information about this general settings option
                </p>
              </div>
              <!-- /.form-group -->
            </form>
          </div>
          <!-- /.tab-pane -->
        </div>
      </aside>
      <!-- /.control-sidebar -->
      <!-- Add the sidebar's background. This div must be placed
      immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <script src="{{asset('js/app.js')}}"></script>
  </body>
</html>