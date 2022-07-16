@extends('adminlte::page')

@section('title', 'AZ WORLD')

@section('content_header')
    <h1 class="m-0 text-dark">List users banned</h1>
@stop



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="col-md-4 offset-md-8">
                    <form action="" method="GET">
                        <div class="input-group input-group-lg">
                            <input type="search" class="form-control form-control-lg" name="user_search"
                                placeholder="Enter email or address... ">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                @if (!empty($success))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> Success!</h5>
                        {{ $success }}
                    </div>
                @endif
                @if (!empty($error))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-ban"></i> Alert!</h5>
                        {{ $error }}
                    </div>
                @endif
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>User Id</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Balance</th>
                                <th>Pending</th>
                                <th>Frozen</th>
                                <th>Role</th>
                                <th>Date</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td></td>
                                    <td class="id">{{ $user->id }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td> <a href="https://bscscan.com/token/0x1f2cfde19976a2bf0a250900f7ace9c362908c93?a={{ $user->address }}"
                                            target="_blank"> {{ $user->address }}</a></td>
                                    <td>{{ $user->balance }}</td>
                                    <td>{{ $user->pending_balance }}</td>
                                    <td>{{ $user->frozen }}</td>
                                    <td>{{ $user->is_admin == 1 ? 'Admin' : 'User' }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>Banned</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-2">
                        @php
                            $query = isset($_GET) ? $_GET : [];
                        @endphp
                        {{ $users->appends($query)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

        });
    </script>

@stop
