@php
  $setting = App\Models\Setting::first();
  $image = $setting ? asset($setting->site_image) : asset('login-default-image.jpg');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('theme/dashboard/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('theme/dashboard/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('theme/dashboard/dist/css/adminlte.min.css')}}">
  
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
    }
    
    .split-container {
      display: flex;
      height: 100vh;
      width: 100vw;
    }
    
    .image-side {
      flex: 1;
      position: relative;
      overflow: hidden;
    }
    
    .image-side img {
      width: 100%;
      height: 100%;
      object-fit: contain; /* This ensures the entire image is visible */
      object-position: center;
      max-width: 100%;
      max-height: 100%;
    }
    
    .form-side {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f4f6f9;
      padding: 2rem;
    }
    
    .login-box {
      width: 100%;
      max-width: 400px;
      margin: 0;
    }
    
    .login-card-body {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      padding: 2rem;
    }
    
    .login-logo {
      margin-bottom: 2rem;
      text-align: center;
    }
    
    .login-logo a {
      font-size: 2rem;
      font-weight: bold;
      color: #333;
    }
    
    .login-box-msg {
      text-align: center;
      margin-bottom: 1.5rem;
    }
    
    @media (max-width: 768px) {
      .split-container {
        flex-direction: column;
      }
      
      .image-side {
        height: 30vh;
      }
      
      .form-side {
        height: 70vh;
      }
    }
  </style>
</head>
<body>
<div class="split-container">
  <div class="image-side">
    <img src="{{$image}}" alt="Login Image">
  </div>
  <div class="form-side">
    <div class="login-box">
      <div class="login-logo">
        <a>{{getSiteName()}}</a>
      </div>
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">{{ trans('admin.Login') }}</p>

          <form action="" method="post">
            @csrf
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="username" name="username">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" placeholder="Password" name="password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block form-control">{{ trans('admin.Sign In') }}</button>
              </div>
            </div>
          </form>
          
          @if (session('error'))
            <div class="mt-3 text-center text-danger">
              username or password is wrong
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="{{ asset('theme/dashboard/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('theme/dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('theme/dashboard/dist/js/adminlte.min.js')}}"></script>
</body>
</html>