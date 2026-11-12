<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Panel') - OneMovies</title>

  <link href="{{ asset('template/css/bootstrap.css') }}" rel="stylesheet" type="text/css" media="all" />
  <link href="{{ asset('template/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="all" />

  <style>
    body {
      background-color: #f4f6f9;
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    .admin-navbar {
      background-color: #222d32;
      border: none;
      border-radius: 0;
      margin-bottom: 0;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .admin-navbar .navbar-brand {
      color: #fff;
      font-weight: bold;
      font-size: 18px;
    }
    .admin-navbar .navbar-brand span {
      color: #FF8D1B;
    }
    .admin-navbar .navbar-nav > li > a {
      color: #b8c7ce;
    }
    .admin-navbar .navbar-nav > li > a:hover {
      color: #fff;
      background-color: #1a2226;
    }
    .admin-wrapper {
      display: flex;
      min-height: calc(100vh - 50px);
    }
    .admin-sidebar {
      width: 230px;
      background-color: #222d32;
      color: #b8c7ce;
      flex-shrink: 0;
    }
    .admin-sidebar .sidebar-heading {
      padding: 12px 15px;
      font-size: 11px;
      text-transform: uppercase;
      color: #4b646f;
      background: #1a2226;
      font-weight: bold;
      letter-spacing: 1px;
    }
    .admin-sidebar ul.nav-sidebar {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .admin-sidebar ul.nav-sidebar > li > a {
      display: block;
      padding: 12px 15px;
      color: #b8c7ce;
      text-decoration: none;
      border-left: 3px solid transparent;
      transition: all 0.2s ease;
    }
    .admin-sidebar ul.nav-sidebar > li > a:hover,
    .admin-sidebar ul.nav-sidebar > li.active > a {
      color: #ffffff;
      background-color: #1e282c;
      border-left-color: #FF8D1B;
    }
    .admin-sidebar ul.nav-sidebar > li > a i {
      margin-right: 10px;
      width: 16px;
      text-align: center;
    }
    .admin-sidebar ul.nav-sidebar > li.disabled > a {
      color: #6c7b88;
      cursor: not-allowed;
    }
    .admin-sidebar ul.nav-sidebar > li.disabled > a:hover {
      background: transparent;
      border-left-color: transparent;
    }
    .admin-content {
      flex-grow: 1;
      padding: 25px 30px;
      overflow-x: auto;
    }
    .content-header {
      margin-bottom: 20px;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 10px;
    }
    .content-header h1 {
      margin: 0;
      font-size: 24px;
      font-weight: 600;
      color: #333;
    }
    .stat-card {
      background: #fff;
      border-radius: 4px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      border-left: 4px solid #FF8D1B;
    }
    .stat-card .stat-icon {
      font-size: 36px;
      margin-right: 20px;
      color: #FF8D1B;
    }
    .stat-card .stat-num {
      font-size: 28px;
      font-weight: bold;
      color: #222;
      line-height: 1;
    }
    .stat-card .stat-label {
      font-size: 13px;
      text-transform: uppercase;
      color: #777;
      margin-top: 5px;
      letter-spacing: 0.5px;
    }
    .panel {
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      border-radius: 4px;
    }
    .table-poster {
      width: 50px;
      height: 70px;
      object-fit: cover;
      border-radius: 3px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .btn-orange {
      background-color: #FF8D1B;
      color: #fff;
      border-color: #e57d15;
    }
    .btn-orange:hover, .btn-orange:focus {
      background-color: #e57d15;
      color: #fff;
    }
    @media (max-width: 768px) {
      .admin-wrapper {
        flex-direction: column;
      }
      .admin-sidebar {
        width: 100%;
      }
    }
  </style>
  @stack('styles')
</head>
<body>

  <!-- Top Navbar -->
  <nav class="navbar navbar-default admin-navbar">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
          One<span>Movies</span> Admin
        </a>
      </div>
      <div class="collapse navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li>
            <a href="{{ route('home') }}" target="_blank">
              <i class="fa fa-external-link"></i> Lihat Website
            </a>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-user-circle"></i> {{ Auth::user()->name }} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                <form method="POST" action="{{ route('logout') }}" id="admin-logout-form" style="display: none;">
                  @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                  <i class="fa fa-sign-out"></i> Logout
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Content Wrapper -->
  <div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-heading">Menu Utama</div>
      <ul class="nav-sidebar">
        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}">
            <i class="fa fa-dashboard"></i> Dashboard
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.films.*') ? 'active' : '' }}">
          <a href="{{ route('admin.films.index') }}">
            <i class="fa fa-film"></i> Film
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.genres.*') ? 'active' : '' }}">
          <a href="{{ route('admin.genres.index') }}">
            <i class="fa fa-tags"></i> Genre
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.casts.*') ? 'active' : '' }}">
          <a href="{{ route('admin.casts.index') }}">
            <i class="fa fa-users"></i> Cast
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.perans.*') ? 'active' : '' }}">
          <a href="{{ route('admin.perans.index') }}">
            <i class="fa fa-id-badge"></i> Peran
          </a>
        </li>
        <li class="{{ request()->routeIs('admin.kritiks.*', 'admin.komentar.*') ? 'active' : '' }}">
          <a href="{{ route('admin.kritiks.index') }}">
            <i class="fa fa-comments"></i> Komentar
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Terjadi kesalahan input:</strong>
          <ul style="margin-top: 5px; margin-bottom: 0; padding-left: 20px;">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  <script src="{{ asset('template/js/jquery-2.1.4.min.js') }}"></script>
  <script src="{{ asset('template/js/bootstrap.min.js') }}"></script>
  @stack('scripts')
</body>
</html>

