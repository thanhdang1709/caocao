@extends('adminlte::page')

@section('title', 'AZ WORLD')

@section('content_header')
    <h1 class="m-0 text-dark">List withdraw</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="col-md-4 offset-md-8 input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" name="daterange" style="margin-right: 20px;" />
                    <form action="" method="GET" style="margin-right: 20px;">
                        <div class="input-group input-group-lg">
                            <input type="search" class="form-control form-control-lg user_search" name="user_search" placeholder="Enter email or address... ">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn-primary searchbtn">Search...</button>
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
                                <th>Devices</th>
                                <th>Balance</th>
                                <th>Pending</th>
                                <th>Frozen</th>
                                <th>Address</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Request date</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($earns as $key => $earn)
                                {{-- @dd($earn); --}}
                                <tr>
                                    <td>{{ $earn->id }}</td>
                                    <td>{{ $earn->user_id }}</td>
                                    <td>{{ $earn->user->email }}</td>
                                    <td><a href="/withdraw/list?user_search={{ substr($earn->user->fcm_token, 0, 10) }}" target="_blank" class="click_to_search">{{ substr($earn->user->fcm_token, 0, 10) }}</a></td>
                                    <td>{{ $earn->user->balance }}</td>
                                    <td>{{ $earn->user->pending_balance }}</td>
                                    <td>{{ $earn->user->frozen }}

                                    </td>
                                    <td> <a href="https://bscscan.com/token/0x1f2cfde19976a2bf0a250900f7ace9c362908c93?a={{ $earn->user->address }}" target="_blank"> {{ $earn->user->address }}</a></td>
                                    <td class="text-red text-bold">{{ round($earn->amount) }}</td>
                                    <td>{{ $earn->status }}</td>
                                    <td>{{ $earn->description }}</td>
                                    <th>{{ $earn->created_at }}</th>
                                    <td class="d-flex"> <button data-id="{{ $earn->id }}" class="btn btn-success mr-2 confirm">Approve</button> <button data-id="{{ $earn->id }}" class="btn btn-danger mr-2 reject">Reject</button> </td>
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

            $('.searchbtn').click(function() {
                var user_id = $('.user_id').val();
                var start = $('input[name="daterange"]').data('daterangepicker').startDate.format(
                    'YYYY-MM-DD');
                var end = $('input[name="daterange"]').data('daterangepicker').endDate.format(
                    'YYYY-MM-DD');
                var user_search = $('.user_search').val();
                window.location.href = '/withdraw/list?from_date=' + start + '&to_date=' + end + '&user_search=' + user_search +
                    '&status=1';
            });

            $('input[name="daterange"]').daterangepicker({
                    startDate: moment().subtract('days', 29),
                    endDate: moment(),
                    dateLimit: {
                        days: 60
                    },
                    showDropdowns: true,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                        'Today': [moment(), moment()],
                    },
                    opens: 'left',
                    buttonClasses: ['btn btn-default'],
                    applyClass: 'btn-small btn-primary',
                    cancelClass: 'btn-small',
                    format: 'DD/MM/YYYY',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Submit',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom Range',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
                            'September', 'October', 'November', 'December'
                        ],
                        firstDay: 1
                    }
                },
                function(start, end) {
                    console.log("Callback has been called!");
                    $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format(
                        'D MMMM YYYY'));

                }
            );

            $('#reportrange span').html(moment().subtract('days', 29).format('D MMMM YYYY') + ' - ' + moment()
                .format('D MMMM YYYY'));


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
