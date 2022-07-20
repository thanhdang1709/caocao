@extends('adminlte::page')

@section('title', 'AZ WORLD SENT FCM')

@section('content_header')
    <h1 class="m-0 text-dark">Sent FCM</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="row">
                    <div class="col-4">
                        <form action="" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="">Enter user id:</label>
                                    <input type="text" class="form-control" name="user_id" id="user_id" placeholder="User id" value="@php if(isset($_GET['user_id'])){echo $_GET['user_id'];} @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="">Enter title:</label>
                                    <input type="text" class="form-control" name="title" id="title" placeholder="Title">
                                </div>
                                <div class="form-group">
                                    <label for="">Enter notification:</label>
                                    <br />
                                    <textarea class="form-control" name="notification" id="notification" cols="100%" rows="5" placeholder="Description"></textarea>
                                    {{-- <input type="text" class="form-control" name="description" id="description"
                                        placeholder="User id"> --}}
                                </div>
                                <div class="card-footer">
                                    <button type="" name="submit" id="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-4">
                        <p>Withdrawal will resolve in 2-3 weeks, please wait for my partner.</p>
                        <p>Pending will resolve in 2-3 weeks, please wait for my partner.</p>
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

            $("#submit").click(function(event) {
                var self = this;
                // let earn_id = $(this).attr('data-id');
                var user_id = $("#user_id").val();
                var title = $("#title").val();
                var notification = $("#notification").val();
                // alert(title);
                event.preventDefault();
                Swal.fire({
                    title: 'Do you want to approve the request?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    denyButtonText: `Don't`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/ticket/sent",
                            type: "GET",
                            data: {
                                // earn_id: earn_id,
                                user_id: user_id,
                                title: title,
                                notification: notification,
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
        .form-control {
            width: 500px;
        }

        form {
            /* margin: auto; */
        }
    </style>


@stop
