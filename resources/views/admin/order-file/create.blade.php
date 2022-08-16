@extends('admin.layouts.popup')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="error_message">
            @include('message.alert')
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="card-title">Upload Files</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.order-file.store') }}" method="post"  enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="order_id" value="{{$order_id}}" id="order_id" required>

                        <div class="form-group">
                            <label>Order Files</label>
                            <input name="order_files[]" type="file" multiple="multiple" />
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="" class="btn btn-secondary"  data-dismiss="modal">Close</a>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>

@endsection