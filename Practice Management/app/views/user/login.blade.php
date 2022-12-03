<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Login Form</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{asset('bootstrap-files/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('css/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('css/ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <script type="text/javascript" src="{{asset('js/jquery-1.9.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap.min.js')}}"></script>

</head>

<body>
<div class="cleaner-h5"></div>
<div class="cleaner-h5"></div>
<div class="cleaner-h5"></div>


<div class="login">
    @include('layout/flashMessages')
    {{Form::open()}}
    <label>User Name</label>
    <input type="text" name="user_name" class="name"/>

    <div class="cleaner-h1"></div>

    <label>Password</label>
    <input type="password" name="password" class="name"/>

    <div class="cleaner-h1"></div>

    <div class="cleaner-h1"></div>

    <button type="submit" class="btn btn-info">Login</button>

    {{Form::close()}}
</div>
<!--end login-->

</body>
</html>
