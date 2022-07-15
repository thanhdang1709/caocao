@extends('layouts.app')
@extends('adminlte::page')

@section('title', 'AZ WORLD LOGIN')

@section('content_header')
    <h1 class="m-0 text-dark">Login</h1>
@stop
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="alert alert-error">
                                <ul>
                                    <li>You don't have admin access.</li>
                                </ul>
                            </div>
                        {{-- @if (\Session::has('permission'))

                        @endif --}}
                        {{-- {{ __('You are logged in!') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
