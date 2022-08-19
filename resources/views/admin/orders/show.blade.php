@extends('admin.layouts.popup')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="card-title">{{$order->item_title}} order by {{$order->supplier->name}} </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body mt-4">
                    <div class="form-group">
                        <div class="user-add-shedule-list">
                            <h2 class="table-avatar">
                                <label>Supplier: </label>
                                <a href="" class="avatar ml-4" flow="right"><img src="{{$order->supplier->getImageUrlAttribute($order->supplier->id)}}" alt="user_id_{{$order->supplier->id}}" class="profile-user-img-small img-circle">  </a>
                                <a href="" class="ml-2"> {{$order->supplier->name}} </a>
                            </h2>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Item Name</label>
                        {{$order->item_title}}
                    </div>
                    <div class="form-group">
                        <label>Item Id / SKU</label>
                        {{$order->item_id}} / {{$order->sku}}
                    </div>
                    <div class="form-group">
                        <label>Item Image</label>
                        <img src="{{$base64}}" alt="Item Image" class="profile-user-img-small img-fluid" style="width: 70px;height: 60px;">
                    </div>

                    <div class="form-group">
                        <label>Currency</label>
                        {{$order->currency}}
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        {{$order->price}}
                    </div>

                    <div class="form-group">
                        <label>Due Date</label>
                        {{$order->due_date?$order->due_date : 'N/A'}}
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <span class="{{$class}}">{{$status}}</span>
                    </div>  

                    <div class="form-group">
                        <label>Order Note</label>
                        {!!$order->notes?$order->notes : 'N/A'!!}
                    </div>
                    @if(count($order->order_files)>0)
                    <div class="form-group">
                        <label>Order File</label>
                        <table class="table table-hover dataTable no-footer" id="table_order_files" width="100%">
                            <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Type</th>
                                <th>Uploaded By</th>
                                <th>Download</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($order->order_files as $order_file)
                                <tr>
                                    <td>{{$order_file->originalname}}</td>
                                    <td>{{$order_file->type}}</td>
                                    <td><img src="{{$order_file->creator->getImageUrlAttribute($order_file->creator->id)}}" alt="user_id_{{$order_file->creator->id}}" class="profile-user-img-small img-circle"></td>
                                    <td><a href="{{asset('public/order_files')}}/{{$order_file->file_name}}" download><i class="fas fa-download"></i></a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>
<!-- <script>
function datatables() {

    var table = $('#table_task_history').DataTable({
        dom: 'RBfrtip',
        buttons: [],
        select: true,
        
        aaSorting     : [[0, 'asc']],
        iDisplayLength: 25,
        stateSave     : true,
        responsive    : true,
        fixedHeader   : true,
        processing    : true,
        serverSide    : true,
        "bDestroy"    : true,
        pagingType    : "full_numbers",
        ajax          : {
            url     : '{{ url('admin/task/ajax/datatables_task_history') }}',
            dataType: 'json',
            data: {
                "task_id": {{$order->id}}
            },
        },
        columns       : [
            {data: 'taskAccepted', name: 'taskAccepted'},
            {data: 'status', name: 'status'},
            {data: 'data_created_at', name: 'data_created_at'},
            /*{data: 'data_updated_at', name: 'data_updated_at'},*/
        ],
    });
}

datatables()
</script> -->
@endsection