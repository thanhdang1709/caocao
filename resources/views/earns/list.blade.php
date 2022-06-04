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
                    {{$success}}
                    </div>
                @endif
                @if (!empty($error)) 
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Alert!</h5>
                   {{$error}}
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
                                <th>Reward</th>
                                <th>Status</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($earns as $key => $earn )
                            
                            <tr>
                                <td>{{$earn->id}}</td>
                                <td>{{$earn->user_id}}</td>
                                <td>{{$earn->user->email}}</td>
                                <td> <a href="https://bscscan.com/token/0x1f2cfde19976a2bf0a250900f7ace9c362908c93?a={{$earn->user->address}}" target="_blank" > {{$earn->user->address}}</a></td>
                                <td class="text-red text-bold">{{number_format($earn->reward)}}</td>
                                <td>{{$earn->status}}</td>
                                <td>{{$earn->subject}}</td>
                                <td>{{$earn->description}}</td>
                                <th>{{$earn->created_at}}</th>
                                <td class="d-flex">  <button  data-id="{{$earn->id}}" class="btn btn-success mr-2 confirm">Approve</button> <button  data-id="{{$earn->id}}"   class="btn btn-danger mr-2 reject">Reject</button> </td>
                            </tr>
                            
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-2">
                        {{$earns->appends(['sort' => 'reward', 'status' => "1", 'today'=> 1])->links('pagination::bootstrap-4')}}
                      </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script >
    

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN':  "{{ csrf_token() }}"
            }
        });

        $(".confirm").click(function(event){
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
                    type:"POST",
                    data:{
                    earn_id: earn_id,
                    },
                    success:function(response){
                        if(response.type == "RESPONSE_OK") {
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


        $(".reject").click(function(event){
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
                    type:"POST",
                    data:{
                    earn_id: earn_id,
                    },
                    success:function(response){
                        if(response.type == "RESPONSE_OK") {
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