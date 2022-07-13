@extends('adminlte::page')

@section('title', 'AZ WORLD')

@section('content_header')
    <h1 class="m-0 text-dark">List withdraw</h1>
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
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>User Id</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th style="width: 180px">Date</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $key => $ticket)
                                <tr>
                                    <td class="id">{{ $ticket->id }}</td>
                                    <td><a href="/ticket/add?user_id={{ $ticket->user_id }}">{{ $ticket->user_id }}</a>
                                    </td>
                                    <td>{{ $ticket->user->email }}</td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td>{{ $ticket->description }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                    <td>
                                        <button class="btn btn-primary change_status">Done</button>
                                    </td>
                                    {{-- <td><button class="btn btn-primary">Reply</button></td> --}}
                                    {{-- <td class="d-flex"> <button data-id="{{ $earn->id }}"
                                            class="btn btn-success mr-2 confirm">Approve</button> <button
                                            data-id="{{ $earn->id }}"
                                            class="btn btn-danger mr-2 reject">Reject</button> </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-2">
                        @php
                            $query = isset($_GET) ? $_GET : [];
                        @endphp
                        {{-- {{ $earns->appends($query)->links('pagination::bootstrap-4') }} --}}
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

            $(".change_status").on("click", function() {
                var id = $(this).closest("tr").find(".id").text();
                event.preventDefault();
                Swal.fire({
                    title: 'Do you want to change status?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'YES',
                    denyButtonText: `NO`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/ticket/update",
                            type: "GET",
                            data: {
                                id: id
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
            })

            $(".confirm").click(function(event) {
                var self = this;
                let earn_id = $(this).attr('data-id');
                event.preventDefault();
                Swal.fire({
                    title: 'Do you want to approve the request?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    denyButtonText: `Don't`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/withdraw/approve_request",
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
                    title: 'Do you want to reject the request?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'YES',
                    denyButtonText: `NO`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/withdraw/reject_request",
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
        });
    </script>


@stop
