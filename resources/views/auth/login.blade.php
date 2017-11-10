@extends('layouts.login', ['title' => trans('auth.login.title')])

@section('content')
<h3>{{ trans('auth.login.title') }}</h3>

<div class="panel-body">
    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ trans('auth.login.fields.email') }}" required autofocus>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" required placeholder="{{ trans('auth.login.fields.password') }}">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        {{ trans('auth.login.remember_me') }}
                    </label>
                </div>
            </div>
            <div class="col-xs-4">
                <button type="submit" class="btn pull-right btn-primary">
                    {{ trans('auth.login.submit') }}
                </button>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">

            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-md-12">
            <div class="social-auth-links text-center">
                <p>- {{ trans('auth.login.or') }} -</p>
                <a href="{{ route('auth.provider', ['provider' => 'facebook']) }}" class="btn btn-block btn-facebook"><i class="fa fa-facebook"></i> {{ trans('auth.login.login_with_facebook') }}</a>
            </div>

            <a class="btn btn-link" href="{{ route('password.request') }}">
                {{ trans('auth.login.password_forgotten') }}
            </a>
            <a class="btn btn-link" href="{{ route('register') }}">
                {{ trans('auth.login.new_account') }}
            </a>

        </div>
    </div>
</div>
@endsection