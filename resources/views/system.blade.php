@extends('adminlte::page')

@section('title', 'AZ WORLD')

@section('content_header')
    <h1 class="m-0 text-dark">System</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <form action="" method="POST">
                            @csrf
                            <div class="card-body">
                                @foreach ($systems as $key => $system)
                                    <div class="form-group">
                                        <label for="">{{ $system->key }}:</label>
                                        <input style="width: 400px" type="text" class="form-control" value="{{ $system->value }}" name="{{ $system->key }}">
                                    </div>
                                @endforeach
                                <div class="card-footer">
                                    <button type="submit" id="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
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
