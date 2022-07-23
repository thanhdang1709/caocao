@extends('adminlte::page')

@section('title', 'AZ WORLD')

@section('content_header')
    <h1 class="m-0 text-dark">List earns</h1>
@stop



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>User Id</th>
                                <th>Content</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $key => $post)
                                <tr>
                                    <td class="id">{{ $post->id }}</td>
                                    <td>{{ $post->user_id }}</td>
                                    <td>{{ $post->content }}</td>
                                    <td>{{ $post->image }}</td>
                                    <td>{{ $post->status }}</td>
                                    <td>{{ $post->created_at }}</td>
                                    <td><button class="btn btn-danger submit">Delete</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-2">
                        @php
                            $query = isset($_GET) ? $_GET : [];
                        @endphp
                        {{ $posts->appends($query)->links('pagination::bootstrap-4') }}
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

            $(".submit").on("click", function() {
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
                            url: "/post/delete/" + id,
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
