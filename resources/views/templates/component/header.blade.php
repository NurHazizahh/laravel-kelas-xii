<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="#" method="post">
        <input type="text" name="Search" placeholder="Search" required="">
        <input type="submit" value="Go">
      </form>
    </div>
    <div class="w3l_sign_in_register">
      <ul>
        @guest
          <li><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>
        @endguest
        @auth
          @if(Auth::user()->isAdmin())
            <li><a href="{{ route('admin.dashboard') }}" style="background:#212121; color:#fff; padding:6px 15px; margin-right:5px; text-decoration:none; text-transform:uppercase; font-weight:bold; display:inline-block;">Dashboard</a></li>
          @endif
          <li><i class="fa fa-user" aria-hidden="true" style="color: #FF8D1B;"></i> <span style="color:#212121; font-weight:bold;">{{ Auth::user()->name }}</span></li>
          <li>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
              @csrf
              <button type="submit" style="background:#FF8D1B; color:#fff; border:none; padding:6px 20px; font-weight:bold; text-transform:uppercase; cursor:pointer;">Logout</button>
            </form>
          </li>
        @endauth
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>
