@extends('adminlte::page')

@section('title', 'AZ WORLD')

@section('content_header')
    <h1 class="m-0 text-dark">List earns</h1>
@stop



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
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
                    <div class="offset-md-8 input-group">
                        {{-- <div class="checkedbox">
                            <label for="">Status: </label>
                            <input type="checkbox" id="vehicle2" name="vehicle2" value="Car">
                            <label for="">đã duyệt</label>
                        </div> --}}
                        <input type="text" name="user_id" id="iser_id" placeholder="User id..."
                            style="width: 100px; margin-right:40px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        {{-- <input type="text" class="form-control float-right" id="reservation"> --}}
                        <input type="text" name="daterange" style="margin-right: 20px;" />
                        <button class="btn btn-primary searchbtn">Search...</button>
                    </div>
                    <br>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>User Id</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Reward</th>
                                <th>Status</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($earns as $key => $earn)
                                <tr>
                                    <td>{{ $earn->id }}</td>
                                    <td>{{ $earn->user_id }}</td>
                                    @if ($earn->user)
                                        <td>{{ $earn->user->email }}
                                            <button user_id="{{ $earn->user_id }}"
                                                class="btn btn-success mr-2 confirm-user">Approve user</button> <button
                                                user_id="{{ $earn->user_id }}"
                                                class="btn btn-danger mr-2 reject-user">Reject user</button>
                                        </td>

                                        <td> <a href="https://bscscan.com/token/0x1f2cfde19976a2bf0a250900f7ace9c362908c93?a={{ $earn->user->address }}"
                                                target="_blank"> {{ $earn->user->address }}</a></td>
                                    @endif
                                    <td class="text-red text-bold">{{ number_format($earn->reward) }}</td>
                                    <td>{{ $earn->status }}</td>
                                    <td>{{ $earn->subject }}</td>
                                    <td>{{ $earn->description }}</td>
                                    <th>{{ $earn->created_at }}</th>
                                    <td class="d-flex"> <button data-id="{{ $earn->id }}"
                                            class="btn btn-success mr-2 confirm">Approve</button> <button
                                            data-id="{{ $earn->id }}"
                                            class="btn btn-danger mr-2 reject">Reject</button> </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-2">
                        @php
                            $query = isset($_GET) ? $_GET : [];
                        @endphp
                        {{ $earns->appends($query)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://adminlte.io/themes/v3/plugins/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            $(function() {
                $('input[name="daterange"]').daterangepicker({
                    opens: 'left'
                }, function(start, end, label) {
                    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') +
                        ' to ' + end.format('YYYY-MM-DD'));
                    var start = start.format('YYYY-MM-DD');
                    var end = end.format('YYYY-MM-DD');

                });
            });

            $('.searchbtn').click(function(){
                alert();
                window.location.href = '/earn/list?from_date=' + start + '&to_date=' + end +
                        '&status=1';
            });


            $(".confirm").click(function(event) {
                var self = this;
                let earn_id = $(this).attr('data-id');
                event.preventDefault();
                Swal.fire({
                    title: 'Do you want to approve the task?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    denyButtonText: `Don't`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/earn/approve_task",
                            type: "POST",
                            data: {
                                earn_id: earn_id,
                            },
                            success: function(response) {
                                if (response.type == "RESPONSE_OK") {
                                    location.reload();
                                }
                            },
                            error: function(error) {
                                console.log(error);
                                Swal.fire('Changes are not saved', error, 'info')
                            }
                        });


                    } else if (result.isDenied) {
                        Swal.fire('Changes are not saved', '', 'info')
                    }
                });
            });


            $(".reject").click(function(event) {
                var self = this;
                let earn_id = $(this).attr('data-id');
                event.preventDefault();
                Swal.fire({
                    title: 'Do you want to reject the task?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'YES',
                    denyButtonText: `NO`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/earn/reject_task",
                            type: "POST",
                            data: {
                                earn_id: earn_id,
                            },
                            success: function(response) {
                                if (response.type == "RESPONSE_OK") {
                                    location.reload();
                                }
                            },
                            error: function(error) {
                                console.log(error);
                                Swal.fire('Changes are not saved', error, 'info')
                            }
                        });


                    } else if (result.isDenied) {
                        Swal.fire('Changes are not saved', '', 'info')
                    }
                });
            });



            $(".confirm-user").click(function(event) {
                var self = this;
                let user_id = $(this).attr('user_id');
                event.preventDefault();
                Swal.fire({
                    title: 'Do you want to approve the task?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    denyButtonText: `Don't`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/earn/approve_user",
                            type: "POST",
                            data: {
                                user_id: user_id,
                            },
                            success: function(response) {
                                if (response.type == "RESPONSE_OK") {
                                    location.reload();
                                }
                            },
                            error: function(error) {
                                console.log(error);
                                Swal.fire('Changes are not saved', error, 'info')
                            }
                        });


                    } else if (result.isDenied) {
                        Swal.fire('Changes are not saved', '', 'info')
                    }
                });
            });


            $(".reject-user").click(function(event) {
                var self = this;
                let user_id = $(this).attr('user_id');
                event.preventDefault();
                Swal.fire({
                    title: 'Do you want to reject the task?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'YES',
                    denyButtonText: `NO`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/earn/reject_user",
                            type: "POST",
                            data: {
                                user_id: user_id,
                            },
                            success: function(response) {
                                if (response.type == "RESPONSE_OK") {
                                    location.reload();
                                }
                            },
                            error: function(error) {
                                console.log(error);
                                Swal.fire('Changes are not saved', error, 'info')
                            }
                        });


                    } else if (result.isDenied) {
                        Swal.fire('Changes are not saved', '', 'info')
                    }
                });
            });
        });
    </script>

    <style>
        .checkedbox {
            margin-right: 50px;
        }
    </style>

@stop
