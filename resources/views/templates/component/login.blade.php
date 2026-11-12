<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Click Me</div>
              </div>
              <div class="form">
                <h3>Login to your account</h3>

                @if((old('_form') === 'login' || !old('_form')) && ($errors->has('login_error') || $errors->has('email') || $errors->has('password')))
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 8px 12px; text-align: left;">
                    @if($errors->has('login_error'))
                      <div>{{ $errors->first('login_error') }}</div>
                    @else
                      <ul style="margin: 0; padding-left: 20px;">
                        @foreach (['email', 'password'] as $field)
                          @if($errors->has($field))
                            <li>{{ $errors->first($field) }}</li>
                          @endif
                        @endforeach
                      </ul>
                    @endif
                  </div>
                @endif

                <form action="{{ route('login') }}" method="post">
                  @csrf
                  <input type="hidden" name="_form" value="login">
                  <input type="email" name="email" placeholder="Email Address" value="{{ (old('_form') === 'login' || !old('_form')) ? old('email') : '' }}" required="">
                  <input type="password" name="password" placeholder="Password" required="">
                  <div style="margin-bottom: 15px; text-align: left;">
                    <label style="font-weight: normal; font-size: 13px; cursor: pointer; color: #555;">
                      <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} style="margin-right: 5px; vertical-align: middle;"> Ingat saya
                    </label>
                  </div>
                  <input type="submit" value="Login">
                </form>
              </div>

              <div class="form">
                <h3>Create an account</h3>

                @if(old('_form') === 'register' && $errors->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 8px 12px; text-align: left;">
                    <ul style="margin: 0; padding-left: 20px;">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('register') }}" method="post">
                  @csrf
                  <input type="hidden" name="_form" value="register">
                  <input type="text" name="name" placeholder="Full Name" value="{{ old('_form') === 'register' ? old('name') : '' }}" required="">
                  <input type="email" name="email" placeholder="Email Address" value="{{ old('_form') === 'register' ? old('email') : '' }}" required="">
                  <input type="password" name="password" placeholder="Password (min. 8 characters)" required="">
                  <input type="password" name="password_confirmation" placeholder="Confirm Password" required="">
                  <input type="submit" value="Register">
                </form>
              </div>

              <div class="cta"><a href="#">Forgot your password?</a></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){
    @if($errors->any() || session('open_modal'))
      $('#myModal').modal('show');
      @if(old('_form') === 'register' || session('open_modal') === 'register')
        $('.form-module .toggle i').removeClass('fa-pencil');
        $('.form-module .form').first().hide();
        $('.form-module .form').last().show();
      @endif
    @endif

    $('.toggle').click(function(){
      // Switches the Icon
      $(this).children('i').toggleClass('fa-pencil');
      // Switches the forms  
      $('.form').animate({
        height: "toggle",
        'padding-top': 'toggle',
        'padding-bottom': 'toggle',
        opacity: "toggle"
      }, "slow");
    });
  });
</script>
