@extends('adminlte::page')

@section('title', 'AZ WORLD SENT FCM')

@section('content_header')
    <h1 class="m-0 text-dark">Sent FCM</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                
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
            title: 'Do you want to approve the request?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Yes',
            denyButtonText: `Don't`,
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/withdraw/1",
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