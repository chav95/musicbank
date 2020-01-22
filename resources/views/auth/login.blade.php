@extends('layouts.auth')

@section('head_title')
    <title>{{ ucwords(str_replace('_', ' ', config('app.name', 'Laravel'))) }} | Log In</title>
@endsection

@section('auth_body')
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="">
                <b>{{strtok(ucwords(config('app.name', 'Laravel')), '_')}}</b> <br>
                {{ucwords(str_replace('_', ' ', strstr(config('app.name', 'Laravel'), '_')))}}
            </a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">LOG IN</p>

            <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input id="email" type="text" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email" required autofocus>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @elseif (isset($_SESSION['message']))
                        <span class="help-block">
                            <strong>{{$_SESSION['message']}}</strong>
                        </span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
</body
@endsection
